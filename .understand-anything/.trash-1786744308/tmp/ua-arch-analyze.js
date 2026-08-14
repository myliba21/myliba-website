const fs = require('fs');
const path = require('path');

function die(error) {
  console.error(error instanceof Error ? error.stack : String(error));
  process.exit(1);
}

function commonDirectoryPrefix(paths) {
  if (!paths.length) return [];
  const dirs = paths.map((filePath) => filePath.split('/').slice(0, -1));
  const prefix = [];
  for (let i = 0; ; i += 1) {
    const value = dirs[0][i];
    if (value === undefined || !dirs.every((parts) => parts[i] === value)) break;
    prefix.push(value);
  }
  return prefix;
}

function directoryPattern(group) {
  const patterns = {
    api: ['routes', 'api', 'controllers', 'controller', 'endpoints', 'handlers', 'routers', 'serializers', 'blueprints'],
    service: ['services', 'core', 'lib', 'domain', 'logic', 'internal', 'signals', 'composables', 'mailers', 'jobs', 'channels'],
    data: ['models', 'db', 'data', 'persistence', 'repository', 'entities', 'entity', 'migrations', 'sql', 'database', 'schema'],
    ui: ['components', 'views', 'pages', 'ui', 'layouts', 'screens'],
    middleware: ['middleware', 'plugins', 'interceptors', 'guards'],
    utility: ['utils', 'helpers', 'common', 'shared', 'tools', 'pkg', 'templatetags'],
    config: ['config', 'constants', 'env', 'settings', 'management', 'commands'],
    test: ['__tests__', 'test', 'tests', 'spec', 'specs'],
    types: ['types', 'interfaces', 'schemas', 'contracts', 'dtos', 'dto', 'request', 'response'],
    hooks: ['hooks'], state: ['store', 'state', 'reducers', 'actions', 'slices'],
    assets: ['assets', 'static', 'public'], entry: ['cmd', 'bin'],
    documentation: ['docs', 'documentation', 'wiki'],
    infrastructure: ['deploy', 'deployment', 'infra', 'infrastructure', 'k8s', 'kubernetes', 'helm', 'charts', 'terraform', 'tf', 'docker'],
    'ci-cd': ['.github', '.gitlab', '.circleci']
  };
  const lower = group.toLowerCase();
  for (const [label, names] of Object.entries(patterns)) if (names.includes(lower)) return label;
  return null;
}

function filePattern(filePath) {
  const name = path.posix.basename(filePath);
  if (/\.(test|spec)\./i.test(name) || /^test_.*\.py$/i.test(name) || /_test\.go$/i.test(name) || /(Test\.java|_spec\.rb|Test\.php|Tests\.cs)$/i.test(name)) return 'test';
  if (/\.d\.ts$/i.test(name)) return 'types';
  if (/^(index\.(ts|js)|__init__\.py)$/i.test(name)) return 'entry';
  if (/^(manage\.py|config\.ru|Application\.java|Program\.cs)$/i.test(name)) return 'entry';
  if (/^(wsgi|asgi)\.py$/i.test(name)) return 'config';
  if (/^(Cargo\.toml|go\.mod|Gemfile|pom\.xml|build\.gradle|composer\.json)$/i.test(name)) return 'config';
  if (/^Dockerfile/i.test(name) || /^docker-compose\./i.test(name) || /\.(tf|tfvars)$/i.test(name) || name === 'Makefile') return 'infrastructure';
  if (filePath.startsWith('.github/workflows/') || name === '.gitlab-ci.yml' || name === 'Jenkinsfile') return 'ci-cd';
  if (/\.sql$/i.test(name)) return 'data';
  if (/\.(graphql|gql|proto)$/i.test(name)) return 'types';
  if (/\.(md|rst)$/i.test(name)) return 'documentation';
  return null;
}

try {
  const [inputPath, outputPath] = process.argv.slice(2);
  if (!inputPath || !outputPath) throw new Error('Usage: node ua-arch-analyze.js <input.json> <output.json>');
  const input = JSON.parse(fs.readFileSync(inputPath, 'utf8'));
  const { fileNodes, importEdges, allEdges } = input;
  const byId = new Map(fileNodes.map((node) => [node.id, node]));
  const prefix = commonDirectoryPrefix(fileNodes.map((node) => node.filePath));
  const directoryGroups = {};
  const groupById = {};
  for (const node of fileNodes) {
    const parts = node.filePath.split('/');
    let group = parts.length > prefix.length + 1 ? parts[prefix.length] : (parts.length > 1 ? parts[0] : 'root');
    if (fileNodes.every((item) => item.filePath.split('/').length <= prefix.length + 1)) group = filePattern(node.filePath) || path.posix.extname(node.filePath) || 'other';
    (directoryGroups[group] ||= []).push(node.id);
    groupById[node.id] = group;
  }
  const nodeTypeGroups = {};
  for (const node of fileNodes) (nodeTypeGroups[node.type] ||= []).push(node.id);
  const fileFanIn = Object.fromEntries(fileNodes.map((node) => [node.id, 0]));
  const fileFanOut = Object.fromEntries(fileNodes.map((node) => [node.id, 0]));
  const adjacency = Object.fromEntries(fileNodes.map((node) => [node.id, []]));
  const interCounts = new Map();
  const groupRelations = {};
  for (const group of Object.keys(directoryGroups)) groupRelations[group] = { importsFrom: new Set(), importedBy: new Set() };
  for (const edge of importEdges) {
    fileFanOut[edge.source] += 1; fileFanIn[edge.target] += 1; adjacency[edge.source].push(edge.target);
    const from = groupById[edge.source], to = groupById[edge.target];
    if (from !== to) {
      interCounts.set(`${from}\u0000${to}`, (interCounts.get(`${from}\u0000${to}`) || 0) + 1);
      groupRelations[from].importsFrom.add(to); groupRelations[to].importedBy.add(from);
    }
  }
  const crossCounts = new Map();
  for (const edge of allEdges) {
    const fromType = byId.get(edge.source).type, toType = byId.get(edge.target).type;
    const key = `${fromType}\u0000${toType}\u0000${edge.type}`;
    crossCounts.set(key, (crossCounts.get(key) || 0) + 1);
  }
  const interGroupImports = [...interCounts].map(([key, count]) => { const [from, to] = key.split('\u0000'); return { from, to, count }; });
  const intraGroupDensity = {};
  for (const group of Object.keys(directoryGroups)) {
    let internalEdges = 0, totalEdges = 0;
    for (const edge of importEdges) {
      const sourceGroup = groupById[edge.source], targetGroup = groupById[edge.target];
      if (sourceGroup === group || targetGroup === group) totalEdges += 1;
      if (sourceGroup === group && targetGroup === group) internalEdges += 1;
    }
    intraGroupDensity[group] = { internalEdges, totalEdges, density: totalEdges ? internalEdges / totalEdges : 0 };
  }
  const patternMatches = {};
  for (const group of Object.keys(directoryGroups)) patternMatches[group] = directoryPattern(group);
  const infraFiles = fileNodes.filter((node) => ['infrastructure', 'ci-cd'].includes(filePattern(node.filePath)) || ['service', 'pipeline', 'resource'].includes(node.type)).map((node) => node.filePath);
  const deploymentTopology = {
    hasDockerfile: fileNodes.some((node) => /^Dockerfile/i.test(path.posix.basename(node.filePath))),
    hasCompose: fileNodes.some((node) => /^docker-compose\./i.test(path.posix.basename(node.filePath))),
    hasK8s: fileNodes.some((node) => /(^|\/)(k8s|kubernetes|helm|charts)(\/|$)/i.test(node.filePath)),
    hasTerraform: fileNodes.some((node) => /\.(tf|tfvars)$/i.test(node.filePath)),
    hasCI: fileNodes.some((node) => filePattern(node.filePath) === 'ci-cd'), infraFiles
  };
  const dataPipeline = {
    schemaFiles: fileNodes.filter((node) => ['schema', 'table'].includes(node.type) || /\.(sql|graphql|gql|proto|prisma)$/i.test(node.filePath)).map((node) => node.filePath),
    migrationFiles: fileNodes.filter((node) => /(^|\/)migrations?(\/|$)/i.test(node.filePath)).map((node) => node.filePath),
    dataModelFiles: fileNodes.filter((node) => /(^|\/)(models?|entities|repository)(\/|$)/i.test(node.filePath) || (node.tags || []).some((tag) => /model|data/i.test(tag))).map((node) => node.filePath),
    apiHandlerFiles: fileNodes.filter((node) => ['endpoint'].includes(node.type) || /(^|\/)(routes?|api|controllers?|handlers?)(\/|$)/i.test(node.filePath)).map((node) => node.filePath)
  };
  const docNodes = fileNodes.filter((node) => node.type === 'document' || filePattern(node.filePath) === 'documentation');
  const documented = new Set();
  for (const node of docNodes) {
    const group = groupById[node.id]; documented.add(group);
    const text = `${node.summary || ''} ${(node.tags || []).join(' ')}`.toLowerCase();
    for (const candidate of Object.keys(directoryGroups)) if (text.includes(candidate.toLowerCase())) documented.add(candidate);
  }
  const totalGroups = Object.keys(directoryGroups).length;
  const docCoverage = { groupsWithDocs: documented.size, totalGroups, coverageRatio: totalGroups ? documented.size / totalGroups : 0, undocumentedGroups: Object.keys(directoryGroups).filter((group) => !documented.has(group)) };
  const dependencyDirection = [];
  const seenPairs = new Set();
  for (const { from, to } of interGroupImports) {
    const pair = [from, to].sort().join('\u0000'); if (seenPairs.has(pair)) continue; seenPairs.add(pair);
    const forward = interCounts.get(`${from}\u0000${to}`) || 0, reverse = interCounts.get(`${to}\u0000${from}`) || 0;
    if (forward >= reverse) dependencyDirection.push({ dependent: from, dependsOn: to });
    else dependencyDirection.push({ dependent: to, dependsOn: from });
  }
  const output = {
    scriptCompleted: true, commonPrefix: prefix.join('/'), directoryGroups, nodeTypeGroups, adjacency,
    groupRelations: Object.fromEntries(Object.entries(groupRelations).map(([group, value]) => [group, { importsFrom: [...value.importsFrom], importedBy: [...value.importedBy] }])),
    crossCategoryEdges: [...crossCounts].map(([key, count]) => { const [fromType, toType, edgeType] = key.split('\u0000'); return { fromType, toType, edgeType, count }; }),
    interGroupImports, intraGroupDensity, patternMatches, deploymentTopology, dataPipeline, docCoverage, dependencyDirection,
    fileStats: { totalFileNodes: fileNodes.length, filesPerGroup: Object.fromEntries(Object.entries(directoryGroups).map(([group, ids]) => [group, ids.length])), nodeTypeCounts: Object.fromEntries(Object.entries(nodeTypeGroups).map(([type, ids]) => [type, ids.length])) },
    fileFanIn, fileFanOut
  };
  fs.writeFileSync(outputPath, `${JSON.stringify(output, null, 2)}\n`);
} catch (error) { die(error); }
