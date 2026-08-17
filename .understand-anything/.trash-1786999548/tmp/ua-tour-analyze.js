#!/usr/bin/env node
const fs = require('fs');

try {
  const [inputPath, outputPath] = process.argv.slice(2);
  if (!inputPath || !outputPath) throw new Error('Kullanım: node ua-tour-analyze.js <input> <output>');
  const input = JSON.parse(fs.readFileSync(inputPath, 'utf8'));
  const nodes = input.nodes || [];
  const edges = input.edges || [];
  const layers = input.layers || [];
  const byId = new Map(nodes.map((node) => [node.id, node]));
  const fanIn = new Map(nodes.map((node) => [node.id, 0]));
  const fanOut = new Map(nodes.map((node) => [node.id, 0]));
  for (const edge of edges) {
    if (fanIn.has(edge.target)) fanIn.set(edge.target, fanIn.get(edge.target) + 1);
    if (fanOut.has(edge.source)) fanOut.set(edge.source, fanOut.get(edge.source) + 1);
  }
  const rank = (counts, key) => nodes
    .map((node) => ({ id: node.id, [key]: counts.get(node.id), name: node.name }))
    .sort((a, b) => b[key] - a[key] || a.id.localeCompare(b.id))
    .slice(0, 20);
  const sortedOut = [...fanOut.values()].sort((a, b) => a - b);
  const sortedIn = [...fanIn.values()].sort((a, b) => a - b);
  const out90 = sortedOut[Math.max(0, Math.floor(sortedOut.length * 0.9))] || 0;
  const in25 = sortedIn[Math.max(0, Math.floor(sortedIn.length * 0.25))] || 0;
  const standardEntries = new Set(['index.ts','index.js','main.ts','main.js','app.ts','app.js','server.ts','server.js','mod.rs','main.go','main.py','main.rs','manage.py','app.py','wsgi.py','asgi.py','run.py','__main__.py','application.java','program.cs','config.ru','index.php','app.swift','application.kt','main.cpp','main.c']);
  const candidates = [];
  for (const node of nodes) {
    const path = node.filePath || '';
    const name = (node.name || '').toLowerCase();
    const depth = path.split('/').filter(Boolean).length;
    let score = 0;
    if (node.type === 'document') {
      if (name === 'readme.md' && depth <= 2) score += 5;
      else if (name.endsWith('.md') && depth <= 2) score += 2;
    } else if (node.type === 'file') {
      if (standardEntries.has(name)) score += 3;
      if (/\/themes\/[^/]+\/functions\.php$/i.test(path)) score += 3;
      const pluginMatch = path.match(/\/plugins\/([^/]+)\/([^/]+)\.php$/i);
      if (pluginMatch && pluginMatch[1].toLowerCase() === pluginMatch[2].toLowerCase()) score += 3;
      if (depth <= 2) score += 1;
      if (fanOut.get(node.id) >= out90 && fanOut.get(node.id) > 0) score += 1;
      if (fanIn.get(node.id) <= in25) score += 1;
    }
    if (score > 0) candidates.push({ id: node.id, score, name: node.name, summary: node.summary || '' });
  }
  candidates.sort((a, b) => b.score - a.score || a.id.localeCompare(b.id));
  const entryPointCandidates = candidates.slice(0, 5);
  const codeStart = entryPointCandidates.find((candidate) => byId.get(candidate.id)?.type === 'file');
  const adjacency = new Map(nodes.map((node) => [node.id, []]));
  for (const edge of edges) {
    if ((edge.type === 'imports' || edge.type === 'calls') && adjacency.has(edge.source) && byId.has(edge.target)) {
      adjacency.get(edge.source).push(edge.target);
    }
  }
  const order = [];
  const depthMap = {};
  const byDepth = {};
  if (codeStart) {
    const queue = [codeStart.id];
    depthMap[codeStart.id] = 0;
    while (queue.length) {
      const current = queue.shift();
      order.push(current);
      const depth = depthMap[current];
      (byDepth[depth] ||= []).push(current);
      for (const next of adjacency.get(current) || []) {
        if (depthMap[next] === undefined) {
          depthMap[next] = depth + 1;
          queue.push(next);
        }
      }
    }
  }
  const compact = (node) => ({ id: node.id, name: node.name, type: node.type, summary: node.summary || '' });
  const nonCodeFiles = {
    documentation: nodes.filter((n) => n.type === 'document').map(compact),
    infrastructure: nodes.filter((n) => ['service','pipeline','resource'].includes(n.type)).map(compact),
    data: nodes.filter((n) => ['table','schema','endpoint'].includes(n.type)).map(compact),
    config: nodes.filter((n) => n.type === 'config').map(compact),
  };
  const reciprocalPairs = [];
  const directional = new Set(edges.filter((e) => ['imports','calls'].includes(e.type) && byId.has(e.source) && byId.has(e.target)).map((e) => `${e.source}\u0000${e.target}\u0000${e.type}`));
  for (const edge of edges) {
    if (!['imports','calls'].includes(edge.type) || !byId.has(edge.source) || !byId.has(edge.target)) continue;
    if (directional.has(`${edge.target}\u0000${edge.source}\u0000${edge.type}`) && edge.source < edge.target) reciprocalPairs.push([edge.source, edge.target]);
  }
  const clusters = reciprocalPairs.slice(0, 10).map((pair) => ({ nodes: pair, edgeCount: edges.filter((e) => pair.includes(e.source) && pair.includes(e.target)).length }));
  const nodeSummaryIndex = Object.fromEntries(nodes.map((node) => [node.id, { name: node.name, type: node.type, summary: node.summary || '' }]));
  const result = {
    scriptCompleted: true,
    entryPointCandidates,
    fanInRanking: rank(fanIn, 'fanIn'),
    fanOutRanking: rank(fanOut, 'fanOut'),
    bfsTraversal: { startNode: codeStart?.id || null, order, depthMap, byDepth },
    nonCodeFiles,
    clusters,
    layers: { count: layers.length, list: layers.map(({ id, name, description }) => ({ id, name, description })) },
    nodeSummaryIndex,
    totalNodes: nodes.length,
    totalEdges: edges.length,
  };
  fs.writeFileSync(outputPath, JSON.stringify(result, null, 2) + '\n');
} catch (error) {
  console.error(error.stack || error.message);
  process.exit(1);
}
