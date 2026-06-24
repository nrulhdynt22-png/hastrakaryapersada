<?php
require_once __DIR__ . '/config/functions.php';

$slug    = isset($_GET['slug']) ? sanitize($_GET['slug']) : null;
$service = null;

if ($slug) {
    try {
        $stmt = $db->prepare("SELECT * FROM services WHERE slug = ?");
        $stmt->execute([$slug]);
        $service = $stmt->fetch();
    } catch (Exception $e) {}
    if (!$service) { header("Location: " . base_url('layanan.php')); exit(); }
    $page_title = $service['title'] . " — PT. Hastra Karya Persada";
    $page_desc  = $service['short_description'];
} else {
    $page_title = "Layanan — PT. Hastra Karya Persada";
    $page_desc  = "Jasa konstruksi, pengadaan, dan konsultansi manajemen proyek dari PT. Hastra Karya Persada.";
}

include __DIR__ . '/includes/header.php';

// Fallback service images
$svc_fallback = [
    'konstruksi-dan-infrastruktur' => "https://images.unsplash.com/photo-1504307651254-35680f356dfd?auto=format&fit=crop&w=1200&q=80",
    'pengadaan-barang-dan-jasa'    => "https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=1200&q=80",
    'konsultansi-manajemen-proyek' => "https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&w=1200&q=80",
    '_default'                     => "https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=1200&q=80",
];

function svc_img_url($s, $fallback_map, $dir) {
    if (!empty($s['image']) && file_exists($dir.'/assets/uploads/services/'.$s['image'])) {
        return base_url('assets/uploads/services/'.$s['image']);
    }
    return $fallback_map[$s['slug']] ?? $fallback_map['_default'];
}
?>

<!-- BREADCRUMB HEADER -->
<section class="breadcrumb-section">
    <div class="container" style="position:relative;z-index:1;">
        <div class="row">
            <div class="col-lg-8">
                <p style="color:var(--gold-light);font-size:.75rem;letter-spacing:3px;text-transform:uppercase;font-weight:700;margin-bottom:.75rem;">Bidang Usaha</p>
                <h1 style="font-family:var(--font-head);font-size:clamp(2rem,4vw,3.2rem);font-weight:800;color:#fff;line-height:1.15;margin-bottom:1rem;">
                    <?php echo $service ? sanitize($service['title']) : 'Layanan <em style="font-style:italic;background:var(--grad-gold);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">Kami</em>'; ?>
                </h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-custom mb-0">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>">Home</a></li>
                        <?php if ($service): ?>
                        <li class="breadcrumb-item"><a href="<?php echo base_url('layanan.php'); ?>">Layanan</a></li>
                        <li class="breadcrumb-item active"><?php echo sanitize($service['title']); ?></li>
                        <?php else: ?>
                        <li class="breadcrumb-item active">Layanan</li>
                        <?php endif; ?>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>


<?php if ($service): ?>
<!-- ==================== DETAIL VIEW ==================== -->
<section style="background:var(--white);padding:5rem 0;">
    <div class="container">
        <div class="row g-5">
            <!-- Main Content -->
            <div class="col-lg-8">
                <?php $img = svc_img_url($service, $svc_fallback, __DIR__); ?>
                <div style="border-radius:var(--radius);overflow:hidden;margin-bottom:2.5rem;box-shadow:var(--shadow-hover);">
                    <img src="<?php echo $img; ?>" class="w-100" alt="<?php echo sanitize($service['title']); ?>" style="height:420px;object-fit:cover;display:block;">
                </div>

                <p class="section-tag">Deskripsi Layanan</p>
                <h2 style="font-family:var(--font-head);font-size:2rem;color:var(--navy);margin-bottom:1.25rem;"><?php echo sanitize($service['title']); ?></h2>
                <p style="font-size:1.05rem;line-height:1.85;color:var(--gray-text);"><?php echo nl2br(sanitize($service['description'])); ?></p>

                <!-- Keunggulan -->
                <?php if (!empty($service['advantages'])): ?>
                <div style="margin:3rem 0;">
                    <p class="section-tag">Keunggulan</p>
                    <h3 style="font-family:var(--font-head);font-size:1.5rem;color:var(--navy);margin-bottom:1.5rem;">Keunggulan Layanan Ini</h3>
                    <div class="row g-3">
                        <?php foreach (array_filter(explode("\n", $service['advantages']), 'trim') as $adv): ?>
                        <div class="col-md-6">
                            <div style="display:flex;align-items:flex-start;gap:.75rem;padding:1rem 1.25rem;background:var(--off-white);border-radius:var(--radius-sm);border:1px solid rgba(11,31,58,.06);">
                                <i class="bi-check-circle-fill" style="color:var(--gold);flex-shrink:0;font-size:1rem;margin-top:.15rem;"></i>
                                <span style="font-size:.9rem;color:var(--navy);font-weight:600;"><?php echo sanitize($adv); ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Alur Kerja -->
                <?php if (!empty($service['workflow'])): ?>
                <div style="margin:3rem 0;">
                    <p class="section-tag">SOP</p>
                    <h3 style="font-family:var(--font-head);font-size:1.5rem;color:var(--navy);margin-bottom:2rem;">Alur Kerja Pelaksanaan</h3>
                    <?php
                    $steps = array_filter(explode("\n", $service['workflow']), 'trim');
                    foreach ($steps as $step):
                        $parts = explode(".", $step, 2);
                        if (count($parts) < 2) continue;
                        [$num, $rest] = $parts;
                        $sub = explode(":", $rest, 2);
                        $title = sanitize(trim($sub[0]));
                        $desc  = sanitize(trim($sub[1] ?? $sub[0]));
                    ?>
                    <div class="workflow-step">
                        <div class="workflow-number"><?php echo sanitize(trim($num)); ?></div>
                        <h5 style="color:var(--navy);font-weight:700;margin-bottom:.25rem;"><?php echo $title; ?></h5>
                        <p style="font-size:.88rem;margin:0;"><?php echo $desc; ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="position-sticky" style="top:100px;display:flex;flex-direction:column;gap:1.5rem;">
                    <!-- Other Services -->
                    <div style="background:var(--off-white);border-radius:var(--radius);padding:2rem;border:1px solid rgba(11,31,58,.07);">
                        <h4 style="font-size:.75rem;letter-spacing:3px;text-transform:uppercase;font-weight:800;background:var(--grad-gold);-webkit-background-clip:text;-webkit-text-fill-color:transparent;margin-bottom:1.25rem;">Layanan Lainnya</h4>
                        <?php
                        $others = [];
                        try {
                            $st = $db->prepare("SELECT title, slug, icon FROM services WHERE id != ? ORDER BY id ASC");
                            $st->execute([$service['id']]);
                            $others = $st->fetchAll();
                        } catch (Exception $e) {}
                        foreach ($others as $o): ?>
                        <a href="<?php echo base_url('layanan/'.$o['slug']); ?>"
                           style="display:flex;align-items:center;gap:.75rem;padding:.75rem 0;border-bottom:1px solid rgba(11,31,58,.06);text-decoration:none;transition:all .3s;"
                           onmouseover="this.style.paddingLeft='.5rem'" onmouseout="this.style.paddingLeft='0'">
                            <i class="bi <?php echo sanitize($o['icon']); ?>" style="color:var(--gold);font-size:1rem;flex-shrink:0;"></i>
                            <span style="font-size:.9rem;font-weight:600;color:var(--navy);"><?php echo sanitize($o['title']); ?></span>
                            <i class="bi-chevron-right ms-auto" style="color:var(--gray-text);font-size:.75rem;"></i>
                        </a>
                        <?php endforeach; ?>
                    </div>

                    <!-- CTA Widget -->
                    <div style="background:var(--grad-navy);border-radius:var(--radius);padding:2rem;text-align:center;border:1px solid rgba(201,162,39,.15);">
                        <i class="bi-chat-dots-fill" style="font-size:2rem;background:var(--grad-gold);-webkit-background-clip:text;-webkit-text-fill-color:transparent;display:block;margin-bottom:1rem;"></i>
                        <h4 style="color:#fff;font-size:1.1rem;margin-bottom:.5rem;">Konsultasi Gratis</h4>
                        <p style="color:rgba(255,255,255,.5);font-size:.85rem;margin-bottom:1.5rem;">Diskusikan kebutuhan konstruksi atau pengadaan proyek Anda.</p>
                        <a href="<?php echo base_url('hubungi-kami.php'); ?>" class="btn-gold" style="display:inline-flex;width:100%;justify-content:center;">
                            <i class="bi-whatsapp"></i> Hubungi Kami
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
        <div class="text-center mb-5 reveal">
            <p class="section-tag">Jasa & Keahlian</p>
            <h2 class="section-title">Layanan Solusi Bisnis Kami</h2>
            <span class="section-divider center"></span>
            <p style="max-width:600px;margin:1.5rem auto 0;font-size:1.05rem;">Kami menyediakan solusi konstruksi, pengadaan logistik, dan konsultansi terintegrasi untuk proyek skala nasional.</p>
        </div>

        <div class="services-track">
            <?php
            $all = [];
            try { $all = $db->query("SELECT * FROM services ORDER BY id ASC")->fetchAll(); } catch (Exception $e) {}
            foreach ($all as $i => $s):
                $img = svc_img_url($s, $svc_fallback, __DIR__);
            ?>
            <div class="service-card reveal" style="transition-delay:<?php echo $i * .12; ?>s">
                <img src="<?php echo $img; ?>" alt="<?php echo sanitize($s['title']); ?>" class="service-card-img">
                <div class="service-card-overlay"></div>
                <div class="service-card-body">
                    <i class="bi <?php echo sanitize($s['icon']); ?> service-card-icon"></i>
                    <div class="service-card-title"><?php echo sanitize($s['title']); ?></div>
                    <p class="service-card-desc"><?php echo sanitize($s['short_description']); ?></p>
                    <a href="<?php echo base_url('layanan/'.$s['slug']); ?>" class="service-card-link">
                        Selengkapnya <i class="bi-arrow-right"></i>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Process Strip -->
<section style="background:var(--off-white);padding:5rem 0;">
    <div class="container">
        <div class="text-center mb-5 reveal">
            <p class="section-tag">Cara Kerja</p>
            <h2 class="section-title">Proses Mudah Bersama Kami</h2>
            <span class="section-divider center"></span>
        </div>
        <div class="row g-4 text-center">
            <?php
            $steps = [
                ['01','bi-telephone','Konsultasi Awal','Hubungi tim kami dan ceritakan kebutuhan proyek Anda.'],
                ['02','bi-file-text','Perencanaan','Kami menyusun proposal, anggaran, dan timeline yang transparan.'],
                ['03','bi-gear','Eksekusi','Pelaksanaan proyek dengan pengawasan mutu dan keselamatan ketat.'],
                ['04','bi-trophy','Serah Terima','Proyek selesai tepat waktu, sesuai standar, siap beroperasi.'],
            ];
            foreach ($steps as $i => $s): ?>
            <div class="col-lg-3 col-sm-6 reveal" style="transition-delay:<?php echo $i * .1; ?>s">
                <div style="padding:2rem 1.5rem;border-radius:var(--radius);background:var(--white);border:1px solid rgba(11,31,58,.06);position:relative;box-shadow:var(--shadow-card);">
                    <div style="font-size:3.5rem;font-weight:900;background:var(--grad-gold);-webkit-background-clip:text;-webkit-text-fill-color:transparent;opacity:.15;position:absolute;top:.5rem;right:1rem;line-height:1;font-family:var(--font-body);"><?php echo $s[0]; ?></div>
                    <i class="bi <?php echo $s[1]; ?>" style="font-size:2rem;background:var(--grad-gold);-webkit-background-clip:text;-webkit-text-fill-color:transparent;display:block;margin-bottom:1rem;"></i>
                    <h5 style="color:var(--navy);font-weight:800;margin-bottom:.5rem;"><?php echo $s[2]; ?></h5>
                    <p style="font-size:.85rem;margin:0;"><?php echo $s[3]; ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
