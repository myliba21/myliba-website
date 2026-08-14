#!/usr/bin/env node
'use strict';

const fs = require('fs');
const path = require('path');

try {
  const [inputPath, outputPath] = process.argv.slice(2);
  if (!inputPath || !outputPath) {
    throw new Error('Usage: node ua-tour-analyze.js <input.json> <output.json>');
  }

  const input = JSON.parse(fs.readFileSync(inputPath, 'utf8'));
  const nodes = Array.isArray(input.nodes) ? input.nodes : [];
  const edges = Array.isArray(input.edges) ? input.edges : [];
  const layers = Array.isArray(input.layers) ? input.layers : [];
  const nodeById = new Map(nodes.map((node) => [node.id, node]));
  const fanIn = new Map(nodes.map((node) => [node.id, 0]));
  const fanOut = new Map(nodes.map((node) => [node.id, 0]));

  for (const edge of edges) {
    if (fanOut.has(edge.source)) fanOut.set(edge.source, fanOut.get(edge.source) + 1);
    if (fanIn.has(edge.target)) fanIn.set(edge.target, fanIn.get(edge.target) + 1);
  }

  const rank = (counts, key) => nodes
    .map((node) => ({ id: node.id, [key]: counts.get(node.id) || 0, name: node.name }))
    .sort((a, b) => b[key] - a[key] || a.id.localeCompare(b.id));
  const allFanIn = rank(fanIn, 'fanIn');
  const allFanOut = rank(fanOut, 'fanOut');
  const topOutCount = Math.max(1, Math.ceil(nodes.length * 0.10));
  const bottomInCount = Math.max(1, Math.ceil(nodes.length * 0.25));
  const highFanOut = new Set(allFanOut.slice(0, topOutCount).map((item) => item.id));
  const lowFanIn = new Set(allFanIn.slice(-bottomInCount).map((item) => item.id));
  const entryNames = new Set([
    'index.ts', 'index.js', 'main.ts', 'main.js', 'app.ts', 'app.js', 'server.ts', 'server.js',
    'mod.rs', 'main.go', 'main.py', 'main.rs', 'manage.py', 'app.py', 'wsgi.py', 'asgi.py',
    'run.py', '__main__.py', 'Application.java', 'Main.java', 'Program.cs', 'config.ru',
    'index.php', 'App.swift', 'Application.kt', 'main.cpp', 'main.c'
  ]);
  const entryPointCandidates = nodes.map((node) => {
    let score = 0;
    const filePath = node.filePath || '';
    const filename = path.basename(filePath || node.name || '');
    const depth = filePath.split('/').filter(Boolean).length;
    if (node.type === 'file') {
      if (entryNames.has(filename)) score += 3;
      if (depth <= 2) score += 1;
      if (highFanOut.has(node.id)) score += 1;
      if (lowFanIn.has(node.id)) score += 1;
    } else if (node.type === 'document') {
      if (filePath === 'README.md') score += 5;
      else if (/^[^/]+\.md$/i.test(filePath)) score += 2;
    }
    return { id: node.id, score, name: node.name, summary: node.summary };
  }).filter((candidate) => candidate.score > 0)
    .sort((a, b) => b.score - a.score || a.id.localeCompare(b.id))
    .slice(0, 5);

  const codeEntry = entryPointCandidates.find((candidate) => nodeById.get(candidate.id)?.type === 'file')
    || nodes.find((node) => node.type === 'file');
  const allowedTypes = new Set(['imports', 'calls']);
  const adjacency = new Map(nodes.map((node) => [node.id, []]));
  for (const edge of edges) {
    if (allowedTypes.has(edge.type) && adjacency.has(edge.source) && nodeById.has(edge.target)) {
      adjacency.get(edge.source).push(edge.target);
    }
  }
  for (const targets of adjacency.values()) targets.sort();
  const bfsTraversal = { startNode: codeEntry?.id || null, order: [], depthMap: {}, byDepth: {} };
  if (codeEntry) {
    const queue = [codeEntry.id];
    bfsTraversal.depthMap[codeEntry.id] = 0;
    for (let index = 0; index < queue.length; index += 1) {
      const current = queue[index];
      bfsTraversal.order.push(current);
      const depth = bfsTraversal.depthMap[current];
      (bfsTraversal.byDepth[depth] ||= []).push(current);
      for (const target of adjacency.get(current) || []) {
        if (bfsTraversal.depthMap[target] === undefined) {
          bfsTraversal.depthMap[target] = depth + 1;
          queue.push(target);
        }
      }
    }
  }

  const shape = (node) => ({ id: node.id, name: node.name, type: node.type, summary: node.summary });
  const nonCodeFiles = {
    documentation: nodes.filter((node) => node.type === 'document').map(shape),
    infrastructure: nodes.filter((node) => ['service', 'pipeline', 'resource'].includes(node.type)).map(shape),
    data: nodes.filter((node) => ['table', 'schema', 'endpoint'].includes(node.type)).map(shape),
    config: nodes.filter((node) => node.type === 'config').map(shape)
  };

  const directed = new Set(edges.filter((edge) => allowedTypes.has(edge.type))
    .map((edge) => `${edge.type}\u0000${edge.source}\u0000${edge.target}`));
  const mutualPairs = [];
  for (const edge of edges) {
    if (allowedTypes.has(edge.type) && directed.has(`${edge.type}\u0000${edge.target}\u0000${edge.source}`) && edge.source < edge.target) {
      mutualPairs.push([edge.source, edge.target]);
    }
  }
  const clusters = [];
  for (const pair of mutualPairs) {
    const cluster = new Set(pair);
    let expanded = true;
    while (expanded && cluster.size < 5) {
      expanded = false;
      for (const candidate of nodes.map((node) => node.id).sort()) {
        if (cluster.has(candidate)) continue;
        const connected = [...cluster].filter((member) => edges.some((edge) =>
          allowedTypes.has(edge.type) && ((edge.source === candidate && edge.target === member) || (edge.source === member && edge.target === candidate))
        )).length;
        if (connected >= 2) {
          cluster.add(candidate);
          expanded = true;
          if (cluster.size === 5) break;
        }
      }
    }
    const sortedNodes = [...cluster].sort();
    if (!clusters.some((existing) => existing.nodes.every((id) => cluster.has(id)) && existing.nodes.length === sortedNodes.length)) {
      const edgeCount = edges.filter((edge) => cluster.has(edge.source) && cluster.has(edge.target)).length;
      clusters.push({ nodes: sortedNodes, edgeCount });
    }
  }
  clusters.sort((a, b) => b.edgeCount - a.edgeCount || b.nodes.length - a.nodes.length || a.nodes[0].localeCompare(b.nodes[0]));

  const nodeSummaryIndex = Object.fromEntries(nodes.map((node) => [node.id, {
    name: node.name,
    type: node.type,
    summary: node.summary
  }]));
  const result = {
    scriptCompleted: true,
    entryPointCandidates,
    fanInRanking: allFanIn.slice(0, 20),
    fanOutRanking: allFanOut.slice(0, 20),
    bfsTraversal,
    nonCodeFiles,
    clusters: clusters.slice(0, 10),
    layers: { count: layers.length, list: layers.map(({ id, name, description }) => ({ id, name, description })) },
    nodeSummaryIndex,
    totalNodes: nodes.length,
    totalEdges: edges.length
  };
  fs.writeFileSync(outputPath, `${JSON.stringify(result, null, 2)}\n`);
} catch (error) {
  console.error(error.stack || error.message);
  process.exit(1);
}
