<?php
require_once __DIR__ . '/config/functions.php';

$page_title = "Company Profile Resmi — PT. Hastra Karya Persada";
$page_desc = "Unduh Company Profile resmi PT. Hastra Karya Persada. Informasi legalitas, visi misi, tata kelola, dan sertifikasi mutu konstruksi.";
include __DIR__ . '/includes/header.php';

// Fetch company profile data
$profile = [];
try {
    $stmt = $db->query("SELECT * FROM company_profile WHERE id = 1");
    $profile = $stmt->fetch();
} catch (Exception $e) {
    // Fallback
}
?>

<!-- BREADCRUMB -->
<section class="breadcrumb-section">
    <div class="container" style="position:relative;z-index:1;">
        <div class="row align-items-end">
            <div class="col-lg-7">
                <p style="color:var(--gold-light);font-size:.75rem;letter-spacing:3px;text-transform:uppercase;font-weight:700;margin-bottom:.75rem;">Publikasi Korporat</p>
                <h1 style="font-family:var(--font-head);font-size:clamp(2.2rem,4vw,3.5rem);font-weight:800;color:#fff;line-height:1.1;margin-bottom:1rem;">
                    Company <em style="font-style:italic;background:var(--grad-gold);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">Profile</em>
                </h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-custom mb-0">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>">Home</a></li>
                        <li class="breadcrumb-item active">Company Profile</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Main Profile Content -->
<section style="background:var(--white);padding:6rem 0;">
    <div class="container">
        <!-- 1. Sambutan Direksi & PDF Download -->
        <div class="row align-items-center g-5 mb-5">
            <div class="col-lg-8 reveal-left">
                <span class="section-tag">Sambutan Direktur Utama</span>
                <h2 class="section-title text-navy fw-bold">Menghadapi Tantangan Global dengan Optimisme</h2>
                <span class="section-divider mb-4"></span>
                <div style="padding:2rem;background:var(--off-white);border-radius:var(--radius);border-left:4px solid var(--gold);box-shadow:var(--shadow-card);margin-bottom:2rem;">
                    <p style="margin:0;font-size:1.02rem;line-height:1.85;color:var(--navy);font-style:italic;">
                        "<?php echo nl2br(sanitize($profile['director_speech'] ?? 'Selamat datang di website resmi PT. Hastra Karya Persada. Komitmen kami adalah memberikan yang terbaik bagi negeri melalui keahlian terbaik kami.')); ?>"
                    </p>
                </div>
                <div style="display:flex;align-items:center;gap:1rem;">
                    <div style="background:var(--grad-navy);border:1px solid rgba(201,162,39,.3);color:var(--gold);border-radius:50%;display:flex;align-items:center;justify-content:center;width:54px;height:54px;">
                        <i class="bi-person-badge fs-4"></i>
                    </div>
                    <div>
                        <h5 style="color:var(--navy);font-weight:800;margin-bottom:0;font-size:1rem;">Hendra Hastra Wijaya</h5>
                        <p class="small text-muted mb-0">Direktur Utama PT. Hastra Karya Persada</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 reveal-right">
                <div style="background:var(--grad-navy);border:1px solid rgba(201,162,39,.18);border-radius:var(--radius);padding:2.5rem;text-align:center;box-shadow:var(--shadow-hover);">
                    <i class="bi-file-earmark-pdf" style="font-size:4rem;background:var(--grad-gold);-webkit-background-clip:text;-webkit-text-fill-color:transparent;display:block;margin-bottom:1.25rem;"></i>
                    <h4 style="color:#fff;font-size:1.2rem;font-weight:700;margin-bottom:.5rem;">Company Profile PDF</h4>
                    <p style="color:rgba(255,255,255,.5);font-size:.85rem;margin-bottom:2rem;line-height:1.6;">Unduh dokumen resmi profil korporasi kami untuk keperluan evaluasi kemitraan bisnis Anda.</p>
                    <?php 
                    $pdf_url = !empty($profile['pdf_path']) ? base_url($profile['pdf_path']) : '#';
                    $onclick = empty($profile['pdf_path']) ? "onclick=\"window.print(); return false;\"" : "";
                    ?>
                    <a href="<?php echo $pdf_url; ?>" <?php echo $onclick; ?> class="btn-gold" style="display:inline-flex;width:100%;justify-content:center;">
                        <i class="bi-download"></i> <?php echo empty($profile['pdf_path']) ? 'Cetak Halaman Profile' : 'Unduh PDF Profile'; ?>
                    </a>
                    <?php if (empty($profile['pdf_path'])): ?>
                        <span style="display:block;font-size:.7rem;color:rgba(255,255,255,.35);margin-top:.75rem;">(Mencetak halaman profile saat diklik)</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <hr style="margin:5rem 0;border-top:1px solid rgba(11,31,58,.08);">

        <!-- 2. Profil Lengkap & Legalitas -->
        <div class="row g-5 mb-5">
            <!-- Profil Perusahaan -->
            <div class="col-lg-6 reveal-left">
                <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1.25rem;">
                    <i class="bi-info-circle text-gold fs-3"></i>
                    <h3 style="color:var(--navy);font-weight:800;font-size:1.5rem;margin:0;">Profil Singkat Korporasi</h3>
                </div>
                <p class="text-muted leading-relaxed mb-5" style="font-size:1.02rem;">
                    <?php echo nl2br(sanitize($profile['profile_text'] ?? 'PT. Hastra Karya Persada didirikan sebagai bentuk komitmen dalam memajukan infrastruktur nasional dengan kualitas unggul dan profesionalisme tinggi.')); ?>
                </p>
                
                <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1.5rem;">
                    <i class="bi-bookmark-check text-gold fs-3"></i>
                    <h3 style="color:var(--navy);font-weight:800;font-size:1.5rem;margin:0;">Sertifikasi Penjaminan Mutu</h3>
                </div>
                <div class="row g-3">
                    <?php 
                    $certs = explode("\n", $profile['certificates'] ?? "ISO 9001:2015\nISO 14001:2015\nISO 45001:2018\nSertifikasi SMK3");
                    foreach ($certs as $cert):
                        if (trim($cert) === '') continue;
                    ?>
                        <div class="col-md-6">
                            <div style="padding:1rem 1.25rem;background:var(--off-white);border-radius:var(--radius-sm);border:1px solid rgba(11,31,58,.06);display:flex;align-items:center;gap:.75rem;">
                                <i class="bi-patch-check-fill text-gold fs-4"></i>
                                <span style="font-weight:700;color:var(--navy);font-size:.85rem;"><?php echo sanitize($cert); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Legalitas Perusahaan -->
            <div class="col-lg-6 reveal-right">
                <div style="background:var(--off-white);border:1px solid rgba(11,31,58,.07);border-radius:var(--radius);padding:2.5rem;box-shadow:var(--shadow-card);height:100%;">
                    <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1.5rem;">
                        <i class="bi-file-earmark-text text-gold fs-3"></i>
                        <h3 style="color:var(--navy);font-weight:800;font-size:1.5rem;margin:0;">Legalitas Hukum Perusahaan</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-borderless small mb-0">
                            <tbody>
                                <?php 
                                $legals = explode("\n", $profile['legality'] ?? "Akte Pendirian : No. 45 Kemenkumham\nNPWP : 91.222.333.4\nNIB : 91200012345");
                                foreach ($legals as $legal):
                                    $parts = explode(":", $legal, 2);
                                    if (count($parts) < 2) continue;
                                ?>
                                    <tr style="border-bottom:1px solid rgba(11,31,58,.05);">
                                        <td style="font-weight:700;color:var(--navy);padding:1rem 0;font-size:.85rem;width:35%;"><?php echo sanitize(trim($parts[0])); ?></td>
                                        <td style="color:var(--gray-text);padding:1rem 0;font-size:.85rem;"><?php echo sanitize(trim($parts[1])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <hr style="margin:5rem 0;border-top:1px solid rgba(11,31,58,.08);">

        <!-- 3. Bidang Usaha Perusahaan -->
        <div class="mb-5 reveal">
            <div class="text-center mb-5">
                <span class="section-tag">Fokus Bisnis</span>
                <h2 class="section-title">Bidang Usaha Operasional</h2>
                <span class="section-divider center"></span>
            </div>
            
            <div class="row g-4">
                <?php 
                $sectors = explode("\n", $profile['business_sectors'] ?? "Konstruksi Sipil & Bangunan\nPengadaan Alat Berat & Rantai Pasok\nKonsultansi Manajemen Konstruksi");
                foreach ($sectors as $index => $sector):
                    if (trim($sector) === '') continue;
                ?>
                    <div class="col-md-4">
                        <div class="value-card" style="padding:2.5rem 2rem;">
                            <div class="value-icon">
                                <i class="bi-lightning-charge"></i>
                            </div>
                            <h4 style="font-size:1.15rem;font-weight:800;color:var(--navy);margin:0;line-height:1.4;"><?php echo sanitize($sector); ?></h4>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <hr style="margin:5rem 0;border-top:1px solid rgba(11,31,58,.08);">

        <!-- 4. Struktur Organisasi -->
        <div class="mb-4 reveal">
            <div class="text-center mb-5">
                <span class="section-tag">Tata Kelola</span>
                <h2 class="section-title">Struktur Organisasi Perusahaan</h2>
                <span class="section-divider center"></span>
            </div>

            <?php if (!empty($profile['structure_img']) && file_exists(__DIR__ . '/' . $profile['structure_img'])): ?>
                <div class="text-center">
                    <img src="<?php echo base_url($profile['structure_img']); ?>" class="img-fluid rounded shadow border border-light" alt="Struktur Organisasi" style="max-height: 500px; border-radius:var(--radius);">
                </div>
            <?php else: ?>
                <!-- CSS Organizational Tree Diagram -->
                <div class="row justify-content-center text-center">
                    <div class="col-12 mb-4">
                        <div style="background:var(--grad-navy);border:1px solid rgba(201,162,39,.35);border-radius:var(--radius-sm);padding:1.25rem 2rem;display:inline-block;min-width:220px;box-shadow:var(--shadow-card);">
                            <h5 style="color:var(--gold-light);font-weight:800;margin-bottom:.25rem;font-size:1.05rem;">Direktur Utama</h5>
                            <p style="color:rgba(255,255,255,.6);font-size:.8rem;margin:0;">Hendra Hastra Wijaya</p>
                        </div>
                    </div>
                    
                    <div class="col-12 d-flex justify-content-center gap-4 flex-wrap mt-3">
                        <div style="background:var(--off-white);border:1px solid rgba(11,31,58,.08);border-top:3px solid var(--gold);border-radius:var(--radius-sm);padding:1.25rem 1.75rem;min-width:190px;box-shadow:var(--shadow-card);">
                            <h6 style="color:var(--navy);font-weight:800;margin-bottom:.25rem;font-size:.9rem;">Manajer Konstruksi</h6>
                            <p style="color:var(--gray-text);font-size:.78rem;margin:0;">Ir. Budi Santoso</p>
                        </div>
                        <div style="background:var(--off-white);border:1px solid rgba(11,31,58,.08);border-top:3px solid var(--gold);border-radius:var(--radius-sm);padding:1.25rem 1.75rem;min-width:190px;box-shadow:var(--shadow-card);">
                            <h6 style="color:var(--navy);font-weight:800;margin-bottom:.25rem;font-size:.9rem;">Manajer Keuangan</h6>
                            <p style="color:var(--gray-text);font-size:.78rem;margin:0;">Rina Melati, SE</p>
                        </div>
                        <div style="background:var(--off-white);border:1px solid rgba(11,31,58,.08);border-top:3px solid var(--gold);border-radius:var(--radius-sm);padding:1.25rem 1.75rem;min-width:190px;box-shadow:var(--shadow-card);">
                            <h6 style="color:var(--navy);font-weight:800;margin-bottom:.25rem;font-size:.9rem;">Manajer Operasional</h6>
                            <p style="color:var(--gray-text);font-size:.78rem;margin:0;">Andi Wijaya, MT</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
