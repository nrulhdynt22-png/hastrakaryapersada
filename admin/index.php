<?php
include __DIR__ . '/includes/header.php';

// Fetch content counts
$count_services = $count_portfolio = $count_articles = $count_careers = $count_applications = 0;
try {
    $count_services     = $db->query("SELECT COUNT(*) FROM services")->fetchColumn();
    $count_portfolio    = $db->query("SELECT COUNT(*) FROM portfolio")->fetchColumn();
    $count_articles     = $db->query("SELECT COUNT(*) FROM articles")->fetchColumn();
    $count_careers      = $db->query("SELECT COUNT(*) FROM careers")->fetchColumn();
    $count_applications = $db->query("SELECT COUNT(*) FROM career_applications")->fetchColumn();
} catch (Exception $e) {}

// Fetch recent applications
$applications = [];
try {
    $stmt = $db->query("SELECT ca.*, c.title AS job_title FROM career_applications ca JOIN careers c ON ca.career_id = c.id ORDER BY ca.created_at DESC LIMIT 5");
    $applications = $stmt->fetchAll();
} catch (Exception $e) {}
?>

<!-- ─── STAT CARDS ─── -->
<div class="stat-grid">

    <div class="stat-card accent">
        <i class="bi-gear-wide-connected stat-icon"></i>
        <div class="stat-label">Layanan Aktif</div>
        <div class="stat-value"><?php echo $count_services; ?></div>
        <a href="services.php" class="stat-link">Kelola <i class="bi-arrow-right"></i></a>
    </div>

    <div class="stat-card">
        <i class="bi-building stat-icon"></i>
        <div class="stat-label">Portofolio Proyek</div>
        <div class="stat-value"><?php echo $count_portfolio; ?></div>
        <a href="portfolio.php" class="stat-link">Kelola <i class="bi-arrow-right"></i></a>
    </div>

    <div class="stat-card accent">
        <i class="bi-newspaper stat-icon"></i>
        <div class="stat-label">Artikel Berita</div>
        <div class="stat-value"><?php echo $count_articles; ?></div>
        <a href="articles.php" class="stat-link">Kelola <i class="bi-arrow-right"></i></a>
    </div>

    <div class="stat-card">
        <i class="bi-briefcase stat-icon"></i>
        <div class="stat-label">Lowongan Aktif</div>
        <div class="stat-value"><?php echo $count_careers; ?></div>
        <a href="careers.php" class="stat-link">Kelola <i class="bi-arrow-right"></i></a>
    </div>

    <div class="stat-card accent">
        <i class="bi-people stat-icon"></i>
        <div class="stat-label">Total Pelamar</div>
        <div class="stat-value"><?php echo $count_applications; ?></div>
        <a href="careers.php" class="stat-link">Lihat <i class="bi-arrow-right"></i></a>
    </div>

</div>

<!-- ─── RECENT APPLICATIONS ─── -->
<div class="admin-card">
    <div class="admin-card-header">
        <h6 class="admin-card-title">
            <i class="bi-person-lines-fill"></i>
            Pelamar Kerja Terbaru
        </h6>
        <a href="careers.php" class="btn-admin-outline btn-admin-sm">Lihat Semua</a>
    </div>
    <div style="overflow-x:auto;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Posisi</th>
                    <th>Nama Pelamar</th>
                    <th>Email</th>
                    <th>Telepon</th>
                    <th style="text-align:right;">Berkas</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($applications) > 0): ?>
                    <?php foreach ($applications as $app): ?>
                        <tr>
                            <td><span class="badge-admin badge-navy"><?php echo format_date_id($app['created_at']); ?></span></td>
                            <td style="font-weight:700;"><?php echo sanitize($app['job_title']); ?></td>
                            <td><?php echo sanitize($app['name']); ?></td>
                            <td style="color:var(--a-gray);"><?php echo sanitize($app['email']); ?></td>
                            <td style="color:var(--a-gray);"><?php echo sanitize($app['phone']); ?></td>
                            <td style="text-align:right;">
                                <a href="<?php echo base_url($app['cv_path']); ?>" target="_blank" class="btn-admin-danger btn-admin-sm">
                                    <i class="bi-file-earmark-pdf"></i> CV PDF
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align:center;padding:3rem;color:var(--a-gray);">
                            <i class="bi-inbox" style="font-size:2rem;display:block;margin-bottom:.75rem;"></i>
                            Belum ada pelamar kerja yang masuk.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ─── QUICK LINKS ─── -->
<div class="row g-3 mt-1">
    <?php
    $quick = [
        ['sliders.php',         'bi-images',                  'Slider Beranda',   'Kelola banner hero homepage'],
        ['services.php',        'bi-gear-wide-connected',     'Layanan',          'Tambah atau edit layanan'],
        ['portfolio.php',       'bi-building',                'Portofolio',       'Rekam jejak proyek unggulan'],
        ['gallery.php',         'bi-camera-fill',             'Galeri Foto',      'Unggah dokumentasi visual'],
        ['company_profile.php', 'bi-file-earmark-person-fill','Company Profile',  'Sambutan direksi & legalitas'],
        ['settings.php',        'bi-sliders',                 'Pengaturan Situs', 'SEO, kontak, statistik'],
    ];
    foreach ($quick as $q): ?>
    <div class="col-md-4 col-sm-6">
        <a href="<?php echo $q[0]; ?>" class="admin-card d-flex align-items-center gap-3 p-4 text-decoration-none" style="margin-bottom:0;transition:all .35s;">
            <div style="width:46px;height:46px;border-radius:12px;background:rgba(201,162,39,.1);border:1px solid rgba(201,162,39,.2);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="bi <?php echo $q[1]; ?>" style="font-size:1.3rem;color:var(--a-gold);"></i>
            </div>
            <div>
                <div style="font-weight:700;font-size:.9rem;color:var(--a-navy);"><?php echo $q[2]; ?></div>
                <div style="font-size:.78rem;color:var(--a-gray);"><?php echo $q[3]; ?></div>
            </div>
        </a>
    </div>
    <?php endforeach; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
