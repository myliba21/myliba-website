const fs = require('fs');

const [inputPath, outputPath] = process.argv.slice(2);
if (!inputPath || !outputPath) {
  console.error('Usage: node ua-arch-layers.js <input.json> <layers.json>');
  process.exit(1);
}

const { fileNodes } = JSON.parse(fs.readFileSync(inputPath, 'utf8'));
const layers = [
  {
    id: 'layer:content-admin',
    name: 'İçerik Modeli ve Yönetim',
    description: 'myliba-core eklentisindeki özel içerik türlerini, yönetim ekranlarını, meta alanlarını, site seçeneklerini, formları, SEO kurallarını ve içerik taşıma komutlarını kapsar.',
    nodeIds: []
  },
  {
    id: 'layer:theme-presentation',
    name: 'Tema Sunumu',
    description: 'Myliba özel temasının WordPress sayfa, arşiv, tekil içerik ve yeniden kullanılabilir şablonlarıyla yönetilen içeriği ziyaretçiye sunar.',
    nodeIds: []
  },
  {
    id: 'layer:frontend-assets',
    name: 'Ön Yüz Varlıkları',
    description: 'Temanın responsive tasarım sistemini, WordPress tema stil girişini ve tarayıcı tarafındaki erişilebilir etkileşim davranışlarını sağlar.',
    nodeIds: []
  },
  {
    id: 'layer:infrastructure',
    name: 'Altyapı ve Proje Desteği',
    description: 'Docker Compose tabanlı WordPress ortamını, Nginx güvenlik ve önbellek ayarlarını, yerel otomasyonu, ortam yapılandırmasını ve depo koruma dosyalarını barındırır.',
    nodeIds: []
  },
  {
    id: 'layer:documentation',
    name: 'Dokümantasyon ve Geçiş Rehberleri',
    description: 'WordPress kurulumunu, eski içeriklerin yeni yönetilebilir modele eşlenmesini ve üretime geçiş değerlendirmelerini belgeleyen proje rehberlerini içerir.',
    nodeIds: []
  }
];

const byId = Object.fromEntries(layers.map((layer) => [layer.id, layer]));
for (const node of fileNodes) {
  let layerId;
  if (node.type === 'document') {
    layerId = 'layer:documentation';
  } else if (node.filePath.includes('/wp-content/plugins/myliba-core/')) {
    layerId = 'layer:content-admin';
  } else if (
    node.filePath.includes('/wp-content/themes/myliba/assets/') ||
    node.filePath === 'wordpress/wp-content/themes/myliba/style.css'
  ) {
    layerId = 'layer:frontend-assets';
  } else if (node.filePath.includes('/wp-content/themes/myliba/')) {
    layerId = 'layer:theme-presentation';
  } else {
    layerId = 'layer:infrastructure';
  }
  byId[layerId].nodeIds.push(node.id);
}

fs.writeFileSync(outputPath, `${JSON.stringify(layers, null, 2)}\n`);
