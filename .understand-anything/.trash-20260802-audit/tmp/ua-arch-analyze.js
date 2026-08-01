const fs = require('fs');
const path = require('path');

function fail(error) {
  console.error(error instanceof Error ? error.stack : String(error));
  process.exit(1);
}

function commonDirectoryPrefix(paths) {
  if (!paths.length) return [];
  const dirs = paths.map((p) => p.split('/').slice(0, -1));
  const prefix = [];
  for (let i = 0; ; i += 1) {
    const value = dirs[0][i];
    if (value === undefined || !dirs.every((parts) => parts[i] === value)) break;
    prefix.push(value);
  }
  return prefix;
}

function classifyGroup(group) {
  const patterns = [
    ['api', /^(routes?|api|controllers?|endpoints?|handlers?|serializers?|routers?|blueprints?)$/i],
    ['service', /^(services?|core|lib|domain|logic|internal|composables?|mailers?|jobs?|channels?|signals?)$/i],
    ['data', /^(models?|db|data|persistence|repositories?|entities|entity|migrations?|sql|database|schemas?)$/i],
    ['ui', /^(components?|views?|pages?|ui|layouts?|screens?)$/i],
    ['middleware', /^(middleware|plugins?|interceptors?|guards?)$/i],
    ['utility', /^(utils?|helpers?|common|shared|tools|templatetags|pkg)$/i],
    ['config', /^(config|constants|env|settings|management|commands?)$/i],
    ['test', /^(__tests__|tests?|specs?|src\/test\/java)$/i],
    ['types', /^(types?|interfaces?|schemas?|contracts?|dtos?|dto|requests?|responses?)$/i],
    ['hooks', /^hooks$/i], ['state', /^(store|state|reducers?|actions?|slices?)$/i],
    ['assets', /^(assets|static|public)$/i], ['entry', /^(cmd|bin)$/i],
    ['documentation', /^(docs|documentation|wiki)$/i],
    ['infrastructure', /^(deploy|deployment|infra|infrastructure|k8s|kubernetes|helm|charts|terraform|tf|docker)$/i],
    ['ci-cd', /^(\.github|\.gitlab|\.circleci)$/i]
  ];
  return patterns.find(([, regex]) => regex.test(group))?.[0] || null;
}

function classifyFile(filePath) {
  const base = path.posix.basename(filePath);
  if (/((\.test|\.spec)\.|^test_.*\.py$|_test\.go$|Test\.java$|_spec\.rb$|Test\.php$|Tests\.cs$)/i.test(base)) return 'test';
  if (/\.d\.ts$/i.test(base)) return 'types';
  if (/^(index\.(ts|js)|__init__\.py)$/i.test(base)) return 'entry';
  if (/^(manage\.py|config\.ru|Application\.java|Program\.cs)$/i.test(base)) return 'entry';
  if (/^(wsgi|asgi)\.py$/i.test(base)) return 'config';
  if (/^(Cargo\.toml|go\.mod|Gemfile|pom\.xml|build\.gradle|composer\.json)$/i.test(base)) return 'config';
  if (/^Dockerfile(\..+)?$/i.test(base) || /^docker-compose\..+$/i.test(base) || /^Makefile$/i.test(base)) return 'infrastructure';
  if (/\.(tf|tfvars)$/i.test(base)) return 'infrastructure';
  if (/^\.github\/workflows\//.test(filePath) || /^(\.gitlab-ci\.yml|Jenkinsfile)$/i.test(base)) return 'ci-cd';
  if (/\.sql$/i.test(base)) return 'data';
  if (/\.(graphql|gql|proto)$/i.test(base)) return 'types';
  if (/\.(md|rst)$/i.test(base)) return 'documentation';
  return null;
}

try {
  const [inputPath, outputPath] = process.argv.slice(2);
  if (!inputPath || !outputPath) throw new Error('Usage: node ua-arch-analyze.js <input.json> <output.json>');
  const input = JSON.parse(fs.readFileSync(inputPath, 'utf8'));
  const { fileNodes = [], importEdges = [], allEdges = [] } = input;
  const byId = new Map(fileNodes.map((node) => [node.id, node]));
  const prefix = commonDirectoryPrefix(fileNodes.map((node) => node.filePath));
  const isFlat = fileNodes.every((node) => node.filePath.split('/').length - prefix.length <= 1);
  const groupFor = (node) => {
    const parts = node.filePath.split('/');
    const remaining = parts.slice(prefix.length);
    if (isFlat) return classifyFile(node.filePath) || path.posix.extname(node.filePath).slice(1) || 'root';
    return remaining.length > 1 ? remaining[0] : 'root';
  };
  const directoryGroups = {};
  const nodeTypeGroups = {};
  const idToGroup = new Map();
  for (const node of fileNodes) {
    const group = groupFor(node);
    idToGroup.set(node.id, group);
    (directoryGroups[group] ||= []).push(node.id);
    (nodeTypeGroups[node.type] ||= []).push(node.id);
  }
  const fileFanIn = Object.fromEntries(fileNodes.map((node) => [node.id, 0]));
  const fileFanOut = Object.fromEntries(fileNodes.map((node) => [node.id, 0]));
  const importAdjacency = Object.fromEntries(fileNodes.map((node) => [node.id, []]));
  const inter = new Map();
  const groupImportsFrom = {};
  const groupImportedBy = {};
  for (const group of Object.keys(directoryGroups)) {
    groupImportsFrom[group] = new Set();
    groupImportedBy[group] = new Set();
  }
  for (const edge of importEdges) {
    if (!byId.has(edge.source) || !byId.has(edge.target)) continue;
    importAdjacency[edge.source].push(edge.target);
    fileFanOut[edge.source] += 1;
    fileFanIn[edge.target] += 1;
    const from = idToGroup.get(edge.source);
    const to = idToGroup.get(edge.target);
    if (from !== to) {
      groupImportsFrom[from].add(to);
      groupImportedBy[to].add(from);
    }
    inter.set(`${from}\u0000${to}`, (inter.get(`${from}\u0000${to}`) || 0) + 1);
  }
  const interGroupImports = [...inter].map(([key, count]) => {
    const [from, to] = key.split('\u0000');
    return { from, to, count };
  });
  const intraGroupDensity = {};
  for (const group of Object.keys(directoryGroups)) {
    let internalEdges = 0;
    let totalEdges = 0;
    for (const edge of importEdges) {
      const from = idToGroup.get(edge.source);
      const to = idToGroup.get(edge.target);
      if (from === group || to === group) totalEdges += 1;
      if (from === group && to === group) internalEdges += 1;
    }
    intraGroupDensity[group] = { internalEdges, totalEdges, density: totalEdges ? internalEdges / totalEdges : 0 };
  }
  const crossCounts = new Map();
  const nonCodeConnections = [];
  for (const edge of allEdges) {
    const source = byId.get(edge.source);
    const target = byId.get(edge.target);
    if (!source || !target) continue;
    const key = `${source.type}\u0000${target.type}\u0000${edge.type}`;
    crossCounts.set(key, (crossCounts.get(key) || 0) + 1);
    if (source.type !== 'file' || target.type !== 'file') {
      nonCodeConnections.push({ source: edge.source, target: edge.target, edgeType: edge.type });
    }
  }
  const crossCategoryEdges = [...crossCounts].map(([key, count]) => {
    const [fromType, toType, edgeType] = key.split('\u0000');
    return { fromType, toType, edgeType, count };
  });
  const patternMatches = {};
  for (const group of Object.keys(directoryGroups)) patternMatches[group] = classifyGroup(group);
  const paths = fileNodes.map((node) => node.filePath);
  const infraFiles = paths.filter((p) => /(^|\/)(Dockerfile(\..+)?|docker-compose\..+|Makefile|\.gitlab-ci\.yml|Jenkinsfile)$|(^|\/)\.github\/workflows\/|(^|\/)(k8s|kubernetes|helm|charts|terraform|tf)\//i.test(p));
  const deploymentTopology = {
    hasDockerfile: paths.some((p) => /(^|\/)Dockerfile(\..+)?$/i.test(p)),
    hasCompose: paths.some((p) => /(^|\/)docker-compose\.(yml|yaml)$/i.test(p)),
    hasK8s: paths.some((p) => /(^|\/)(k8s|kubernetes|helm|charts)\//i.test(p)),
    hasTerraform: paths.some((p) => /(^|\/)(terraform|tf)\/|\.(tf|tfvars)$/i.test(p)),
    hasCI: paths.some((p) => /(^|\/)\.github\/workflows\/|(^|\/)(\.gitlab-ci\.yml|Jenkinsfile)$/i.test(p)),
    infraFiles
  };
  const dataPipeline = {
    schemaFiles: paths.filter((p) => /\.(sql|graphql|gql|proto)$/i.test(p) || /(^|\/)schema(s)?\//i.test(p)),
    migrationFiles: paths.filter((p) => /(^|\/)migrations?\//i.test(p)),
    dataModelFiles: fileNodes.filter((n) => /(^|\/)(models?|entities|repository|persistence)\//i.test(n.filePath) || n.tags?.some((t) => /model|veri-model/i.test(t))).map((n) => n.filePath),
    apiHandlerFiles: fileNodes.filter((n) => /(^|\/)(routes?|api|controllers?|handlers?)\//i.test(n.filePath) || n.tags?.some((t) => /api|endpoint|handler/i.test(t))).map((n) => n.filePath)
  };
  const docNodes = fileNodes.filter((node) => node.type === 'document' || /\.(md|rst)$/i.test(node.filePath));
  const groupsWithDocsSet = new Set();
  for (const doc of docNodes) {
    const direct = idToGroup.get(doc.id);
    if (direct) groupsWithDocsSet.add(direct);
    for (const group of Object.keys(directoryGroups)) {
      if (doc.summary?.toLowerCase().includes(group.toLowerCase())) groupsWithDocsSet.add(group);
    }
  }
  const groups = Object.keys(directoryGroups);
  const docCoverage = {
    groupsWithDocs: groupsWithDocsSet.size,
    totalGroups: groups.length,
    coverageRatio: groups.length ? groupsWithDocsSet.size / groups.length : 0,
    undocumentedGroups: groups.filter((group) => !groupsWithDocsSet.has(group))
  };
  const dependencyDirection = [];
  const seenPairs = new Set();
  for (const item of interGroupImports.filter((x) => x.from !== x.to)) {
    const pair = [item.from, item.to].sort().join('\u0000');
    if (seenPairs.has(pair)) continue;
    seenPairs.add(pair);
    const forward = inter.get(`${item.from}\u0000${item.to}`) || 0;
    const reverse = inter.get(`${item.to}\u0000${item.from}`) || 0;
    if (forward > reverse) dependencyDirection.push({ dependent: item.from, dependsOn: item.to, count: forward - reverse });
    else if (reverse > forward) dependencyDirection.push({ dependent: item.to, dependsOn: item.from, count: reverse - forward });
  }
  const result = {
    scriptCompleted: true,
    commonPathPrefix: prefix.join('/'),
    directoryGroups,
    nodeTypeGroups,
    importAdjacency,
    groupImportAdjacency: Object.fromEntries(groups.map((g) => [g, { importsFrom: [...groupImportsFrom[g]], importedBy: [...groupImportedBy[g]] }])),
    crossCategoryEdges,
    nonCodeConnections,
    interGroupImports,
    intraGroupDensity,
    patternMatches,
    filePatternMatches: Object.fromEntries(fileNodes.map((node) => [node.id, classifyFile(node.filePath)]).filter(([, value]) => value)),
    deploymentTopology,
    dataPipeline,
    docCoverage,
    dependencyDirection,
    fileStats: {
      totalFileNodes: fileNodes.length,
      filesPerGroup: Object.fromEntries(Object.entries(directoryGroups).map(([group, ids]) => [group, ids.length])),
      nodeTypeCounts: Object.fromEntries(Object.entries(nodeTypeGroups).map(([type, ids]) => [type, ids.length]))
    },
    fileFanIn,
    fileFanOut
  };
  fs.writeFileSync(outputPath, `${JSON.stringify(result, null, 2)}\n`);
} catch (error) {
  fail(error);
}
