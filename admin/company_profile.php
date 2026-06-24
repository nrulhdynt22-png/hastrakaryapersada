<?php
include __DIR__ . '/includes/header.php';

$success = $error = '';

// --- Migrate table if old columns still exist ---
try {
    $db->exec("ALTER TABLE `company_profile`
        ADD COLUMN IF NOT EXISTS `visi_title`      VARCHAR(255) NULL,
        ADD COLUMN IF NOT EXISTS `visi_text`        TEXT NULL,
        ADD COLUMN IF NOT EXISTS `misi_items`       TEXT NULL,
        ADD COLUMN IF NOT EXISTS `nilai_json`       LONGTEXT NULL,
        ADD COLUMN IF NOT EXISTS `milestones_json`  LONGTEXT NULL");
    // Remove old columns that no longer exist (silently)
    foreach (['director_speech','legality','structure_img','business_sectors','pdf_path'] as $col) {
        try { $db->exec("ALTER TABLE `company_profile` DROP COLUMN `$col`"); } catch(Exception $e) {}
    }
} catch (Exception $e) {}

// Ensure row id=1 exists
$db->exec("INSERT IGNORE INTO `company_profile` (id) VALUES (1)");

// --- Handle POST ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Token keamanan tidak valid. Silakan coba lagi.';
    } else {
        $profile_text     = trim($_POST['profile_text']     ?? '');
        $visi_title       = trim($_POST['visi_title']       ?? '');
        $visi_text        = trim($_POST['visi_text']        ?? '');
        $misi_items       = trim($_POST['misi_items']       ?? '');
        $certificates     = trim($_POST['certificates']     ?? '');

        // Build nilai_json from individual fields
        $nilai_icons  = $_POST['nilai_icon']  ?? [];
        $nilai_titles = $_POST['nilai_title'] ?? [];
        $nilai_descs  = $_POST['nilai_desc']  ?? [];
        $nilai_arr = [];
        for ($i = 0; $i < count($nilai_titles); $i++) {
            if (!empty(trim($nilai_titles[$i]))) {
                $nilai_arr[] = [
                    'icon'  => trim($nilai_icons[$i]  ?? 'bi-star'),
                    'title' => trim($nilai_titles[$i]),
                    'desc'  => trim($nilai_descs[$i]  ?? ''),
                ];
            }
        }

        // Build milestones_json
        $ms_years = $_POST['ms_year'] ?? [];
        $ms_descs = $_POST['ms_desc'] ?? [];
        $ms_arr   = [];
        for ($i = 0; $i < count($ms_years); $i++) {
            if (!empty(trim($ms_years[$i]))) {
                $ms_arr[] = ['year' => trim($ms_years[$i]), 'desc' => trim($ms_descs[$i] ?? '')];
            }
        }

        try {
            $stmt = $db->prepare("UPDATE `company_profile` SET
                profile_text    = ?,
                visi_title      = ?,
                visi_text       = ?,
                misi_items      = ?,
                nilai_json      = ?,
                milestones_json = ?,
                certificates    = ?
                WHERE id = 1");
            $stmt->execute([
                $profile_text,
                $visi_title,
                $visi_text,
                $misi_items,
                json_encode($nilai_arr, JSON_UNESCAPED_UNICODE),
                json_encode($ms_arr,    JSON_UNESCAPED_UNICODE),
                $certificates,
            ]);
            $success = 'Data Tentang berhasil disimpan!';
        } catch (Exception $e) {
            $error = 'Gagal menyimpan: ' . $e->getMessage();
        }
    }
}

// --- Load current data ---
$p = $db->query("SELECT * FROM `company_profile` WHERE id = 1")->fetch();

$nilai_data     = json_decode($p['nilai_json']      ?? '[]', true) ?: [
    ['icon' => 'bi-award',        'title' => 'Integritas',       'desc' => 'Menjunjung tinggi etika bisnis, kejujuran, dan transparansi.'],
    ['icon' => 'bi-shield-heart', 'title' => 'Keselamatan (K3)', 'desc' => 'Mengutamakan K3 di setiap area proyek.'],
    ['icon' => 'bi-gem',          'title' => 'Mutu Unggul',      'desc' => 'Tidak berkompromi terhadap standar kualitas.'],
    ['icon' => 'bi-people',       'title' => 'Kolaborasi',       'desc' => 'Bekerja sinergis dengan klien dan mitra.'],
];
$milestones_data = json_decode($p['milestones_json'] ?? '[]', true) ?: [
    ['year' => '2020', 'desc' => 'Perusahaan didirikan'],
    ['year' => '2021', 'desc' => 'Proyek pertama senilai Rp 15M'],
    ['year' => '2022', 'desc' => 'Raih ISO 9001:2015'],
    ['year' => '2024', 'desc' => '150+ proyek, 80+ klien'],
];
?>

<?php if ($success): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi-check-circle-fill me-2"></i><?php echo $success; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<?php if ($error): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi-exclamation-triangle-fill me-2"></i><?php echo $error; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<form method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">

    <div class="row g-4">

        <!-- Profil Perusahaan -->
        <div class="col-12">
            <div class="admin-card p-4">
                <h6 class="mb-3" style="font-weight:700;color:var(--a-navy);"><i class="bi-building me-2" style="color:var(--a-gold);"></i>Profil Perusahaan</h6>
                <label class="form-label small fw-semibold">Teks Profil / Sejarah</label>
                <textarea name="profile_text" rows="5" class="form-control"><?php echo htmlspecialchars($p['profile_text'] ?? ''); ?></textarea>
            </div>
        </div>

        <!-- Visi & Misi -->
        <div class="col-lg-6">
            <div class="admin-card p-4" style="height:100%;">
                <h6 class="mb-3" style="font-weight:700;color:var(--a-navy);"><i class="bi-eye me-2" style="color:var(--a-gold);"></i>Visi</h6>
                <label class="form-label small fw-semibold">Judul Visi</label>
                <input type="text" name="visi_title" class="form-control mb-3" value="<?php echo htmlspecialchars($p['visi_title'] ?? ''); ?>">
                <label class="form-label small fw-semibold">Isi Visi</label>
                <textarea name="visi_text" rows="4" class="form-control"><?php echo htmlspecialchars($p['visi_text'] ?? ''); ?></textarea>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="admin-card p-4" style="height:100%;">
                <h6 class="mb-3" style="font-weight:700;color:var(--a-navy);"><i class="bi-compass me-2" style="color:var(--a-gold);"></i>Misi</h6>
                <label class="form-label small fw-semibold">Poin-Poin Misi <small class="text-muted">(satu baris = satu poin)</small></label>
                <textarea name="misi_items" rows="7" class="form-control"><?php echo htmlspecialchars($p['misi_items'] ?? ''); ?></textarea>
            </div>
        </div>

        <!-- Milestones -->
        <div class="col-12">
            <div class="admin-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0" style="font-weight:700;color:var(--a-navy);"><i class="bi-clock-history me-2" style="color:var(--a-gold);"></i>Perjalanan / Milestones</h6>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addMilestone()"><i class="bi-plus me-1"></i>Tambah</button>
                </div>
                <div id="ms-list">
                    <?php foreach ($milestones_data as $i => $ms): ?>
                    <div class="row g-2 align-items-center mb-2 ms-row">
                        <div class="col-3 col-md-2">
                            <input type="text" name="ms_year[]" class="form-control form-control-sm" placeholder="Tahun" value="<?php echo htmlspecialchars($ms['year']); ?>">
                        </div>
                        <div class="col">
                            <input type="text" name="ms_desc[]" class="form-control form-control-sm" placeholder="Keterangan" value="<?php echo htmlspecialchars($ms['desc']); ?>">
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.ms-row').remove()"><i class="bi-trash"></i></button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Nilai Perusahaan -->
        <div class="col-12">
            <div class="admin-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0" style="font-weight:700;color:var(--a-navy);"><i class="bi-stars me-2" style="color:var(--a-gold);"></i>Nilai Perusahaan</h6>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addNilai()"><i class="bi-plus me-1"></i>Tambah</button>
                </div>
                <div id="nilai-list">
                    <?php foreach ($nilai_data as $v): ?>
                    <div class="row g-2 align-items-center mb-2 nilai-row">
                        <div class="col-md-3">
                            <input type="text" name="nilai_icon[]"  class="form-control form-control-sm" placeholder="Icon (bi-award)" value="<?php echo htmlspecialchars($v['icon']); ?>">
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="nilai_title[]" class="form-control form-control-sm" placeholder="Judul" value="<?php echo htmlspecialchars($v['title']); ?>">
                        </div>
                        <div class="col">
                            <input type="text" name="nilai_desc[]"  class="form-control form-control-sm" placeholder="Deskripsi" value="<?php echo htmlspecialchars($v['desc']); ?>">
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.nilai-row').remove()"><i class="bi-trash"></i></button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Sertifikasi -->
        <div class="col-12">
            <div class="admin-card p-4">
                <h6 class="mb-3" style="font-weight:700;color:var(--a-navy);"><i class="bi-patch-check-fill me-2" style="color:var(--a-gold);"></i>Sertifikasi</h6>
                <label class="form-label small fw-semibold">Daftar Sertifikat <small class="text-muted">(satu baris = satu sertifikat)</small></label>
                <textarea name="certificates" rows="5" class="form-control"><?php echo htmlspecialchars($p['certificates'] ?? ''); ?></textarea>
            </div>
        </div>

        <div class="col-12 text-end">
            <button type="submit" class="btn btn-primary px-5 py-2" style="background:var(--a-gold);border:none;color:#fff;font-weight:700;">
                <i class="bi-save me-2"></i>Simpan Semua Perubahan
            </button>
        </div>

    </div>
</form>

<script>
function addMilestone() {
    const row = `<div class="row g-2 align-items-center mb-2 ms-row">
        <div class="col-3 col-md-2"><input type="text" name="ms_year[]" class="form-control form-control-sm" placeholder="Tahun"></div>
        <div class="col"><input type="text" name="ms_desc[]" class="form-control form-control-sm" placeholder="Keterangan"></div>
        <div class="col-auto"><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.ms-row').remove()"><i class="bi-trash"></i></button></div>
    </div>`;
    document.getElementById('ms-list').insertAdjacentHTML('beforeend', row);
}
function addNilai() {
    const row = `<div class="row g-2 align-items-center mb-2 nilai-row">
        <div class="col-md-3"><input type="text" name="nilai_icon[]"  class="form-control form-control-sm" placeholder="Icon (bi-award)"></div>
        <div class="col-md-3"><input type="text" name="nilai_title[]" class="form-control form-control-sm" placeholder="Judul"></div>
        <div class="col"><input type="text" name="nilai_desc[]"  class="form-control form-control-sm" placeholder="Deskripsi"></div>
        <div class="col-auto"><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.nilai-row').remove()"><i class="bi-trash"></i></button></div>
    </div>`;
    document.getElementById('nilai-list').insertAdjacentHTML('beforeend', row);
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
