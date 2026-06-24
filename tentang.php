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
                <p class="section-tag">Sejarah & Profil</p>
                <h2 class="section-title">Dedikasi Membangun Solusi Terbaik <em style="font-style:italic;background:var(--grad-gold);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">Sejak 2020</em></h2>
                <span class="section-divider"></span>
                <div class="mt-4" style="font-size:1.05rem;line-height:1.85;white-space:pre-wrap;color:var(--gray-text);">
                    <?php echo sanitize($profile['profile_text'] ?? 'Sejarah dan profil perusahaan belum diatur.'); ?>
                </div>
                <div class="row g-3 mt-2">
                    <?php
                    $milestones = [
                        ['2020','Perusahaan didirikan'],
                        ['2021','Proyek pertama senilai Rp 15M'],
                        ['2022','Raih ISO 9001:2015'],
                        ['2024','150+ proyek, 80+ klien'],
                    ];
                    foreach ($milestones as $m): ?>
                    <div class="col-6">
                        <div style="display:flex;align-items:flex-start;gap:.75rem;padding:1rem;background:var(--off-white);border-radius:var(--radius-sm);border:1px solid rgba(11,31,58,.06);">
                            <span style="font-size:1.2rem;font-weight:900;background:var(--grad-gold);-webkit-background-clip:text;-webkit-text-fill-color:transparent;line-height:1;"><?php echo $m[0]; ?></span>
                            <span style="font-size:.82rem;color:var(--gray-text);line-height:1.4;"><?php echo $m[1]; ?></span>
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
                        <div style="font-size:2rem;font-weight:900;background:var(--grad-gold);-webkit-background-clip:text;-webkit-text-fill-color:transparent;line-height:1;">99%</div>
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
            <p class="section-tag">Arah & Tujuan</p>
            <h2 class="section-title">Visi & Misi Perusahaan</h2>
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
                    <h3 style="font-family:var(--font-head);color:#fff;font-size:1.6rem;margin-bottom:1rem;">Menjadi Kontraktor Terkemuka Berskala Nasional</h3>
                    <p style="color:rgba(255,255,255,.55);line-height:1.8;margin:0;">
                        Menjadi perusahaan konstruksi, pengadaan, dan konsultansi multi-jasa terkemuka berskala nasional yang dikenal karena integritas, keandalan, inovasi berkelanjutan, serta komitmen penuh menghasilkan kualitas kerja berkelas dunia.
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
                    <?php
                    $missions = [
                        'Menyediakan solusi jasa terintegrasi dengan standar keselamatan kerja dan mutu internasional.',
                        'Membangun kemitraan strategis jangka panjang berdasarkan prinsip transparansi dan saling menguntungkan.',
                        'Memberdayakan talenta profesional lokal terbaik dan memanfaatkan teknologi modern untuk efisiensi.',
                        'Memberikan dampak positif bagi masyarakat melalui program pembangunan ramah lingkungan.',
                    ];
                    foreach ($missions as $m): ?>
                    <div style="display:flex;align-items:flex-start;gap:.75rem;margin-bottom:1rem;padding-bottom:1rem;border-bottom:1px solid rgba(11,31,58,.05);">
                        <i class="bi-check-circle-fill" style="color:var(--gold);flex-shrink:0;margin-top:.15rem;"></i>
                        <span style="font-size:.9rem;color:var(--gray-text);line-height:1.65;"><?php echo $m; ?></span>
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

        // Build map by parent_id
        $org_map = [];
        foreach ($org_members as $om) {
            $org_map[$om['parent_id'] ?? 'root'][] = $om;
        }

        // Helper to render an org card
        function render_org_card($m, $size = 'md') {
            $photo_url = !empty($m['photo'])
                ? base_url('assets/uploads/org/' . $m['photo'])
                : null;
            $card_w = $size === 'lg' ? '180px' : ($size === 'sm' ? '140px' : '160px');
            $img_size = $size === 'lg' ? '72px' : '56px';
            echo "<div style='display:inline-block;background:#fff;border:2px solid #e8ecf0;border-radius:10px;padding:1rem .75rem;text-align:center;min-width:{$card_w};max-width:{$card_w};box-shadow:0 4px 20px rgba(11,31,58,.08);transition:all .3s;vertical-align:top;'
                         onmouseover=\"this.style.borderColor='var(--gold)';this.style.boxShadow='0 8px 30px rgba(201,162,39,.2)'\"
                         onmouseout=\"this.style.borderColor='#e8ecf0';this.style.boxShadow='0 4px 20px rgba(11,31,58,.08)'\">";
            if ($photo_url) {
                echo "<img src='" . htmlspecialchars($photo_url) . "' style='width:{$img_size};height:{$img_size};border-radius:50%;object-fit:cover;border:3px solid rgba(201,162,39,.4);margin-bottom:.6rem;display:block;margin-left:auto;margin-right:auto;' alt='" . htmlspecialchars($m['name']) . "'>";
            } else {
                echo "<div style='width:{$img_size};height:{$img_size};border-radius:50%;background:var(--grad-navy);display:flex;align-items:center;justify-content:center;margin:0 auto .6rem;border:3px solid rgba(201,162,39,.25);'><i class=\"bi-person-fill\" style='font-size:1.4rem;background:var(--grad-gold);-webkit-background-clip:text;-webkit-text-fill-color:transparent;'></i></div>";
            }
            echo "<div style='font-size:.82rem;font-weight:800;color:var(--navy);line-height:1.3;margin-bottom:.25rem;'>" . htmlspecialchars($m['name']) . "</div>";
            echo "<div style='font-size:.7rem;color:var(--gold);font-style:italic;font-weight:600;'>" . htmlspecialchars($m['position']) . "</div>";
            echo "</div>";
        }

        // Connector line style
        $v_line = "<div style='width:2px;height:28px;background:linear-gradient(to bottom,rgba(201,162,39,.7),rgba(201,162,39,.3));margin:0 auto;'></div>";
        $h_connector = "<div style='width:70%;height:2px;background:rgba(201,162,39,.4);margin:0 auto;'></div>";

        if (!empty($org_members)):
            $roots = $org_map['root'] ?? ($org_map[null] ?? []);
        ?>
        <div style="overflow-x:auto;padding-bottom:1rem;">
        <div style="min-width:700px;text-align:center;">

        <?php foreach ($roots as $root): // Level 1: Commissioner ?>
            <div style="display:inline-block;text-align:center;width:100%;">
                <?php render_org_card($root, 'lg'); ?>
            </div>
            <?php echo $v_line; ?>

            <?php
            $level2 = $org_map[$root['id']] ?? [];
            foreach ($level2 as $l2): // Level 2: Director
            ?>
            <div style="display:inline-block;text-align:center;width:100%;">
                <?php render_org_card($l2, 'lg'); ?>
            </div>
            <?php echo $v_line; ?>

            <?php
            $level3 = $org_map[$l2['id']] ?? [];
            if (!empty($level3)):
            ?>
            <!-- H-connector for level 3 -->
            <?php echo $h_connector; ?>
            <div style="display:flex;justify-content:center;align-items:flex-start;gap:1.5rem;flex-wrap:wrap;margin-bottom:0;">
                <?php foreach ($level3 as $l3): // Level 3: Managers ?>
                <div style="display:flex;flex-direction:column;align-items:center;">
                    <div style="width:2px;height:20px;background:rgba(201,162,39,.4);"></div>
                    <?php render_org_card($l3, 'md'); ?>

                    <?php $level4 = $org_map[$l3['id']] ?? []; ?>
                    <?php if (!empty($level4)): ?>
                    <div style="width:2px;height:20px;background:rgba(201,162,39,.3);"></div>
                    <?php if (count($level4) > 1): ?>
                    <div style="width:80%;height:2px;background:rgba(201,162,39,.3);"></div>
                    <?php endif; ?>
                    <div style="display:flex;justify-content:center;gap:1rem;flex-wrap:wrap;">
                        <?php foreach ($level4 as $l4): // Level 4: Staff ?>
                        <div style="display:flex;flex-direction:column;align-items:center;">
                            <div style="width:2px;height:16px;background:rgba(201,162,39,.25);"></div>
                            <?php render_org_card($l4, 'sm'); ?>

                            <?php $level5 = $org_map[$l4['id']] ?? []; ?>
                            <?php if (!empty($level5)): ?>
                            <div style="width:2px;height:16px;background:rgba(201,162,39,.2);"></div>
                            <?php if (count($level5) > 1): ?>
                            <div style="width:80%;height:2px;background:rgba(201,162,39,.2);"></div>
                            <?php endif; ?>
                            <div style="display:flex;justify-content:center;gap:.75rem;flex-wrap:wrap;">
                                <?php foreach ($level5 as $l5): // Level 5 ?>
                                <div style="display:flex;flex-direction:column;align-items:center;">
                                    <div style="width:2px;height:14px;background:rgba(201,162,39,.15);"></div>
                                    <?php render_org_card($l5, 'sm'); ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <?php endforeach; ?>
        <?php endforeach; ?>

        </div><!-- /min-width -->
        </div><!-- /overflow-x:auto -->

        <?php else: ?>
        <p class="text-center text-muted">Struktur organisasi belum diatur. <a href="<?php echo base_url('admin/org_structure.php'); ?>">Atur di Admin Panel</a>.</p>
        <?php endif; ?>

    </div>
</section>


<!-- === NILAI PERUSAHAAN === -->
<section style="background:var(--white);padding:5rem 0;">
    <div class="container">
        <div class="text-center mb-5 reveal">
            <p class="section-tag">Budaya Kami</p>
            <h2 class="section-title">Nilai Perusahaan & Budaya Kerja</h2>
            <span class="section-divider center"></span>
        </div>
        <div class="row g-4">
            <?php
            $values = [
                ['bi-award','Integritas','Menjunjung tinggi etika bisnis, kejujuran, dan transparansi di setiap kesepakatan.'],
                ['bi-shield-heart','Keselamatan (K3)','Mengutamakan keselamatan dan kesehatan kerja karyawan di setiap area proyek.'],
                ['bi-gem','Mutu Unggul','Tidak berkompromi terhadap standar kualitas pengerjaan di setiap detail proyek.'],
                ['bi-people','Kolaborasi','Bekerja secara sinergis dengan klien, mitra bisnis, dan vendor rantai pasok.'],
            ];
            foreach ($values as $i => $v): ?>
            <div class="col-lg-3 col-sm-6 reveal" style="transition-delay:<?php echo $i * .1; ?>s">
                <div style="background:var(--off-white);border-radius:var(--radius);padding:2rem 1.75rem;text-align:center;border:1px solid rgba(11,31,58,.06);transition:all .4s var(--ease);box-shadow:var(--shadow-card);"
                     onmouseover="this.style.borderColor='rgba(201,162,39,.3)';this.style.transform='translateY(-8px)';this.style.boxShadow='0 20px 60px rgba(11,31,58,.12)'"
                     onmouseout="this.style.borderColor='rgba(11,31,58,.06)';this.style.transform='translateY(0)';this.style.boxShadow='var(--shadow-card)'">
                    <div style="width:64px;height:64px;border-radius:50%;background:var(--grad-navy);display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;">
                        <i class="bi <?php echo $v[0]; ?>" style="font-size:1.6rem;background:var(--grad-gold);-webkit-background-clip:text;-webkit-text-fill-color:transparent;"></i>
                    </div>
                    <h4 style="font-size:1rem;font-weight:800;color:var(--navy);margin-bottom:.75rem;"><?php echo $v[1]; ?></h4>
                    <p style="font-size:.875rem;margin:0;line-height:1.7;"><?php echo $v[2]; ?></p>
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
            <?php
            $raw_certs = explode("\n", $profile['certificates'] ?? "ISO 9001:2015\nISO 14001:2015\nISO 45001:2018\nSMK3");
            $certs = [];
            foreach ($raw_certs as $rc) {
                if(trim($rc) !== '') {
                    $certs[] = [trim($rc), 'Standar Internasional', 'bi-patch-check-fill'];
                }
            }
            foreach ($certs as $c): ?>
            <div class="col-lg-3 col-sm-6">
                <div style="background:rgba(255,255,255,.04);border:1px solid rgba(201,162,39,.2);border-radius:var(--radius);padding:2rem 1.5rem;transition:all .4s var(--ease);"
                     onmouseover="this.style.background='rgba(201,162,39,.08)';this.style.borderColor='rgba(201,162,39,.4)';this.style.transform='translateY(-6px)'"
                     onmouseout="this.style.background='rgba(255,255,255,.04)';this.style.borderColor='rgba(201,162,39,.2)';this.style.transform='translateY(0)'">
                    <i class="bi <?php echo $c[2]; ?>" style="font-size:2rem;background:var(--grad-gold);-webkit-background-clip:text;-webkit-text-fill-color:transparent;display:block;margin-bottom:1rem;"></i>
                    <div style="font-size:1.15rem;font-weight:900;background:var(--grad-gold);-webkit-background-clip:text;-webkit-text-fill-color:transparent;margin-bottom:.4rem;"><?php echo $c[0]; ?></div>
                    <div style="color:rgba(255,255,255,.45);font-size:.82rem;"><?php echo $c[1]; ?></div>
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
