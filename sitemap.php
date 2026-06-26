<?php
header("Content-Type: application/xml; charset=utf-8");
require_once __DIR__ . '/config/functions.php';

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc><?php echo base_url(); ?></loc>
        <priority>1.0</priority>
        <changefreq>daily</changefreq>
    </url>
    <url>
        <loc><?php echo base_url('tentang.php'); ?></loc>
        <priority>0.8</priority>
        <changefreq>monthly</changefreq>
    </url>
    <url>
        <loc><?php echo base_url('layanan.php'); ?></loc>
        <priority>0.8</priority>
        <changefreq>weekly</changefreq>
    </url>
    <url>
        <loc><?php echo base_url('portofolio.php'); ?></loc>
        <priority>0.8</priority>
        <changefreq>weekly</changefreq>
    </url>
    <url>
        <loc><?php echo base_url('hubungi-kami.php'); ?></loc>
        <priority>0.8</priority>
        <changefreq>monthly</changefreq>
    </url>
    
    <!-- Dynamic Services -->
    <?php
    try {
        $stmt = $db->query("SELECT slug FROM services");
        while ($row = $stmt->fetch()) {
            echo '<url>';
            echo '<loc>' . base_url('layanan/' . $row['slug']) . '</loc>';
            echo '<priority>0.7</priority>';
            echo '<changefreq>monthly</changefreq>';
            echo '</url>';
        }
    } catch (Exception $e) {}
    ?>
    
    <!-- Dynamic Portfolio -->
    <?php
    try {
        $stmt = $db->query("SELECT slug FROM portfolio");
        while ($row = $stmt->fetch()) {
            echo '<url>';
            echo '<loc>' . base_url('portofolio/' . $row['slug']) . '</loc>';
            echo '<priority>0.7</priority>';
            echo '<changefreq>monthly</changefreq>';
            echo '</url>';
        }
    } catch (Exception $e) {}
    ?>
    
    <!-- Dynamic Articles -->
    <?php
    try {
        $stmt = $db->query("SELECT slug FROM articles WHERE status = 'published'");
        while ($row = $stmt->fetch()) {
            echo '<url>';
            echo '<loc>' . base_url('berita/' . $row['slug']) . '</loc>';
            echo '<priority>0.6</priority>';
            echo '<changefreq>weekly</changefreq>';
            echo '</url>';
        }
    } catch (Exception $e) {}
    ?>
</urlset>
