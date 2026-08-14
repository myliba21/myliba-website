const fs = require('fs');

const [inputPath, outputPath] = process.argv.slice(2);
if (!inputPath || !outputPath) throw new Error('Usage: node generate-layers.js <input.json> <layers.json>');
const { fileNodes } = JSON.parse(fs.readFileSync(inputPath, 'utf8'));

const definitions = [
  {
    id: 'layer:analysis-knowledge-graph',
    name: 'Analiz ve Bilgi Grafiği',
    description: 'Understand Anything tarama, denetim, parmak izi, bilgi grafiği ve geçici analiz artefaktlarını bir arada tutar.',
    match: (path) => path.startsWith('.understand-anything/')
  },
  {
    id: 'layer:infrastructure-config',
    name: 'Altyapı ve Yapılandırma',
    description: 'Docker Compose geliştirme yığını, WP-CLI otomasyonu, PHP yükleme ayarları, ortam örneği ve Nginx ters vekil kurallarını kapsar.',
    match: (path) => path === '.DS_Store' || path === 'nginx/default.conf' || ['wordpress/.env.example', 'wordpress/Makefile', 'wordpress/docker-compose.yml', 'wordpress/uploads.ini'].includes(path)
  },
  {
    id: 'layer:documentation',
    name: 'Proje Dokümantasyonu',
    description: 'WordPress kurulumu, içerik taşıma eşlemesi ve geçiş inceleme yol haritasını açıklayan proje belgelerini içerir.',
    match: (path) => path === 'wordpress/README.md' || path.startsWith('wordpress/migration/')
  },
  {
    id: 'layer:wordpress-platform',
    name: 'WordPress Platform Entegrasyonu',
    description: 'Dizin koruma girişleri ile otomatik güncelleme ve SMTP davranışını belirleyen zorunlu WordPress eklentilerini barındırır.',
    match: (path) => path.startsWith('wordpress/wp-content/mu-plugins/') || /wordpress\/wp-content\/(index|plugins\/index|themes\/index)\.php$/.test(path)
  },
  {
    id: 'layer:admin-content-model',
    name: 'Yönetim ve İçerik Modeli',
    description: 'Yönetim menüleri, çok dilli ayarlar, meta kutuları, yapılandırılmış sayfa içerikleri ve içerik taşıma araçlarıyla editoryal modeli yönetir.',
    match: (path) => /wordpress\/wp-content\/plugins\/myliba-core\/includes\/(admin|content|meta|options|page-content|wp-cli)\.php$/.test(path)
  },
  {
    id: 'layer:core-services',
    name: 'Çekirdek Uygulama Hizmetleri',
    description: 'Myliba Core başlangıcını, özel yazı türlerini, formları, SEO üretimini ve güvenli görsel işleme davranışını sağlar.',
    match: (path) => path.startsWith('wordpress/wp-content/plugins/myliba-core/')
  },
  {
    id: 'layer:theme-foundation',
    name: 'Tema Temeli ve Ön Yüz Varlıkları',
    description: 'Tema kurulumu ve yardımcıları, Customizer ayarları, tema metadatası ile ortak CSS ve JavaScript davranışlarını tanımlar.',
    match: (path) => path.startsWith('wordpress/wp-content/themes/myliba/assets/') || path.startsWith('wordpress/wp-content/themes/myliba/inc/') || ['wordpress/wp-content/themes/myliba/functions.php', 'wordpress/wp-content/themes/myliba/style.css'].includes(path)
  },
  {
    id: 'layer:presentation-templates',
    name: 'Sunum ve Sayfa Şablonları',
    description: 'Yönetilebilir içeriği ziyaretçiye gösteren ana sayfa, arşiv, tekil içerik, özel sayfa ve yeniden kullanılabilir tema şablonlarını içerir.',
    match: (path) => path.startsWith('wordpress/wp-content/themes/myliba/')
  }
];

const layers = definitions.map(({ match, ...layer }) => ({ ...layer, nodeIds: [] }));
for (const node of fileNodes) {
  const index = definitions.findIndex((definition) => definition.match(node.filePath));
  if (index < 0) throw new Error(`Katmansız dosya: ${node.id}`);
  layers[index].nodeIds.push(node.id);
}

const assigned = layers.flatMap((layer) => layer.nodeIds);
const unique = new Set(assigned);
if (layers.length < 3 || layers.length > 10) throw new Error(`Geçersiz katman sayısı: ${layers.length}`);
if (layers.some((layer) => layer.nodeIds.length === 0)) throw new Error('Boş katman bulundu');
if (assigned.length !== fileNodes.length || unique.size !== fileNodes.length) throw new Error(`Atama hatası: ${assigned.length}/${unique.size}/${fileNodes.length}`);
const inputIds = new Set(fileNodes.map((node) => node.id));
if (assigned.some((id) => !inputIds.has(id))) throw new Error('Girdide bulunmayan düğüm atandı');

fs.writeFileSync(outputPath, `${JSON.stringify(layers, null, 2)}\n`);
