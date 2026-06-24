<?php
$page_title = "Solusi Konstruksi & Infrastruktur Kelas Enterprise";
include __DIR__ . '/includes/header.php';

// Fetch sliders with fallback
$sliders = [];
try {
    $stmt = $db->query("SELECT * FROM sliders WHERE status = 1 ORDER BY id ASC");
    $sliders = $stmt->fetchAll();
} catch (Exception $e) {}

// If no sliders in DB, use default fallback
if (empty($sliders)) {
    $sliders = [
        [
            'image'       => '',
            'title'       => 'Membangun Infrastruktur Berkelas Dunia',
            'subheadline' => 'Solusi konstruksi, pengadaan, dan konsultansi terpercaya untuk proyek skala nasional bersama tenaga ahli bersertifikat.',
            'link_url'    => 'tentang.php',
            'link_text'   => 'Tentang Kami',
        ],
        [
            'image'       => '',
            'title'       => 'Mitra Strategis Pembangunan Nasional',
            'subheadline' => 'Lebih dari 150 proyek sukses, 80+ klien pemerintah dan swasta — kami hadir menjawab tantangan terbesar Anda.',
            'link_url'    => 'portofolio.php',
            'link_text'   => 'Lihat Portofolio',
        ],
    ];
}

// Fetch active services
$services = [];
try {
    $stmt = $db->query("SELECT * FROM services ORDER BY id ASC LIMIT 3");
    $services = $stmt->fetchAll();
} catch (Exception $e) {}

// Fetch advantages
$advantages = [];
try {
    $db->exec("CREATE TABLE IF NOT EXISTS `advantages` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `icon` VARCHAR(50) NOT NULL DEFAULT 'bi-star',
        `title` VARCHAR(150) NOT NULL,
        `description` TEXT NOT NULL,
        `sort_order` INT NOT NULL DEFAULT 0
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $advantages = $db->query("SELECT * FROM advantages ORDER BY sort_order ASC, id ASC LIMIT 4")->fetchAll();
} catch (Exception $e) {}
if (empty($advantages)) {
    $advantages = [
        ['icon'=>'bi-shield-check','title'=>'Profesional Tersertifikasi','description'=>'Seluruh tim kami memegang sertifikasi resmi — dari SKA, SMK3, hingga ISO internasional.'],
        ['icon'=>'bi-graph-up-arrow','title'=>'Rekam Jejak Terbukti','description'=>'Lebih dari 150 proyek sukses diselesaikan tepat waktu dengan tingkat kepuasan 99%.'],
        ['icon'=>'bi-gem','title'=>'Standar Mutu Global','description'=>'Material SNI, prosedur kerja ISO, dan pengawasan kualitas berlapis di setiap tahap.'],
        ['icon'=>'bi-clock-history','title'=>'Zero Delay Delivery','description'=>'Manajemen proyek berbasis teknologi untuk memastikan tidak ada keterlambatan jadwal.'],
    ];
}

// Fallback images
$hero_imgs = [
    "https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=1920&q=80",
    "https://images.unsplash.com/photo-1541888946425-d81bb19240f5?auto=format&fit=crop&w=1920&q=80"
];
$svc_imgs = [
    'konstruksi-dan-infrastruktur' => "https://images.unsplash.com/photo-1504307651254-35680f356dfd?auto=format&fit=crop&w=800&q=80",
    'pengadaan-barang-dan-jasa'    => "https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=800&q=80",
    'konsultansi-manajemen-proyek' => "https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&w=800&q=80",
];
?>

<!-- ============================================================
     1. HERO SLIDER — Full immersive
============================================================ -->
<section class="hero-slider-container">

    <!-- Decorative background circles -->
    <div class="hero-deco hero-deco-1"></div>
    <div class="hero-deco hero-deco-2"></div>
    <div class="hero-deco hero-deco-3"></div>

    <?php $idx = 0; foreach ($sliders as $sl):
        $bg = !empty($sl['image']) && file_exists(__DIR__.'/assets/uploads/sliders/'.$sl['image'])
            ? base_url('assets/uploads/sliders/'.$sl['image'])
            : ($hero_imgs[$idx] ?? $hero_imgs[0]);
    ?>
    <div class="hero-slide <?php echo $idx === 0 ? 'active' : ''; ?>" style="background-image:url('<?php echo $bg; ?>')">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="hero-label">
                            <i class="bi-building"></i>
                            PT. Hastra Karya Persada
                        </div>
                        <h1 class="hero-title">
                            <?php
                            // Add italic emphasis on last word for visual punch
                            $words = explode(' ', sanitize($sl['title']));
                            $last  = array_pop($words);
                            echo implode(' ', $words) . ' <em>' . $last . '</em>';
                            ?>
                        </h1>
                        <p class="hero-subheadline"><?php echo sanitize($sl['subheadline']); ?></p>
                        <div class="hero-buttons">
                            <a href="<?php echo base_url(sanitize($sl['link_url'])); ?>" class="btn-gold">
                                <?php echo sanitize($sl['link_text']); ?> <i class="bi-arrow-right"></i>
                            </a>
                            <a href="<?php echo base_url('hubungi-kami.php'); ?>" class="btn-outline-white">
                                Hubungi Kami
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php $idx++; endforeach; ?>

    <!-- Prev/Next arrows -->
    <?php if (count($sliders) > 1): ?>
    <button onclick="changeSlide(-1)" style="position:absolute;left:2rem;top:50%;transform:translateY(-50%);z-index:3;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:50%;width:48px;height:48px;color:rgba(255,255,255,.7);font-size:1.2rem;cursor:pointer;transition:all .3s;display:flex;align-items:center;justify-content:center;" onmouseover="this.style.background='rgba(201,162,39,.2)';this.style.borderColor='rgba(201,162,39,.4)'" onmouseout="this.style.background='rgba(255,255,255,.06)';this.style.borderColor='rgba(255,255,255,.1)'">
        <i class="bi-chevron-left"></i>
    </button>
    <button onclick="changeSlide(1)" style="position:absolute;right:2rem;top:50%;transform:translateY(-50%);z-index:3;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:50%;width:48px;height:48px;color:rgba(255,255,255,.7);font-size:1.2rem;cursor:pointer;transition:all .3s;display:flex;align-items:center;justify-content:center;" onmouseover="this.style.background='rgba(201,162,39,.2)';this.style.borderColor='rgba(201,162,39,.4)'" onmouseout="this.style.background='rgba(255,255,255,.06)';this.style.borderColor='rgba(255,255,255,.1)'">
        <i class="bi-chevron-right"></i>
    </button>
    <?php endif; ?>

    <!-- Scroll Indicator -->
    <div class="hero-scroll-hint">
        <span></span>
        Scroll
    </div>

    <!-- Slide dots -->
    <?php if (count($sliders) > 1): ?>
    <div style="position:absolute;bottom:1.5rem;right:2rem;z-index:3;display:flex;gap:.5rem;" id="hero-dots">
        <?php for ($d = 0; $d < count($sliders); $d++): ?>
        <button onclick="goToSlide(<?php echo $d; ?>)" class="hero-dot <?php echo $d === 0 ? 'active' : ''; ?>" id="dot-<?php echo $d; ?>" style="width:<?php echo $d === 0 ? '28px' : '8px'; ?>;height:8px;border-radius:100px;border:none;background:<?php echo $d === 0 ? 'var(--gold)' : 'rgba(255,255,255,.3)'; ?>;cursor:pointer;transition:all .4s;padding:0;"></button>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</section>

<script>
let currentSlide = 0;
const slides = document.querySelectorAll('.hero-slide');
const dots   = document.querySelectorAll('.hero-dot');

function goToSlide(n) {
    slides[currentSlide].classList.remove('active');
    if (dots[currentSlide]) { dots[currentSlide].style.width = '8px'; dots[currentSlide].style.background = 'rgba(255,255,255,.3)'; }
    currentSlide = (n + slides.length) % slides.length;
    slides[currentSlide].classList.add('active');
    if (dots[currentSlide]) { dots[currentSlide].style.width = '28px'; dots[currentSlide].style.background = 'var(--gold)'; }
}
function changeSlide(dir) { goToSlide(currentSlide + dir); }
if (slides.length > 1) setInterval(() => changeSlide(1), 7000);
</script>


<!-- ============================================================
     2. ABOUT STRIP — Number + Image + Copy side by side
============================================================ -->
<section class="py-5 overflow-hidden" style="background:var(--white);">
    <div class="container py-5">
        <div class="row align-items-center g-5">

            <!-- Left: Large number + tagline  -->
            <div class="col-lg-5 reveal-left">
                <p class="section-tag">Tentang Perusahaan</p>
                <h2 class="section-title"><?php echo html_entity_decode($settings['home_about_title'] ?? 'Membangun Masa Depan dengan <em>Presisi</em>'); ?></h2>
                <span class="section-divider"></span>
                <p class="mt-4 mb-4" style="font-size:1.05rem;line-height:1.8;">
                    <?php echo sanitize($settings['home_about_text'] ?? 'Sejak 2020, PT. Hastra Karya Persada telah menjadi mitra pembangunan nasional yang dipercaya. Kami menghadirkan standar konstruksi, pengadaan, dan konsultansi kelas dunia dengan tim profesional bersertifikat internasional.'); ?>
                </p>
                <a href="<?php echo base_url('tentang.php'); ?>" class="btn-gold" style="display:inline-flex;">Profil Kami <i class="bi-arrow-right ms-1"></i></a>
            </div>

            <!-- Right: staggered stats + image -->
            <div class="col-lg-7 reveal-right">
                <div class="position-relative">
                    <img src="https://images.unsplash.com/photo-1504307651254-35680f356dfd?auto=format&fit=crop&w=900&q=85"
                         class="img-fluid rounded-4 w-100" alt="Konstruksi Hastra"
                         style="height:420px;object-fit:cover;box-shadow:0 40px 80px rgba(11,31,58,.15);">

                    <!-- Floating stats card -->
                    <div style="position:absolute;bottom:-30px;left:-30px;background:var(--navy);border-radius:var(--radius);padding:1.75rem 2rem;box-shadow:0 20px 50px rgba(0,0,0,.25);border:1px solid rgba(201,162,39,.2);">
                        <div class="d-flex gap-4">
                            <div class="text-center">
                                <div style="font-size:2.2rem;font-weight:900;background:var(--grad-gold);-webkit-background-clip:text;-webkit-text-fill-color:transparent;line-height:1;"><?php echo sanitize($settings['stat_proyek'] ?? '150'); ?>+</div>
                                <div style="color:rgba(255,255,255,.5);font-size:.75rem;letter-spacing:1px;margin-top:.25rem;">PROYEK</div>
                            </div>
                            <div style="width:1px;background:rgba(255,255,255,.08);"></div>
                            <div class="text-center">
                                <div style="font-size:2.2rem;font-weight:900;background:var(--grad-gold);-webkit-background-clip:text;-webkit-text-fill-color:transparent;line-height:1;"><?php echo sanitize($settings['stat_mitra'] ?? '80'); ?>+</div>
                                <div style="color:rgba(255,255,255,.5);font-size:.75rem;letter-spacing:1px;margin-top:.25rem;">KLIEN</div>
                            </div>
                            <div style="width:1px;background:rgba(255,255,255,.08);"></div>
                            <div class="text-center">
                                <div style="font-size:2.2rem;font-weight:900;background:var(--grad-gold);-webkit-background-clip:text;-webkit-text-fill-color:transparent;line-height:1;"><?php echo sanitize($settings['stat_kepuasan'] ?? '99'); ?>%</div>
                                <div style="color:rgba(255,255,255,.5);font-size:.75rem;letter-spacing:1px;margin-top:.25rem;">PUAS</div>
                            </div>
                        </div>
                    </div>

                    <!-- ISO badge -->
                    <div style="position:absolute;top:1.5rem;right:-1rem;background:var(--white);border-radius:var(--radius-sm);padding:.75rem 1.25rem;box-shadow:var(--shadow-hover);border:1px solid rgba(11,31,58,.06);">
                        <div style="font-size:.65rem;letter-spacing:2px;text-transform:uppercase;color:var(--gray-text);margin-bottom:.2rem;">Bersertifikasi</div>
                        <div style="font-weight:800;font-size:.85rem;color:var(--navy);">ISO 9001:2015</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- ============================================================
     3. KEUNGGULAN — Offset asymmetric cards
============================================================ -->
<section class="py-5" style="background:var(--off-white);padding-top:5rem!important;padding-bottom:5rem!important;">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-4 reveal">
                <p class="section-tag">Keunggulan</p>
                <h2 class="section-title">Mengapa Kami Berbeda?</h2>
                <span class="section-divider"></span>
                <p class="mt-4">Empat pilar yang membedakan kami dari kompetitor — bukan sekadar klaim, melainkan standar yang kami buktikan di setiap proyek.</p>
            </div>
            <div class="col-lg-8">
                <div class="advantage-grid">
                    <?php foreach (array_slice($advantages, 0, 4) as $i => $adv): ?>
                    <div class="advantage-card reveal" style="transition-delay:<?php echo ($i+1)*0.1; ?>s">
                        <div style="font-size:1.8rem;background:var(--grad-gold);-webkit-background-clip:text;-webkit-text-fill-color:transparent;margin-bottom:1rem;">
                            <i class="bi <?php echo sanitize($adv['icon']); ?>"></i>
                        </div>
                        <h5 class="mb-2"><?php echo sanitize($adv['title']); ?></h5>
                        <p class="small mb-0"><?php echo sanitize($adv['description']); ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- ============================================================
     4. LAYANAN — Full-bleed image cards with hover reveal
============================================================ -->
<section class="py-5" style="background:var(--white);padding-top:6rem!important;padding-bottom:6rem!important;">
    <div class="container">
        <div class="text-center mb-5 reveal">
            <p class="section-tag">Bidang Usaha</p>
            <h2 class="section-title">Layanan Unggulan Kami</h2>
            <span class="section-divider center"></span>
        </div>
        <div class="services-track">
            <?php foreach ($services as $s):
                $img = !empty($s['image']) && file_exists(__DIR__.'/assets/uploads/services/'.$s['image'])
                    ? base_url('assets/uploads/services/'.$s['image'])
                    : ($svc_imgs[$s['slug']] ?? $svc_imgs['konstruksi-dan-infrastruktur']);
            ?>
            <div class="service-card reveal" style="transition-delay:<?php echo array_search($s, $services) * 0.15; ?>s">
                <img src="<?php echo $img; ?>" alt="<?php echo sanitize($s['title']); ?>" class="service-card-img">
                <div class="service-card-overlay"></div>
                <div class="service-card-body">
                    <i class="bi <?php echo sanitize($s['icon']); ?> service-card-icon"></i>
                    <div class="service-card-title"><?php echo sanitize($s['title']); ?></div>
                    <p class="service-card-desc"><?php echo sanitize($s['short_description']); ?></p>
                    <a href="<?php echo base_url('layanan/'.$s['slug']); ?>" class="service-card-link">
                        Detail Layanan <i class="bi-arrow-right"></i>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-5 reveal">
            <a href="<?php echo base_url('layanan.php'); ?>" class="btn-gold" style="display:inline-flex;">Semua Layanan <i class="bi-arrow-right"></i></a>
        </div>
    </div>
</section>


<!-- ============================================================
     5. STATS — Diagonal dark section
============================================================ -->
<section class="counter-section">
    <div class="container">
        <div class="row align-items-center justify-content-center g-4 text-center counter-row">
            <div class="col-lg-3 col-6">
                <div class="counter-box">
                    <h3><span class="counter-value" data-target="<?php echo sanitize($settings['stat_proyek'] ?? '150'); ?>">0</span>+</h3>
                    <p>Proyek Selesai</p>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="counter-box">
                    <h3><span class="counter-value" data-target="<?php echo sanitize($settings['stat_mitra'] ?? '80'); ?>">0</span>+</h3>
                    <p>Mitra &amp; Klien</p>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="counter-box">
                    <h3><span class="counter-value" data-target="<?php echo sanitize($settings['stat_pengalaman'] ?? '15'); ?>">0</span>+</h3>
                    <p>Tahun Pengalaman</p>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="counter-box">
                    <h3><span class="counter-value" data-target="<?php echo sanitize($settings['stat_kepuasan'] ?? '99'); ?>">0</span>%</h3>
                    <p>Tingkat Kepuasan</p>
                </div>
            </div>
        </div>
    </div>
</section>




<!-- ============================================================
     7. KLIEN / PARTNER LOGOS STRIP
============================================================ -->
<section style="background:var(--off-white);padding:3rem 0;border-top:1px solid rgba(11,31,58,.05);border-bottom:1px solid rgba(11,31,58,.05);">
    <div class="container">
        <p class="text-center mb-4 reveal" style="font-size:.75rem;letter-spacing:3px;text-transform:uppercase;color:var(--gray-text);font-weight:700;">Dipercaya oleh</p>
        <div class="row align-items-center justify-content-center g-4 text-center reveal">
            <?php
            $partners = [
                ['icon'=>'bi-building-fill', 'name'=>'BUMN RI'],
                ['icon'=>'bi-shield-check',  'name'=>'Adhi Karya'],
                ['icon'=>'bi-box-seam',      'name'=>'Waskita'],
                ['icon'=>'bi-gear-wide',     'name'=>'Hastra GP'],
                ['icon'=>'bi-house-check',   'name'=>'Wijaya Karya'],
            ];
            foreach ($partners as $p): ?>
            <div class="col-4 col-md-2">
                <div style="padding:1.25rem .75rem;border-radius:var(--radius-sm);background:var(--white);border:1px solid rgba(11,31,58,.06);transition:all .3s;box-shadow:var(--shadow-card);" onmouseover="this.style.borderColor='rgba(201,162,39,.3)';this.style.transform='translateY(-4px)'" onmouseout="this.style.borderColor='rgba(11,31,58,.06)';this.style.transform='translateY(0)'">
                    <i class="bi <?php echo $p['icon']; ?>" style="font-size:1.5rem;color:var(--navy);display:block;margin-bottom:.5rem;"></i>
                    <div style="font-size:.75rem;font-weight:700;color:var(--navy);letter-spacing:.5px;"><?php echo $p['name']; ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>


<!-- ============================================================
     8. CTA — Full dark panel
============================================================ -->
<section class="cta-section">
    <div class="container reveal">
        <div class="cta-box">
            <div class="row align-items-center g-4" style="position:relative;z-index:1;">
                <div class="col-lg-8">
                    <p class="section-tag" style="margin-bottom:.5rem;">Mulai Sekarang</p>
                    <h2 style="font-family:var(--font-head);color:#fff;font-size:clamp(1.75rem,3.5vw,2.75rem);font-weight:800;margin-bottom:1rem;">
                        Siap Membangun Proyek<br><em style="background:var(--grad-gold);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">Impian Bersama Kami?</em>
                    </h2>
                    <p style="color:rgba(255,255,255,.55);margin:0;font-size:1.05rem;">Konsultasikan kebutuhan proyek konstruksi, pengadaan, atau manajemen Anda kepada tim ahli kami sekarang.</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="<?php echo base_url('hubungi-kami.php'); ?>" class="btn-gold" style="display:inline-flex;font-size:1rem;padding:1rem 2.25rem;">
                        Hubungi Tim Kami <i class="bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
