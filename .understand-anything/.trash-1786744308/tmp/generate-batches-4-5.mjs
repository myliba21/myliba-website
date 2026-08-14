import fs from 'node:fs';
import path from 'node:path';

const root = '/Users/okantoper/Documents/Myliba Projects/myliba-website';
const intermediate = path.join(root, '.understand-anything/intermediate');
const read = (name) => JSON.parse(fs.readFileSync(path.join(root, '.understand-anything/tmp', name), 'utf8'));
const extracted = {4: read('ua-file-extract-results-4.json'), 5: read('ua-file-extract-results-5.json')};
const inputs = {4: read('ua-file-analyzer-input-4.json'), 5: read('ua-file-analyzer-input-5.json')};

const fileInfo = {
  'wordpress/.env.example': ['Yerel WordPress, MariaDB, yönetici hesabı, hero görselleri ve SMTP için örnek ortam değişkenlerini tanımlar; hassas değerlerin dağıtımdan önce değiştirilmesini zorunlu kılar.', ['yapılandırma','ortam-değişkenleri','güvenlik','wordpress']],
  'wordpress/Makefile': ['Docker Compose tabanlı WordPress ortamını başlatma, kurma, örnek içerikle doldurma, durdurma ve SMTP testi görevlerini tek komutlarda toplar.', ['altyapı','build-system','wordpress','otomasyon']],
  'wordpress/README.md': ['Myliba WordPress kurulumunu, iki dilli URL yapısını, içerik içe aktarımını, eklenti tercihlerini ve üretime geçiş kontrol listesini açıklar.', ['dokümantasyon','wordpress','kurulum','dağıtım']],
  'wordpress/docker-compose.yml': ['MariaDB, Mailpit, Apache tabanlı WordPress ve WP-CLI servislerinden oluşan yerel geliştirme yığınını kalıcı hacimler ve sağlık kontrolleriyle orkestre eder.', ['altyapı','orkestrasyon','containerization','wordpress']],
  'wordpress/uploads.ini': ['WordPress/PHP yükleme boyutu, bellek ve çalışma süresi sınırlarını yerel geliştirme için yükseltir.', ['yapılandırma','php','dosya-yükleme','performans']],
  '.DS_Store': ['macOS Finder tarafından üretilmiş ikili dizin meta verisidir; uygulama çalışma zamanında işlevsel bir rolü yoktur.', ['yardımcı-dosya','macos','meta-veri']],
  '.understand-anything/.trash-20260802-audit/tmp/ua-arch-analyze.js': ['Eski Understand Anything denetiminde dosyaları sınıflandırıp düğümleri mimari gruplara ayıran ve katmanlar arası ilişkileri hesaplayan analiz betiğidir.', ['analiz','bilgi-grafiği','mimari','script']],
  '.understand-anything/.trash-20260802-audit/tmp/ua-arch-layers.js': ['Eski denetim grafiğindeki dosyaları dizin ve rol örüntülerine göre mimari katmanlara dönüştüren geçici betiktir.', ['analiz','bilgi-grafiği','katmanlama','script']],
  '.understand-anything/.trash-20260802-audit/tmp/ua-arch-prepare.js': ['Eski mimari analiz için birleştirilmiş grafiği okuyup model girdisini hazırlayan geçici yardımcı betiktir.', ['analiz','veri-hazırlama','bilgi-grafiği','script']],
  '.understand-anything/.trash-20260802-audit/tmp/ua-inline-validate.cjs': ['Eski denetim çıktılarındaki JSON yapılarını ve grafik referanslarını satır içi doğrulayan CommonJS yardımcı betiğidir.', ['doğrulama','bilgi-grafiği','json','script']],
  '.understand-anything/.trash-20260802-audit/tmp/ua-tour-analyze.js': ['Eski bilgi grafiğinden proje tanıtım turu adımlarını ve anlatı sırasını üreten analiz betiğidir.', ['analiz','proje-turu','bilgi-grafiği','script']],
  '.understand-anything/.trash-20260802-audit/tmp/ua-tour-prepare.js': ['Eski proje turu analizi için grafik verisini küçültüp uygun girdi biçimine getiren geçici betiktir.', ['veri-hazırlama','proje-turu','bilgi-grafiği','script']],
  '.understand-anything/.understandignore': ['Understand Anything taramasından üçüncü taraf, üretilmiş, medya ve geçici dosyaları hariç tutan desenleri içerir.', ['yapılandırma','tarama','hariç-tutma','bilgi-grafiği']],
  '.understand-anything/intermediate/scan-result.json': ['Proje tarayıcısının dosya envanteri, dil/kategori bilgileri, içe aktarımlar ve dışa aktarılan semboller dahil ara sonucunu saklar.', ['yapılandırma','tarama-sonucu','bilgi-grafiği','json']],
  'nginx/default.conf': ['WordPress trafiğini ters vekil üzerinden yönlendirir; güvenlik başlıkları, sıkıştırma, statik önbellek, form hız sınırlama ve XML-RPC engelleme kurallarını uygular.', ['ters-vekil','güvenlik','önbellek','wordpress']],
  'wordpress/migration/content-map.md': ['Eski site içerik türlerini WordPress sayfaları, yerel yazılar, özel yazı türleri ve Myliba ayar ekranlarıyla eşleştirir; SEO taşıma kontrol listesini verir.', ['dokümantasyon','içerik-modeli','migration','wordpress']],
  'wordpress/migration/review-report.md': ['WordPress geçişinin UI, UX, SEO, responsive tasarım, içerik ve teknik mimari eksiklerini değerlendirip üretim yol haritasını tanımlar.', ['dokümantasyon','inceleme','seo','yol-haritası']],
  'wordpress/wp-content/index.php': ['wp-content dizininin doğrudan listelenmesine karşı boş WordPress koruma giriş dosyasıdır.', ['güvenlik','wordpress','giriş-noktası']],
  'wordpress/wp-content/mu-plugins/automation-by-installatron.php': ['Installatron harici yönetimi nedeniyle WordPress otomatik güncellemelerini ve ilgili Site Health kontrolünü devre dışı bırakır.', ['wordpress','otomasyon','güncelleme-yönetimi','mu-plugin']],
  'wordpress/wp-content/mu-plugins/myliba-smtp.php': ['WP Mail SMTP sabitlerini yerel Mailpit veya üretim Gmail OAuth ayarlarına göre tanımlayan zorunlu eklentidir.', ['wordpress','smtp','yapılandırma','mu-plugin']],
  'wordpress/wp-content/plugins/index.php': ['Eklentiler dizininin doğrudan listelenmesini önleyen boş WordPress koruma dosyasıdır.', ['güvenlik','wordpress','giriş-noktası']],
  'wordpress/wp-content/plugins/myliba-core/includes/admin.php': ['Myliba yönetim menüsünü ve ayar ekranını kurar, WordPress panelini sadeleştirir ve içerik sayaçlarını gösterir.', ['wordpress','yönetim-paneli','yapılandırma','event-handler']],
  'wordpress/wp-content/plugins/myliba-core/includes/content.php': ['TR/EN site ve arayüz metinleri için katalog, geçersiz kılma, eski veri taşıma ve yönetim ekranı işlevlerini sağlar.', ['wordpress','çok-dillilik','içerik-yönetimi','migration']],
  'wordpress/wp-content/plugins/myliba-core/includes/forms.php': ['İletişim/demo/akademi formlarını kısa kodlarla üretir; başvuruları doğrular, hız sınırlar, kaydeder ve e-posta bildirimi gönderir.', ['wordpress','form','validation','bildirim']],
  'wordpress/wp-content/plugins/myliba-core/includes/images.php': ['Yüklemeleri izin verilen görsel türleriyle sınırlar, SVG dosyalarını güvenli hale getirir ve yönetici uyarıları üretir.', ['wordpress','görsel','güvenlik','validation']],
  'wordpress/wp-content/plugins/myliba-core/includes/meta.php': ['Myliba içerikleri için dil, hero, SEO, etkinlik, dönüşüm, ana sayfa ve diğer yönetim meta kutularını tanımlar, render eder ve güvenli biçimde kaydeder.', ['wordpress','meta-kutuları','yönetim-paneli','içerik-modeli']],
  'wordpress/wp-content/plugins/myliba-core/includes/options.php': ['Yerelleştirilmiş site ayar şemasını, varsayılanları, okuma yardımcılarını ve WordPress Settings API doğrulama/kaydetme akışını yönetir.', ['wordpress','yapılandırma','çok-dillilik','validation']],
  'wordpress/wp-content/plugins/myliba-core/includes/page-content.php': ['Yazılım, çözüm, rapor, e-kitap, hikâye, etik ve SSS sayfaları için yapılandırılmış içerik şemalarını, varsayılanları, yönetim editörlerini ve kayıt işlemlerini sağlar.', ['wordpress','içerik-modeli','yönetim-paneli','serialization']],
  'wordpress/wp-content/plugins/myliba-core/includes/post-types.php': ['Myliba özel yazı türlerini, yerelleştirilmiş kalıcı bağlantıları ve Polylang entegrasyonunu kaydeder; rota dilini ve özel yönlendirmeleri uygular.', ['wordpress','özel-yazı-türü','routing','çok-dillilik']],
  'wordpress/wp-content/plugins/myliba-core/includes/seo.php': ['Noindex politikası, robots, canonical/Open Graph/hreflang ve Article, FAQ, Breadcrumb, Course gibi JSON-LD şemaları için SEO yedeğini sağlar.', ['wordpress','seo','schema','çok-dillilik']]
};

const moduleLabel = (p) => ({
  'admin.php':'yönetim paneli', 'content.php':'arayüz metinleri', 'forms.php':'form işleme',
  'images.php':'görsel güvenliği', 'meta.php':'içerik meta alanları', 'options.php':'site ayarları',
  'page-content.php':'sayfa içerik şeması', 'post-types.php':'özel yazı türleri', 'seo.php':'SEO'
}[path.basename(p)] || 'modül');

const trName = (name) => name.replaceAll('_', ' ');
function functionInfo(filePath, name) {
  const label = moduleLabel(filePath);
  let summary = `${label} kapsamındaki “${trName(name)}” işlemini gerçekleştirir.`;
  let tags = ['yardımcı-fonksiyon','wordpress',label.replaceAll(' ','-')];
  if (name === 'boot') { summary = `${label} modülünün WordPress action ve filter bağlantılarını kaydederek modülü başlatır.`; tags = ['başlatma','wordpress-hooks','event-handler']; }
  else if (name.startsWith('render_')) { summary = `${trName(name.slice(7))} için yönetim arayüzünü veya frontend çıktısını güvenli HTML olarak üretir.`; tags = ['render','yönetim-paneli','wordpress']; }
  else if (name.startsWith('register_') || name === 'register') { summary = `${trName(name.replace(/^register_?/,'')) || label} tanımlarını ve ilgili WordPress bağlantılarını kaydeder.`; tags = ['kayıt','wordpress-hooks','yapılandırma']; }
  else if (name.startsWith('save_') || name === 'save') { summary = `${trName(name.replace(/^save_?/,'')) || label} verisini doğrulayıp temizleyerek WordPress kalıcı deposuna kaydeder.`; tags = ['kayıt','validation','wordpress']; }
  else if (name.startsWith('sanitize_') || name === 'sanitize') { summary = `${trName(name.replace(/^sanitize_?/,'')) || 'girdi'} verisini izin verilen yapı ve değerlerle sınırlandırarak güvenli hale getirir.`; tags = ['validation','güvenlik','veri-temizleme']; }
  else if (name.endsWith('_definition') || name === 'definition' || name === 'field_definitions' || name === 'homepage_section_definitions') { summary = `${trName(name)} için alan, tür, etiket ve davranışlardan oluşan içerik şemasını döndürür.`; tags = ['schema-definition','içerik-modeli','wordpress']; }
  else if (name.endsWith('_defaults') || name === 'defaults' || name === 'localized_default') { summary = `${trName(name)} kapsamında eksik içerik alanlarında kullanılacak varsayılan değerleri üretir.`; tags = ['varsayılanlar','içerik-modeli','yapılandırma']; }
  else if (name.startsWith('field_')) { summary = `${trName(name.slice(6))} türündeki yönetim alanını kaçış uygulanmış değerlerle HTML olarak oluşturur.`; tags = ['form-alanı','yönetim-paneli','render']; }
  else if (name.includes('schema')) { summary = `${trName(name)} için arama motorlarına sunulacak yapılandırılmış veri nesnesini oluşturur.`; tags = ['seo','schema','serialization']; }
  else if (name.startsWith('redirect_')) { summary = `${trName(name.slice(9))} koşulunu denetleyip uygun yerelleştirilmiş hedefe güvenli yönlendirme uygular.`; tags = ['routing','yönlendirme','çok-dillilik']; }
  else if (name.includes('localized') || name.includes('locale') || name.includes('language')) { summary = `${trName(name)} üzerinden içerik veya rota için yerel dil değerlerini çözümler.`; tags = ['çok-dillilik','yerelleştirme','wordpress']; }
  else if (name.includes('override') || name.includes('catalog') || name.includes('translation')) { summary = `${trName(name)} ile çevrilebilir arayüz metinlerini veya kullanıcı geçersiz kılmalarını çözümler.`; tags = ['çok-dillilik','içerik-yönetimi','wordpress']; }
  else if (name.includes('shortcode')) { summary = `${trName(name)} kısa kodunu ilgili Myliba formu veya bileşen çıktısına dönüştürür.`; tags = ['shortcode','form','wordpress']; }
  else if (name === 'handle') { summary = 'Form gönderimini nonce, bot tuzağı ve alan kurallarıyla doğrular; başvuruyu kaydedip bildirimi tetikler.'; tags = ['form-handler','validation','güvenlik']; }
  else if (name === 'rate_limited') { summary = 'İstemci bazlı geçici sayaçla form gönderim sıklığını denetler ve kötüye kullanımı sınırlar.'; tags = ['rate-limiting','güvenlik','form']; }
  else if (name === 'send_notification') { summary = 'Doğrulanmış form verisini site yöneticisine e-posta bildirimi olarak gönderir.'; tags = ['bildirim','e-posta','form']; }
  else if (name.includes('upload') || name.includes('filetype') || name.includes('svg')) { summary = `${trName(name)} ile yüklenen görsellerin türünü ve içeriğini güvenlik kurallarına göre denetler.`; tags = ['dosya-yükleme','güvenlik','validation']; }
  else if (name.includes('homepage') || name.includes('hero') || name.includes('performance')) { summary = `${trName(name)} verisini ana sayfa düzenleyicisi için üretir, normalleştirir veya yönetir.`; tags = ['anasayfa','içerik-yönetimi','wordpress']; }
  else if (name.includes('robots') || name.includes('noindex') || name.includes('indexing') || name.includes('staging')) { summary = `${trName(name)} koşullarına göre arama motoru indeksleme ve robots davranışını belirler.`; tags = ['seo','indexleme','güvenlik']; }
  else if (name === 'commonDirectoryPrefix') { summary = 'Verilen dosya yollarının paylaştığı en uzun dizin önekini hesaplar.'; tags = ['utility','dosya-yolu','analiz']; }
  else if (name === 'classifyGroup' || name === 'classifyFile') { summary = `${trName(name)} ile dosya veya grubun mimari rolünü yol ve ad örüntülerinden sınıflandırır.`; tags = ['sınıflandırma','mimari','analiz']; }
  else if (name.startsWith('polylang_')) { summary = `${trName(name)} listesini Myliba içerik türlerinin Polylang tarafından çevrilebilmesi için genişletir.`; tags = ['polylang','çok-dillilik','wordpress']; }
  else if (name === 'installatron_filter_site_status_tests') { summary = 'Installatron tarafından yönetilen güncellemeye ait WordPress Site Health testini sonuç listesinden çıkarır.'; tags = ['installatron','wordpress-hooks','güncelleme-yönetimi']; }
  return [summary, tags];
}

const fileNodeType = (f) => {
  if (f.fileCategory === 'config') return ['config', `config:${f.path}`];
  if (f.fileCategory === 'docs') return ['document', `document:${f.path}`];
  if (f.fileCategory === 'infra') return ['service', `service:${f.path}`];
  return ['file', `file:${f.path}`];
};
const complexity = (lines) => lines > 200 ? 'complex' : lines >= 50 ? 'moderate' : 'simple';

function buildBatch(index) {
  const resultByPath = new Map(extracted[index].results.map((r) => [r.path, r]));
  const nodes = [];
  const edges = [];
  for (const f of inputs[index].batchFiles) {
    const r = resultByPath.get(f.path);
    if (!r) throw new Error(`Eksik çıkarım sonucu: ${f.path}`);
    const [type, id] = fileNodeType(f);
    const [summary, tags] = fileInfo[f.path];
    const fileNode = {id, type, name:path.basename(f.path), filePath:f.path, summary, tags, complexity:complexity(r.nonEmptyLines ?? f.sizeLines)};
    if (f.path === 'wordpress/docker-compose.yml') fileNode.languageNotes = 'Compose dosyası servis sağlık koşulları, profiles ile isteğe bağlı WP-CLI ve kalıcı Docker hacimleri kullanır.';
    if (f.path.endsWith('.php') && (r.functions?.length || 0) > 5) fileNode.languageNotes = 'İsim alanlı PHP fonksiyonları WordPress action/filter yaşam döngüsüne statik callback olarak bağlanır.';
    nodes.push(fileNode);
    if (f.fileCategory === 'code') {
      const exported = new Set((r.exports || []).map((x) => x.name));
      for (const fn of (r.functions || [])) {
        const span = fn.endLine - fn.startLine + 1;
        if (span < 10 && !exported.has(fn.name)) continue;
        const fnId = `function:${f.path}:${fn.name}`;
        const [fnSummary, fnTags] = functionInfo(f.path, fn.name);
        nodes.push({id:fnId, type:'function', name:fn.name, filePath:f.path, lineRange:[fn.startLine,fn.endLine], summary:fnSummary, tags:fnTags, complexity:complexity(span)});
        edges.push({source:id,target:fnId,type:'contains',direction:'forward',weight:1.0});
        if (exported.has(fn.name)) edges.push({source:id,target:fnId,type:'exports',direction:'forward',weight:0.8});
      }
    }
    for (const target of (inputs[index].batchImportData[f.path] || [])) {
      edges.push({source:id,target:`file:${target}`,type:'imports',direction:'forward',weight:0.7});
    }
  }
  if (index === 4) {
    edges.push(
      {source:'config:wordpress/.env.example',target:'service:wordpress/docker-compose.yml',type:'configures',direction:'forward',weight:0.6},
      {source:'config:wordpress/uploads.ini',target:'service:wordpress/docker-compose.yml',type:'configures',direction:'forward',weight:0.6},
      {source:'service:wordpress/Makefile',target:'service:wordpress/docker-compose.yml',type:'depends_on',direction:'forward',weight:0.6},
      {source:'document:wordpress/README.md',target:'service:wordpress/Makefile',type:'documents',direction:'forward',weight:0.5},
      {source:'document:wordpress/README.md',target:'service:wordpress/docker-compose.yml',type:'documents',direction:'forward',weight:0.5}
    );
  } else {
    edges.push(
      {source:'file:nginx/default.conf',target:'file:wordpress/wp-content/index.php',type:'routes',direction:'forward',weight:0.6}
    );
  }
  return {nodes, edges};
}

function writeBatch(index, graph) {
  const nodeCount = graph.nodes.length;
  const edgeCount = graph.edges.length;
  const parts = Math.ceil(Math.max(nodeCount / 60, edgeCount / 120));
  if (parts <= 1) {
    fs.writeFileSync(path.join(intermediate, `batch-${index}.json`), JSON.stringify(graph, null, 2) + '\n');
    return [`batch-${index}.json`];
  }
  const files = [...inputs[index].batchFiles].sort((a,b) => a.path.localeCompare(b.path)).map((f) => f.path);
  const groups = [];
  let current = [];
  let currentNodeCount = 0;
  let currentEdgeCount = 0;
  for (const filePath of files) {
    const fileNodes = graph.nodes.filter((n) => n.filePath === filePath);
    const fileNodeIds = new Set(fileNodes.map((n) => n.id));
    const fileEdges = graph.edges.filter((e) => fileNodeIds.has(e.source));
    if (fileNodes.length > 60 || fileEdges.length > 120) {
      throw new Error(`Tek dosya parça sınırını aşıyor ve alt düğüm bölme istisnası gerekiyor: ${filePath}`);
    }
    if (current.length && (currentNodeCount + fileNodes.length > 60 || currentEdgeCount + fileEdges.length > 120)) {
      groups.push(current);
      current = [];
      currentNodeCount = 0;
      currentEdgeCount = 0;
    }
    current.push(filePath);
    currentNodeCount += fileNodes.length;
    currentEdgeCount += fileEdges.length;
  }
  if (current.length) groups.push(current);
  if (groups.length < parts) throw new Error(`Kapasite bölümü beklenen asgari parça sayısından az: ${groups.length} < ${parts}`);
  const written = [];
  for (let i = 0; i < groups.length; i++) {
    const partFiles = new Set(groups[i]);
    const partNodes = graph.nodes.filter((n) => partFiles.has(n.filePath));
    const sourceIds = new Set(partNodes.map((n) => n.id));
    const partEdges = graph.edges.filter((e) => sourceIds.has(e.source));
    const name = `batch-${index}-part-${i + 1}.json`;
    fs.writeFileSync(path.join(intermediate, name), JSON.stringify({nodes:partNodes,edges:partEdges}, null, 2) + '\n');
    written.push(name);
  }
  return written;
}

for (const index of [4,5]) {
  const graph = buildBatch(index);
  const files = writeBatch(index, graph);
  console.log(JSON.stringify({batchIndex:index, files, nodes:graph.nodes.length, edges:graph.edges.length, skipped:extracted[index].filesSkipped || []}));
}
