<?php
require_once __DIR__ . '/config/functions.php';

$slug = isset($_GET['slug']) ? sanitize($_GET['slug']) : null;
$article = null;

if ($slug) {
    // Detail View: Find published article matching slug
    try {
        $stmt = $db->prepare("SELECT * FROM articles WHERE slug = ? AND status = 'published'");
        $stmt->execute([$slug]);
        $article = $stmt->fetch();
    } catch (Exception $e) {}
    
    // Redirect if article not found
    if (!$article) {
        header("Location: " . base_url('berita.php'));
        exit();
    }
    
    $page_title = $article['title'];
    $page_desc = strip_tags(substr($article['content'], 0, 160));
} else {
    // List View
    $page_title = "Berita & Kegiatan Perusahaan — PT. Hastra Karya Persada";
    $page_desc = "Ikuti kumpulan berita, press release, kegiatan corporate, dan artikel edukasi dari PT. Hastra Karya Persada.";
}

include __DIR__ . '/includes/header.php';
?>

<!-- BREADCRUMB -->
<section class="breadcrumb-section">
    <div class="container" style="position:relative;z-index:1;">
        <div class="row align-items-end">
            <div class="col-lg-8">
                <p style="color:var(--gold-light);font-size:.75rem;letter-spacing:3px;text-transform:uppercase;font-weight:700;margin-bottom:.75rem;">Berita &amp; Rilis</p>
                <h1 style="font-family:var(--font-head);font-size:clamp(1.8rem,3.5vw,2.8rem);font-weight:800;color:#fff;line-height:1.15;margin-bottom:1rem;">
                    <?php echo $article ? sanitize($article['title']) : 'Kabar &amp; <em style="font-style:italic;background:var(--grad-gold);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">Kegiatan</em>'; ?>
                </h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-custom mb-0">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>">Home</a></li>
                        <?php if ($article): ?>
                            <li class="breadcrumb-item"><a href="<?php echo base_url('berita.php'); ?>">Berita</a></li>
                            <li class="breadcrumb-item active"><?php echo sanitize(substr($article['title'], 0, 30)) . '...'; ?></li>
                        <?php else: ?>
                            <li class="breadcrumb-item active">Berita</li>
                        <?php endif; ?>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<?php if ($article): ?>
    <!-- ================= DETAIL VIEW ================= -->
    <section style="background:var(--white);padding:5rem 0;">
        <div class="container">
            <div class="row g-5">
                <!-- Main Content -->
                <div class="col-lg-8 reveal-left">
                    <?php 
                    $art_img = base_url('assets/uploads/articles/' . $article['image']);
                    if (empty($article['image']) || !file_exists(__DIR__ . '/assets/uploads/articles/' . $article['image'])) {
                        if ($article['slug'] === 'pt-hastra-karya-persada-raih-sertifikasi-iso-9001-2015-sistem-manajemen-mutu') {
                            $art_img = "https://images.unsplash.com/photo-1590283603385-17ffb3a7f29f?auto=format&fit=crop&w=1200&q=80";
                        } else {
                            $art_img = "https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=1200&q=80";
                        }
                    }
                    ?>
                    <div style="border-radius:var(--radius);overflow:hidden;margin-bottom:2.5rem;box-shadow:var(--shadow-hover);">
                        <img src="<?php echo $art_img; ?>" class="w-100" alt="<?php echo sanitize($article['title']); ?>" style="height:460px;object-fit:cover;display:block;">
                    </div>
                    
                    <!-- Article Meta -->
                    <div style="display:flex;flex-wrap:wrap;gap:1.5rem;font-size:.85rem;color:var(--gray-text);margin-bottom:2rem;padding-bottom:1.25rem;border-bottom:1px solid rgba(11,31,58,.06);">
                        <span><i class="bi-calendar3 text-gold me-2"></i> <?php echo format_date_id($article['created_at']); ?></span>
                        <span><i class="bi-person text-gold me-2"></i> <?php echo sanitize($article['author']); ?></span>
                        <span><i class="bi-shield-check text-gold me-2"></i> Rilis Resmi Korporat</span>
                    </div>

                    <!-- Content -->
                    <div style="font-size:1.05rem;line-height:1.9;color:var(--navy);margin-bottom:3rem;">
                        <?php echo $article['content']; ?>
                    </div>

                    <!-- Tags -->
                    <?php if (!empty($article['tags'])): ?>
                        <div style="margin-top:3rem;padding-top:1.5rem;border-top:1px solid rgba(11,31,58,.06);">
                            <span style="font-weight:700;color:var(--navy);font-size:.88rem;margin-right:.5rem;">Tagar:</span>
                            <?php 
                            $tags = explode(",", $article['tags']);
                            foreach ($tags as $tag):
                            ?>
                                <span style="display:inline-block;font-size:.8rem;padding:.4rem 1rem;background:var(--off-white);border:1px solid rgba(11,31,58,.08);color:var(--navy);border-radius:100px;margin-right:.4rem;font-weight:500;"><?php echo sanitize(trim($tag)); ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div style="margin-top:4rem;">
                        <a href="<?php echo base_url('berita.php'); ?>" style="display:inline-flex;align-items:center;gap:.5rem;color:var(--navy);font-weight:600;text-decoration:none;font-size:.9rem;" onmouseover="this.style.color='var(--gold)'" onmouseout="this.style.color='var(--navy)'">
                            <i class="bi-arrow-left"></i> Kembali ke Berita
                        </a>
                    </div>
                </div>

                <!-- Sidebar News -->
                <div class="col-lg-4">
                    <div class="position-sticky" style="top:100px;display:flex;flex-direction:column;gap:1.5rem;">
                        <!-- Recent Articles Widget -->
                        <div class="sidebar-widget">
                            <span class="sidebar-widget-title">Berita Terbaru</span>
                            <div style="display:flex;flex-direction:column;gap:1.25rem;">
                                <?php 
                                $recent_articles = [];
                                try {
                                    $stmt = $db->prepare("SELECT title, slug, created_at, image FROM articles WHERE id != ? AND status = 'published' ORDER BY created_at DESC LIMIT 3");
                                    $stmt->execute([$article['id']]);
                                    $recent_articles = $stmt->fetchAll();
                                } catch (Exception $e) {}
                                
                                foreach ($recent_articles as $ra):
                                    $ra_img = base_url('assets/uploads/articles/' . $ra['image']);
                                    if (empty($ra['image']) || !file_exists(__DIR__ . '/assets/uploads/articles/' . $ra['image'])) {
                                        if ($ra['slug'] === 'pt-hastra-karya-persada-raih-sertifikasi-iso-9001-2015-sistem-manajemen-mutu') {
                                            $ra_img = "https://images.unsplash.com/photo-1590283603385-17ffb3a7f29f?auto=format&fit=crop&w=150&q=80";
                                        } else {
                                            $ra_img = "https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=150&q=80";
                                        }
                                    }
                                ?>
                                    <div style="display:flex;align-items:center;gap:1rem;">
                                        <img src="<?php echo $ra_img; ?>" class="rounded-3" alt="<?php echo sanitize($ra['title']); ?>" style="width:70px;height:70px;object-fit:cover;flex-shrink:0;">
                                        <div>
                                            <a href="<?php echo base_url('berita/' . $ra['slug']); ?>" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;text-decoration:none;font-weight:700;font-size:.85rem;color:var(--navy);line-height:1.35;margin-bottom:.25rem;" onmouseover="this.style.color='var(--gold)'" onmouseout="this.style.color='var(--navy)'"><?php echo sanitize($ra['title']); ?></a>
                                            <span style="font-size:0.72rem;color:var(--gray-text);"><i class="bi-calendar3 text-gold me-1"></i> <?php echo format_date_id($ra['created_at']); ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Humas Widget -->
                        <div class="sidebar-cta">
                            <i class="bi-headset" style="font-size:2rem;background:var(--grad-gold);-webkit-background-clip:text;-webkit-text-fill-color:transparent;display:block;margin-bottom:1rem;"></i>
                            <h4 style="color:#fff;font-size:1.1rem;margin-bottom:.5rem;">Hubungan Humas &amp; Media</h4>
                            <p style="color:rgba(255,255,255,.5);font-size:.85rem;margin-bottom:1.5rem;">Untuk keperluan wawancara pers, kunjungan kerja, atau rilis media, silakan hubungi Humas kami.</p>
                            <a href="<?php echo base_url('hubungi-kami.php'); ?>" class="btn-gold" style="display:inline-flex;width:100%;justify-content:center;">Kontak Humas</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php else: ?>
    <!-- ================= LIST VIEW ================= -->
    <section style="background:var(--white);padding:5rem 0;">
        <div class="container">
            <div class="row justify-content-center mb-5 reveal">
                <div class="col-lg-6 text-center">
                    <p class="section-tag">Rilis Korporat</p>
                    <h2 class="section-title">Kabar &amp; Media Center</h2>
                    <span class="section-divider center"></span>
                </div>
            </div>
            
            <div class="row g-4">
                <?php 
                $all_articles = [];
                try {
                    $all_articles = $db->query("SELECT * FROM articles WHERE status = 'published' ORDER BY created_at DESC")->fetchAll();
                } catch (Exception $e) {}

                if (count($all_articles) > 0):
                    foreach ($all_articles as $i => $a): 
                        $art_img = base_url('assets/uploads/articles/' . $a['image']);
                        if (empty($a['image']) || !file_exists(__DIR__ . '/assets/uploads/articles/' . $a['image'])) {
                            if ($a['slug'] === 'pt-hastra-karya-persada-raih-sertifikasi-iso-9001-2015-sistem-manajemen-mutu') {
                                    $art_img = "https://images.unsplash.com/photo-1590283603385-17ffb3a7f29f?auto=format&fit=crop&w=800&q=80";
                            } else {
                                $art_img = "https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=800&q=80";
                            }
                        }
                ?>
                    <div class="col-lg-4 col-md-6 reveal" style="transition-delay:<?php echo ($i * 0.12); ?>s">
                        <div style="background:var(--white);border:1px solid rgba(11,31,58,.07);border-radius:var(--radius);overflow:hidden;transition:all .4s var(--ease);box-shadow:var(--shadow-card);height:100%;display:flex;flex-direction:column;"
                             onmouseover="this.style.transform='translateY(-6px)';this.style.boxShadow='var(--shadow-hover)'"
                             onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='var(--shadow-card)'">
                            <img src="<?php echo $art_img; ?>" style="width:100%;height:230px;object-fit:cover;display:block;" alt="<?php echo sanitize($a['title']); ?>">
                            <div style="padding:2rem;flex-grow:1;display:flex;flex-direction:column;justify-content:space-between;">
                                <div>
                                    <div style="font-size:.78rem;color:var(--gray-text);margin-bottom:.75rem;"><i class="bi-calendar3 text-gold me-2"></i> <?php echo format_date_id($a['created_at']); ?></div>
                                    <h4 style="font-family:var(--font-head);font-size:1.25rem;color:var(--navy);margin-bottom:1rem;line-height:1.45;"><a href="<?php echo base_url('berita/' . $a['slug']); ?>" style="text-decoration:none;color:var(--navy);" onmouseover="this.style.color='var(--gold)'" onmouseout="this.style.color='var(--navy)'"><?php echo sanitize($a['title']); ?></a></h4>
                                    <p style="font-size:.88rem;line-height:1.65;color:var(--gray-text);margin-bottom:1.5rem;"><?php echo sanitize(substr(strip_tags($a['content']), 0, 110)) . '...'; ?></p>
                                </div>
                                <a href="<?php echo base_url('berita/' . $a['slug']); ?>" class="btn-gold w-100 justify-content-center" style="font-size:.82rem;padding:.75rem 1.5rem;">Baca Selengkapnya <i class="bi-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                <?php 
                    endforeach;
                else:
                ?>
                    <div class="col-12 text-center py-5">
                        <i class="bi-newspaper text-muted display-1"></i>
                        <p class="mt-3 text-muted">Belum ada berita yang diterbitkan saat ini.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
