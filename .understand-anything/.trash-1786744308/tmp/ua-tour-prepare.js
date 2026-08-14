#!/usr/bin/env node
'use strict';

const fs = require('fs');

try {
  const [graphPath, layersPath, outputPath] = process.argv.slice(2);
  if (!graphPath || !layersPath || !outputPath) {
    throw new Error('Usage: node ua-tour-prepare.js <graph.json> <layers.json> <output.json>');
  }

  const graph = JSON.parse(fs.readFileSync(graphPath, 'utf8'));
  const rawLayers = JSON.parse(fs.readFileSync(layersPath, 'utf8'));
  const layers = (Array.isArray(rawLayers) ? rawLayers : rawLayers.layers || []).map(
    ({ id, name, description }) => ({ id, name, description })
  );
  fs.writeFileSync(outputPath, `${JSON.stringify({ nodes: graph.nodes, edges: graph.edges, layers }, null, 2)}\n`);
} catch (error) {
  console.error(error.stack || error.message);
  process.exit(1);
}
