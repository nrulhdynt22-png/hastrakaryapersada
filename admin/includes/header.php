<?php
require_once __DIR__ . '/../../config/functions.php';
require_once __DIR__ . '/auth.php';

$admin_current_page = basename($_SERVER['PHP_SELF']);

$page_titles = [
    'index.php'           => ['Dashboard',             'Ringkasan aktivitas dan statistik konten website.'],
    'sliders.php'         => ['Home (Slider)',         'Kelola banner slide hero pada halaman utama.'],
    'services.php'        => ['Layanan',               'Tambah, ubah, dan hapus layanan perusahaan.'],
    'portfolio.php'       => ['Proyek',                'Manajemen data rekam jejak proyek konstruksi.'],
    'company_profile.php' => ['Tentang',               'Perbarui visi, misi, nilai, dan profil korporat.'],
    'org_structure.php'   => ['Struktur Organisasi',    'Kelola hierarki jabatan dan foto anggota perusahaan.'],
    'advantages.php'      => ['Home (Keunggulan)',      'Tambah, ubah, dan hapus keunggulan yang tampil di homepage.'],
    'partners.php'        => ['Home (Mitra)',           'Kelola nama mitra atau klien yang ditampilkan di homepage.'],
    'settings.php'        => ['Kontak & Pengaturan',    'Konfigurasi kontak, SEO, media sosial, dan statistik.'],
];
$pt = $page_titles[$admin_current_page] ?? ['Admin Panel', 'PT. Hastra Karya Persada'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pt[0]; ?> | Admin PT. Hastra Karya Persada</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?php echo base_url('assets/css/admin.css'); ?>" rel="stylesheet">
</head>
<body>

<div class="admin-shell">
    <!-- ====================== SIDEBAR ====================== -->
    <aside class="admin-sidebar">

        <!-- Brand -->
        <div class="sidebar-brand">
            <div style="display:flex;align-items:center;gap:.75rem;">
                <img src="<?php echo base_url('assets/img/logo.png'); ?>" alt="Logo" style="height:36px;width:auto;object-fit:contain;flex-shrink:0;">
                <div>
                    <h5 class="sidebar-brand-title">PT. HASTRA KARYA <span>PERSADA</span></h5>
                    <div class="sidebar-brand-sub">CMS Admin Panel</div>
                </div>
            </div>
        </div>

        <!-- User -->
        <div class="sidebar-user">
            <div class="sidebar-avatar"><i class="bi-person-fill"></i></div>
            <div>
                <div class="sidebar-username"><?php echo sanitize($_SESSION['admin_username']); ?></div>
                <div class="sidebar-role">Administrator</div>
            </div>
        </div>

        <!-- Nav -->
        <nav class="sidebar-nav">
            <div class="sidebar-section-label">Menu Utama</div>

            <a class="sidebar-link <?php echo ($admin_current_page === 'index.php') ? 'active' : ''; ?>" href="index.php">
                <i class="bi-speedometer2"></i><span>Dashboard</span>
            </a>
            <a class="sidebar-link <?php echo ($admin_current_page === 'sliders.php') ? 'active' : ''; ?>" href="sliders.php">
                <i class="bi-images"></i><span>Home (Slider)</span>
            </a>

            <div class="sidebar-section-label">Konten</div>

            <a class="sidebar-link <?php echo ($admin_current_page === 'company_profile.php') ? 'active' : ''; ?>" href="company_profile.php">
                <i class="bi-building-fill"></i><span>Tentang</span>
            </a>
            <a class="sidebar-link <?php echo ($admin_current_page === 'org_structure.php') ? 'active' : ''; ?>" href="org_structure.php">
                <i class="bi-diagram-3-fill"></i><span>Struktur Organisasi</span>
            </a>
            <a class="sidebar-link <?php echo ($admin_current_page === 'services.php') ? 'active' : ''; ?>" href="services.php">
                <i class="bi-gear-wide-connected"></i><span>Layanan</span>
            </a>
            <a class="sidebar-link <?php echo ($admin_current_page === 'portfolio.php') ? 'active' : ''; ?>" href="portfolio.php">
                <i class="bi-briefcase-fill"></i><span>Proyek</span>
            </a>
            <a class="sidebar-link <?php echo ($admin_current_page === 'advantages.php') ? 'active' : ''; ?>" href="advantages.php">
                <i class="bi-stars"></i><span>Home (Keunggulan)</span>
            </a>
            <a class="sidebar-link <?php echo ($admin_current_page === 'partners.php') ? 'active' : ''; ?>" href="partners.php">
                <i class="bi-people-fill"></i><span>Home (Mitra)</span>
            </a>

            <div class="sidebar-section-label">Sistem</div>

            <a class="sidebar-link <?php echo ($admin_current_page === 'settings.php') ? 'active' : ''; ?>" href="settings.php">
                <i class="bi-sliders"></i><span>Kontak & Pengaturan</span>
            </a>

            <hr class="sidebar-divider">

            <a class="sidebar-link logout-link" href="logout.php" onclick="return confirm('Keluar dari Dashboard Admin?')">
                <i class="bi-box-arrow-right"></i><span>Keluar (Logout)</span>
            </a>
        </nav>
    </aside>

    <!-- ====================== MAIN ====================== -->
    <main class="admin-main">

        <!-- Top Bar -->
        <div class="admin-topbar">
            <div>
                <div class="topbar-title"><?php echo $pt[0]; ?></div>
                <div class="topbar-sub"><?php echo $pt[1]; ?></div>
            </div>
            <div class="topbar-actions">
                <a href="<?php echo base_url(); ?>" target="_blank" class="topbar-btn topbar-btn-outline">
                    <i class="bi-box-arrow-up-right"></i> Lihat Website
                </a>
                <a href="logout.php" onclick="return confirm('Keluar?')" class="topbar-btn" style="background:rgba(239,68,68,.08);color:#dc2626!important;border:1.5px solid rgba(239,68,68,.2);">
                    <i class="bi-power"></i> Logout
                </a>
            </div>
        </div>

        <!-- Content -->
        <div class="admin-content">
