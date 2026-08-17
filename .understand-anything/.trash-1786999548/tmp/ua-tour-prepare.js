#!/usr/bin/env node
const fs = require('fs');

try {
  const [graphPath, layersPath, outputPath] = process.argv.slice(2);
  if (!graphPath || !layersPath || !outputPath) throw new Error('Kullanım: node ua-tour-prepare.js <graph> <layers> <output>');
  const graph = JSON.parse(fs.readFileSync(graphPath, 'utf8'));
  const layers = JSON.parse(fs.readFileSync(layersPath, 'utf8'));
  const fileLevelTypes = new Set(['file', 'config', 'document', 'service', 'pipeline', 'resource', 'table', 'schema', 'endpoint']);
  const nodes = graph.nodes.filter((node) => fileLevelTypes.has(node.type));
  fs.writeFileSync(outputPath, JSON.stringify({ nodes, edges: graph.edges, layers }, null, 2) + '\n');
} catch (error) {
  console.error(error.stack || error.message);
  process.exit(1);
}
