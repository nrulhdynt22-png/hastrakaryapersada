<?php
$page_title = "Tentang Kami — PT. Hastra Karya Persada";
$page_desc  = "Kenali sejarah, visi, misi, dan nilai-nilai PT. Hastra Karya Persada — mitra konstruksi dan infrastruktur terpercaya sejak 2020.";
include __DIR__ . '/includes/header.php';

// Fetch profile data
$profile = [];
try {
    $stmt_profile = $db->query("SELECT * FROM company_profile WHERE id = 1");
    $profile = $stmt_profile->fetch();
} catch (Exception $e) {}

// Decode dynamic fields
$nilai_data = json_decode($profile['nilai_json'] ?? '[]', true) ?: [
    ['icon' => 'bi-award',        'title' => 'Integritas',        'desc' => 'Menjunjung tinggi etika bisnis, kejujuran, dan transparansi di setiap kesepakatan.'],
    ['icon' => 'bi-shield-heart', 'title' => 'Keselamatan (K3)',  'desc' => 'Mengutamakan keselamatan dan kesehatan kerja karyawan di setiap area proyek.'],
    ['icon' => 'bi-gem',          'title' => 'Mutu Unggul',       'desc' => 'Tidak berkompromi terhadap standar kualitas pengerjaan di setiap detail proyek.'],
    ['icon' => 'bi-people',       'title' => 'Kolaborasi',        'desc' => 'Bekerja secara sinergis dengan klien, mitra bisnis, dan vendor rantai pasok.'],
];
$milestones_data = json_decode($profile['milestones_json'] ?? '[]', true) ?: [
    ['year' => '2020', 'desc' => 'Perusahaan didirikan'],
    ['year' => '2021', 'desc' => 'Proyek pertama senilai Rp 15M'],
    ['year' => '2022', 'desc' => 'Raih ISO 9001:2015'],
    ['year' => '2024', 'desc' => '150+ proyek, 80+ klien'],
];
$misi_items = array_filter(explode("\n", $profile['misi_items'] ?? "Menyediakan solusi jasa terintegrasi dengan standar keselamatan kerja dan mutu internasional.\nMembangun kemitraan strategis jangka panjang berdasarkan prinsip transparansi dan saling menguntungkan.\nMemberdayakan talenta profesional lokal terbaik dan memanfaatkan teknologi modern untuk efisiensi.\nMemberikan dampak positif bagi masyarakat melalui program pembangunan ramah lingkungan."), 'trim');

$visi_title = $profile['visi_title'] ?? 'Menjadi Kontraktor Terkemuka Berskala Nasional';
$visi_text  = $profile['visi_text']  ?? 'Menjadi perusahaan konstruksi, pengadaan, dan konsultansi multi-jasa terkemuka berskala nasional yang dikenal karena integritas, keandalan, inovasi berkelanjutan, serta komitmen penuh menghasilkan kualitas kerja berkelas dunia.';
$profile_text = $profile['profile_text'] ?? 'PT. Hastra Karya Persada didirikan dengan tujuan menjadi mitra bisnis utama di bidang pembangunan infrastruktur, pengadaan rantai pasok, dan konsultansi manajemen proyek nasional.';

$raw_certs = array_filter(explode("\n", $profile['certificates'] ?? "ISO 9001:2015\nISO 14001:2015\nISO 45001:2018\nSMK3"), 'trim');
?>

<!-- Breadcrumb Header -->
<section class="breadcrumb-section">
    <div class="container" style="position:relative;z-index:1;">
        <div class="row align-items-end">
            <div class="col-lg-7">
                <p style="color:var(--gold-light);font-size:.75rem;letter-spacing:3px;text-transform:uppercase;font-weight:700;margin-bottom:.75rem;">PT. Hastra Karya Persada</p>
                <h1 style="font-family:var(--font-head);font-size:clamp(2.2rem,4vw,3.5rem);font-weight:800;color:#fff;line-height:1.1;margin-bottom:1rem;">
                    Tentang <em style="font-style:italic;background:var(--grad-gold);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">Perusahaan</em>
                </h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-custom mb-0">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>">Home</a></li>
                        <li class="breadcrumb-item active">Tentang Kami</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>


<!-- === SEJARAH & PROFIL === -->
<section class="py-5 overflow-hidden" style="background:var(--white);padding-top:5rem!important;padding-bottom:5rem!important;">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 reveal-left">
                <p class="section-tag">Sejarah &amp; Profil</p>
                <h2 class="section-title">Dedikasi Membangun Solusi Terbaik <em style="font-style:italic;background:var(--grad-gold);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">Sejak 2020</em></h2>
                <span class="section-divider"></span>
                <div class="mt-4" style="font-size:1.05rem;line-height:1.85;color:var(--gray-text);">
                    <?php echo nl2br(sanitize($profile_text)); ?>
                </div>
                <div class="row g-3 mt-2">
                    <?php foreach ($milestones_data as $m): ?>
                    <div class="col-6">
                        <div style="display:flex;align-items:flex-start;gap:.75rem;padding:1rem;background:var(--off-white);border-radius:var(--radius-sm);border:1px solid rgba(11,31,58,.06);">
                            <span style="font-size:1.2rem;font-weight:900;background:var(--grad-gold);-webkit-background-clip:text;-webkit-text-fill-color:transparent;line-height:1;"><?php echo sanitize($m['year']); ?></span>
                            <span style="font-size:.82rem;color:var(--gray-text);line-height:1.4;"><?php echo sanitize($m['desc']); ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-lg-6 reveal-right">
                <div class="position-relative">
                    <img src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=900&q=85"
                         class="img-fluid rounded-4 w-100"
                         alt="Kantor Hastra"
                         style="height:480px;object-fit:cover;box-shadow:0 40px 80px rgba(11,31,58,.15);">
                    <!-- Floating badge -->
                    <div style="position:absolute;bottom:2rem;left:-2rem;background:var(--navy);border-radius:var(--radius);padding:1.5rem 2rem;border:1px solid rgba(201,162,39,.2);box-shadow:0 20px 50px rgba(0,0,0,.25);">
                        <div style="font-size:2rem;font-weight:900;background:var(--grad-gold);-webkit-background-clip:text;-webkit-text-fill-color:transparent;line-height:1;"><?php echo sanitize($settings['stat_kepuasan'] ?? '99'); ?>%</div>
                        <div style="color:rgba(255,255,255,.5);font-size:.75rem;letter-spacing:1.5px;text-transform:uppercase;margin-top:.25rem;">Kepuasan Klien</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- === VISI & MISI === -->
<section style="background:var(--off-white);padding:5rem 0;">
    <div class="container">
        <div class="text-center mb-5 reveal">
            <p class="section-tag">Arah &amp; Tujuan</p>
            <h2 class="section-title">Visi &amp; Misi Perusahaan</h2>
            <span class="section-divider center"></span>
        </div>
        <div class="row g-4">
            <!-- Visi -->
            <div class="col-lg-6 reveal-left">
                <div style="background:var(--grad-navy);border-radius:var(--radius);padding:2.5rem;height:100%;position:relative;overflow:hidden;border:1px solid rgba(201,162,39,.15);">
                    <div style="position:absolute;top:-30px;right:-30px;width:150px;height:150px;border:1px solid rgba(201,162,39,.08);border-radius:50%;"></div>
                    <div style="display:inline-flex;align-items:center;gap:.75rem;background:rgba(201,162,39,.08);border:1px solid rgba(201,162,39,.2);border-radius:100px;padding:.5rem 1.25rem;margin-bottom:1.5rem;">
                        <i class="bi-eye" style="background:var(--grad-gold);-webkit-background-clip:text;-webkit-text-fill-color:transparent;font-size:1.1rem;"></i>
                        <span style="font-size:.75rem;letter-spacing:3px;text-transform:uppercase;font-weight:800;background:var(--grad-gold);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">Visi</span>
                    </div>
                    <h3 style="font-family:var(--font-head);color:#fff;font-size:1.6rem;margin-bottom:1rem;"><?php echo sanitize($visi_title); ?></h3>
                    <p style="color:rgba(255,255,255,.55);line-height:1.8;margin:0;">
                        <?php echo sanitize($visi_text); ?>
                    </p>
                </div>
            </div>
            <!-- Misi -->
            <div class="col-lg-6 reveal-right">
                <div style="background:var(--white);border-radius:var(--radius);padding:2.5rem;height:100%;border:1px solid rgba(11,31,58,.07);box-shadow:var(--shadow-card);">
                    <div style="display:inline-flex;align-items:center;gap:.75rem;background:rgba(11,31,58,.04);border:1px solid rgba(11,31,58,.1);border-radius:100px;padding:.5rem 1.25rem;margin-bottom:1.5rem;">
                        <i class="bi-compass" style="color:var(--navy);font-size:1.1rem;"></i>
                        <span style="font-size:.75rem;letter-spacing:3px;text-transform:uppercase;font-weight:800;color:var(--navy);">Misi</span>
                    </div>
                    <h3 style="font-family:var(--font-head);color:var(--navy);font-size:1.6rem;margin-bottom:1.5rem;">Komitmen Nyata di Setiap Proyek</h3>
                    <?php foreach ($misi_items as $m): if(trim($m)==='') continue; ?>
                    <div style="display:flex;align-items:flex-start;gap:.75rem;margin-bottom:1rem;padding-bottom:1rem;border-bottom:1px solid rgba(11,31,58,.05);">
                        <i class="bi-check-circle-fill" style="color:var(--gold);flex-shrink:0;margin-top:.15rem;"></i>
                        <span style="font-size:.9rem;color:var(--gray-text);line-height:1.65;"><?php echo sanitize($m); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- === STRUKTUR PERUSAHAAN === -->
<section id="struktur" style="background:var(--white);padding:5rem 0;">
    <div class="container">
        <div class="text-center mb-5 reveal">
            <p class="section-tag">Organisasi</p>
            <h2 class="section-title">Struktur <em style="font-style:italic;background:var(--grad-gold);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">Perusahaan</em></h2>
            <span class="section-divider center"></span>
            <p class="text-muted mt-3" style="max-width:580px;margin:0 auto;">Tim profesional kami yang berpengalaman berkomitmen untuk menghadirkan solusi terbaik di setiap lini operasional perusahaan.</p>
        </div>

        <?php
        // Fetch all org members from DB
        $org_members = [];
        try {
            $org_stmt = $db->query("SELECT * FROM org_structure ORDER BY sort_order ASC, id ASC");
            $org_members = $org_stmt->fetchAll();
        } catch (Exception $e) {}

        // Build map keyed by parent_id
        $org_by_parent = [];
        foreach ($org_members as $om) {
            $key = (isset($om['parent_id']) && $om['parent_id'] !== null && $om['parent_id'] !== '') ? (int)$om['parent_id'] : 'root';
            $org_by_parent[$key][] = $om;
        }

        // Recursively build tree array with children
        if (!function_exists('build_org_tree')) {
        function build_org_tree($map, $parent_key = 'root') {
            $nodes = $map[$parent_key] ?? [];
            foreach ($nodes as &$node) {
                $node['children'] = build_org_tree($map, (int)$node['id']);
            }
            return $nodes;
        }
        }

        // Render a single org card
        if (!function_exists('org_card_html')) {
        function org_card_html($m, $level = 0) {
            $photo_url = !empty($m['photo']) ? base_url('assets/uploads/org/' . $m['photo']) : null;
            $img_size  = $level === 0 ? '80px' : ($level === 1 ? '64px' : '52px');
            $card_bg   = $level === 0 ? 'linear-gradient(135deg,var(--navy) 0%,#1a3a5c 100%)' : '#fff';
            $name_color= $level === 0 ? '#fff' : 'var(--navy)';
            $pos_color = $level === 0 ? 'rgba(201,162,39,.9)' : 'var(--gold)';
            $border    = $level === 0 ? '2px solid rgba(201,162,39,.4)' : '1.5px solid #e4e8ef';
            $shadow    = $level === 0 ? '0 8px 32px rgba(11,31,58,.25)' : '0 4px 18px rgba(11,31,58,.08)';
            $shadow_h  = $level === 0 ? '0 16px 40px rgba(11,31,58,.3)' : '0 12px 32px rgba(11,31,58,.14)';
            $border_h  = $level === 0 ? 'rgba(201,162,39,.7)' : 'rgba(201,162,39,.5)';
            $padding   = $level === 0 ? '1.25rem 1rem' : '1rem .875rem';
            $min_w     = $level === 0 ? '160px' : ($level === 1 ? '145px' : '130px');
            $font_name = $level === 0 ? '.85rem' : '.78rem';

            $html  = "<div style='display:inline-flex;flex-direction:column;align-items:center;background:{$card_bg};border:{$border};border-radius:12px;padding:{$padding};text-align:center;min-width:{$min_w};max-width:{$min_w};box-shadow:{$shadow};transition:all .3s ease;cursor:default;'";
            $html .= " onmouseover=\"this.style.transform='translateY(-5px)';this.style.boxShadow='{$shadow_h}';this.style.borderColor='{$border_h}'\"";
            $html .= " onmouseout=\"this.style.transform='translateY(0)';this.style.boxShadow='{$shadow}';this.style.borderColor='" . ($level === 0 ? 'rgba(201,162,39,.4)' : '#e4e8ef') . "'\">";

            if ($photo_url) {
                $html .= "<img src='" . htmlspecialchars($photo_url) . "' style='width:{$img_size};height:{$img_size};border-radius:50%;object-fit:cover;border:3px solid rgba(201,162,39,.5);margin-bottom:.6rem;flex-shrink:0;' alt='" . htmlspecialchars($m['name']) . "'>";
            } else {
                $avt_bg = $level === 0 ? 'rgba(255,255,255,.1)' : 'var(--grad-navy)';
                $icon_fs = $level === 0 ? '1.8rem' : '1.3rem';
                $html .= "<div style='width:{$img_size};height:{$img_size};border-radius:50%;background:{$avt_bg};display:flex;align-items:center;justify-content:center;margin:0 auto .6rem;border:3px solid rgba(201,162,39,.3);flex-shrink:0;'>";
                $html .= "<i class=\"bi-person-fill\" style='font-size:{$icon_fs};background:var(--grad-gold);-webkit-background-clip:text;-webkit-text-fill-color:transparent;'></i>";
                $html .= "</div>";
            }

            $html .= "<div style='font-size:{$font_name};font-weight:800;color:{$name_color};line-height:1.3;margin-bottom:.2rem;word-break:break-word;'>" . htmlspecialchars($m['name']) . "</div>";
            $html .= "<div style='font-size:.67rem;color:{$pos_color};font-weight:600;line-height:1.3;font-style:italic;word-break:break-word;'>" . htmlspecialchars($m['position']) . "</div>";
            $html .= "</div>";
            return $html;
        }
        }

        // Recursively render org tree as HTML
        if (!function_exists('render_org_tree_html')) {
        function render_org_tree_html($nodes, $level = 0) {
            if (empty($nodes)) return '';
            $count = count($nodes);
            $html  = '';

            // Vertical line from parent
            $html .= "<div style='width:2px;height:24px;background:linear-gradient(to bottom,rgba(201,162,39,.7),rgba(201,162,39,.3));margin:0 auto;'></div>";

            if ($count === 1) {
                $node = $nodes[0];
                $html .= "<div style='display:flex;flex-direction:column;align-items:center;'>";
                $html .= org_card_html($node, $level);
                if (!empty($node['children'])) {
                    $html .= render_org_tree_html($node['children'], $level + 1);
                }
                $html .= "</div>";
            } else {
                // Multiple siblings — horizontal branch
                $html .= "<div style='display:flex;flex-direction:column;align-items:center;width:100%;'>";
                $html .= "<div style='display:flex;justify-content:center;align-items:flex-start;gap:1.25rem;flex-wrap:wrap;position:relative;'>";
                // Top horizontal line spanning siblings
                $html .= "<div style='position:absolute;top:0;left:8%;right:8%;height:2px;background:linear-gradient(to right,transparent,rgba(201,162,39,.5) 20%,rgba(201,162,39,.5) 80%,transparent);'></div>";
                foreach ($nodes as $node) {
                    $html .= "<div style='display:flex;flex-direction:column;align-items:center;margin-top:24px;'>";
                    $html .= "<div style='width:2px;height:0;background:rgba(201,162,39,.4);'></div>";
                    $html .= org_card_html($node, $level);
                    if (!empty($node['children'])) {
                        $html .= render_org_tree_html($node['children'], $level + 1);
                    }
                    $html .= "</div>";
                }
                $html .= "</div></div>";
            }
            return $html;
        }
        }

        $org_tree = build_org_tree($org_by_parent);

        if (!empty($org_tree)):
        ?>
        <div style="overflow-x:auto;padding-bottom:1.5rem;-webkit-overflow-scrolling:touch;">
            <div style="min-width:600px;text-align:center;padding:1rem 2rem 2rem;">
                <?php foreach ($org_tree as $i => $root): ?>
                <?php if ($i > 0): ?><div style="height:2.5rem;"></div><?php endif; ?>
                <div style="display:flex;flex-direction:column;align-items:center;">
                    <?php echo org_card_html($root, 0); ?>
                    <?php if (!empty($root['children'])): ?>
                    <?php echo render_org_tree_html($root['children'], 1); ?>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <?php else: ?>
        <p class="text-center text-muted py-4">
            Struktur organisasi belum diatur.
            <a href="<?php echo base_url('admin/org_structure.php'); ?>">Atur di Admin Panel</a>.
        </p>
        <?php endif; ?>

    </div>
</section>


<!-- === NILAI PERUSAHAAN === -->
<section style="background:var(--off-white);padding:5rem 0;">
    <div class="container">
        <div class="text-center mb-5 reveal">
            <p class="section-tag">Budaya Kami</p>
            <h2 class="section-title">Nilai Perusahaan &amp; Budaya Kerja</h2>
            <span class="section-divider center"></span>
        </div>
        <div class="row g-4">
            <?php foreach ($nilai_data as $i => $v): ?>
            <div class="col-lg-3 col-sm-6 reveal" style="transition-delay:<?php echo $i * .1; ?>s">
                <div style="background:var(--white);border-radius:var(--radius);padding:2rem 1.75rem;text-align:center;border:1px solid rgba(11,31,58,.06);transition:all .4s var(--ease);box-shadow:var(--shadow-card);"
                     onmouseover="this.style.borderColor='rgba(201,162,39,.3)';this.style.transform='translateY(-8px)';this.style.boxShadow='0 20px 60px rgba(11,31,58,.12)'"
                     onmouseout="this.style.borderColor='rgba(11,31,58,.06)';this.style.transform='translateY(0)';this.style.boxShadow='var(--shadow-card)'">
                    <div style="width:64px;height:64px;border-radius:50%;background:var(--grad-navy);display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;">
                        <i class="bi <?php echo sanitize($v['icon']); ?>" style="font-size:1.6rem;background:var(--grad-gold);-webkit-background-clip:text;-webkit-text-fill-color:transparent;"></i>
                    </div>
                    <h4 style="font-size:1rem;font-weight:800;color:var(--navy);margin-bottom:.75rem;"><?php echo sanitize($v['title']); ?></h4>
                    <p style="font-size:.875rem;margin:0;line-height:1.7;"><?php echo sanitize($v['desc']); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>


<!-- === SERTIFIKASI === -->
<section style="background:var(--grad-navy);padding:5rem 0;position:relative;overflow:hidden;">
    <div style="position:absolute;top:-80px;right:-80px;width:300px;height:300px;border:1px solid rgba(201,162,39,.07);border-radius:50%;"></div>
    <div style="position:absolute;bottom:-60px;left:-60px;width:200px;height:200px;border:1px solid rgba(201,162,39,.07);border-radius:50%;"></div>
    <div class="container text-center" style="position:relative;z-index:1;">
        <p class="section-tag reveal">Standar Global</p>
        <h2 style="font-family:var(--font-head);color:#fff;font-size:clamp(1.75rem,3vw,2.5rem);margin-bottom:1rem;" class="reveal">Komitmen Mutu Kelas Dunia</h2>
        <p style="color:rgba(255,255,255,.5);max-width:600px;margin:0 auto 3rem;" class="reveal">Seluruh aspek operasional kami mengacu pada standar internasional yang diakui secara global.</p>
        <div class="row justify-content-center g-4 reveal">
            <?php foreach ($raw_certs as $cert): if(trim($cert)==='') continue; ?>
            <div class="col-lg-3 col-sm-6">
                <div style="background:rgba(255,255,255,.04);border:1px solid rgba(201,162,39,.2);border-radius:var(--radius);padding:2rem 1.5rem;transition:all .4s var(--ease);"
                     onmouseover="this.style.background='rgba(201,162,39,.08)';this.style.borderColor='rgba(201,162,39,.4)';this.style.transform='translateY(-6px)'"
                     onmouseout="this.style.background='rgba(255,255,255,.04)';this.style.borderColor='rgba(201,162,39,.2)';this.style.transform='translateY(0)'">
                    <i class="bi bi-patch-check-fill" style="font-size:2rem;background:var(--grad-gold);-webkit-background-clip:text;-webkit-text-fill-color:transparent;display:block;margin-bottom:1rem;"></i>
                    <div style="font-size:1.1rem;font-weight:900;background:var(--grad-gold);-webkit-background-clip:text;-webkit-text-fill-color:transparent;margin-bottom:.4rem;"><?php echo sanitize(trim($cert)); ?></div>
                    <div style="color:rgba(255,255,255,.45);font-size:.82rem;">Standar Internasional</div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- === CTA === -->
<section style="background:var(--off-white);padding:5rem 0;">
    <div class="container reveal">
        <div class="cta-box">
            <div class="row align-items-center g-4" style="position:relative;z-index:1;">
                <div class="col-lg-8">
                    <p class="section-tag">Bergabung Bersama Kami</p>
                    <h2 style="font-family:var(--font-head);color:#fff;font-size:clamp(1.5rem,3vw,2.5rem);margin-bottom:.75rem;">Mulai Proyek Impian Anda Sekarang</h2>
                    <p style="color:rgba(255,255,255,.5);margin:0;">Kami siap menjadi mitra konstruksi dan pengadaan terpercaya untuk bisnis Anda.</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="<?php echo base_url('hubungi-kami.php'); ?>" class="btn-gold" style="display:inline-flex;">
                        Konsultasi Gratis <i class="bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
