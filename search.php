<?php
require_once __DIR__ . '/config/functions.php';

$keyword = isset($_GET['q']) ? sanitize($_GET['q']) : '';

$page_title = "Hasil Pencarian: " . (!empty($keyword) ? "\"$keyword\"" : "") . " — PT. Hastra Karya Persada";
$page_desc = "Hasil pencarian kata kunci \"$keyword\" pada situs web resmi PT. Hastra Karya Persada.";
include __DIR__ . '/includes/header.php';

// Arrays to store matches
$matched_services = [];
$matched_portfolio = [];
$matched_articles = [];

if (!empty($keyword)) {
    $search_pattern = "%" . $keyword . "%";
    
    // 1. Search Services
    try {
        $stmt = $db->prepare("SELECT title, slug, short_description FROM services WHERE title LIKE ? OR description LIKE ? OR short_description LIKE ?");
        $stmt->execute([$search_pattern, $search_pattern, $search_pattern]);
        $matched_services = $stmt->fetchAll();
    } catch (Exception $e) {}
    
    // 2. Search Portfolio
    try {
        $stmt = $db->prepare("SELECT title, slug, category, location, year FROM portfolio WHERE title LIKE ? OR description LIKE ? OR category LIKE ?");
        $stmt->execute([$search_pattern, $search_pattern, $search_pattern]);
        $matched_portfolio = $stmt->fetchAll();
    } catch (Exception $e) {}
    
    // 3. Search Articles
    try {
        $stmt = $db->prepare("SELECT title, slug, content, created_at FROM articles WHERE (title LIKE ? OR content LIKE ? OR tags LIKE ?) AND status = 'published'");
        $stmt->execute([$search_pattern, $search_pattern, $search_pattern]);
        $matched_articles = $stmt->fetchAll();
    } catch (Exception $e) {}
}

$total_results = count($matched_services) + count($matched_portfolio) + count($matched_articles);
?>

<!-- BREADCRUMB -->
<section class="breadcrumb-section">
    <div class="container" style="position:relative;z-index:1;">
        <div class="row align-items-end">
            <div class="col-lg-8">
                <p style="color:var(--gold-light);font-size:.75rem;letter-spacing:3px;text-transform:uppercase;font-weight:700;margin-bottom:.75rem;">Pencarian Situs</p>
                <h1 style="font-family:var(--font-head);font-size:clamp(2rem,4vw,3.2rem);font-weight:800;color:#fff;line-height:1.15;margin-bottom:1rem;">
                    Hasil <em style="font-style:italic;background:var(--grad-gold);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">Pencarian</em>
                </h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-custom mb-0">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>">Home</a></li>
                        <li class="breadcrumb-item active">Pencarian</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Search Results Content -->
<section style="background:var(--white);padding:5rem 0;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10 reveal">
                <h2 style="font-family:var(--font-head);font-size:1.8rem;color:var(--navy);margin-bottom:.75rem;">
                    Ditemukan <?php echo $total_results; ?> hasil untuk: "<?php echo sanitize($keyword); ?>"
                </h2>
                <span class="section-divider mb-5"></span>

                <?php if ($total_results > 0): ?>
                    
                    <!-- 1. Matches in Services -->
                    <?php if (count($matched_services) > 0): ?>
                        <div style="background:var(--off-white);border:1px solid rgba(11,31,58,.06);border-radius:var(--radius);padding:2.5rem;box-shadow:var(--shadow-card);margin-bottom:3rem;">
                            <h3 style="font-family:var(--font-head);font-size:1.35rem;color:var(--navy);margin-bottom:1.5rem;padding-bottom:.75rem;border-bottom:1px solid rgba(11,31,58,.08);">
                                <i class="bi-gear-wide-connected text-gold me-2"></i> Layanan Perusahaan
                            </h3>
                            <div style="display:flex;flex-direction:column;gap:1.5rem;">
                                <?php foreach ($matched_services as $s): ?>
                                    <div style="border-bottom:1px solid rgba(11,31,58,.05);padding-bottom:1rem;">
                                        <h4 style="font-weight:700;font-size:1.05rem;margin-bottom:.35rem;">
                                            <a href="<?php echo base_url('layanan/' . $s['slug']); ?>" style="text-decoration:none;color:var(--navy);" onmouseover="this.style.color='var(--gold)'" onmouseout="this.style.color='var(--navy)'">
                                                <?php echo sanitize($s['title']); ?>
                                            </a>
                                        </h4>
                                        <p style="font-size:.88rem;color:var(--gray-text);margin:0;"><?php echo sanitize($s['short_description']); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- 2. Matches in Portfolios -->
                    <?php if (count($matched_portfolio) > 0): ?>
                        <div style="background:var(--off-white);border:1px solid rgba(11,31,58,.06);border-radius:var(--radius);padding:2.5rem;box-shadow:var(--shadow-card);margin-bottom:3rem;">
                            <h3 style="font-family:var(--font-head);font-size:1.35rem;color:var(--navy);margin-bottom:1.5rem;padding-bottom:.75rem;border-bottom:1px solid rgba(11,31,58,.08);">
                                <i class="bi-building-fill text-gold me-2"></i> Portofolio Proyek
                            </h3>
                            <div style="display:flex;flex-direction:column;gap:1.5rem;">
                                <?php foreach ($matched_portfolio as $p): ?>
                                    <div style="border-bottom:1px solid rgba(11,31,58,.05);padding-bottom:1rem;">
                                        <h4 style="font-weight:700;font-size:1.05rem;margin-bottom:.35rem;">
                                            <a href="<?php echo base_url('portofolio/' . $p['slug']); ?>" style="text-decoration:none;color:var(--navy);" onmouseover="this.style.color='var(--gold)'" onmouseout="this.style.color='var(--navy)'">
                                                <?php echo sanitize($p['title']); ?>
                                            </a>
                                        </h4>
                                        <p style="font-size:.82rem;color:var(--gray-text);margin:0;">
                                            Kategori: <strong style="color:var(--navy);"><?php echo sanitize($p['category']); ?></strong> &middot; Lokasi: <strong style="color:var(--navy);"><?php echo sanitize($p['location']); ?></strong> &middot; Tahun: <strong style="color:var(--navy);"><?php echo sanitize($p['year']); ?></strong>
                                        </p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- 3. Matches in Articles -->
                    <?php if (count($matched_articles) > 0): ?>
                        <div style="background:var(--off-white);border:1px solid rgba(11,31,58,.06);border-radius:var(--radius);padding:2.5rem;box-shadow:var(--shadow-card);margin-bottom:3rem;">
                            <h3 style="font-family:var(--font-head);font-size:1.35rem;color:var(--navy);margin-bottom:1.5rem;padding-bottom:.75rem;border-bottom:1px solid rgba(11,31,58,.08);">
                                <i class="bi-newspaper text-gold me-2"></i> Berita &amp; Kegiatan
                            </h3>
                            <div style="display:flex;flex-direction:column;gap:1.5rem;">
                                <?php foreach ($matched_articles as $a): ?>
                                    <div style="border-bottom:1px solid rgba(11,31,58,.05);padding-bottom:1rem;">
                                        <h4 style="font-weight:700;font-size:1.05rem;margin-bottom:.35rem;">
                                            <a href="<?php echo base_url('berita/' . $a['slug']); ?>" style="text-decoration:none;color:var(--navy);" onmouseover="this.style.color='var(--gold)'" onmouseout="this.style.color='var(--navy)'">
                                                <?php echo sanitize($a['title']); ?>
                                            </a>
                                        </h4>
                                        <p style="font-size:.88rem;color:var(--gray-text);margin-bottom:.5rem;"><?php echo sanitize(substr(strip_tags($a['content']), 0, 160)) . '...'; ?></p>
                                        <span style="font-size: 0.72rem;color:var(--gray-text);"><i class="bi-calendar3 text-gold me-1"></i> <?php echo format_date_id($a['created_at']); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                <?php else: ?>
                    <div style="text-align:center;padding:5rem 0;">
                        <i class="bi-search text-muted" style="font-size:4rem;display:block;margin-bottom:1.5rem;"></i>
                        <p style="color:var(--gray-text);font-size:1.1rem;margin-bottom:2.5rem;">Pencarian tidak ditemukan. Coba dengan kata kunci lain.</p>
                        <div style="max-width: 450px; margin: 0 auto;">
                            <form action="<?php echo base_url('search.php'); ?>" method="GET" class="form-branded">
                                <div class="input-group">
                                    <input type="text" name="q" class="form-control" placeholder="Cari kata kunci lain..." value="<?php echo sanitize($keyword); ?>" required>
                                    <button class="btn-gold" type="submit" style="padding:0 2rem;border-radius:0 var(--radius-sm) var(--radius-sm) 0;box-shadow:none;">Cari</button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
