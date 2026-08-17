const fs = require('fs');
const path = require('path');

const root = '/Users/okantoper/Documents/Myliba Projects/myliba-website';
const extracted = JSON.parse(fs.readFileSync(path.join(root, '.understand-anything/tmp/ua-file-extract-results-5.json'), 'utf8'));

const fileMeta = {
  '.DS_Store': ['macOS tarafından oluşturulan Finder görünüm meta verisini taşır; uygulama çalışma zamanında bir rol üstlenmez.', ['sistem-meta-verisi', 'yardımcı-dosya', 'macos']],
  '.understand-anything/.trash-20260802-audit/tmp/ua-arch-analyze.js': ['Bilgi grafiğindeki dosya düğümlerini dizin, tür ve bağımlılık sinyallerine göre inceleyerek mimari katman analizine girdi olacak istatistikleri üretir.', ['mimari-analiz', 'bilgi-grafiği', 'yardımcı-betik', 'istatistik']],
  '.understand-anything/.trash-20260802-audit/tmp/ua-arch-layers.js': ['Önceden hazırlanmış mimari analiz verilerinden katman tanımlarını ve düğüm atamalarını oluşturan denetim amaçlı yardımcı betiktir.', ['mimari-katman', 'bilgi-grafiği', 'yardımcı-betik']],
  '.understand-anything/.trash-20260802-audit/tmp/ua-arch-prepare.js': ['Birleştirilmiş bilgi grafiğini mimari katman çözümlemesinin beklediği sade giriş biçimine dönüştürür.', ['veri-hazırlama', 'mimari-analiz', 'yardımcı-betik']],
  '.understand-anything/.trash-20260802-audit/tmp/ua-inline-validate.cjs': ['Bilgi grafiğinin düğüm, kenar, katman ve tur bütünlüğünü deterministik kurallarla doğrular ve sorun raporu üretir.', ['doğrulama', 'bilgi-grafiği', 'kalite-kontrol']],
  '.understand-anything/.trash-20260802-audit/tmp/ua-tour-analyze.js': ['Bilgi grafiğindeki giriş noktalarını, bağlantı yoğunluklarını ve kümeleri hesaplayarak yönlendirilmiş kod turu hazırlanmasını destekler.', ['kod-turu', 'graf-analizi', 'yardımcı-betik', 'istatistik']],
  '.understand-anything/.trash-20260802-audit/tmp/ua-tour-prepare.js': ['Bilgi grafiği ile mimari katmanları tur analiz betiğine uygun tek bir giriş belgesinde birleştirir.', ['veri-hazırlama', 'kod-turu', 'yardımcı-betik']],
  '.understand-anything/.understandignore': ['Understand Anything taramasından medya, yedek, üçüncü taraf ve geçici dosyaları dışlamak için kullanılan proje kapsamı kurallarını tanımlar.', ['tarama-yapılandırması', 'hariç-tutma', 'bilgi-grafiği']],
  '.understand-anything/intermediate/scan-result.json': ['Projenin dilleri, frameworkleri, dosya envanteri, kategorileri ve çözümlenmiş içe aktarma haritasını içeren ara tarama sonucudur.', ['yapılandırma', 'proje-envanteri', 'bilgi-grafiği', 'ara-çıktı']],
  'nginx/default.conf': ['WordPress trafiğini ters vekil üzerinden yönlendirir; güvenlik başlıkları, statik önbellek, SVG izolasyonu, yükleme koruması ve form hız sınırlamasını uygular.', ['ters-vekil', 'güvenlik', 'önbellek', 'wordpress', 'nginx']],
  'wordpress/migration/content-map.md': ['Eski site içerik türlerini WordPress sayfaları, yerel yazılar, özel yazı tipleri ve site ayarlarıyla eşleştirir; SEO geçiş kontrol listesini de kaydeder.', ['dokümantasyon', 'içerik-geçişi', 'wordpress', 'seo']],
  'wordpress/migration/review-report.md': ['Myliba WordPress geçişini UI, UX, SEO, responsive tasarım, içerik modeli ve üretime hazırlık açısından değerlendirip uygulanabilir yol haritası sunar.', ['dokümantasyon', 'üretim-yol-haritası', 'wordpress', 'seo', 'ux']],
  'wordpress/wp-content/index.php': ['WordPress içerik dizinine doğrudan erişildiğinde çıktı üretmeyen koruyucu giriş dosyasıdır.', ['erişim-koruması', 'wordpress', 'giriş-dosyası']],
  'wordpress/wp-content/mu-plugins/automation-by-installatron.php': ['Installatron otomasyonunun WordPress Site Health testlerine eklediği kontrolü filtreleyerek yönetilen kurulum uyumluluğunu sağlar.', ['mu-plugin', 'installatron', 'site-health', 'wordpress']],
  'wordpress/wp-content/mu-plugins/myliba-smtp.php': ['WP Mail SMTP sabitlerini yerel Mailpit ve üretim Gmail OAuth senaryoları için ortam değişkenleriyle yapılandırır.', ['mu-plugin', 'smtp', 'e-posta', 'yapılandırma']],
  'wordpress/wp-content/plugins/index.php': ['WordPress eklenti dizinine doğrudan erişildiğinde çıktı üretmeyen koruyucu giriş dosyasıdır.', ['erişim-koruması', 'wordpress', 'giriş-dosyası']],
  'wordpress/wp-content/plugins/myliba-core/includes/admin.php': ['Myliba yönetim deneyimini sadeleştirir; özel menü ve ayar ekranını, klasik editör tercihlerini, bildirimleri ve pano sayaçlarını yönetir.', ['yönetim-paneli', 'ayarlar', 'wordpress-hook', 'editör-deneyimi']],
  'wordpress/wp-content/plugins/myliba-core/includes/content.php': ['Türkçe ve İngilizce arayüz metinlerini merkezi bir katalogda toplar, yönetim panelinden override edilmesini ve eski ayarların yeni modele taşınmasını sağlar.', ['içerik-yönetimi', 'çeviri', 'yönetim-paneli', 'yerelleştirme']],
  'wordpress/wp-content/plugins/myliba-core/includes/forms.php': ['İletişim, demo ve akademi formlarını oluşturur; doğrulama, hız sınırlama, kayıt saklama ve e-posta bildirim akışını yürütür.', ['form-işleme', 'doğrulama', 'güvenlik', 'e-posta', 'shortcode']],
  'wordpress/wp-content/plugins/myliba-core/includes/images.php': ['Yüklenen görsellerin MIME türlerini sınırlar, dosya imzalarını doğrular ve SVG içeriklerini güvenli bir alt kümeye temizler.', ['görsel-güvenliği', 'dosya-yükleme', 'svg', 'doğrulama']],
  'wordpress/wp-content/plugins/myliba-core/includes/meta.php': ['Myliba içerik türleri için yönetim meta kutularını ve ana sayfa oluşturucusunu sunar; alanların gösterimi, doğrulanması ve kalıcılaştırılmasını kapsar.', ['meta-alanları', 'yönetim-paneli', 'ana-sayfa-oluşturucu', 'doğrulama']],
  'wordpress/wp-content/plugins/myliba-core/includes/options.php': ['Site geneli ve yerelleştirilmiş ayarların şemasını, varsayılanlarını, okuma yardımcılarını ve güvenli kayıt sürecini tanımlar.', ['site-ayarları', 'yerelleştirme', 'doğrulama', 'yapılandırma']],
  'wordpress/wp-content/plugins/myliba-core/includes/page-content.php': ['Farklı sayfa şablonlarının yapılandırılmış metin ve koleksiyon şemalarını, varsayılan içeriklerini, yönetim editörünü ve kayıt işlemlerini yönetir.', ['sayfa-içeriği', 'içerik-şeması', 'yönetim-paneli', 'yerelleştirme', 'doğrulama']],
  'wordpress/wp-content/plugins/myliba-core/includes/post-types.php': ['Myliba özel yazı tiplerini kaydeder; çok dilli kalıcı bağlantıları, rewrite kurallarını, locale yönlendirmelerini ve Polylang uyumluluğunu yönetir.', ['özel-yazı-tipi', 'yönlendirme', 'yerelleştirme', 'polylang', 'wordpress-hook']],
  'wordpress/wp-content/plugins/myliba-core/includes/seo.php': ['Staging noindex korumasını, meta ve hreflang çıktısını, breadcrumb/article/FAQ/course JSON-LD şemalarını ve SEO eklentisi fallback davranışını sağlar.', ['seo', 'yapısal-veri', 'hreflang', 'noindex', 'wordpress-hook']]
};

const exactFunctionSummary = {
  commonDirectoryPrefix: 'Dosya yollarının ortak dizin önekini segment bazında hesaplar.',
  classifyGroup: 'Dizin grup adını bilinen mimari sorumluluk kalıplarından biriyle eşleştirir.',
  classifyFile: 'Dosya adını ve uzantısını kullanarak test, giriş noktası, altyapı, veri veya dokümantasyon rolünü sınıflandırır.',
  installatron_filter_site_status_tests: 'Installatron tarafından eklenen istenmeyen Site Health testini test listesinden çıkarır.',
  migrate_legacy_overrides: 'Eski arayüz metni override kayıtlarını yeni yerelleştirilmiş seçenek yapısına tek seferlik olarak taşır.',
  interface_translation_pairs: 'Kaynak arayüz metinlerinin Türkçe ve İngilizce karşılıklarını tanımlayan merkezi eşleştirme listesini döndürür.',
  interface_catalog: 'Çeviri çiftlerinden yönetim ekranında kullanılacak normalize edilmiş arayüz metni kataloğunu oluşturur.',
  materialize: 'İçerik kaynağı için locale, override ve varsayılan sırasını uygulayarak gösterilecek nihai metni üretir.',
  render_form: 'Bağlama göre iletişim, demo veya akademi formunun erişilebilir HTML alanlarını ve güvenlik belirteçlerini üretir.',
  handle: 'Form isteğini doğrular, hız sınırını uygular, gönderimi kaydeder ve bildirim sonucuna göre kullanıcıyı yönlendirir.',
  academy_program_exists: 'Seçilen akademi programının yayınlanmış içerik kayıtlarında bulunup bulunmadığını doğrular.',
  rate_limited: 'İstemci sinyaline göre geçici sayaç tutarak kısa sürede aşırı form gönderimini engeller.',
  send_notification: 'Doğrulanmış form verisini site yöneticisine WordPress posta altyapısıyla bildirir.',
  sanitize_svg_file: 'SVG belgesinden betik, olay, harici kaynak ve tehlikeli öğeleri çıkararak güvenli dosyayı yeniden yazar.',
  localized_schema: 'Site genelindeki çevrilebilir seçeneklerin tür, varsayılan ve locale şemasını tanımlar.',
  ensure_defaults: 'Eksik site ayarlarını mevcut değerleri ezmeden varsayılan ve yerelleştirilmiş değerlerle tamamlar.',
  sanitize_localized_value: 'Bir yerelleştirilmiş ayar değerini şemadaki alan türüne göre temizleyip doğrular.',
  save_localized_values: 'Gönderilen locale bazlı ayar değerlerini normalize ederek seçenek deposuna kaydeder.',
  register_localized_rewrite_rules: 'Çok dilli özel yazı tipi tabanları için WordPress rewrite kurallarını kaydeder.',
  enforce_route_locale: 'Çözümlenen rota dilini sorgu ve aktif locale ile tutarlı hale getirir.',
  redirect_missing_localized_solution: 'Yerel karşılığı bulunmayan çözüm rotalarını güvenli hedefe yönlendirir.',
  redirect_solution_custom_url: 'Çözüm içeriğinde tanımlı özel URL varsa kanonik hedefe yönlendirme yapar.',
  localized_post_type_link: 'Özel yazı tipinin kalıcı bağlantısını içerik locale ve yerelleştirilmiş tabanla üretir.',
  render_fallback_meta: 'Harici SEO eklentisi yokken canonical, açıklama ve sosyal paylaşım meta etiketlerini üretir.',
  render_hreflang: 'Polylang veya sayfa eşleştirmelerini kullanarak locale alternatifleri ve x-default hreflang bağlantılarını basar.',
  render_schema: 'Sayfa bağlamına uygun JSON-LD varlıklarını bir araya getirip güvenli biçimde çıktılar.',
  breadcrumb_schema: 'Geçerli sayfanın hiyerarşisinden Schema.org BreadcrumbList verisi oluşturur.',
  article_schema: 'Tekil yazı için başlık, tarih, URL ve görsel alanlarını içeren Article şeması üretir.',
  faq_schema: 'Yapılandırılmış alanlar veya FAQ içeriklerinden Schema.org FAQPage verisi oluşturur.',
  educational_organization_schema: 'Akademi açılış sayfası için EducationalOrganization JSON-LD nesnesi kurar.',
  academy_course_schemas: 'Yayınlanmış akademi programlarından Course şemaları üretir.',
  software_definition: 'Yazılım sayfasında düzenlenebilen alan ve koleksiyonların içerik şemasını tanımlar.',
  software_defaults: 'Yazılım sayfası için locale duyarlı başlangıç metinlerini ve koleksiyon verilerini sağlar.',
  solutions_definition: 'Çözümler listeleme sayfasının yapılandırılmış içerik alanlarını tanımlar.',
  solutions_defaults: 'Çözümler listeleme sayfasının varsayılan içerik değerlerini sağlar.',
  development_definition: 'Gelişim merkezi sayfasının metin ve koleksiyon şemasını tanımlar.',
  development_defaults: 'Gelişim merkezi sayfasının locale duyarlı varsayılan içeriğini sağlar.',
  solution_definition: 'Tekil çözüm sayfasının düzenlenebilir alan ve bölümlerini tanımlar.',
  solution_defaults: 'Tekil çözüm sayfası için içerik ve yazı bağlamına göre varsayılan değerleri üretir.',
  report_definition: 'Rapor açılış sayfasının alan ve form odaklı içerik şemasını tanımlar.',
  report_defaults: 'Rapor sayfası için yazı bağlamına göre varsayılan içerik değerlerini üretir.',
  ebook_definition: 'E-kitap açılış sayfasının yapılandırılmış içerik şemasını tanımlar.',
  ebook_defaults: 'E-kitap sayfası için yazı bağlamına göre varsayılan içerik değerlerini üretir.',
  story_definition: 'Kurumsal hikâye sayfasının bölümlerini ve düzenlenebilir alanlarını tanımlar.',
  story_defaults: 'Kurumsal hikâye sayfasının başlangıç metinlerini ve koleksiyonlarını sağlar.',
  ethics_definition: 'Etik danışmanlık sayfasının yapılandırılmış içerik alanlarını tanımlar.',
  ethics_defaults: 'Etik danışmanlık sayfasının varsayılan metin ve koleksiyonlarını sağlar.',
  faq_definition: 'SSS sayfasının başlık, kategori ve soru-cevap alanlarından oluşan şemasını tanımlar.',
  faq_defaults: 'SSS sayfasının locale ve içerik bağlamına göre varsayılan soru-cevap verilerini sağlar.'
};

function functionSummary(name, file) {
  if (exactFunctionSummary[name]) return exactFunctionSummary[name];
  const labels = {
    boot: 'İlgili modülün WordPress action ve filter bağlantılarını kaydederek çalışma zamanını başlatır.',
    register_menu: 'Modülün yönetim menüsü ve alt menü sayfalarını gerekli yetkilerle kaydeder.',
    register_settings: 'Site ayarlarını WordPress Settings API üzerinden kaydeder.',
    register_meta: 'Yapılandırılmış sayfa alanlarını WordPress meta kayıt sistemine tanıtır.',
    register_meta_boxes: 'İçerik türüne uygun Myliba meta kutularını yönetim editörüne ekler.',
    register_page_box: 'Desteklenen sayfalara yapılandırılmış içerik düzenleme kutusunu ekler.',
    register: 'Myliba özel yazı tiplerini yönetim ve ön yüz davranışlarıyla birlikte kaydeder.',
    save: 'Yetki, nonce ve autosave kontrollerinden sonra gönderilen alanları temizleyerek yazı meta verisine kaydeder.',
    render_page: 'Arayüz metni yönetim ekranını katalog, locale ve mevcut override değerleriyle oluşturur.',
    render_settings: 'Myliba site ayarları ekranındaki alanları mevcut seçeneklerle birlikte oluşturur.',
    render_homepage_builder: 'Ana sayfa bölümlerini sıralama ve içerik alanlarıyla yönetilebilen düzenleyici arayüzünü üretir.',
    field_definitions: 'İçerik türüne göre gösterilecek meta alanlarının tür, etiket ve doğrulama tanımlarını döndürür.',
    homepage_section_definitions: 'Ana sayfa oluşturucusunda kullanılabilen bölümlerin başlık ve alan yapılarını tanımlar.',
    homepage_default_sections: 'Yeni veya eksik ana sayfa yapılandırmaları için varsayılan bölüm sırasını döndürür.',
    homepage_sections: 'Kaydedilmiş ana sayfa bölümlerini tanımlarla birleştirip normalize edilmiş sırada döndürür.',
    schema_for_post: 'Verilen yazı için slug ve içerik bağlamından uygun sayfa içerik şemasını belirler.',
    document: 'Kaydedilmiş alanları varsayılanlarla birleştirerek şemaya uygun sayfa içerik belgesi oluşturur.',
    defaults: 'Modülün temel site ayarları için varsayılan değer kümesini döndürür.',
    definition: 'İstenen sayfa şeması adını ilgili alan tanımlarıyla eşleştirir.',
    sections: 'Sayfa belgesindeki görünür bölüm listesini normalize edilmiş biçimde döndürür.',
    collection: 'Sayfa belgesinden belirtilen yapılandırılmış koleksiyon alanını döndürür.',
    text: 'Sayfa belgesinden belirtilen metin alanını güvenli varsayılanıyla döndürür.',
    current_url: 'Geçerli isteğin kanonik site URL’sini WordPress yönlendirme kurallarına uygun biçimde oluşturur.'
  };
  if (labels[name]) return labels[name];
  if (name.startsWith('render_')) return `${name.replace(/^render_/, '').replaceAll('_', ' ')} alanı için yönetim veya ön yüz HTML çıktısını güvenli biçimde üretir.`;
  if (name.startsWith('save_')) return `${name.replace(/^save_/, '').replaceAll('_', ' ')} verisini doğrulayıp temizleyerek WordPress meta veya seçenek deposuna kaydeder.`;
  if (name.startsWith('sanitize_')) return `${name.replace(/^sanitize_/, '').replaceAll('_', ' ')} girdisini beklenen yapıya ve güvenlik kurallarına göre temizler.`;
  if (name.startsWith('field_')) return `${name.replace(/^field_/, '').replaceAll('_', ' ')} türündeki yönetim alanının güvenli HTML kontrolünü üretir.`;
  if (name.startsWith('homepage_')) return `Ana sayfa oluşturucusundaki ${name.replace(/^homepage_/, '').replaceAll('_', ' ')} bilgisini hesaplar ve normalize eder.`;
  if (name.startsWith('interface_')) return `Yönetilebilir arayüz metinleri için ${name.replace(/^interface_/, '').replaceAll('_', ' ')} işlemini yürütür.`;
  if (name.startsWith('legacy_')) return `Eski metin override modelindeki ${name.replace(/^legacy_/, '').replaceAll('_', ' ')} değerini uyumluluk amacıyla çözümler.`;
  if (name.startsWith('localized_')) return `Çok dilli içerik yönlendirmesinde ${name.replace(/^localized_/, '').replaceAll('_', ' ')} bilgisini üretir.`;
  if (name.startsWith('polylang_')) return `Myliba içeriklerini Polylang kapsamına almak için ${name.replace(/^polylang_/, '').replaceAll('_', ' ')} listesini filtreler.`;
  if (name.startsWith('redirect_')) return `İstek bağlamını denetleyerek uygun kanonik Myliba adresine yönlendirme uygular.`;
  if (name.startsWith('render')) return 'İlgili yönetim veya ön yüz bileşeninin güvenli HTML çıktısını oluşturur.';
  if (name.startsWith('register')) return 'İlgili WordPress bileşenini gerekli hook ve yapılandırmalarla kaydeder.';
  if (name.startsWith('simplify_')) return `WordPress ${name.replace(/^simplify_/, '').replaceAll('_', ' ')} arayüzünü Myliba editörlerinin ihtiyaçlarına göre sadeleştirir.`;
  if (name.startsWith('use_classic_editor')) return 'Myliba tarafından yönetilen içerik türlerinde blok editörü yerine klasik editör kullanımını belirler.';
  if (name === 'admin_notices') return 'Yönetim panelinde kurulum ve yapılandırma durumuna ilişkin Myliba bildirimlerini gösterir.';
  if (name === 'dashboard_counts') return 'Myliba içerik türleri için yayın ve taslak sayılarını pano özetinde gösterir.';
  if (name === 'shortcode' || name.endsWith('_shortcode')) return 'İlgili form bağlamını seçerek Myliba form HTML’ini üreten shortcode geri çağrısını sağlar.';
  if (name === 'site_text') return 'Formlarda kullanılan metni merkezi içerik kataloğu ve locale üzerinden çözümler.';
  if (name.includes('upload') || name.includes('filetype')) return 'Yüklenen dosyanın türünü ve içeriğini Myliba güvenlik politikasına göre doğrular veya sınırlar.';
  if (name.includes('hero')) return 'Ana sayfa hero verisini okur, düzenler veya güvenli biçimde normalize eder.';
  if (name.includes('performance_tab')) return 'Performans sekmesi verisini yönetim arayüzünde işler ve güvenli yapıya dönüştürür.';
  if (name.includes('media')) return 'WordPress medya seçicisiyle çalışan yönetim alanını ve gerekli istemci davranışını sağlar.';
  if (name.includes('noindex') || name.includes('staging') || name.includes('robots') || name.includes('sitemap')) return 'Ortam ve içerik ayarlarına göre arama motoru indeksleme davranışını belirler.';
  if (name.includes('schema')) return 'Geçerli içerik bağlamı için yapılandırılmış veri şemasını üretir veya seçer.';
  if (name.includes('seo_plugin')) return 'Desteklenen harici SEO eklentilerinden birinin etkin olup olmadığını denetler.';
  if (name === 'document_title') return 'Tekil içeriklerde kayıtlı SEO başlığını WordPress belge başlığına uygular.';
  if (name === 'current_host') return 'Geçerli isteğin normalize edilmiş ana makine adını güvenli kaynaklardan çözümler.';
  if (name === 'is_academy_landing') return 'Geçerli sayfanın desteklenen akademi açılış sayfalarından biri olup olmadığını belirler.';
  if (name === 'format_placeholders') return 'Çeviri metnindeki biçim yer tutucularını yönetim ekranında korunabilir temsile dönüştürür.';
  if (name.includes('override')) return 'Yerelleştirilmiş arayüz metni için kaydedilmiş override değerini çözümler veya birleştirir.';
  if (name.includes('locales')) return 'Site tarafından desteklenen locale kodları ve etiketlerini döndürür.';
  if (name === 'get_all' || name === 'get') return 'Site seçeneklerini varsayılanlarla birleştirerek güvenli okuma erişimi sağlar.';
  if (name === 'indexing_enabled') return 'Site ayarlarından arama motoru indekslemesinin etkin olup olmadığını döndürür.';
  if (name === 'sanitize') return 'Gönderilen site seçeneklerini şemaya göre doğrulayıp temizler.';
  if (name.includes('query_vars')) return 'Yerelleştirilmiş rotaların ihtiyaç duyduğu WordPress sorgu değişkenlerini ekler.';
  if (name === 'print_media_field_script') return 'Meta kutularındaki medya seçici kontrollerini çalıştıran yönetim JavaScript’ini basar.';
  return `${name.replaceAll('_', ' ')} sorumluluğunu bu modülün WordPress akışı içinde uygular.`;
}

function complexity(lines) {
  if (lines > 200) return 'complex';
  if (lines >= 50) return 'moderate';
  return 'simple';
}

function functionTags(name, file) {
  const tags = ['wordpress-işlevi', 'iş-mantığı'];
  if (file.endsWith('/admin.php') || file.endsWith('/meta.php') || file.endsWith('/page-content.php')) tags.push('yönetim-paneli');
  else if (file.endsWith('/seo.php')) tags.push('seo');
  else if (file.endsWith('/forms.php')) tags.push('form-işleme');
  else if (file.endsWith('/images.php')) tags.push('görsel-güvenliği');
  else if (file.endsWith('/options.php')) tags.push('site-ayarları');
  else if (file.endsWith('/post-types.php')) tags.push('içerik-modeli');
  else if (file.endsWith('/content.php')) tags.push('yerelleştirme');
  else if (file.endsWith('.js')) tags.splice(0, tags.length, 'yardımcı-betik', 'graf-analizi', 'javascript');
  else tags.push('wordpress-hook');
  return tags.slice(0, 5);
}

const nodes = [];
const edges = [];
for (const result of extracted.results) {
  const [summary, tags] = fileMeta[result.path];
  const type = result.fileCategory === 'config' ? 'config' : result.fileCategory === 'docs' ? 'document' : 'file';
  const id = `${type}:${result.path}`;
  nodes.push({ id, type, name: path.posix.basename(result.path), filePath: result.path, summary, tags, complexity: complexity(result.nonEmptyLines || 0) });

  const exports = new Set((result.exports || []).map((item) => item.name));
  for (const fn of result.functions || []) {
    const lineCount = fn.endLine - fn.startLine + 1;
    if (lineCount < 10 && !exports.has(fn.name)) continue;
    const fnId = `function:${result.path}:${fn.name}`;
    nodes.push({
      id: fnId,
      type: 'function',
      name: fn.name,
      filePath: result.path,
      lineRange: [fn.startLine, fn.endLine],
      summary: functionSummary(fn.name, result.path),
      tags: functionTags(fn.name, result.path),
      complexity: complexity(lineCount)
    });
    edges.push({ source: id, target: fnId, type: 'contains', direction: 'forward', weight: 1.0 });
    if (exports.has(fn.name)) edges.push({ source: id, target: fnId, type: 'exports', direction: 'forward', weight: 0.8 });
  }
}

const semanticEdges = [
  ['config:.understand-anything/intermediate/scan-result.json', 'file:.understand-anything/.trash-20260802-audit/tmp/ua-arch-prepare.js', 'configures', 0.6],
  ['document:wordpress/migration/content-map.md', 'file:wordpress/wp-content/plugins/myliba-core/includes/post-types.php', 'documents', 0.5],
  ['document:wordpress/migration/content-map.md', 'file:wordpress/wp-content/plugins/myliba-core/includes/meta.php', 'documents', 0.5],
  ['document:wordpress/migration/review-report.md', 'file:wordpress/wp-content/plugins/myliba-core/includes/seo.php', 'documents', 0.5],
  ['document:wordpress/migration/review-report.md', 'file:wordpress/wp-content/plugins/myliba-core/includes/page-content.php', 'documents', 0.5],
  ['file:wordpress/wp-content/plugins/myliba-core/includes/forms.php', 'file:wordpress/wp-content/mu-plugins/myliba-smtp.php', 'depends_on', 0.6]
];
for (const [source, target, type, weight] of semanticEdges) edges.push({ source, target, type, direction: 'forward', weight });

const sortedFiles = extracted.results.map((r) => r.path).sort();
const partCount = Math.ceil(Math.max(nodes.length / 60, edges.length / 120));
const chunkSize = Math.ceil(sortedFiles.length / partCount);
for (let i = 0; i < partCount; i += 1) {
  const fileSet = new Set(sortedFiles.slice(i * chunkSize, (i + 1) * chunkSize));
  const partNodes = nodes.filter((node) => fileSet.has(node.filePath));
  const nodeIds = new Set(partNodes.map((node) => node.id));
  const partEdges = edges.filter((edge) => nodeIds.has(edge.source));
  const output = path.join(root, `.understand-anything/intermediate/batch-5-part-${i + 1}.json`);
  fs.writeFileSync(output, `${JSON.stringify({ nodes: partNodes, edges: partEdges }, null, 2)}\n`);
}

process.stdout.write(JSON.stringify({ partCount, nodeCount: nodes.length, edgeCount: edges.length, chunkSize }, null, 2));
