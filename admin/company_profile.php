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
        $business_sectors = sanitize($_POST['business_sectors']);
        $certificates = sanitize($_POST['certificates']);
        
        $upload_ok = true;
        

        

        
        if ($upload_ok) {
            try {
                $stmt = $db->prepare("UPDATE company_profile SET director_speech = ?, profile_text = ?, business_sectors = ?, certificates = ? WHERE id = 1");
                $stmt->execute([
                    $director_speech,
                    $profile_text,
                    $business_sectors,
                    $certificates
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
    <span class="small text-muted" style="color: var(--a-gray) !important;">Perbarui sambutan direksi, profil perusahaan, bidang usaha, dan sertifikasi.</span>
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


        
        <div class="mt-4 pt-4 border-top" style="border-color: var(--a-border) !important;">
            <button type="submit" class="btn-admin-primary"><i class="bi-save"></i> Simpan Perubahan Profil</button>
        </div>
    </form>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
