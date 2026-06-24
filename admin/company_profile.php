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
        $director_speech   = sanitize($_POST['director_speech'] ?? '');
        $profile_text      = sanitize($_POST['profile_text'] ?? '');
        $business_sectors  = sanitize($_POST['business_sectors'] ?? '');
        $certificates      = sanitize($_POST['certificates'] ?? '');

        // Visi & Misi
        $visi_title = sanitize($_POST['visi_title'] ?? '');
        $visi_text  = sanitize($_POST['visi_text'] ?? '');
        // Misi items as newline-separated
        $misi_items = sanitize($_POST['misi_items'] ?? '');

        // Nilai Perusahaan (4 items serialized as JSON)
        $nilai = [];
        for ($i = 1; $i <= 4; $i++) {
            $nilai[] = [
                'icon'  => sanitize($_POST["nilai_icon_$i"] ?? 'bi-star'),
                'title' => sanitize($_POST["nilai_title_$i"] ?? ''),
                'desc'  => sanitize($_POST["nilai_desc_$i"] ?? ''),
            ];
        }
        $nilai_json = json_encode($nilai, JSON_UNESCAPED_UNICODE);

        // Milestones (4 items)
        $milestones = [];
        for ($i = 1; $i <= 4; $i++) {
            $milestones[] = [
                'year' => sanitize($_POST["milestone_year_$i"] ?? ''),
                'desc' => sanitize($_POST["milestone_desc_$i"] ?? ''),
            ];
        }
        $milestones_json = json_encode($milestones, JSON_UNESCAPED_UNICODE);

        try {
            // Add columns if they don't exist yet (safe to call repeatedly)
            $cols_to_add = [
                'visi_title'      => 'TEXT',
                'visi_text'       => 'TEXT',
                'misi_items'      => 'TEXT',
                'nilai_json'      => 'TEXT',
                'milestones_json' => 'TEXT',
            ];
            foreach ($cols_to_add as $col => $type) {
                try {
                    $db->exec("ALTER TABLE company_profile ADD COLUMN `$col` $type");
                } catch (Exception $e) { /* column already exists, ignore */ }
            }

            $stmt = $db->prepare("UPDATE company_profile SET
                director_speech   = ?,
                profile_text      = ?,
                business_sectors  = ?,
                certificates      = ?,
                visi_title        = ?,
                visi_text         = ?,
                misi_items        = ?,
                nilai_json        = ?,
                milestones_json   = ?
                WHERE id = 1");
            $stmt->execute([
                $director_speech,
                $profile_text,
                $business_sectors,
                $certificates,
                $visi_title,
                $visi_text,
                $misi_items,
                $nilai_json,
                $milestones_json,
            ]);
            $success_msg = 'Halaman Tentang berhasil diperbarui!';

            // Refetch
            $stmt2 = $db->query("SELECT * FROM company_profile WHERE id = 1");
            $profile = $stmt2->fetch();
        } catch (Exception $e) {
            $error_msg = 'Terjadi kesalahan database: ' . $e->getMessage();
        }
    }
}

// Decode JSON fields
$nilai_data = json_decode($profile['nilai_json'] ?? '[]', true) ?: [
    ['icon' => 'bi-award',        'title' => 'Integritas',     'desc' => 'Menjunjung tinggi etika bisnis, kejujuran, dan transparansi.'],
    ['icon' => 'bi-shield-heart', 'title' => 'Keselamatan (K3)', 'desc' => 'Mengutamakan keselamatan dan kesehatan kerja karyawan.'],
    ['icon' => 'bi-gem',          'title' => 'Mutu Unggul',    'desc' => 'Tidak berkompromi terhadap standar kualitas pengerjaan.'],
    ['icon' => 'bi-people',       'title' => 'Kolaborasi',     'desc' => 'Bekerja secara sinergis dengan klien, mitra, dan vendor.'],
];
$milestones_data = json_decode($profile['milestones_json'] ?? '[]', true) ?: [
    ['year' => '2020', 'desc' => 'Perusahaan didirikan'],
    ['year' => '2021', 'desc' => 'Proyek pertama senilai Rp 15M'],
    ['year' => '2022', 'desc' => 'Raih ISO 9001:2015'],
    ['year' => '2024', 'desc' => '150+ proyek, 80+ klien'],
];
?>

<!-- Header -->
<div class="mb-4">
    <h1 class="h3 fw-bold text-primary mb-0" style="color: var(--a-navy) !important;">Kelola Halaman Tentang</h1>
    <span class="small text-muted" style="color: var(--a-gray) !important;">Edit semua konten yang tampil di halaman Tentang Kami — profil, visi &amp; misi, nilai, struktur, dan sertifikasi.</span>
</div>

<?php if (!empty($success_msg)): ?>
    <div class="alert-admin alert-admin-success mb-4"><i class="bi-check-circle-fill"></i> <?php echo $success_msg; ?></div>
<?php endif; ?>
<?php if (!empty($error_msg)): ?>
    <div class="alert-admin alert-admin-danger mb-4"><i class="bi-exclamation-triangle-fill"></i> <?php echo $error_msg; ?></div>
<?php endif; ?>

<!-- TABS -->
<div class="admin-tabs mb-4" id="tentangTab" role="tablist">
    <button class="admin-tab active" data-bs-toggle="tab" data-bs-target="#tab-profil" type="button">Profil &amp; Sejarah</button>
    <button class="admin-tab" data-bs-toggle="tab" data-bs-target="#tab-visi" type="button">Visi &amp; Misi</button>
    <button class="admin-tab" data-bs-toggle="tab" data-bs-target="#tab-nilai" type="button">Nilai Perusahaan</button>
    <button class="admin-tab" data-bs-toggle="tab" data-bs-target="#tab-sertifikasi" type="button">Sertifikasi</button>
</div>

<div class="admin-card mb-5">
    <div class="admin-card-body">
    <form action="" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">

        <div class="tab-content">

            <!-- TAB 1: Profil & Sejarah -->
            <div class="tab-pane fade show active" id="tab-profil">
                <div class="mb-4">
                    <label class="form-label-admin">Sambutan Direktur Utama</label>
                    <textarea class="form-control-admin" name="director_speech" rows="4"><?php echo sanitize($profile['director_speech'] ?? ''); ?></textarea>
                    <span class="text-muted d-block mt-1" style="font-size:.75rem;">Kutipan ini tampil di halaman Company Profile.</span>
                </div>
                <div class="mb-4">
                    <label class="form-label-admin">Profil Singkat / Sejarah Perusahaan</label>
                    <textarea class="form-control-admin" name="profile_text" rows="6"><?php echo sanitize($profile['profile_text'] ?? ''); ?></textarea>
                    <span class="text-muted d-block mt-1" style="font-size:.75rem;">Tampil di bagian "Sejarah &amp; Profil" halaman Tentang. Setiap baris baru = paragraf baru.</span>
                </div>
                <div class="mb-4">
                    <label class="form-label-admin">Timeline / Milestones (4 Item)</label>
                    <div class="row g-3">
                        <?php for ($i = 0; $i < 4; $i++): $m = $milestones_data[$i] ?? ['year'=>'','desc'=>'']; ?>
                        <div class="col-md-6">
                            <div style="background:var(--a-bg);border:1px solid var(--a-border);border-radius:8px;padding:1rem;">
                                <label class="form-label-admin mb-1">Milestone <?php echo $i+1; ?> — Tahun</label>
                                <input type="text" class="form-control-admin mb-2" name="milestone_year_<?php echo $i+1; ?>" value="<?php echo sanitize($m['year']); ?>" placeholder="cth: 2020">
                                <label class="form-label-admin mb-1">Keterangan</label>
                                <input type="text" class="form-control-admin" name="milestone_desc_<?php echo $i+1; ?>" value="<?php echo sanitize($m['desc']); ?>" placeholder="cth: Perusahaan didirikan">
                            </div>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>

            <!-- TAB 2: Visi & Misi -->
            <div class="tab-pane fade" id="tab-visi">
                <div class="mb-4">
                    <label class="form-label-admin">Judul Visi</label>
                    <input type="text" class="form-control-admin" name="visi_title" value="<?php echo sanitize($profile['visi_title'] ?? 'Menjadi Kontraktor Terkemuka Berskala Nasional'); ?>">
                </div>
                <div class="mb-4">
                    <label class="form-label-admin">Teks Visi (Deskripsi)</label>
                    <textarea class="form-control-admin" name="visi_text" rows="4"><?php echo sanitize($profile['visi_text'] ?? 'Menjadi perusahaan konstruksi, pengadaan, dan konsultansi multi-jasa terkemuka berskala nasional yang dikenal karena integritas, keandalan, inovasi berkelanjutan, serta komitmen penuh menghasilkan kualitas kerja berkelas dunia.'); ?></textarea>
                </div>
                <div class="mb-4">
                    <label class="form-label-admin">Item-item Misi (pisahkan dengan baris baru, maks. 5 item)</label>
                    <textarea class="form-control-admin" name="misi_items" rows="6"><?php echo sanitize($profile['misi_items'] ?? "Menyediakan solusi jasa terintegrasi dengan standar keselamatan kerja dan mutu internasional.\nMembangun kemitraan strategis jangka panjang berdasarkan prinsip transparansi dan saling menguntungkan.\nMemberdayakan talenta profesional lokal terbaik dan memanfaatkan teknologi modern untuk efisiensi.\nMemberikan dampak positif bagi masyarakat melalui program pembangunan ramah lingkungan."); ?></textarea>
                    <span class="text-muted d-block mt-1" style="font-size:.75rem;">Setiap baris akan ditampilkan sebagai satu poin misi di website.</span>
                </div>
            </div>

            <!-- TAB 3: Nilai Perusahaan -->
            <div class="tab-pane fade" id="tab-nilai">
                <p class="text-muted mb-4" style="font-size:.875rem;">Isi 4 nilai budaya perusahaan yang tampil di bagian "Nilai Perusahaan &amp; Budaya Kerja". Gunakan nama ikon dari <a href="https://icons.getbootstrap.com" target="_blank">Bootstrap Icons</a> (cth: <code>bi-award</code>).</p>
                <div class="row g-4">
                    <?php for ($i = 0; $i < 4; $i++): $n = $nilai_data[$i] ?? ['icon'=>'bi-star','title'=>'','desc'=>'']; ?>
                    <div class="col-md-6">
                        <div style="background:var(--a-bg);border:1px solid var(--a-border);border-radius:8px;padding:1.25rem;">
                            <label class="form-label-admin mb-1">Nilai <?php echo $i+1; ?> — Ikon Bootstrap (cth: bi-award)</label>
                            <input type="text" class="form-control-admin mb-2" name="nilai_icon_<?php echo $i+1; ?>" value="<?php echo sanitize($n['icon']); ?>">
                            <label class="form-label-admin mb-1">Judul Nilai</label>
                            <input type="text" class="form-control-admin mb-2" name="nilai_title_<?php echo $i+1; ?>" value="<?php echo sanitize($n['title']); ?>">
                            <label class="form-label-admin mb-1">Deskripsi</label>
                            <textarea class="form-control-admin" name="nilai_desc_<?php echo $i+1; ?>" rows="2"><?php echo sanitize($n['desc']); ?></textarea>
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- TAB 4: Sertifikasi -->
            <div class="tab-pane fade" id="tab-sertifikasi">
                <div class="mb-4">
                    <label class="form-label-admin">Sektor Bidang Usaha (pisahkan dengan baris baru)</label>
                    <textarea class="form-control-admin" name="business_sectors" rows="5"><?php echo sanitize($profile['business_sectors'] ?? "Konstruksi Sipil & Bangunan\nPengadaan Alat Berat & Rantai Pasok\nKonsultansi Manajemen Konstruksi"); ?></textarea>
                    <span class="text-muted d-block mt-1" style="font-size:.75rem;">Tampil di halaman Company Profile.</span>
                </div>
                <div class="mb-4">
                    <label class="form-label-admin">Sertifikasi Internasional (pisahkan dengan baris baru)</label>
                    <textarea class="form-control-admin" name="certificates" rows="5"><?php echo sanitize($profile['certificates'] ?? "ISO 9001:2015\nISO 14001:2015\nISO 45001:2018\nSMK3"); ?></textarea>
                    <span class="text-muted d-block mt-1" style="font-size:.75rem;">Tampil di bagian "Komitmen Mutu Kelas Dunia" halaman Tentang dan Company Profile.</span>
                </div>
            </div>

        </div><!-- /tab-content -->

        <div class="mt-4 pt-4 border-top" style="border-color: var(--a-border) !important;">
            <button type="submit" class="btn-admin-primary"><i class="bi-save"></i> Simpan Semua Perubahan</button>
        </div>
    </form>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
