<?php
include __DIR__ . '/includes/header.php';

$success_msg = '';
$error_msg = '';

// Process Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        $error_msg = 'Token CSRF tidak valid. Silakan muat ulang halaman.';
    } else {
        // Prepare update query
        try {
            // Handle file uploads
            $upload_dir = __DIR__ . '/../assets/img/';
            
            if (!empty($_FILES['site_logo']['tmp_name'])) {
                $ext = strtolower(pathinfo($_FILES['site_logo']['name'], PATHINFO_EXTENSION));
                $filename = 'logo_' . time() . '.' . $ext;
                if (move_uploaded_file($_FILES['site_logo']['tmp_name'], $upload_dir . $filename)) {
                    $_POST['site_logo'] = $filename;
                }
            }
            if (!empty($_FILES['site_favicon']['tmp_name'])) {
                $ext = strtolower(pathinfo($_FILES['site_favicon']['name'], PATHINFO_EXTENSION));
                $filename = 'favicon_' . time() . '.' . $ext;
                if (move_uploaded_file($_FILES['site_favicon']['tmp_name'], $upload_dir . $filename)) {
                    $_POST['site_favicon'] = $filename;
                }
            }

            $stmt = $db->prepare("INSERT INTO settings (key_name, key_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE key_value = ?");
            
            // Loop through all keys from POST request except csrf_token and save_settings
            foreach ($_POST as $key => $value) {
                if ($key === 'csrf_token' || $key === 'save_settings') continue;
                $stmt->execute([$key, $value, $value]);
            }
            
            $success_msg = 'Pengaturan website berhasil diperbarui!';
            
            // Reload global settings
            $stmt_reload = $db->query("SELECT key_name, key_value FROM settings");
            while ($row = $stmt_reload->fetch()) {
                $settings[$row['key_name']] = $row['key_value'];
            }
        } catch (Exception $e) {
            $error_msg = 'Gagal menyimpan pengaturan: ' . $e->getMessage();
        }
    }
}
?>

<!-- Header -->
<div class="mb-4">
    <h1 class="h3 fw-bold text-primary mb-0" style="color: var(--a-navy) !important;">Pengaturan Website & SEO</h1>
    <span class="small text-muted" style="color: var(--a-gray) !important;">Ubah konfigurasi SEO, informasi kontak perusahaan, tautan media sosial, dan peta Google Maps.</span>
</div>

<!-- Status Alerts -->
<?php if (!empty($success_msg)): ?>
    <div class="alert-admin alert-admin-success mb-4"><i class="bi-check-circle-fill"></i> <?php echo $success_msg; ?></div>
<?php endif; ?>
<?php if (!empty($error_msg)): ?>
    <div class="alert-admin alert-admin-danger mb-4"><i class="bi-exclamation-triangle-fill"></i> <?php echo $error_msg; ?></div>
<?php endif; ?>

<!-- Tabs Navigation -->
<div class="admin-tabs mb-4" id="settingsTab" role="tablist">
    <button class="admin-tab active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab" aria-controls="general" aria-selected="true">Info Umum &amp; SEO</button>
    <button class="admin-tab" id="contacts-tab" data-bs-toggle="tab" data-bs-target="#contacts" type="button" role="tab" aria-controls="contacts" aria-selected="false">Kontak &amp; Lokasi</button>
    <button class="admin-tab" id="social-tab" data-bs-toggle="tab" data-bs-target="#social" type="button" role="tab" aria-controls="social" aria-selected="false">Media Sosial</button>
    <button class="admin-tab" id="smtp-tab" data-bs-toggle="tab" data-bs-target="#smtp" type="button" role="tab" aria-controls="smtp" aria-selected="false">⚙️ Pengaturan Email (SMTP)</button>
</div>

<div class="admin-card mb-5">
    <div class="admin-card-body">
        <form action="" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">
        
        <div class="tab-content" id="settingsTabContent">
            
            <!-- Tab 1: General & SEO Settings -->
            <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label for="site_name" class="form-label-admin">Nama Perusahaan</label>
                        <input type="text" class="form-control-admin" id="site_name" name="site_name" required value="<?php echo sanitize($settings['site_name'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="site_tagline" class="form-label-admin">Tagline Perusahaan</label>
                        <input type="text" class="form-control-admin" id="site_tagline" name="site_tagline" required value="<?php echo sanitize($settings['site_tagline'] ?? ''); ?>">
                    </div>
                    <div class="col-12">
                        <label for="site_description" class="form-label-admin">Meta Deskripsi Website (SEO)</label>
                        <textarea class="form-control-admin" id="site_description" name="site_description" rows="3" required><?php echo sanitize($settings['site_description'] ?? ''); ?></textarea>
                    </div>
                    <div class="col-12">
                        <label for="site_keywords" class="form-label-admin">Meta Keywords (SEO - Pisahkan dengan koma)</label>
                        <input type="text" class="form-control-admin" id="site_keywords" name="site_keywords" required value="<?php echo sanitize($settings['site_keywords'] ?? ''); ?>">
                    </div>

                    <div class="col-12 mt-4">
                        <h6 style="font-size:.9rem;font-weight:700;color:var(--a-navy);border-bottom:1px solid var(--a-border);padding-bottom:.75rem;margin-bottom:0;">Identitas Visual (Logo & Favicon)</h6>
                    </div>
                    <div class="col-md-6">
                        <label for="site_logo" class="form-label-admin">Logo Website</label>
                        <input type="file" class="form-control-admin" id="site_logo" name="site_logo" accept="image/*">
                        <small class="text-muted d-block mt-1">Biarkan kosong jika tidak ingin mengubah. Saat ini: <a href="<?php echo base_url('assets/img/' . ($settings['site_logo'] ?? 'logo.png')); ?>" target="_blank">Lihat Logo</a></small>
                    </div>
                    <div class="col-md-6">
                        <label for="site_favicon" class="form-label-admin">Ikon Web (Favicon)</label>
                        <input type="file" class="form-control-admin" id="site_favicon" name="site_favicon" accept="image/*,.ico,.svg">
                        <small class="text-muted d-block mt-1">Biarkan kosong jika tidak ingin mengubah. Saat ini: <a href="<?php echo base_url('assets/img/' . ($settings['site_favicon'] ?? 'favicon.svg')); ?>" target="_blank">Lihat Ikon</a></small>
                    </div>

                    <div class="col-12 mt-4">
                        <h6 style="font-size:.9rem;font-weight:700;color:var(--a-navy);border-bottom:1px solid var(--a-border);padding-bottom:.75rem;margin-bottom:0;">Konten Beranda (Home)</h6>
                    </div>
                    <div class="col-md-12">
                        <label for="home_about_title" class="form-label-admin">Judul Tentang Perusahaan</label>
                        <input type="text" class="form-control-admin" id="home_about_title" name="home_about_title" placeholder="Contoh: Membangun Masa Depan dengan <em>Presisi</em>" value="<?php echo htmlspecialchars($settings['home_about_title'] ?? 'Membangun Masa Depan dengan <em>Presisi</em>'); ?>">
                        <small class="text-muted">Gunakan tag <code>&lt;em&gt;Teks&lt;/em&gt;</code> untuk memberikan efek teks berwarna emas.</small>
                    </div>
                    <div class="col-md-12">
                        <label for="home_about_text" class="form-label-admin">Teks Tentang Perusahaan</label>
                        <textarea class="form-control-admin" id="home_about_text" name="home_about_text" rows="3"><?php echo htmlspecialchars($settings['home_about_text'] ?? 'Sejak 2020, PT. Hastra Karya Persada telah menjadi mitra pembangunan nasional yang dipercaya. Kami menghadirkan standar konstruksi, pengadaan, dan konsultansi kelas dunia dengan tim profesional bersertifikat internasional.'); ?></textarea>
                    </div>
                    
                    <div class="col-12 mt-4">
                        <h6 style="font-size:.9rem;font-weight:700;color:var(--a-navy);border-bottom:1px solid var(--a-border);padding-bottom:.75rem;margin-bottom:0;">Statistik Halaman Utama</h6>
                    </div>
                    <div class="col-md-3">
                        <label for="stat_proyek" class="form-label-admin">Proyek Selesai</label>
                        <input type="number" class="form-control-admin" id="stat_proyek" name="stat_proyek" required value="<?php echo sanitize($settings['stat_proyek'] ?? '150'); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="stat_mitra" class="form-label-admin">Mitra & Klien</label>
                        <input type="number" class="form-control-admin" id="stat_mitra" name="stat_mitra" required value="<?php echo sanitize($settings['stat_mitra'] ?? '80'); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="stat_pengalaman" class="form-label-admin">Tahun Pengalaman</label>
                        <input type="number" class="form-control-admin" id="stat_pengalaman" name="stat_pengalaman" required value="<?php echo sanitize($settings['stat_pengalaman'] ?? '15'); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="stat_kepuasan" class="form-label-admin">Tingkat Kepuasan (%)</label>
                        <input type="number" class="form-control-admin" id="stat_kepuasan" name="stat_kepuasan" required value="<?php echo sanitize($settings['stat_kepuasan'] ?? '99'); ?>">
                    </div>
                </div>
            </div>
            
            <!-- Tab 2: Contacts & Google Maps Settings -->
            <div class="tab-pane fade" id="contacts" role="tabpanel" aria-labelledby="contacts-tab">
                <div class="row g-4">
                    <div class="col-md-4">
                        <label for="phone" class="form-label-admin">No. Telepon Kantor</label>
                        <input type="text" class="form-control-admin" id="phone" name="phone" required value="<?php echo sanitize($settings['phone'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="email" class="form-label-admin">Alamat E-mail Hubungan Bisnis</label>
                        <input type="email" class="form-control-admin" id="email" name="email" required value="<?php echo sanitize($settings['email'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="whatsapp" class="form-label-admin">No. WhatsApp Hubungan Cepat (Format Internasional)</label>
                        <input type="text" class="form-control-admin" id="whatsapp" name="whatsapp" required value="<?php echo sanitize($settings['whatsapp'] ?? ''); ?>">
                    </div>
                    <div class="col-12">
                        <label for="whatsapp_text" class="form-label-admin">Pesan Teks Default WhatsApp</label>
                        <textarea class="form-control-admin" id="whatsapp_text" name="whatsapp_text" rows="2" required placeholder="Contoh: Halo PT. Hastra Karya Persada, saya ingin berkonsultasi..."><?php echo sanitize($settings['whatsapp_text'] ?? 'Halo PT. Hastra Karya Persada, saya ingin berkonsultasi.'); ?></textarea>
                    </div>
                    <div class="col-12">
                        <label for="address" class="form-label-admin">Alamat Lengkap Kantor Pusat</label>
                        <textarea class="form-control-admin" id="address" name="address" rows="2" required><?php echo sanitize($settings['address'] ?? ''); ?></textarea>
                    </div>
                    <div class="col-12">
                        <label for="google_maps" class="form-label-admin">Tautan atau Embed Code Google Maps</label>
                        <textarea class="form-control-admin" id="google_maps" name="google_maps" rows="4"><?php echo $settings['google_maps']; ?></textarea>
                        <span class="text-muted d-block mt-1" style="font-size: 0.75rem;">Anda bisa menempelkan (paste) tautan (link) URL Google Maps biasa, atau kode sematan (embed iframe) dari Google Maps.</span>
                    </div>
                </div>
            </div>
            
            <!-- Tab 3: Social Media Links -->
            <div class="tab-pane fade" id="social" role="tabpanel" aria-labelledby="social-tab">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label for="social_facebook" class="form-label-admin">Tautan URL Facebook</label>
                        <input type="url" class="form-control-admin" id="social_facebook" name="social_facebook" value="<?php echo sanitize($settings['social_facebook'] ?? ''); ?>" placeholder="https://facebook.com/company">
                    </div>
                    <div class="col-md-6">
                        <label for="social_twitter" class="form-label-admin">Tautan URL Twitter (X)</label>
                        <input type="url" class="form-control-admin" id="social_twitter" name="social_twitter" value="<?php echo sanitize($settings['social_twitter'] ?? ''); ?>" placeholder="https://twitter.com/company">
                    </div>
                    <div class="col-md-6">
                        <label for="social_instagram" class="form-label-admin">Tautan URL Instagram</label>
                        <input type="url" class="form-control-admin" id="social_instagram" name="social_instagram" value="<?php echo sanitize($settings['social_instagram'] ?? ''); ?>" placeholder="https://instagram.com/company">
                    </div>
                    <div class="col-md-6">
                        <label for="social_linkedin" class="form-label-admin">Tautan URL LinkedIn</label>
                        <input type="url" class="form-control-admin" id="social_linkedin" name="social_linkedin" value="<?php echo sanitize($settings['social_linkedin'] ?? ''); ?>" placeholder="https://linkedin.com/company">
                    </div>
                </div>
            </div>

            <!-- Tab 4: SMTP Email Settings -->
            <div class="tab-pane fade" id="smtp" role="tabpanel" aria-labelledby="smtp-tab">
                <div class="mb-4 p-3 rounded" style="background:rgba(212,167,78,.08);border:1px solid rgba(212,167,78,.3);">
                    <p class="mb-1 small fw-bold" style="color:var(--a-navy);"><i class="bi-info-circle-fill me-2" style="color:var(--a-gold);"></i>Cara mendapatkan kredensial SMTP Hostinger:</p>
                    <ol class="mb-0 small text-muted ps-3">
                        <li>Login ke <strong>hPanel Hostinger</strong> → <strong>Email</strong> → <strong>Akun Email</strong></li>
                        <li>Buat atau gunakan email <code>info@hastrakaryapersada.com</code></li>
                        <li>SMTP Host: <code>smtp.hostinger.com</code> | Port: <code>587</code> | Enkripsi: <code>tls</code></li>
                        <li>Username = alamat email lengkap, Password = password email tersebut</li>
                    </ol>
                </div>
                <div class="row g-4">
                    <div class="col-md-6">
                        <label for="smtp_host" class="form-label-admin">SMTP Host</label>
                        <input type="text" class="form-control-admin" id="smtp_host" name="smtp_host"
                               value="<?php echo sanitize($settings['smtp_host'] ?? 'smtp.hostinger.com'); ?>"
                               placeholder="smtp.hostinger.com">
                    </div>
                    <div class="col-md-3">
                        <label for="smtp_port" class="form-label-admin">SMTP Port</label>
                        <input type="number" class="form-control-admin" id="smtp_port" name="smtp_port"
                               value="<?php echo sanitize($settings['smtp_port'] ?? '587'); ?>"
                               placeholder="587">
                    </div>
                    <div class="col-md-3">
                        <label for="smtp_encryption" class="form-label-admin">Enkripsi</label>
                        <select class="form-control-admin" id="smtp_encryption" name="smtp_encryption">
                            <option value="tls" <?php echo ($settings['smtp_encryption'] ?? 'tls') === 'tls' ? 'selected' : ''; ?>>TLS (Port 587) — Disarankan</option>
                            <option value="ssl" <?php echo ($settings['smtp_encryption'] ?? '') === 'ssl' ? 'selected' : ''; ?>>SSL (Port 465)</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="smtp_username" class="form-label-admin">SMTP Username (Alamat Email Pengirim)</label>
                        <input type="email" class="form-control-admin" id="smtp_username" name="smtp_username"
                               value="<?php echo sanitize($settings['smtp_username'] ?? ''); ?>"
                               placeholder="info@hastrakaryapersada.com">
                    </div>
                    <div class="col-md-6">
                        <label for="smtp_password" class="form-label-admin">SMTP Password</label>
                        <input type="password" class="form-control-admin" id="smtp_password" name="smtp_password"
                               value="<?php echo sanitize($settings['smtp_password'] ?? ''); ?>"
                               placeholder="Password email Hostinger Anda" autocomplete="new-password">
                        <small class="text-muted">Password tersimpan terenkripsi di database.</small>
                    </div>
                </div>
            </div>
            
        </div>
        
        <div class="mt-4 pt-4 border-top" style="border-color: var(--a-border) !important;">
            <button type="submit" name="save_settings" class="btn-admin-primary"><i class="bi-check-all"></i> Simpan Semua Pengaturan</button>
        </div>
    </form>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
