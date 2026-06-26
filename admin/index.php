<?php
include __DIR__ . '/includes/header.php';

// Fetch content counts
$count_services = $count_portfolio = 0;
try {
    $count_services  = $db->query("SELECT COUNT(*) FROM services")->fetchColumn();
    $count_portfolio = $db->query("SELECT COUNT(*) FROM portfolio")->fetchColumn();
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
        <div class="stat-label">Proyek Tercatat</div>
        <div class="stat-value"><?php echo $count_portfolio; ?></div>
        <a href="portfolio.php" class="stat-link">Kelola <i class="bi-arrow-right"></i></a>
    </div>

</div>

<!-- ─── QUICK LINKS ─── -->
<div class="row g-3 mt-1">
    <?php
    $quick = [
        ['sliders.php',         'bi-images',                  'Kelola Slider',      'Kelola banner hero homepage'],
        ['services.php',        'bi-gear-wide-connected',     'Kelola Layanan',     'Tambah atau edit layanan'],
        ['portfolio.php',       'bi-briefcase-fill',          'Kelola Portofolio',  'Rekam jejak proyek konstruksi'],
        ['company_profile.php', 'bi-building-fill',           'Profil Perusahaan',  'Visi, misi, nilai & profil korporat'],
        ['settings.php',        'bi-sliders',                 'Pengaturan Situs',   'SEO, kontak, maps, statistik'],
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
