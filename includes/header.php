<?php
require_once __DIR__ . '/../config/functions.php';

// Fetch services dynamically for the Layanan Mega Menu
$nav_services = [];
try {
    $stmt = $db->query("SELECT title, slug FROM services ORDER BY title ASC LIMIT 5");
    $nav_services = $stmt->fetchAll();
} catch (Exception $e) {
    // Fail silently
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?php echo base_url('assets/css/style.css'); ?>" rel="stylesheet">
    <?php include __DIR__ . '/seometa.php'; ?>
</head>
<body>

    <!-- PAGE LOADER -->
    <div id="loader">
        <div class="loader-logo">PT. HASTRA KARYA PERSADA</div>
        <div class="loader-line"></div>
    </div>

    <!-- TOP BAR -->
    <div class="top-bar py-2 d-none d-lg-block">
        <div class="container d-flex justify-content-between align-items-center">
            <div>
                <span class="me-4"><i class="bi-telephone-fill me-1" style="color:var(--gold)"></i><?php echo $settings['phone'] ?? ''; ?></span>
                <span><i class="bi-envelope-fill me-1" style="color:var(--gold)"></i><?php echo $settings['email'] ?? ''; ?></span>
            </div>
            <div style="display:flex;gap:1rem;align-items:center;">
                <a href="<?php echo $settings['social_facebook'] ?? '#'; ?>" target="_blank"><i class="bi-facebook"></i></a>
                <a href="<?php echo $settings['social_instagram'] ?? '#'; ?>" target="_blank"><i class="bi-instagram"></i></a>
                <a href="<?php echo $settings['social_linkedin'] ?? '#'; ?>" target="_blank"><i class="bi-linkedin"></i></a>
                <a href="https://wa.me/<?php echo preg_replace('/\D/','',$settings['whatsapp'] ?? ''); ?>" target="_blank"><i class="bi-whatsapp"></i></a>
            </div>
        </div>
    </div>

    <!-- NAVBAR WRAP (for sticky floating effect) -->
    <div class="navbar-wrap" id="navbarWrap">
        <nav class="navbar navbar-expand-lg navbar-custom">
            <div class="container-fluid px-0">
                <a class="navbar-brand" href="<?php echo base_url(); ?>" style="display:flex;align-items:center;gap:.65rem;">
                    <img src="<?php echo base_url('assets/img/logo.png'); ?>" alt="Logo PT. Hastra Karya Persada" style="height:38px;width:38px;border-radius:50%;object-fit:cover;background:#fff;padding:2px;">
                    <span style="line-height:1.15;">PT. HASTRA KARYA <span>PERSADA</span></span>
                </a>

                <button class="navbar-toggler border-0 p-0 ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-label="Toggle navigation"
                        style="background:none;color:rgba(255,255,255,.7);font-size:1.3rem;">
                    <i class="bi-list"></i>
                </button>

                <div class="collapse navbar-collapse" id="mainNavbar">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center gap-lg-1">
                        <li class="nav-item">
                            <a class="nav-link <?php echo is_active('index.php'); ?>" href="<?php echo base_url(); ?>">HOME</a>
                        </li>
                        <li class="nav-item d-none d-lg-block text-white-50 px-1" style="font-size: 0.8rem; opacity: 0.3;">|</li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo is_active('tentang.php'); ?>" href="<?php echo base_url('tentang.php'); ?>">TENTANG</a>
                        </li>
                        <li class="nav-item d-none d-lg-block text-white-50 px-1" style="font-size: 0.8rem; opacity: 0.3;">|</li>
                        <!-- Mega Dropdown -->
                        <li class="nav-item dropdown mega-dropdown">
                            <a class="nav-link dropdown-toggle <?php echo is_active('layanan.php'); ?>" href="<?php echo base_url('layanan.php'); ?>" role="button" id="layananDrop">
                                LAYANAN
                            </a>
                            <div class="dropdown-menu mega-menu">
                                <div class="row g-4">
                                    <div class="col-lg-4">
                                        <h6>Bidang Usaha Kami</h6>
                                        <p style="color:rgba(255,255,255,.45);font-size:.88rem;line-height:1.7;">Solusi konstruksi, pengadaan, dan konsultansi terintegrasi untuk proyek skala nasional.</p>
                                        <a href="<?php echo base_url('layanan.php'); ?>" class="btn-gold" style="display:inline-flex;font-size:.82rem;padding:.6rem 1.25rem;margin-top:.5rem;">Semua Layanan <i class="bi-arrow-right"></i></a>
                                    </div>
                                    <div class="col-lg-4">
                                        <h6>Layanan</h6>
                                        <?php foreach ($nav_services as $s): ?>
                                            <a href="<?php echo base_url('layanan/'.$s['slug']); ?>"><i class="bi-chevron-right me-2" style="color:var(--gold);font-size:.75rem;"></i><?php echo $s['title']; ?></a>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="col-lg-4">
                                        <h6>Keunggulan</h6>
                                        <a href="#"><i class="bi-check-circle me-2" style="color:var(--gold);font-size:.75rem;"></i>Standar ISO Internasional</a>
                                        <a href="#"><i class="bi-check-circle me-2" style="color:var(--gold);font-size:.75rem;"></i>Tenaga Ahli Bersertifikat</a>
                                        <a href="#"><i class="bi-check-circle me-2" style="color:var(--gold);font-size:.75rem;"></i>On-Time Delivery</a>
                                        <a href="#"><i class="bi-check-circle me-2" style="color:var(--gold);font-size:.75rem;"></i>Pengawasan Kualitas 24/7</a>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="nav-item d-none d-lg-block text-white-50 px-1" style="font-size: 0.8rem; opacity: 0.3;">|</li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo is_active('portofolio.php'); ?>" href="<?php echo base_url('portofolio.php'); ?>">PROYEK</a>
                        </li>
                        <li class="nav-item d-none d-lg-block text-white-50 px-1" style="font-size: 0.8rem; opacity: 0.3;">|</li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo is_active('hubungi-kami.php'); ?>" href="<?php echo base_url('hubungi-kami.php'); ?>">KONTAK</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </div><!-- /navbar-wrap -->
