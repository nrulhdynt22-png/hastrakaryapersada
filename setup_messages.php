<?php
/**
 * SETUP SCRIPT — Jalankan sekali, lalu HAPUS file ini!
 * Buat tabel contact_messages di database Hostinger.
 * Akses via browser: https://yourdomain.com/setup_messages.php
 */
require_once __DIR__ . '/config/functions.php';

$sql = "
CREATE TABLE IF NOT EXISTS `contact_messages` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NOT NULL,
    `subject` VARCHAR(255) NOT NULL,
    `message` TEXT NOT NULL,
    `is_read` TINYINT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

try {
    $db->exec($sql);
    echo '<div style="font-family:sans-serif;max-width:600px;margin:3rem auto;padding:2rem;border:2px solid #22c55e;border-radius:12px;background:#f0fdf4;">';
    echo '<h2 style="color:#16a34a;">✅ Berhasil!</h2>';
    echo '<p>Tabel <code>contact_messages</code> berhasil dibuat di database Hostinger.</p>';
    echo '<p style="color:#dc2626;"><strong>⚠️ PENTING: Hapus file <code>setup_messages.php</code> ini sekarang!</strong></p>';
    echo '</div>';
} catch (Exception $e) {
    echo '<div style="font-family:sans-serif;max-width:600px;margin:3rem auto;padding:2rem;border:2px solid #ef4444;border-radius:12px;background:#fef2f2;">';
    echo '<h2 style="color:#dc2626;">❌ Error</h2>';
    echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '</div>';
}
