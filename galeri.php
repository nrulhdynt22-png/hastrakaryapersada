<?php
$page_title = "Galeri Dokumentasi — PT. Hastra Karya Persada";
$page_desc = "Koleksi foto kegiatan proyek, event korporasi, dan aktivitas tim profesional PT. Hastra Karya Persada.";
include __DIR__ . '/includes/header.php';

// Fetch gallery items & auto-seed if empty
$gallery = [];
try {
    $gallery = $db->query("SELECT * FROM gallery ORDER BY id DESC")->fetchAll();
    
    if (count($gallery) === 0) {
        $seed_gallery = [
            ['Rapat Kerja Evaluasi Triwulan II 2025', 'kegiatan', 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=800&q=80'],
            ['Proyek Konstruksi Menara Rasuna', 'proyek', 'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?auto=format&fit=crop&w=800&q=80'],
            ['Ulang Tahun PT. Hastra Karya Persada Ke-5', 'event', 'https://images.unsplash.com/photo-1511578314322-379afb476865?auto=format&fit=crop&w=800&q=80'],
            ['Pelatihan Sertifikasi Ahli K3 Konstruksi', 'kegiatan', 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=800&q=80'],
            ['Pembangunan Jembatan Tol Lingkar Luar', 'proyek', 'https://images.unsplash.com/photo-1590069261209-f8e9b8642343?auto=format&fit=crop&w=800&q=80'],
            ['Customer Gathering & Corporate Award 2025', 'event', 'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?auto=format&fit=crop&w=800&q=80']
        ];
        
        $insert_stmt = $db->prepare("INSERT INTO gallery (title, category, image) VALUES (?, ?, ?)");
        foreach ($seed_gallery as $row) {
            $insert_stmt->execute($row);
        }
        $gallery = $db->query("SELECT * FROM gallery ORDER BY id DESC")->fetchAll();
    }
} catch (Exception $e) {}
?>

<!-- BREADCRUMB -->
<section class="breadcrumb-section">
    <div class="container" style="position:relative;z-index:1;">
        <div class="row align-items-end">
            <div class="col-lg-7">
                <p style="color:var(--gold-light);font-size:.75rem;letter-spacing:3px;text-transform:uppercase;font-weight:700;margin-bottom:.75rem;">Dokumentasi Visual</p>
                <h1 style="font-family:var(--font-head);font-size:clamp(2.2rem,4vw,3.5rem);font-weight:800;color:#fff;line-height:1.1;margin-bottom:1rem;">
                    Galeri <em style="font-style:italic;background:var(--grad-gold);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">Kegiatan</em>
                </h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-custom mb-0">
                        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>">Home</a></li>
                        <li class="breadcrumb-item active">Galeri</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Gallery Section -->
<section style="background:var(--white);padding:6rem 0;">
    <div class="container">
        
        <!-- Filter Tabs -->
        <div class="text-center mb-5 reveal">
            <span class="section-tag">Foto Kegiatan</span>
            <h2 class="section-title">Dokumentasi Korporat &amp; Proyek</h2>
            <span class="section-divider center mb-4"></span>
            
            <div style="display:flex;justify-content:center;flex-wrap:wrap;gap:.5rem;margin-top:2rem;">
                <button class="filter-btn active" data-filter="all">Semua Foto</button>
                <button class="filter-btn" data-filter="kegiatan">Kegiatan Tim</button>
                <button class="filter-btn" data-filter="proyek">Proyek Lapangan</button>
                <button class="filter-btn" data-filter="event">Acara &amp; Event</button>
            </div>
        </div>
        
        <!-- Gallery Grid -->
        <div class="row g-4" id="gallery-grid">
            <?php 
            foreach ($gallery as $i => $item): 
                $img_url = $item['image'];
                if (strpos($img_url, 'http') !== 0) {
                    $img_url = base_url('assets/uploads/gallery/' . $item['image']);
                }
            ?>
                <div class="col-lg-4 col-md-6 reveal" data-category="<?php echo sanitize($item['category']); ?>" style="transition-delay:<?php echo ($i % 6) * .08; ?>s">
                    <a href="<?php echo $img_url; ?>" data-lightbox="<?php echo $img_url; ?>" class="text-decoration-none d-block">
                        <div class="portfolio-item">
                            <img src="<?php echo $img_url; ?>" class="portfolio-img" alt="<?php echo sanitize($item['title']); ?>" loading="lazy" style="height:300px;object-fit:cover;">
                            <div class="portfolio-overlay">
                                <span class="portfolio-category">
                                    <?php 
                                    if ($item['category'] === 'kegiatan') echo 'Kegiatan Tim';
                                    elseif ($item['category'] === 'proyek') echo 'Proyek Lapangan';
                                    elseif ($item['category'] === 'event') echo 'Event Korporat';
                                    ?>
                                </span>
                                <h4 class="portfolio-title"><?php echo sanitize($item['title']); ?></h4>
                                <span class="btn-gold" style="font-size:.78rem;padding:.5rem 1.25rem;margin-top:1.25rem;box-shadow:none;">
                                    <i class="bi-zoom-in"></i> Perbesar Gambar
                                </span>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
