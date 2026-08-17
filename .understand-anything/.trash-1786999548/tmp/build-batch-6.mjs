import fs from 'node:fs';
import path from 'node:path';

const root = '/Users/okantoper/Documents/Myliba Projects/myliba-website';
const intermediate = path.join(root, '.understand-anything/intermediate');
const extraction = JSON.parse(fs.readFileSync(path.join(root, '.understand-anything/tmp/ua-file-extract-results-6.json'), 'utf8'));

const fileSummaries = {
  'wordpress/wp-content/plugins/myliba-core/includes/wp-cli.php': 'WordPress içeriğini tohumlayan, mevcut siteden veri aktaran ve Türkçe öncelikli yayın düzenini uygulayan kapsamlı WP-CLI komut sınıfını sağlar.',
  'wordpress/wp-content/plugins/myliba-core/myliba-core.php': 'Myliba Core eklentisini başlatır; içerik türleri, meta alanları, yönetim ekranları ve WP-CLI komutlarını yüklerken etkinleştirme yaşam döngüsünü yönetir.',
  'wordpress/wp-content/themes/index.php': 'WordPress tema dizinine doğrudan erişimi engelleyen boş güvenlik giriş dosyasıdır.',
  'wordpress/wp-content/themes/myliba/404.php': 'Bulunamayan sayfalarda yerelleştirilmiş açıklama ile demo ve ana sayfa yönlendirmelerini gösteren 404 şablonudur.',
  'wordpress/wp-content/themes/myliba/archive-myliba_academy.php': 'Akademi programlarını açıklama kartları halinde listeleyen yerelleştirilmiş arşiv şablonudur.',
  'wordpress/wp-content/themes/myliba/archive-myliba_ebook.php': 'E-kitap arşivini ortak gelişim kaynağı listeleme bileşenine yönlendiren ince şablondur.',
  'wordpress/wp-content/themes/myliba/archive-myliba_event.php': 'Etkinlikleri tarih, özet ve konum bilgileriyle listeleyen; boş durumda yerelleştirilmiş mesaj sunan arşiv şablonudur.',
  'wordpress/wp-content/themes/myliba/archive-myliba_product.php': 'Myliba ürün modüllerini ikon, başlık ve kısa açıklama kartlarıyla sunan arşiv şablonudur.',
  'wordpress/wp-content/themes/myliba/archive-myliba_report.php': 'Rapor arşivini ortak gelişim kaynağı listeleme bileşenine yönlendiren ince şablondur.',
  'wordpress/wp-content/themes/myliba/archive-myliba_solution.php': 'Çözüm arşivi isteklerini ortak çözümler sayfası bileşenine devreden yönlendirme şablonudur.',
  'wordpress/wp-content/themes/myliba/assets/css/main.css': 'Temanın tipografi, navigasyon, ana sayfa, çözüm, akademi, kaynak ve kurumsal sayfa bileşenlerini tüm kırılımlarda biçimlendiren kapsamlı stil katmanıdır.',
  'wordpress/wp-content/themes/myliba/assets/js/main.js': 'Mobil navigasyon, mega menüler, dil tercihi, sekmeler, slider, promosyon ve erişilebilir klavye etkileşimlerini yöneten istemci tarafı davranış katmanıdır.',
  'wordpress/wp-content/themes/myliba/footer.php': 'Yönetilebilir CTA, menü sütunları, iletişim, sosyal bağlantılar ve yasal bilgileri varsayılanlarla birlikte oluşturan site altbilgi şablonudur.',
  'wordpress/wp-content/themes/myliba/front-page.php': 'Yönetim panelinden sıralanabilen hero, sosyal kanıt, ürün, akademi, performans, kaynak, SSS ve CTA bölümlerini oluşturan ana sayfa şablonudur.',
  'wordpress/wp-content/themes/myliba/functions.php': 'Tema kurulumu, varlık yükleme, yerelleştirme, yönlendirme, menüler, içerik sorguları ve yönetilebilir ana sayfa verileri için merkezi WordPress yardımcı katmanıdır.',
  'wordpress/wp-content/themes/myliba/header.php': 'Duyuru alanı, marka, masaüstü ve mobil menüler, mega navigasyon, dil seçici ve erişilebilir kontrolleri oluşturan site üstbilgi şablonudur.',
  'wordpress/wp-content/themes/myliba/inc/customizer.php': 'Üstbilgi, iletişim ve altbilgi metinleri ile bağlantılarını güvenli sanitize callback’leri üzerinden WordPress Özelleştirici paneline açar.',
  'wordpress/wp-content/themes/myliba/index.php': 'Standart WordPress içerik akışını tarih, başlık, özet ve sayfalama ile gösteren genel arşiv geri dönüş şablonudur.',
  'wordpress/wp-content/themes/myliba/page.php': 'Sayfa slug’ına göre özel tema bileşenlerine yönlendiren, kalan içerikler için hero ve düzenleyici içeriği gösteren genel sayfa yönlendiricisidir.',
  'wordpress/wp-content/themes/myliba/single-myliba_academy.php': 'Tekil akademi kaydını ortak hero ve dönüşüm odaklı detay bileşeniyle sunan şablondur.',
  'wordpress/wp-content/themes/myliba/single-myliba_ebook.php': 'E-kitap içeriğini kapak, özet, kazanımlar, editör içeriği ve indirme çağrılarıyla sunan ayrıntılı kaynak şablonudur.',
  'wordpress/wp-content/themes/myliba/single-myliba_event.php': 'Etkinlik içeriğini tarih, konum ve kayıt bağlantısından oluşan yan panelle birlikte gösteren tekil kayıt şablonudur.',
  'wordpress/wp-content/themes/myliba/single-myliba_landing.php': 'Tekil kampanya sayfasını ortak hero ve dönüşüm detayı bileşenleriyle oluşturan şablondur.',
  'wordpress/wp-content/themes/myliba/single-myliba_product.php': 'Tekil ürün kaydını ortak hero ve dönüşüm detayı bileşenleriyle oluşturan şablondur.',
  'wordpress/wp-content/themes/myliba/single-myliba_report.php': 'Araştırma raporunu metrikler, öne çıkan sonuçlar, editör içeriği ve indirme çağrılarıyla sunan ayrıntılı kaynak şablonudur.'
};

function complexity(lines) {
  if (lines > 200) return 'complex';
  if (lines >= 50) return 'moderate';
  return 'simple';
}

function fileTags(file) {
  const p = file.path;
  if (p.endsWith('/wp-cli.php')) return ['wp-cli', 'içerik-tohumlama', 'veri-aktarımı', 'wordpress', 'yerelleştirme'];
  if (p.endsWith('/myliba-core.php')) return ['eklenti-girişi', 'wordpress', 'başlatma', 'yaşam-döngüsü'];
  if (p.endsWith('/assets/css/main.css')) return ['stil-katmanı', 'responsive', 'tasarım-sistemi', 'bileşenler'];
  if (p.endsWith('/assets/js/main.js')) return ['etkileşim', 'erişilebilirlik', 'navigasyon', 'yerelleştirme'];
  if (p.endsWith('/functions.php')) return ['tema-çekirdeği', 'wordpress-hooks', 'yerelleştirme', 'içerik-yardımcıları', 'yönlendirme'];
  if (p.endsWith('/customizer.php')) return ['özelleştirici', 'yönetim-paneli', 'ayarlar', 'doğrulama'];
  if (p.endsWith('/front-page.php')) return ['ana-sayfa', 'şablon', 'yönetilebilir-içerik', 'dönüşüm'];
  if (p.endsWith('/header.php')) return ['üstbilgi', 'navigasyon', 'mega-menü', 'dil-seçici'];
  if (p.endsWith('/footer.php')) return ['altbilgi', 'navigasyon', 'sosyal-bağlantılar', 'cta'];
  if (p.includes('/archive-')) return ['arşiv-şablonu', 'wordpress', 'içerik-listesi', 'yerelleştirme'];
  if (p.includes('/single-')) return ['tekil-şablon', 'wordpress', 'içerik-detayı', 'dönüşüm'];
  if (p.endsWith('/page.php')) return ['sayfa-yönlendirme', 'şablon', 'wordpress', 'içerik-render'];
  if (p.endsWith('/404.php')) return ['hata-sayfası', 'yerelleştirme', 'navigasyon', 'cta'];
  if (p.endsWith('/index.php') && p.includes('/themes/myliba/')) return ['arşiv-şablonu', 'geri-dönüş', 'sayfalama', 'wordpress'];
  return ['güvenlik', 'giriş-noktası', 'wordpress'];
}

function functionTags(name) {
  if (/translate|locale|language|turkish|polylang/i.test(name)) return ['yerelleştirme', 'dil-yönetimi', 'wordpress', 'yardımcı'];
  if (/redirect|rewrite|url|link|path/i.test(name)) return ['yönlendirme', 'url-üretimi', 'wordpress', 'yardımcı'];
  if (/seed|upsert|materialize|import_current|cleanup/i.test(name)) return ['içerik-yönetimi', 'veri-aktarımı', 'wp-cli', 'wordpress'];
  if (/image|media|asset|style|preload|resource_hint|meta/i.test(name)) return ['medya', 'performans', 'tema-yardımcısı', 'wordpress'];
  if (/menu|nav|header/i.test(name)) return ['navigasyon', 'menü', 'tema-yardımcısı', 'wordpress'];
  if (/home|solution|academy|development|faq|entries|post|content/i.test(name)) return ['içerik-modeli', 'tema-yardımcısı', 'wordpress', 'sunum'];
  if (/social|brand|demo/i.test(name)) return ['bağlantılar', 'tema-yardımcısı', 'dönüşüm', 'wordpress'];
  if (/customize/i.test(name)) return ['özelleştirici', 'yönetim-paneli', 'ayarlar', 'wordpress'];
  if (/boot|activate|deactivate/i.test(name)) return ['eklenti-yaşam-döngüsü', 'başlatma', 'wordpress', 'hook'];
  return ['yardımcı', 'tema-çekirdeği', 'wordpress'];
}

function humanize(name) {
  return name.replace(/^myliba_/, '').replaceAll('_', ' ');
}

function functionSummary(name, filePath) {
  const label = humanize(name);
  const exact = {
    seed: 'Site sayfalarını, özel içerik türlerini, menüleri ve varsayılan tema verilerini güvenli biçimde oluşturan ana WP-CLI tohumlama akışıdır.',
    refresh_site_pages: 'Mevcut ana sayfaları güncel varsayılan içerik ve meta değerleriyle yeniden oluşturan WP-CLI komutudur.',
    tr_first: 'Siteyi onay sonrasında Türkçe öncelikli yayın ve yönlendirme düzenine dönüştüren WP-CLI komutudur.',
    materialize_home: 'Ana sayfanın eksik yönetilebilir meta alanlarını varsayılan değerlerle kalıcılaştırır.',
    import_current: 'Yayımdaki Myliba sitesinden sayfa, ürün, akademi, ekip, logo ve yazı içeriklerini içe aktaran ana komuttur.',
    starter_content: 'Anahtar bazında sayfalar için kapsamlı başlangıç HTML içeriği üretir.',
    home_meta_defaults: 'Seçili dil için ana sayfanın yönetilebilir metin, bağlantı ve görünüm varsayılanlarını döndürür.',
    apply_tr_first_mode: 'İçerik ve ayarları Türkçe öncelikli moda uyarlayarak eski çok dilli kayıtları düzenler.',
    myliba_theme_setup: 'Tema desteklerini, menü konumlarını ve WordPress başlangıç ayarlarını kaydeder.',
    myliba_translation_defaults: 'Tema genelinde kullanılan arayüz metinlerinin Türkçe ve İngilizce karşılıklarını merkezi bir sözlükte tanımlar.',
    myliba_get_primary_nav_items: 'Yönetilen WordPress menüsünü çözümler ve uygun değilse yerelleştirilmiş varsayılan navigasyon öğelerini üretir.',
    myliba_solution_catalog: 'Çözüm sayfalarının yerelleştirilmiş başlık, açıklama, renk ve rota kataloğunu oluşturur.',
    myliba_language_context_url: 'Geçerli içeriğin hedef dildeki en uygun karşılık URL’sini içerik türü ve rota bağlamına göre hesaplar.',
    myliba_home_hero_slides: 'Yönetim panelindeki ana sayfa meta alanlarından doğrulanmış hero slider kayıtlarını oluşturur.',
    myliba_customize_register: 'Tema üstbilgi, iletişim ve altbilgi ayarlarını bölümler ve güvenli kontroller halinde WordPress Özelleştiriciye kaydeder.',
    boot: 'Eklenti bileşenlerini yükleyip özel içerik türleri, meta alanları, yönetim ekranları ve CLI komutlarını başlatır.',
    activate: 'Eklenti etkinleştirildiğinde içerik türlerini kaydeder ve kalıcı bağlantı kurallarını yeniler.',
    deactivate: 'Eklenti devre dışı bırakıldığında kalıcı bağlantı kurallarını temiz biçimde yeniler.'
  };
  if (exact[name]) return exact[name];
  if (/^import_current_/.test(name)) return `Yayımdaki siteden ${label.replace('import current ', '')} verisini ayrıştırıp yerel WordPress kayıtlarına aktaran yardımcıdır.`;
  if (/^(seed_|upsert_)/.test(name)) return `${label} kayıtlarını tekrar çalıştırılabilir biçimde oluşturan veya güncelleyen içerik yönetimi yardımcısıdır.`;
  if (/^(fetch_|extract_|find_|join_|line_|dom_|absolute_|replace_|sideload_)/.test(name)) return `${label} işlemini içe aktarma hattı için gerçekleştiren ayrıştırma ve dönüştürme yardımcısıdır.`;
  if (/^(myliba_translate|myliba_text)/.test(name)) return `${label} işlemiyle tema metinlerini geçerli dil bağlamında çözümler.`;
  if (/locale|language|turkish|polylang/i.test(name)) return `${label} işlemiyle sitenin Türkçe ve İngilizce dil bağlamını güvenli biçimde yönetir.`;
  if (/redirect|rewrite|url|link|path/i.test(name)) return `${label} işlemiyle WordPress rotalarını ve yerelleştirilmiş bağlantıları tutarlı biçimde çözümler.`;
  if (/menu|nav|header/i.test(name)) return `${label} işlemiyle tema navigasyonu ve menü sunumu için gerekli veriyi üretir.`;
  if (/home|solution|academy|development|faq|entries|post|content/i.test(name)) return `${label} işlemiyle yönetilen içeriği tema şablonlarının kullanacağı yapıya dönüştürür.`;
  if (/image|media|asset|style|preload|resource_hint|meta/i.test(name)) return `${label} işlemiyle tema varlıklarını, görsellerini veya performans metadatasını yönetir.`;
  if (/social|brand|demo/i.test(name)) return `${label} işlemiyle marka, sosyal ağ veya dönüşüm bağlantılarını güvenli çıktı için hazırlar.`;
  if (filePath.endsWith('/functions.php')) return `${label} işlemini tema genelinde yeniden kullanılabilir bir WordPress yardımcısı olarak sağlar.`;
  return `${label} işlemini Myliba içerik ve sunum akışı için gerçekleştirir.`;
}

const nodes = [];
const edges = [];
const nodeIds = new Set();

function addNode(node) {
  if (nodeIds.has(node.id)) throw new Error(`Yinelenen düğüm: ${node.id}`);
  nodeIds.add(node.id);
  nodes.push(node);
}

for (const file of extraction.results) {
  const fileId = `file:${file.path}`;
  addNode({
    id: fileId,
    type: 'file',
    name: path.basename(file.path),
    filePath: file.path,
    summary: fileSummaries[file.path],
    tags: fileTags(file),
    complexity: complexity(file.nonEmptyLines ?? file.totalLines ?? 0),
    ...(file.path.endsWith('/functions.php') ? {languageNotes: 'WordPress hook ve filtreleriyle çalışan yordam tabanlı PHP mimarisi, tema davranışlarını tek bir merkezi yardımcı katmanda toplar.'} : {}),
    ...(file.path.endsWith('/assets/js/main.js') ? {languageNotes: 'Bağımlılıksız JavaScript IIFE yapısı, küçük arrow function’lar ve veri öznitelikleri üzerinden bileşen davranışı kurar.'} : {})
  });

  const exports = new Set((file.exports ?? []).map((item) => item.name));
  for (const fn of file.functions ?? []) {
    const length = fn.endLine - fn.startLine + 1;
    if (length < 10 && !exports.has(fn.name)) continue;
    const id = `function:${file.path}:${fn.name}`;
    addNode({
      id,
      type: 'function',
      name: fn.name,
      filePath: file.path,
      lineRange: [fn.startLine, fn.endLine],
      summary: functionSummary(fn.name, file.path),
      tags: functionTags(fn.name),
      complexity: complexity(length)
    });
    edges.push({source: fileId, target: id, type: 'contains', direction: 'forward', weight: 1.0});
    if (exports.has(fn.name)) edges.push({source: fileId, target: id, type: 'exports', direction: 'forward', weight: 0.8});
  }

  for (const cls of file.classes ?? []) {
    const length = cls.endLine - cls.startLine + 1;
    if (length < 20 && (cls.methods ?? []).length < 2 && !exports.has(cls.name)) continue;
    const id = `class:${file.path}:${cls.name}`;
    addNode({
      id,
      type: 'class',
      name: cls.name,
      filePath: file.path,
      lineRange: [cls.startLine, cls.endLine],
      summary: cls.name === 'Commands' ? 'Myliba sitesinin tohumlama, içerik aktarımı, görsel indirme ve dil düzeni operasyonlarını WP-CLI komutları olarak bir araya getirir.' : `${cls.name} sınıfı proje davranışlarını kapsülleyen uygulama bileşenidir.`,
      tags: cls.name === 'Commands' ? ['wp-cli', 'komut-sınıfı', 'içerik-yönetimi', 'veri-aktarımı'] : ['sınıf', 'uygulama-bileşeni', 'wordpress'],
      complexity: complexity(length)
    });
    edges.push({source: fileId, target: id, type: 'contains', direction: 'forward', weight: 1.0});
    if (exports.has(cls.name)) edges.push({source: fileId, target: id, type: 'exports', direction: 'forward', weight: 0.8});
  }
}

const related = [
  ['wordpress/wp-content/themes/myliba/archive-myliba_ebook.php', 'wordpress/wp-content/themes/myliba/archive-myliba_report.php'],
  ['wordpress/wp-content/themes/myliba/assets/css/main.css', 'wordpress/wp-content/themes/myliba/assets/js/main.js'],
  ['wordpress/wp-content/themes/myliba/footer.php', 'wordpress/wp-content/themes/myliba/functions.php'],
  ['wordpress/wp-content/themes/myliba/front-page.php', 'wordpress/wp-content/themes/myliba/functions.php'],
  ['wordpress/wp-content/themes/myliba/header.php', 'wordpress/wp-content/themes/myliba/functions.php'],
  ['wordpress/wp-content/themes/myliba/inc/customizer.php', 'wordpress/wp-content/themes/myliba/functions.php'],
  ['wordpress/wp-content/themes/myliba/index.php', 'wordpress/wp-content/themes/myliba/functions.php'],
  ['wordpress/wp-content/themes/myliba/single-myliba_ebook.php', 'wordpress/wp-content/themes/myliba/single-myliba_report.php']
];
for (const [source, target] of related) {
  edges.push({source: `file:${source}`, target: `file:${target}`, type: 'related', direction: 'forward', weight: 0.5});
}

const totalParts = Math.ceil(Math.max(nodes.length / 60, edges.length / 120));
const files = extraction.results.map((file) => file.path).sort();
const chunkSize = Math.ceil(files.length / totalParts);
for (let index = 0; index < totalParts; index += 1) {
  const partFiles = new Set(files.slice(index * chunkSize, (index + 1) * chunkSize));
  const partNodes = nodes.filter((node) => partFiles.has(node.filePath));
  const partNodeIds = new Set(partNodes.map((node) => node.id));
  const partEdges = edges.filter((edge) => partNodeIds.has(edge.source));
  const output = {nodes: partNodes, edges: partEdges};
  fs.writeFileSync(path.join(intermediate, `batch-6-part-${index + 1}.json`), JSON.stringify(output, null, 2) + '\n');
}

console.log(JSON.stringify({totalParts, nodeCount: nodes.length, edgeCount: edges.length, files: files.length}, null, 2));
