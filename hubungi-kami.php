<?php
require_once __DIR__ . '/config/functions.php';

$page_title = "Hubungi Kami";
$page_desc = "Hubungi PT. Hastra Karya Persada untuk kolaborasi proyek, pengadaan, dan konsultansi manajemen infrastruktur skala nasional.";
include __DIR__ . '/includes/header.php';

// Keep non-JS post fallback just in case
$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        $error_msg = 'Token CSRF tidak valid. Silakan muat ulang halaman.';
    } else {
        $name = sanitize($_POST['name']);
        $email = sanitize($_POST['email']);
        $subject = sanitize($_POST['subject']);
        $message = sanitize($_POST['message']);
        $success_msg = "Terima kasih, <strong>$name</strong>! Pesan Anda mengenai <em>\"$subject\"</em> telah terkirim. Tim marketing/humas kami akan merespons melalui email (<strong>$email</strong>) secepatnya.";
    }
}
?>

<!-- BREADCRUMB -->
<section class="breadcrumb-section">
    <div class="container" style="position:relative;z-index:1;">
        <div class="row align-items-end">
            <div class="col-lg-7">
                <p style="color:var(--gold-light);font-size:.75rem;letter-spacing:3px;text-transform:uppercase;font-weight:700;margin-bottom:.75rem;">Hubungan Bisnis</p>
                <h1 style="font-family:var(--font-head);font-size:clamp(2.2rem,4vw,3.5rem);font-weight:800;color:#fff;line-height:1.1;margin-bottom:1rem;">
                    Hubungi <em style="font-style:italic;background:var(--grad-gold);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">Kami</em>
                </h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-custom mb-0">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>">Home</a></li>
                        <li class="breadcrumb-item active">Hubungi Kami</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- CONTACT SECTION -->
<section style="background:var(--white);padding:6rem 0;">
    <div class="container">
        
        <!-- Status Alerts for Non-JS fallback -->
        <?php if (!empty($success_msg)): ?>
            <div class="alert-branded-success mb-5" role="alert">
                <h5 class="fw-bold"><i class="bi-check-circle-fill me-2"></i>Pesan Terkirim!</h5>
                <p class="mb-0 small"><?php echo $success_msg; ?></p>
            </div>
        <?php endif; ?>
        <?php if (!empty($error_msg)): ?>
            <div class="alert-branded-danger mb-5" role="alert">
                <h5 class="fw-bold"><i class="bi-exclamation-triangle-fill me-2"></i>Terjadi Kesalahan</h5>
                <p class="mb-0 small"><?php echo $error_msg; ?></p>
            </div>
        <?php endif; ?>

        <div class="row g-5">
            <!-- Contact Info -->
            <div class="col-lg-5 reveal-left">
                <span class="section-tag">Kontak Resmi</span>
                <h2 class="section-title">Mari Terhubung Dengan Kami</h2>
                <span class="section-divider mb-4"></span>
                <p class="text-muted leading-relaxed mb-5">Kami siap melayani kebutuhan konsultasi teknis, pengadaan barang, atau pelaksanaan proyek konstruksi Anda. Gunakan saluran kontak di bawah ini.</p>
                
                <div style="display:flex;flex-direction:column;gap:1.5rem;">
                    <!-- Office -->
                    <div class="contact-info-card">
                        <div class="contact-icon-bg">
                            <i class="bi-geo-alt"></i>
                        </div>
                        <div>
                            <div class="contact-label">Kantor Pusat</div>
                            <p class="contact-value"><?php echo sanitize($settings['address'] ?? ''); ?></p>
                        </div>
                    </div>
                    
                    <!-- Email -->
                    <div class="contact-info-card">
                        <div class="contact-icon-bg">
                            <i class="bi-envelope"></i>
                        </div>
                        <div>
                            <div class="contact-label">E-mail Resmi</div>
                            <p class="contact-value"><a href="mailto:<?php echo sanitize($settings['email'] ?? ''); ?>"><?php echo sanitize($settings['email'] ?? ''); ?></a></p>
                        </div>
                    </div>
                    
                    <!-- Phone -->
                    <div class="contact-info-card">
                        <div class="contact-icon-bg">
                            <i class="bi-telephone"></i>
                        </div>
                        <div>
                            <div class="contact-label">Telepon Kantor</div>
                            <p class="contact-value"><?php echo sanitize($settings['phone'] ?? ''); ?></p>
                        </div>
                    </div>

                    <!-- WhatsApp Contact Call-out -->
                    <?php if (!empty($settings['whatsapp'])): ?>
                    <a href="https://wa.me/<?php echo preg_replace('/\D/', '', $settings['whatsapp']); ?>?text=Halo%20PT.%20Hastra%20Karya%20Persada%2C%20saya%20ingin%20berkonsultasi." target="_blank" class="whatsapp-card text-decoration-none">
                        <div class="wa-icon">
                            <i class="bi-whatsapp"></i>
                        </div>
                        <div>
                            <div class="wa-label">WhatsApp Fast Response</div>
                            <p class="wa-value">Chat Customer Service <i class="bi-box-arrow-up-right small ms-1"></i></p>
                        </div>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Contact Form -->
            <div class="col-lg-7 reveal-right">
                <div class="contact-form-card">
                    <h3 class="fw-bold text-navy mb-2 h4">Kirim Pesan Penawaran / Pertanyaan</h3>
                    <p class="small text-muted mb-4">Isi form di bawah ini dengan lengkap. Staf humas kami akan merespons dalam waktu 1x24 jam kerja.</p>
                    
                    <form action="" method="POST" id="contactForm" class="form-branded">
                        <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="name" name="name" required placeholder="Contoh: Andi Wijaya">
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Alamat Email</label>
                                <input type="email" class="form-control" id="email" name="email" required placeholder="Contoh: andi@gmail.com">
                            </div>
                            <div class="col-12">
                                <label for="subject" class="form-label">Subjek Pesan</label>
                                <input type="text" class="form-control" id="subject" name="subject" required placeholder="Contoh: Penawaran Kerja Sama Konstruksi Gedung">
                            </div>
                            <div class="col-12">
                                <label for="message" class="form-label">Isi Pesan</label>
                                <textarea class="form-control" id="message" name="message" rows="5" required placeholder="Tuliskan secara detail mengenai penawaran atau pertanyaan yang ingin diajukan..."></textarea>
                            </div>
                            <div class="col-12 mt-4">
                                <button type="submit" name="send_message" class="btn-gold w-100 justify-content-center text-center">
                                    Kirim Pesan Sekarang <i class="bi-send"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <hr class="my-5 border-light-subtle">

        <!-- Google Maps Embed -->
        <?php if (!empty($settings['google_maps'])): ?>
            <div class="mb-4 reveal">
                <div class="text-center mb-5">
                    <span class="section-tag">Lokasi</span>
                    <h3 class="fw-bold text-navy mb-2">Lokasi Kantor Pusat</h3>
                    <div class="mx-auto bg-gold" style="width: 50px; height: 3px;"></div>
                </div>
                <div class="map-container">
                    <?php echo $settings['google_maps']; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
