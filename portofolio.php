<?php
require_once __DIR__ . '/config/functions.php';

$slug    = isset($_GET['slug']) ? sanitize($_GET['slug']) : null;
$project = null;

if ($slug) {
    try {
        $stmt = $db->prepare("SELECT * FROM portfolio WHERE slug = ?");
        $stmt->execute([$slug]);
        $project = $stmt->fetch();
    } catch (Exception $e) {}
    if (!$project) { header("Location: " . base_url('portofolio.php')); exit(); }
    $page_title = $project['title'] . " — Portofolio PT. Hastra Karya Persada";
    $page_desc  = $project['description'];
} else {
    $page_title = "Portofolio Proyek — PT. Hastra Karya Persada";
    $page_desc  = "Rekam jejak proyek konstruksi, pengadaan, dan konsultansi PT. Hastra Karya Persada.";
}

include __DIR__ . '/includes/header.php';

// Fallback images
$fallback = [
    'pembangunan-gedung-perkantoran-menara-hastra'         => "https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=1200&q=80",
    'pengadaan-alat-berat-proyek-jalan-tol-sumatera'       => "https://images.unsplash.com/photo-1578328819058-b69f3a3b0f6b?auto=format&fit=crop&w=1200&q=80",
    'manajemen-pengawasan-renovasi-bandara-international'  => "https://images.unsplash.com/photo-1436491865332-7a61a109cc05?auto=format&fit=crop&w=1200&q=80",
    '_default'                                             => "https://images.unsplash.com/photo-1504307651254-35680f356dfd?auto=format&fit=crop&w=1200&q=80",
];

function p_img($p, $fb, $dir) {
    if (!empty($p['image']) && file_exists($dir.'/assets/uploads/portfolio/'.$p['image']))
        return base_url('assets/uploads/portfolio/'.$p['image']);
    return $fb[$p['slug']] ?? $fb['_default'];
}
?>

<!-- BREADCRUMB -->
<section class="breadcrumb-section">
    <div class="container" style="position:relative;z-index:1;">
        <div class="row">
            <div class="col-lg-8">
                <p style="color:var(--gold-light);font-size:.75rem;letter-spacing:3px;text-transform:uppercase;font-weight:700;margin-bottom:.75rem;">Rekam Jejak</p>
                <h1 style="font-family:var(--font-head);font-size:clamp(2rem,4vw,3.2rem);font-weight:800;color:#fff;line-height:1.15;margin-bottom:1rem;">
                    <?php echo $project
                        ? sanitize($project['title'])
                        : 'Portofolio <em style="font-style:italic;background:var(--grad-gold);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">Proyek</em>'; ?>
                </h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-custom mb-0">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>">Home</a></li>
                        <?php if ($project): ?>
                        <li class="breadcrumb-item"><a href="<?php echo base_url('portofolio.php'); ?>">Portofolio</a></li>
                        <li class="breadcrumb-item active"><?php echo sanitize($project['title']); ?></li>
                        <?php else: ?>
                        <li class="breadcrumb-item active">Portofolio</li>
                        <?php endif; ?>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>


<?php if ($project): ?>
<!-- ==================== DETAIL VIEW ==================== -->
<section style="background:var(--white);padding:5rem 0;">
    <div class="container">
        <div class="row g-5">
            <!-- Main -->
            <div class="col-lg-8">
                <div style="border-radius:var(--radius);overflow:hidden;margin-bottom:2.5rem;box-shadow:var(--shadow-hover);">
                    <img src="<?php echo p_img($project,$fallback,__DIR__); ?>"
                         class="w-100" alt="<?php echo sanitize($project['title']); ?>"
                         style="height:460px;object-fit:cover;display:block;">
                </div>

                <p class="section-tag">Detail Proyek</p>
                <h2 style="font-family:var(--font-head);font-size:2rem;color:var(--navy);margin-bottom:1.25rem;"><?php echo sanitize($project['title']); ?></h2>
                <div style="font-size:1.05rem;line-height:1.85;color:var(--gray-text);">
                    <?php echo nl2br(sanitize($project['description'])); ?>
                </div>

                <div class="mt-5">
                    <a href="<?php echo base_url('portofolio.php'); ?>" style="display:inline-flex;align-items:center;gap:.5rem;color:var(--navy);font-weight:600;text-decoration:none;font-size:.9rem;transition:color .3s;" onmouseover="this.style.color='var(--gold)'" onmouseout="this.style.color='var(--navy)'">
                        <i class="bi-arrow-left"></i> Kembali ke Portofolio
                    </a>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="position-sticky" style="top:100px;display:flex;flex-direction:column;gap:1.5rem;">
                    <!-- Project Info -->
                    <div style="background:var(--off-white);border-radius:var(--radius);padding:2rem;border:1px solid rgba(11,31,58,.07);">
                        <h4 style="font-size:.75rem;letter-spacing:3px;text-transform:uppercase;font-weight:800;background:var(--grad-gold);-webkit-background-clip:text;-webkit-text-fill-color:transparent;margin-bottom:1.5rem;">Informasi Proyek</h4>
                        <?php
                        $meta = [
                            ['bi-tag','Kategori',$project['category']],
                            ['bi-person','Klien / Mitra',$project['client']],
                            ['bi-geo-alt','Lokasi',$project['location']],
                            ['bi-calendar-check','Tahun Selesai',$project['year']],
                        ];
                        foreach ($meta as $m): if (empty($m[2])) continue; ?>
                        <div style="display:flex;align-items:flex-start;gap:.75rem;padding:.75rem 0;border-bottom:1px solid rgba(11,31,58,.06);">
                            <i class="bi <?php echo $m[0]; ?>" style="color:var(--gold);flex-shrink:0;font-size:.95rem;margin-top:.1rem;"></i>
                            <div>
                                <div style="font-size:.72rem;text-transform:uppercase;letter-spacing:1.5px;color:var(--gray-text);margin-bottom:.15rem;"><?php echo $m[1]; ?></div>
                                <div style="font-weight:700;font-size:.9rem;color:var(--navy);"><?php echo sanitize($m[2]); ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <!-- CTA -->
                    <div style="background:var(--grad-navy);border-radius:var(--radius);padding:2rem;text-align:center;border:1px solid rgba(201,162,39,.15);">
                        <i class="bi-envelope-check-fill" style="font-size:2rem;background:var(--grad-gold);-webkit-background-clip:text;-webkit-text-fill-color:transparent;display:block;margin-bottom:1rem;"></i>
                        <h4 style="color:#fff;font-size:1.1rem;margin-bottom:.5rem;">Ingin Kerja Sama?</h4>
                        <p style="color:rgba(255,255,255,.5);font-size:.85rem;margin-bottom:1.5rem;">Diskusikan rencana pembangunan atau pengadaan proyek Anda bersama kami.</p>
                        <a href="<?php echo base_url('hubungi-kami.php'); ?>" class="btn-gold" style="display:inline-flex;width:100%;justify-content:center;">
                            Mulai Kerja Sama <i class="bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php else: ?>
<!-- ==================== LIST VIEW ==================== -->
<section style="background:var(--white);padding:5rem 0;">
    <div class="container">
        <?php
        // Get categories
        $categories = [];
        try {
            $categories = $db->query("SELECT DISTINCT category FROM portfolio")->fetchAll(PDO::FETCH_COLUMN);
        } catch (Exception $e) {}
        ?>
        <div class="row justify-content-between align-items-end mb-5">
            <div class="col-lg-6 reveal-left">
                <p class="section-tag">Katalog Kerja</p>
                <h2 class="section-title">Rekam Jejak <em style="font-style:italic;background:var(--grad-gold);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">Karya Terbaik</em></h2>
                <span class="section-divider"></span>
            </div>
            <div class="col-lg-6 text-lg-end reveal-right">
                <div style="display:flex;flex-wrap:wrap;gap:.5rem;justify-content:flex-end;">
                    <button class="filter-btn active" data-filter="all">Semua</button>
                    <?php foreach ($categories as $cat): ?>
                    <button class="filter-btn" data-filter="<?php echo sanitize($cat); ?>"><?php echo sanitize($cat); ?></button>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="row g-4" id="portfolio-grid">
            <?php
            $projects = [];
            try { $projects = $db->query("SELECT * FROM portfolio ORDER BY id DESC")->fetchAll(); } catch (Exception $e) {}
            foreach ($projects as $i => $p):
                $img = p_img($p, $fallback, __DIR__);
            ?>
            <div class="col-lg-4 col-md-6 reveal" data-category="<?php echo sanitize($p['category']); ?>" style="transition-delay:<?php echo ($i % 6) * .08; ?>s">
                <a href="<?php echo base_url('portofolio/'.$p['slug']); ?>" class="text-decoration-none d-block">
                    <div class="portfolio-item">
                        <img src="<?php echo $img; ?>" class="portfolio-img" alt="<?php echo sanitize($p['title']); ?>">
                        <div class="portfolio-overlay">
                            <span class="portfolio-category"><?php echo sanitize($p['category']); ?></span>
                            <h4 class="portfolio-title"><?php echo sanitize($p['title']); ?></h4>
                            <p style="color:rgba(255,255,255,.55);font-size:.82rem;margin:.4rem 0 0;">
                                <i class="bi-geo-alt me-1"></i><?php echo sanitize($p['location']); ?> &middot; <?php echo sanitize($p['year']); ?>
                            </p>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach;
            if (empty($projects)): ?>
            <div class="col-12 text-center py-5">
                <i class="bi-folder-x" style="font-size:3rem;color:var(--gray-text);display:block;margin-bottom:1rem;"></i>
                <p style="color:var(--gray-text);">Belum ada portofolio yang tersedia.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
