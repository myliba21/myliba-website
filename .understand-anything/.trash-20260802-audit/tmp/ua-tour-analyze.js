const fs = require('fs');

try {
  const [inputPath, outputPath] = process.argv.slice(2);
  if (!inputPath || !outputPath) {
    throw new Error('Usage: node ua-tour-analyze.js <input.json> <output.json>');
  }
  const input = JSON.parse(fs.readFileSync(inputPath, 'utf8'));
  const nodes = input.nodes || [];
  const edges = input.edges || [];
  const layers = input.layers || [];
  const nodeMap = new Map(nodes.map((node) => [node.id, node]));
  const fanIn = new Map(nodes.map((node) => [node.id, 0]));
  const fanOut = new Map(nodes.map((node) => [node.id, 0]));

  for (const edge of edges) {
    if (fanOut.has(edge.source)) fanOut.set(edge.source, fanOut.get(edge.source) + 1);
    if (fanIn.has(edge.target)) fanIn.set(edge.target, fanIn.get(edge.target) + 1);
  }

  const rank = (counts, field) => nodes
    .map((node) => ({ id: node.id, [field]: counts.get(node.id), name: node.name }))
    .sort((a, b) => b[field] - a[field] || a.id.localeCompare(b.id))
    .slice(0, 20);
  const fanInRanking = rank(fanIn, 'fanIn');
  const fanOutRanking = rank(fanOut, 'fanOut');

  const codeNodes = nodes.filter((node) => node.type === 'file');
  const topOutCount = Math.max(1, Math.ceil(codeNodes.length * 0.10));
  const highOut = new Set([...codeNodes]
    .sort((a, b) => fanOut.get(b.id) - fanOut.get(a.id))
    .slice(0, topOutCount).map((node) => node.id));
  const lowInCount = Math.max(1, Math.ceil(codeNodes.length * 0.25));
  const lowIn = new Set([...codeNodes]
    .sort((a, b) => fanIn.get(a.id) - fanIn.get(b.id))
    .slice(0, lowInCount).map((node) => node.id));
  const entryNames = new Set([
    'index.ts', 'index.js', 'main.ts', 'main.js', 'app.ts', 'app.js', 'server.ts', 'server.js',
    'mod.rs', 'main.go', 'main.py', 'main.rs', 'manage.py', 'app.py', 'wsgi.py', 'asgi.py',
    'run.py', '__main__.py', 'Application.java', 'Main.java', 'Program.cs', 'config.ru', 'index.php',
    'App.swift', 'Application.kt', 'main.cpp', 'main.c'
  ]);

  const entryPointCandidates = nodes.map((node) => {
    let score = 0;
    const filePath = node.filePath || '';
    const parts = filePath.split('/').filter(Boolean);
    if (node.type === 'file') {
      if (entryNames.has(node.name)) score += 3;
      if (parts.length <= 2) score += 1;
      if (highOut.has(node.id)) score += 1;
      if (lowIn.has(node.id)) score += 1;
    } else if (node.type === 'document') {
      if (node.name === 'README.md' && parts.length === 1) score += 5;
      else if (node.name.endsWith('.md') && parts.length === 1) score += 2;
    }
    return { id: node.id, score, name: node.name, summary: node.summary || '' };
  }).filter((candidate) => candidate.score > 0)
    .sort((a, b) => b.score - a.score || a.id.localeCompare(b.id))
    .slice(0, 5);

  const startNode = entryPointCandidates.find((candidate) => nodeMap.get(candidate.id)?.type === 'file')?.id || null;
  const adjacency = new Map(nodes.map((node) => [node.id, []]));
  for (const edge of edges) {
    if ((edge.type === 'imports' || edge.type === 'calls') && adjacency.has(edge.source) && nodeMap.has(edge.target)) {
      adjacency.get(edge.source).push(edge.target);
    }
  }
  const order = [];
  const depthMap = {};
  const byDepth = {};
  if (startNode) {
    const queue = [startNode];
    depthMap[startNode] = 0;
    while (queue.length) {
      const current = queue.shift();
      const depth = depthMap[current];
      order.push(current);
      (byDepth[String(depth)] ||= []).push(current);
      for (const target of adjacency.get(current) || []) {
        if (depthMap[target] === undefined) {
          depthMap[target] = depth + 1;
          queue.push(target);
        }
      }
    }
  }

  const inventory = (types) => nodes.filter((node) => types.includes(node.type)).map((node) => ({
    id: node.id, name: node.name, type: node.type, summary: node.summary || ''
  }));
  const nonCodeFiles = {
    documentation: inventory(['document']),
    infrastructure: inventory(['service', 'pipeline', 'resource']),
    data: inventory(['table', 'schema', 'endpoint']),
    config: inventory(['config'])
  };

  const relationKeys = new Set(edges
    .filter((edge) => edge.type === 'imports' || edge.type === 'calls')
    .map((edge) => `${edge.source}\u0000${edge.target}\u0000${edge.type}`));
  const pairs = [];
  for (const edge of edges) {
    if ((edge.type === 'imports' || edge.type === 'calls') &&
        relationKeys.has(`${edge.target}\u0000${edge.source}\u0000${edge.type}`) && edge.source < edge.target) {
      pairs.push([edge.source, edge.target]);
    }
  }
  const clusters = [];
  const edgeBetweenCount = (set) => edges.filter((edge) => set.has(edge.source) && set.has(edge.target)).length;
  for (const pair of pairs) {
    const set = new Set(pair);
    let expanded = true;
    while (expanded && set.size < 5) {
      expanded = false;
      for (const node of nodes) {
        if (set.has(node.id)) continue;
        const connections = edges.filter((edge) =>
          (edge.source === node.id && set.has(edge.target)) || (edge.target === node.id && set.has(edge.source))
        ).length;
        if (connections >= 2) {
          set.add(node.id);
          expanded = true;
          if (set.size === 5) break;
        }
      }
    }
    const key = [...set].sort().join('|');
    if (!clusters.some((cluster) => cluster.key === key)) {
      clusters.push({ key, nodes: [...set], edgeCount: edgeBetweenCount(set) });
    }
  }
  clusters.sort((a, b) => b.edgeCount - a.edgeCount || b.nodes.length - a.nodes.length);

  const nodeSummaryIndex = Object.fromEntries(nodes.map((node) => [node.id, {
    name: node.name, type: node.type, summary: node.summary || ''
  }]));
  const result = {
    scriptCompleted: true,
    entryPointCandidates,
    fanInRanking,
    fanOutRanking,
    bfsTraversal: { startNode, order, depthMap, byDepth },
    nonCodeFiles,
    clusters: clusters.slice(0, 10).map(({ nodes: ids, edgeCount }) => ({ nodes: ids, edgeCount })),
    layers: { count: layers.length, list: layers.map(({ id, name, description }) => ({ id, name, description })) },
    nodeSummaryIndex,
    totalNodes: nodes.length,
    totalEdges: edges.length
  };
  fs.writeFileSync(outputPath, JSON.stringify(result, null, 2));
} catch (error) {
  console.error(error.stack || error.message);
  process.exit(1);
}
