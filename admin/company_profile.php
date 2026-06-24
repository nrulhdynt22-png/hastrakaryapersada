<?php
include __DIR__ . '/includes/header.php';

$success_msg = '';
$error_msg = '';

// Fetch Profile Data
$profile = [];
try {
    $stmt = $db->query("SELECT * FROM company_profile WHERE id = 1");
    $profile = $stmt->fetch();
} catch (Exception $e) {}

// Process Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        $error_msg = 'Token CSRF tidak valid. Silakan muat ulang halaman.';
    } else {
        $director_speech = sanitize($_POST['director_speech']);
        $profile_text = sanitize($_POST['profile_text']);
        $legality = sanitize($_POST['legality']);
        $business_sectors = sanitize($_POST['business_sectors']);
        $certificates = sanitize($_POST['certificates']);
        
        $structure_img_name = $profile['structure_img'] ?? '';
        $pdf_path_name = $profile['pdf_path'] ?? '';
        
        $upload_ok = true;
        
        // Handle organizational structure image upload
        if (isset($_FILES['structure_img']) && $_FILES['structure_img']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['structure_img']['tmp_name'];
            $file_name = $_FILES['structure_img']['name'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];
            
            if (!in_array($file_ext, $allowed_exts)) {
                $error_msg = 'Format gambar struktur organisasi tidak diperbolehkan (hanya JPG, JPEG, PNG, WEBP).';
                $upload_ok = false;
            } else {
                $structure_img_name = 'structure_' . time() . '.' . $file_ext;
                $dest_path = __DIR__ . '/../assets/uploads/' . $structure_img_name;
                
                if (move_uploaded_file($file_tmp, $dest_path)) {
                    // Delete old structure image if exists
                    if (!empty($profile['structure_img']) && file_exists(__DIR__ . '/../' . $profile['structure_img'])) {
                        unlink(__DIR__ . '/../' . $profile['structure_img']);
                    }
                    $structure_img_name = 'assets/uploads/' . $structure_img_name;
                } else {
                    $error_msg = 'Gagal mengunggah gambar struktur organisasi.';
                    $upload_ok = false;
                }
            }
        }
        
        // Handle Company Profile PDF upload
        if ($upload_ok && isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['pdf_file']['tmp_name'];
            $file_name = $_FILES['pdf_file']['name'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            if ($file_ext !== 'pdf') {
                $error_msg = 'Berkas profil perusahaan harus berformat PDF.';
                $upload_ok = false;
            } else {
                $pdf_path_name = 'company_profile_' . time() . '.pdf';
                $dest_path = __DIR__ . '/../assets/uploads/pdf/' . $pdf_path_name;
                
                if (move_uploaded_file($file_tmp, $dest_path)) {
                    // Delete old PDF file if exists
                    if (!empty($profile['pdf_path']) && file_exists(__DIR__ . '/../' . $profile['pdf_path'])) {
                        unlink(__DIR__ . '/../' . $profile['pdf_path']);
                    }
                    $pdf_path_name = 'assets/uploads/pdf/' . $pdf_path_name;
                } else {
                    $error_msg = 'Gagal mengunggah berkas PDF ke server.';
                    $upload_ok = false;
                }
            }
        }
        
        if ($upload_ok) {
            try {
                $stmt = $db->prepare("UPDATE company_profile SET director_speech = ?, profile_text = ?, legality = ?, structure_img = ?, business_sectors = ?, certificates = ?, pdf_path = ? WHERE id = 1");
                $stmt->execute([
                    $director_speech,
                    $profile_text,
                    $legality,
                    $structure_img_name,
                    $business_sectors,
                    $certificates,
                    $pdf_path_name
                ]);
                $success_msg = 'Profil perusahaan berhasil diperbarui!';
                
                // Refetch
                $stmt_profile = $db->query("SELECT * FROM company_profile WHERE id = 1");
                $profile = $stmt_profile->fetch();
            } catch (Exception $e) {
                $error_msg = 'Terjadi kesalahan database: ' . $e->getMessage();
            }
        }
    }
}
?>

<!-- Header -->
<div class="mb-4">
    <h1 class="h3 fw-bold text-primary mb-0" style="color: var(--a-navy) !important;">Edit Company Profile</h1>
    <span class="small text-muted" style="color: var(--a-gray) !important;">Perbarui sambutan direksi, legalitas hukum, struktur organisasi, dan berkas PDF profil perusahaan.</span>
</div>

<!-- Status Alerts -->
<?php if (!empty($success_msg)): ?>
    <div class="alert-admin alert-admin-success mb-4"><i class="bi-check-circle-fill"></i> <?php echo $success_msg; ?></div>
<?php endif; ?>
<?php if (!empty($error_msg)): ?>
    <div class="alert-admin alert-admin-danger mb-4"><i class="bi-exclamation-triangle-fill"></i> <?php echo $error_msg; ?></div>
<?php endif; ?>

<!-- Form Edit Profile -->
<div class="admin-card mb-5">
    <div class="admin-card-body">
    <form action="" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">
        
        <!-- Sambutan Direksi -->
        <div class="mb-4">
            <label for="director_speech" class="form-label-admin">Sambutan Direktur Utama</label>
            <textarea class="form-control-admin" id="director_speech" name="director_speech" rows="4" required><?php echo sanitize($profile['director_speech'] ?? ''); ?></textarea>
        </div>
        
        <!-- Profil Lengkap -->
        <div class="mb-4">
            <label for="profile_text" class="form-label-admin">Profil Singkat / Sejarah Singkat Korporasi</label>
            <textarea class="form-control-admin" id="profile_text" name="profile_text" rows="5" required><?php echo sanitize($profile['profile_text'] ?? ''); ?></textarea>
        </div>
        
        <!-- Legalitas -->
        <div class="mb-4">
            <label for="legality" class="form-label-admin">Legalitas Hukum Perusahaan (Format: Keterangan: Nilai - Pisahkan dengan baris baru)</label>
            <textarea class="form-control-admin" id="legality" name="legality" rows="5" required><?php echo sanitize($profile['legality'] ?? ''); ?></textarea>
        </div>

        <div class="row mb-4 g-4">
            <!-- Bidang Usaha -->
            <div class="col-md-6">
                <label for="business_sectors" class="form-label-admin">Sektor Bidang Usaha (Pisahkan dengan baris baru)</label>
                <textarea class="form-control-admin" id="business_sectors" name="business_sectors" rows="5" required><?php echo sanitize($profile['business_sectors'] ?? ''); ?></textarea>
            </div>
            <!-- Sertifikasi -->
            <div class="col-md-6">
                <label for="certificates" class="form-label-admin">Sertifikasi Internasional (Pisahkan dengan baris baru)</label>
                <textarea class="form-control-admin" id="certificates" name="certificates" rows="5" required><?php echo sanitize($profile['certificates'] ?? ''); ?></textarea>
            </div>
        </div>

        <!-- File Uploads -->
        <div class="row mb-5 g-4">
            <!-- Struktur Organisasi -->
            <div class="col-md-6">
                <label for="structure_img" class="form-label-admin">Gambar Struktur Organisasi (Format: JPG, JPEG, PNG, WEBP)</label>
                <input type="file" class="form-control-admin" style="background:#fff;" id="structure_img" name="structure_img">
                <?php if (!empty($profile['structure_img'])): ?>
                    <div class="mt-3 text-start">
                        <span class="small d-block mb-2" style="color:var(--a-gray);font-weight:600;">Gambar struktur saat ini:</span>
                        <img src="<?php echo base_url($profile['structure_img']); ?>" style="max-height: 80px; border-radius:var(--a-radius-sm); border:1px solid var(--a-border); box-shadow:0 2px 8px rgba(0,0,0,0.05);">
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- PDF Document -->
            <div class="col-md-6">
                <label for="pdf_file" class="form-label-admin">Berkas PDF Company Profile (Format: PDF)</label>
                <input type="file" class="form-control-admin" style="background:#fff;" id="pdf_file" name="pdf_file" accept=".pdf">
                <?php if (!empty($profile['pdf_path'])): ?>
                    <div class="mt-3 text-start">
                        <span class="small d-block mb-2" style="color:var(--a-gray);font-weight:600;">File PDF saat ini:</span>
                        <a href="<?php echo base_url($profile['pdf_path']); ?>" target="_blank" class="btn-admin-danger"><i class="bi-file-earmark-pdf"></i> Buka PDF</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="mt-4 pt-4 border-top" style="border-color: var(--a-border) !important;">
            <button type="submit" class="btn-admin-primary"><i class="bi-save"></i> Simpan Perubahan Profil</button>
        </div>
    </form>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
