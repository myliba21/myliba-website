import fs from 'node:fs';
import path from 'node:path';

const root = process.cwd();
const intermediate = path.join(root, '.understand-anything/intermediate');
const batchesDoc = JSON.parse(fs.readFileSync(path.join(intermediate, 'batches.json'), 'utf8'));

const fileSummaries = {
  'wordpress/wp-content/plugins/myliba-core/includes/wp-cli.php': 'Myliba sitesini örnek içerikle kuran, mevcut siteden içerik ve görsel aktaran, menüleri oluşturan ve Türkçe öncelikli çalışma kipini uygulayan kapsamlı WP-CLI komut sınıfıdır.',
  'wordpress/wp-content/plugins/myliba-core/myliba-core.php': 'Myliba Core eklentisinin başlangıç dosyasıdır; bağımlılıkları yükler, eklentiyi başlatır ve etkinleştirme/devre dışı bırakma kancalarını yönetir.',
  'wordpress/wp-content/themes/index.php': 'Tema dizininde doğrudan listelemeyi engelleyen boş WordPress koruma dosyasıdır.',
  'wordpress/wp-content/themes/myliba/404.php': 'Bulunamayan sayfalar için marka mesajı, ana sayfa ve demo çağrıları sunan 404 şablonudur.',
  'wordpress/wp-content/themes/myliba/archive-myliba_academy.php': 'Akademi içerik türünün arşiv isteğini Akademi sayfasına yönlendiren tema şablonudur.',
  'wordpress/wp-content/themes/myliba/archive-myliba_ebook.php': 'E-kitap arşivini ortak gelişim kaynağı listeleme bileşeniyle oluşturan şablondur.',
  'wordpress/wp-content/themes/myliba/archive-myliba_event.php': 'Etkinlikleri tarih ve konum metalarıyla kartlar halinde listeleyen arşiv şablonudur.',
  'wordpress/wp-content/themes/myliba/archive-myliba_product.php': 'Ürün içeriklerini dönüşüm odaklı kartlarla listeleyen ürün arşivi şablonudur.',
  'wordpress/wp-content/themes/myliba/archive-myliba_report.php': 'Rapor arşivini ortak gelişim kaynağı listeleme bileşenine bağlayan şablondur.',
  'wordpress/wp-content/themes/myliba/archive-myliba_solution.php': 'Çözüm arşivini temanın genel arşiv görünümüne devreden ince şablondur.',
  'wordpress/wp-content/themes/myliba/assets/css/main.css': 'Myliba temasının tüm sayfa düzenlerini, bileşenlerini, tipografisini, duyarlı kırılımlarını ve görsel durumlarını tanımlayan ana stil paketidir.',
  'wordpress/wp-content/themes/myliba/assets/js/main.js': 'Mobil menü, dil seçici, kaydırma, carousel, FAQ ve diğer etkileşimli tema bileşenlerinin tarayıcı davranışlarını yöneten ana JavaScript dosyasıdır.',
  'wordpress/wp-content/themes/myliba/footer.php': 'Customizer seçenekleri ve sayfa bazlı CTA metalarıyla alt çağrı alanını, iletişim bilgilerini, sosyal bağlantıları ve footer menülerini oluşturan şablondur.',
  'wordpress/wp-content/themes/myliba/front-page.php': 'Yönetim panelindeki ana sayfa meta alanlarını okuyarak hero, güven, problem/çözüm, ürün, akademi, rol kazanımları, FAQ ve CTA bölümlerini sıralı biçimde sunan ana sayfa şablonudur.',
  'wordpress/wp-content/themes/myliba/functions.php': 'Tema kurulumu, varlık yükleme, yerelleştirme, menüler, URL yönlendirme, içerik/meta erişimi ve ana sayfa bölüm modelini sağlayan merkezi WordPress yardımcı katmanıdır.',
  'wordpress/wp-content/themes/myliba/header.php': 'Customizer kontrollü promosyon bandını, marka kimliğini, ana navigasyonu, dil seçiciyi, portal ve demo çağrılarını üreten üst şablondur.',
  'wordpress/wp-content/themes/myliba/inc/customizer.php': 'Header, footer, CTA, iletişim ve sosyal bağlantılar için WordPress Customizer ayarlarını, kontrollerini ve güvenli temizleme kurallarını kaydeder.',
  'wordpress/wp-content/themes/myliba/index.php': 'WordPress döngüsü üzerinden varsayılan içerik listesini ve sayfalama bağlantılarını gösteren tema geri dönüş şablonudur.',
  'wordpress/wp-content/themes/myliba/page.php': 'Sayfa türüne göre özel bölüm şablonlarını seçen, aksi durumda standart içerik ve hero görünümünü kullanan genel sayfa şablonudur.',
  'wordpress/wp-content/themes/myliba/single-myliba_academy.php': 'Tekil akademi içeriğini ortak dönüşüm detayı bileşenine bağlayan şablondur.',
  'wordpress/wp-content/themes/myliba/single-myliba_ebook.php': 'E-kitap yönetim metalarını hero, problem, faydalar, içerik yolculuğu ve indirme CTA bölümlerine dönüştüren tekil içerik şablonudur.',
  'wordpress/wp-content/themes/myliba/single-myliba_event.php': 'Etkinliğin tarih, konum ve kayıt URL’si metalarını içerikle birlikte gösteren tekil etkinlik şablonudur.',
  'wordpress/wp-content/themes/myliba/single-myliba_landing.php': 'Tekil SEO açılış içeriğini ortak dönüşüm detayı bileşenine bağlayan şablondur.',
  'wordpress/wp-content/themes/myliba/single-myliba_product.php': 'Tekil ürün içeriğini ortak dönüşüm detayı bileşeniyle sunan şablondur.',
  'wordpress/wp-content/themes/myliba/single-myliba_report.php': 'Rapor yönetim metalarını araştırma özeti, problem/çözüm, faydalar, FAQ ve CTA bölümleri halinde sunan tekil rapor şablonudur.',
  'wordpress/wp-content/themes/myliba/single-myliba_solution.php': 'Çözüm yönetim metalarını hero, yaklaşım, hedef kitle, kazanımlar, ölçüm alanları, süreç ve CTA bölümlerine dönüştüren ayrıntılı tekil çözüm şablonudur.',
  'wordpress/wp-content/themes/myliba/single.php': 'Blog yazısını okuma süresi, içerik başlıkları, yazar bölümü ve ilişkili çağrılarla gösteren genel tekil yazı şablonudur.',
  'wordpress/wp-content/themes/myliba/style.css': 'WordPress’in Myliba temasını tanıması için tema adı, sürüm ve diğer üstbilgi metadatasını taşır.',
  'wordpress/wp-content/themes/myliba/template-blog.php': 'Dil filtresine uygun blog yazılarını sorgulayan, hero ve sayfalama ile kart listesi oluşturan özel sayfa şablonudur.',
  'wordpress/wp-content/themes/myliba/template-contact.php': 'İletişim sayfasında ortak hero ile yönetilebilir sayfa içeriğini gösteren özel şablondur.',
  'wordpress/wp-content/themes/myliba/template-demo.php': 'Demo talebi sayfasında ortak hero ile form veya yönetilebilir içeriği sunan özel şablondur.',
  'wordpress/wp-content/themes/myliba/template-events.php': 'Etkinlik içeriklerini tarih ve konum metalarıyla sorgulayıp listeleyen özel sayfa şablonudur.',
  'wordpress/wp-content/themes/myliba/template-landing.php': 'SEO açılış sayfasını ortak hero ve dönüşüm detayı bileşenleriyle birleştiren özel şablondur.',
  'wordpress/wp-content/themes/myliba/template-parts/archive-development-resource.php': 'E-kitap ve rapor gibi gelişim kaynaklarını tür, özet ve bağlantı bilgileriyle kart arşivinde sunan ortak bileşendir.',
  'wordpress/wp-content/themes/myliba/template-parts/client-logo-marquee.php': 'İstemci logo gönderilerini veya sağlanan bileşen verisini erişilebilir, kayan bir logo şeridi olarak gösterir.',
  'wordpress/wp-content/themes/myliba/template-parts/conversion-detail.php': 'Ürün, akademi ve landing içeriklerinin problem, çözüm, fayda, modül, FAQ ve CTA meta alanlarını ortak detay düzeninde gösterir.',
  'wordpress/wp-content/themes/myliba/template-parts/expand.php': 'Başlık ve içerik parametrelerini açılıp kapanabilir bir ayrıntı bileşeninde sunar.',
  'wordpress/wp-content/themes/myliba/template-parts/hero.php': 'Sayfa veya içerik metalarından kaş, başlık, alt başlık ve CTA değerlerini okuyup ortak hero alanını üretir.',
  'wordpress/wp-content/themes/myliba/template-parts/page-academy.php': 'Akademi sayfası ve program gönderilerindeki kapsamlı yönetim metalarını program kartları, modüller, eğitmenler, kazanımlar ve CTA bölümlerine dönüştürür.',
  'wordpress/wp-content/themes/myliba/template-parts/page-development-center.php': 'Gelişim Merkezi açılışını yerelleştirilmiş metinler ve kaynak türlerine yönlendiren kartlarla oluşturur.',
  'wordpress/wp-content/themes/myliba/template-parts/page-ethics.php': 'Etik ve uyum sayfasının hero, ilkeler, süreç ve çağrı bölümlerini yönetilebilir sayfa verisiyle sunar.',
  'wordpress/wp-content/themes/myliba/template-parts/page-faq.php': 'Kategori filtreli, aranabilir FAQ arayüzünü sayfa verileri ve iletişim seçenekleriyle oluşturur.',
  'wordpress/wp-content/themes/myliba/template-parts/page-software.php': 'Yazılım ürün sayfasının hero, performans sekmeleri, modüller, medya ve CTA alanlarını yönetim verilerinden üretir.',
  'wordpress/wp-content/themes/myliba/template-parts/page-solutions.php': 'Çözümler sayfasında çözüm kataloğunu başlıklar, açıklamalar ve hedef bağlantılarla sıralı bölümler halinde gösterir.',
  'wordpress/wp-content/themes/myliba/template-parts/page-story.php': 'Myliba hikâyesi sayfasını marka anlatısı, kilometre taşları, ekip ve değer bölümleriyle oluşturan uzun biçimli şablondur.'
};

function complexity(lines) {
  if (lines > 200) return 'complex';
  if (lines >= 50) return 'moderate';
  return 'simple';
}

function functionSummary(name) {
  const n = name.replace(/^myliba_/, '').replaceAll('_', ' ');
  if (name.startsWith('seed_')) return `${n} için başlangıç WordPress içeriğini ve ilişkili metaları tekrar çalıştırılabilir biçimde oluşturur.`;
  if (name.startsWith('import_current')) return `${n} akışında mevcut herkese açık siteden içerik ve medya verisini WordPress kayıtlarına aktarır.`;
  if (name.startsWith('extract_')) return `Alınan HTML içinden ${n.replace('extract ', '')} verisini güvenli ve yeniden kullanılabilir biçimde çıkarır.`;
  if (name.startsWith('fetch_')) return `${n.replace('fetch ', '')} kaynağını HTTP üzerinden alır ve hata durumlarını WP-CLI için yönetir.`;
  if (name.startsWith('upsert_')) return `${n.replace('upsert ', '')} kaydını slug ve içerik türüne göre oluşturur veya mevcut kaydı günceller.`;
  if (name.includes('locale') || name.includes('language') || name.includes('translate') || name.includes('turkish') || name === 'tr_first') return `${n} kapsamında dil algılama, çeviri veya yerelleştirilmiş yönlendirme davranışını uygular.`;
  if (name.includes('menu') || name.includes('nav')) return `${n} için WordPress menü verisini çözümler, oluşturur veya görünüm katmanına hazırlar.`;
  if (name.includes('url') || name.includes('link') || name.includes('redirect')) return `${n} için dil ve site yapılandırmasını dikkate alan güvenli hedef adresini üretir veya yönlendirmeyi uygular.`;
  if (name.includes('home_') || name.includes('hero')) return `${n} için yönetim metaları ile varsayılan değerleri birleştirerek ana sayfa görünüm verisini hazırlar.`;
  if (name.includes('asset') || name.includes('style') || name.includes('preload') || name.includes('critical')) return `${n} kapsamında tema varlıklarının sürümleme, yükleme veya performans optimizasyonunu yönetir.`;
  if (name.includes('meta') || name.includes('option') || name.includes('content')) return `${n} değerini WordPress ayarları veya içerik metalarından güvenli geri dönüşlerle sağlar.`;
  if (name.includes('solution')) return `${n} için çözüm kataloğu ve ilgili görünüm verisini üretir.`;
  if (name.includes('academy')) return `${n} için akademi içeriklerini ve program görünüm verisini hazırlar.`;
  if (name.includes('faq') || name.includes('lines') || name.includes('rows') || name.includes('parse') || name.includes('format')) return `${n} biçimindeki yönetilebilir metni şablonların kullanabileceği yapılandırılmış veriye dönüştürür.`;
  if (name === 'boot') return 'Myliba Core eklentisinin servislerini ve WordPress kancalarını çalışma zamanında başlatır.';
  if (name === 'activate') return 'Eklenti etkinleştirildiğinde gerekli başlangıç kurulumunu ve yeniden yazma durumunu hazırlar.';
  if (name === 'deactivate') return 'Eklenti devre dışı bırakıldığında geçici çalışma durumunu temizler.';
  if (name === 'myliba_customize_register') return 'Header, footer, CTA ve iletişim alanlarını WordPress Customizer içinde bölümler, ayarlar ve kontroller halinde kaydeder.';
  return `${n} işlevi için WordPress verisini doğrular, işler ve tema ya da yönetim akışının kullanacağı sonucu üretir.`;
}

function functionTags(name) {
  if (name.startsWith('seed') || name.startsWith('upsert') || name.includes('import_current')) return ['wp-cli', 'içerik-yönetimi', 'veri-aktarımı'];
  if (name.includes('locale') || name.includes('language') || name.includes('translate') || name.includes('turkish') || name === 'tr_first') return ['yerelleştirme', 'çoklu-dil', 'wordpress'];
  if (name.includes('menu') || name.includes('nav')) return ['navigasyon', 'wordpress-menüsü', 'tema-yardımcısı'];
  if (name.includes('url') || name.includes('link') || name.includes('redirect')) return ['yönlendirme', 'url', 'tema-yardımcısı'];
  if (name.includes('asset') || name.includes('style') || name.includes('preload') || name.includes('critical')) return ['performans', 'tema-varlığı', 'optimizasyon'];
  if (name.includes('home') || name.includes('hero')) return ['ana-sayfa', 'görünüm-modeli', 'meta-alanı'];
  if (name.includes('meta') || name.includes('option') || name.includes('content')) return ['meta-alanı', 'yapılandırma', 'tema-yardımcısı'];
  if (name.includes('extract') || name.includes('parse') || name.includes('lines') || name.includes('rows') || name.includes('format')) return ['ayrıştırma', 'dönüştürme', 'utility'];
  if (['boot','activate','deactivate'].includes(name)) return ['eklenti-yaşam-döngüsü', 'wordpress', 'entry-point'];
  if (name === 'myliba_customize_register') return ['customizer', 'yönetim-paneli', 'configuration'];
  return ['wordpress', 'tema-yardımcısı', 'içerik-yönetimi'];
}

function fileTags(file) {
  const p = file.path;
  if (p.endsWith('main.css')) return ['stil-sistemi', 'responsive-tasarım', 'frontend'];
  if (p.endsWith('style.css')) return ['tema-metadata', 'wordpress', 'configuration'];
  if (p.endsWith('main.js')) return ['etkileşim', 'frontend', 'event-handler'];
  if (p.includes('wp-cli.php')) return ['wp-cli', 'veri-aktarımı', 'içerik-tohumlama', 'yönetim-aracı'];
  if (p.endsWith('myliba-core.php')) return ['eklenti', 'entry-point', 'wordpress'];
  if (p.endsWith('functions.php')) return ['tema-çekirdeği', 'wordpress', 'utility', 'yerelleştirme'];
  if (p.includes('customizer.php')) return ['customizer', 'yönetim-paneli', 'configuration'];
  if (p.includes('archive-') || p.includes('template-blog') || p.includes('template-events') || p.includes('archive-development')) return ['wordpress-şablonu', 'listeleme', 'frontend'];
  if (p.includes('single-') || p.endsWith('/single.php') || p.includes('conversion-detail')) return ['wordpress-şablonu', 'tekil-içerik', 'meta-alanı'];
  if (p.includes('template-parts/page-') || p.includes('template-')) return ['wordpress-şablonu', 'sayfa-bileşeni', 'frontend'];
  if (p.includes('header.php') || p.includes('footer.php')) return ['tema-bileşeni', 'navigasyon', 'customizer'];
  if (p.includes('front-page.php')) return ['ana-sayfa', 'wordpress-şablonu', 'meta-alanı'];
  return ['wordpress-şablonu', 'frontend', 'tema'];
}

function makeBatch(batchIndex) {
  const batch = batchesDoc.batches.find((b) => b.batchIndex === batchIndex);
  const extraction = JSON.parse(fs.readFileSync(path.join(root, `.understand-anything/tmp/ua-file-extract-results-${batchIndex}.json`), 'utf8'));
  const nodes = [];
  const edges = [];
  for (const result of extraction.results) {
    const file = batch.files.find((f) => f.path === result.path);
    const fileId = `file:${file.path}`;
    nodes.push({
      id: fileId,
      type: 'file',
      name: path.basename(file.path),
      filePath: file.path,
      summary: fileSummaries[file.path],
      tags: fileTags(file),
      complexity: complexity(result.nonEmptyLines),
      ...(file.language === 'php' && result.nonEmptyLines > 100 ? {languageNotes: 'WordPress şablon etiketleri ile PHP veri hazırlığını aynı dosyada birleştirir; dinamik alanlar çıktı bağlamına göre escape edilir.'} : {})
    });
    const exported = new Set((result.exports || []).map((e) => e.name));
    for (const cls of result.classes || []) {
      if ((cls.methods?.length || 0) < 2 && cls.endLine - cls.startLine + 1 < 20 && !exported.has(cls.name)) continue;
      const id = `class:${file.path}:${cls.name}`;
      nodes.push({id, type:'class', name:cls.name, filePath:file.path, lineRange:[cls.startLine,cls.endLine], summary:'Myliba içerik kurulumunu, içe aktarımını ve bakım komutlarını tek bir WP-CLI komut sınıfında toplar.', tags:['wp-cli','service','içerik-yönetimi'], complexity:complexity(cls.endLine-cls.startLine+1)});
      edges.push({source:fileId,target:id,type:'contains',direction:'forward',weight:1.0});
      if (exported.has(cls.name)) edges.push({source:fileId,target:id,type:'exports',direction:'forward',weight:0.8});
    }
    for (const fn of result.functions || []) {
      const span = fn.endLine - fn.startLine + 1;
      if (span < 10 && !exported.has(fn.name)) continue;
      const id = `function:${file.path}:${fn.name}`;
      nodes.push({id, type:'function', name:fn.name, filePath:file.path, lineRange:[fn.startLine,fn.endLine], summary:functionSummary(fn.name), tags:functionTags(fn.name), complexity:complexity(span)});
      edges.push({source:fileId,target:id,type:'contains',direction:'forward',weight:1.0});
      if (exported.has(fn.name)) edges.push({source:fileId,target:id,type:'exports',direction:'forward',weight:0.8});
    }
    for (const targetPath of batch.batchImportData[file.path] || []) {
      edges.push({source:fileId,target:`file:${targetPath}`,type:'imports',direction:'forward',weight:0.7});
    }
  }
  const partCount = Math.ceil(Math.max(nodes.length / 60, edges.length / 120, 1));
  const sortedFiles = [...batch.files].sort((a,b) => a.path.localeCompare(b.path));
  const chunkSize = Math.ceil(sortedFiles.length / partCount);
  for (let i=0; i<partCount; i++) {
    const paths = new Set(sortedFiles.slice(i*chunkSize, (i+1)*chunkSize).map((f) => f.path));
    const partNodes = nodes.filter((n) => paths.has(n.filePath));
    const ids = new Set(partNodes.map((n) => n.id));
    const partEdges = edges.filter((e) => ids.has(e.source));
    const out = {nodes:partNodes, edges:partEdges};
    const outputName = partCount === 1 ? `batch-${batchIndex}.json` : `batch-${batchIndex}-part-${i+1}.json`;
    fs.writeFileSync(path.join(intermediate, outputName), JSON.stringify(out, null, 2) + '\n');
  }
  return {batchIndex, partCount, nodeCount:nodes.length, edgeCount:edges.length, skipped:extraction.filesSkipped};
}

const summaries = [makeBatch(6), makeBatch(7)];

// functions.php tek başına normal parça sınırlarını aştığı için dosya düğümünü
// iki parçada tekrarlayıp alt düğümleri bölüyoruz; birleştirici dosya düğümünü
// kimliğine göre tekilleştirir.
const oversizedPath = 'wordpress/wp-content/themes/myliba/functions.php';
const originalPart2Path = path.join(intermediate, 'batch-6-part-2.json');
const originalPart3Path = path.join(intermediate, 'batch-6-part-3.json');
const originalPart2 = JSON.parse(fs.readFileSync(originalPart2Path, 'utf8'));
const originalPart3 = JSON.parse(fs.readFileSync(originalPart3Path, 'utf8'));
const themeFileNode = originalPart2.nodes.find((n) => n.id === `file:${oversizedPath}`);
const themeSubNodes = originalPart2.nodes.filter((n) => n.filePath === oversizedPath && n.id !== themeFileNode.id);
const otherNodes = originalPart2.nodes.filter((n) => n.filePath !== oversizedPath);
const cut = Math.ceil(themeSubNodes.length / 2);
const subGroups = [themeSubNodes.slice(0, cut), themeSubNodes.slice(cut)];
for (let index = 0; index < subGroups.length; index++) {
  const group = subGroups[index];
  const targetIds = new Set(group.map((n) => n.id));
  const nodes = [themeFileNode, ...group, ...(index === 0 ? otherNodes : [])];
  const nodeIds = new Set(nodes.map((n) => n.id));
  const edges = originalPart2.edges.filter((e) => targetIds.has(e.target) || (index === 0 && nodeIds.has(e.target) && !e.target.startsWith(`function:${oversizedPath}:`)));
  fs.writeFileSync(path.join(intermediate, `batch-6-part-${index + 2}.json`), JSON.stringify({nodes, edges}, null, 2) + '\n');
}
fs.writeFileSync(path.join(intermediate, 'batch-6-part-4.json'), JSON.stringify(originalPart3, null, 2) + '\n');
summaries[0].partCount = 4;
console.log(JSON.stringify(summaries, null, 2));
