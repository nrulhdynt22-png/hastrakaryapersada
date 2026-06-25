<?php
include __DIR__ . '/includes/header.php';

$action = isset($_GET['action']) ? sanitize($_GET['action']) : 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$success_msg = '';
$error_msg = '';

// Process Form Submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        $error_msg = 'Token CSRF tidak valid. Silakan muat ulang halaman.';
    } else {
        $title = sanitize($_POST['title']);
        $slug = slugify($title);
        $department = sanitize($_POST['department']);
        $location = sanitize($_POST['location']);
        $job_type = sanitize($_POST['job_type']);
        $description = $_POST['description']; // Allow line breaks/formatting
        $requirements = sanitize($_POST['requirements']);
        $status = isset($_POST['status']) ? 1 : 0;
        
        if ($action === 'add') {
            try {
                $stmt = $db->prepare("INSERT INTO careers (title, slug, department, location, job_type, description, requirements, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$title, $slug, $department, $location, $job_type, $description, $requirements, $status]);
                $success_msg = 'Lowongan pekerjaan baru berhasil ditambahkan!';
                $action = 'list';
            } catch (Exception $e) {
                if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                    $error_msg = 'Lowongan dengan nama serupa sudah ada!';
                } else {
                    $error_msg = 'Terjadi kesalahan database: ' . $e->getMessage();
                }
            }
        } elseif ($action === 'edit' && $id > 0) {
            try {
                $stmt = $db->prepare("UPDATE careers SET title = ?, slug = ?, department = ?, location = ?, job_type = ?, description = ?, requirements = ?, status = ? WHERE id = ?");
                $stmt->execute([$title, $slug, $department, $location, $job_type, $description, $requirements, $status, $id]);
                $success_msg = 'Lowongan pekerjaan berhasil diperbarui!';
                $action = 'list';
            } catch (Exception $e) {
                $error_msg = 'Terjadi kesalahan database: ' . $e->getMessage();
            }
        }
    }
}

// Process Delete Request
if ($action === 'delete' && $id > 0) {
    try {
        $stmt = $db->prepare("DELETE FROM careers WHERE id = ?");
        $stmt->execute([$id]);
        $success_msg = 'Lowongan berhasil dihapus!';
    } catch (Exception $e) {
        $error_msg = 'Gagal menghapus data: ' . $e->getMessage();
    }
    $action = 'list';
}

// Process Delete Applicant
$delete_app_id = isset($_GET['delete_app']) ? (int)$_GET['delete_app'] : 0;
if ($delete_app_id > 0) {
    try {
        // Delete CV file
        $cv_q = $db->prepare("SELECT cv_path FROM career_applications WHERE id = ?");
        $cv_q->execute([$delete_app_id]);
        $cv_file = $cv_q->fetchColumn();
        if (!empty($cv_file) && file_exists(__DIR__ . '/../' . $cv_file)) {
            unlink(__DIR__ . '/../' . $cv_file);
        }
        
        $stmt = $db->prepare("DELETE FROM career_applications WHERE id = ?");
        $stmt->execute([$delete_app_id]);
        $success_msg = 'Berkas lamaran pelamar berhasil dihapus!';
    } catch (Exception $e) {
        $error_msg = 'Gagal menghapus data pelamar: ' . $e->getMessage();
    }
}

// Fetch single career for edit
$job_edit = null;
if ($action === 'edit' && $id > 0) {
    try {
        $stmt = $db->prepare("SELECT * FROM careers WHERE id = ?");
        $stmt->execute([$id]);
        $job_edit = $stmt->fetch();
    } catch (Exception $e) {}
}

// Fetch all career vacancies
$careers_list = [];
try {
    $careers_list = $db->query("SELECT * FROM careers ORDER BY id DESC")->fetchAll();
} catch (Exception $e) {}

// Fetch all applicants
$applicants_list = [];
try {
    $applicants_list = $db->query("SELECT ca.*, c.title AS job_title FROM career_applications ca JOIN careers c ON ca.career_id = c.id ORDER BY ca.created_at DESC")->fetchAll();
} catch (Exception $e) {}
?>

<!-- Header Section -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold text-primary mb-0">Kelola Karir & Pelamar Kerja</h1>
        <span class="small text-muted">Kelola lowongan pekerjaan yang dibuka serta telusuri data pelamar beserta CV mereka.</span>
    </div>
    <?php if ($action === 'list'): ?>
        <a href="careers.php?action=add" class="btn btn-primary"><i class="bi-plus-circle me-1"></i> Tambah Lowongan</a>
    <?php else: ?>
        <a href="careers.php" class="btn btn-outline-secondary"><i class="bi-arrow-left me-1"></i> Kembali ke Daftar</a>
    <?php endif; ?>
</div>

<!-- Status Alerts -->
<?php if (!empty($success_msg)): ?>
    <div class="alert alert-success border-0 shadow-sm mb-4"><?php echo $success_msg; ?></div>
<?php endif; ?>
<?php if (!empty($error_msg)): ?>
    <div class="alert alert-danger border-0 shadow-sm mb-4"><?php echo $error_msg; ?></div>
<?php endif; ?>

<?php if ($action === 'list'): ?>
    <!-- LIST VIEW: vacancies and applicants -->
    
    <!-- Tab Navigation -->
    <ul class="nav nav-tabs mb-4" id="careerTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold text-primary" id="vacancies-tab" data-bs-toggle="tab" data-bs-target="#vacancies" type="button" role="tab" aria-controls="vacancies" aria-selected="true">Daftar Lowongan Kerja</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold text-primary" id="applicants-tab" data-bs-toggle="tab" data-bs-target="#applicants" type="button" role="tab" aria-controls="applicants" aria-selected="false">Daftar Pelamar Kerja (<?php echo count($applicants_list); ?>)</button>
        </li>
    </ul>
    
    <div class="tab-content" id="careerTabContent">
        <!-- Tab 1: Vacancies List -->
        <div class="tab-pane fade show active" id="vacancies" role="tabpanel" aria-labelledby="vacancies-tab">
            <div class="card admin-table-card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0 small text-muted align-middle">
                            <thead class="table-primary text-primary">
                                <tr>
                                    <th class="py-3 ps-4">Judul Lowongan</th>
                                    <th class="py-3">Departemen</th>
                                    <th class="py-3">Lokasi</th>
                                    <th class="py-3">Tipe Kerja</th>
                                    <th class="py-3">Status</th>
                                    <th class="py-3 pe-4 text-end" style="width: 15%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($careers_list) > 0): ?>
                                    <?php foreach ($careers_list as $c): ?>
                                        <tr>
                                            <td class="py-3 ps-4 fw-bold text-primary"><?php echo sanitize($c['title']); ?></td>
                                            <td class="py-3"><?php echo sanitize($c['department']); ?></td>
                                            <td class="py-3"><?php echo sanitize($c['location']); ?></td>
                                            <td class="py-3"><span class="badge bg-light text-primary border border-secondary-subtle px-2 py-1"><?php echo sanitize($c['job_type']); ?></span></td>
                                            <td class="py-3">
                                                <?php if ($c['status'] == 1): ?>
                                                    <span class="badge bg-success">Dibuka</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Ditutup</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="py-3 pe-4 text-end">
                                                <a href="careers.php?action=edit&id=<?php echo $c['id']; ?>" class="btn btn-sm btn-outline-primary me-1"><i class="bi-pencil"></i></a>
                                                <a href="careers.php?action=delete&id=<?php echo $c['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus lowongan ini? Seluruh data lamaran terkait juga akan terhapus.')"><i class="bi-trash"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">Belum ada lowongan pekerjaan dibuka.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tab 2: Applicants Tracker -->
        <div class="tab-pane fade" id="applicants" role="tabpanel" aria-labelledby="applicants-tab">
            <div class="card admin-table-card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0 small text-muted align-middle">
                            <thead class="table-primary text-primary">
                                <tr>
                                    <th class="py-3 ps-4">Tanggal Masuk</th>
                                    <th class="py-3">Posisi Dilamar</th>
                                    <th class="py-3">Nama Pelamar</th>
                                    <th class="py-3">Email & Telp</th>
                                    <th class="py-3" style="width: 30%;">Surat Pengantar</th>
                                    <th class="py-3 pe-4 text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($applicants_list) > 0): ?>
                                    <?php foreach ($applicants_list as $app): ?>
                                        <tr>
                                            <td class="py-3 ps-4"><?php echo format_date_id($app['created_at']); ?></td>
                                            <td class="py-3 fw-bold text-primary"><?php echo sanitize($app['job_title']); ?></td>
                                            <td class="py-3 fw-bold"><?php echo sanitize($app['name']); ?></td>
                                            <td class="py-3">
                                                <div><?php echo sanitize($app['email']); ?></div>
                                                <div class="text-muted" style="font-size: 0.8rem;"><?php echo sanitize($app['phone']); ?></div>
                                            </td>
                                            <td class="py-3 text-truncate" style="max-width: 250px;"><?php echo sanitize($app['cover_letter']); ?></td>
                                            <td class="py-3 pe-4 text-end">
                                                <a href="<?php echo base_url($app['cv_path']); ?>" target="_blank" class="btn btn-sm btn-outline-danger me-1"><i class="bi-file-earmark-pdf me-1"></i> CV PDF</a>
                                                <a href="careers.php?delete_app=<?php echo $app['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data pelamar ini?')"><i class="bi-trash"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">Belum ada pelamar kerja terdaftar saat ini.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php elseif ($action === 'add' || ($action === 'edit' && $job_edit)): ?>
    <!-- ADD & EDIT FORM -->
    <div class="card border-0 shadow-sm bg-white p-4" style="border-top: 4px solid var(--color-secondary);">
        <h3 class="fw-bold text-primary mb-3 h5"><?php echo ($action === 'add') ? 'Tambah Lowongan Baru' : 'Edit Lowongan'; ?></h3>
        
        <form action="?action=<?php echo $action; ?><?php echo $id > 0 ? '&id='.$id : ''; ?>" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">
            
            <div class="mb-3">
                <label for="title" class="form-label small fw-bold text-primary">Nama Lowongan Pekerjaan</label>
                <input type="text" class="form-control" id="title" name="title" required value="<?php echo ($action === 'edit') ? sanitize($job_edit['title']) : ''; ?>" placeholder="Contoh: Senior Civil Engineer">
            </div>
            
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="department" class="form-label small fw-bold text-primary">Departemen / Bidang</label>
                    <input type="text" class="form-control" id="department" name="department" required value="<?php echo ($action === 'edit') ? sanitize($job_edit['department']) : ''; ?>" placeholder="Contoh: Teknik / Logistik">
                </div>
                <div class="col-md-4">
                    <label for="location" class="form-label small fw-bold text-primary">Lokasi Kerja</label>
                    <input type="text" class="form-control" id="location" name="location" required value="<?php echo ($action === 'edit') ? sanitize($job_edit['location']) : ''; ?>" placeholder="Contoh: Jakarta Selatan">
                </div>
                <div class="col-md-4">
                    <label for="job_type" class="form-label small fw-bold text-primary">Tipe Pekerjaan</label>
                    <select class="form-select" id="job_type" name="job_type" required>
                        <option value="Full-time" <?php echo ($action === 'edit' && $job_edit['job_type'] === 'Full-time') ? 'selected' : ''; ?>>Full-time (Penuh Waktu)</option>
                        <option value="Part-time" <?php echo ($action === 'edit' && $job_edit['job_type'] === 'Part-time') ? 'selected' : ''; ?>>Part-time (Paruh Waktu)</option>
                        <option value="Magang" <?php echo ($action === 'edit' && $job_edit['job_type'] === 'Magang') ? 'selected' : ''; ?>>Magang (Internship)</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label small fw-bold text-primary">Deskripsi Pekerjaan</label>
                <textarea class="form-control" id="description" name="description" rows="5" required placeholder="Masukkan deskripsi detail pekerjaan..."><?php echo ($action === 'edit') ? $job_edit['description'] : ''; ?></textarea>
            </div>

            <div class="mb-3">
                <label for="requirements" class="form-label small fw-bold text-primary">Persyaratan (Pisahkan dengan baris baru)</label>
                <textarea class="form-control" id="requirements" name="requirements" rows="5" required placeholder="Contoh:&#10;Pendidikan S1 Teknik Sipil&#10;Pengalaman minimal 3 tahun&#10;Bisa AutoCAD"><?php echo ($action === 'edit') ? sanitize($job_edit['requirements']) : ''; ?></textarea>
            </div>

            <div class="mb-4">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="status" name="status" <?php echo ($action === 'add' || ($action === 'edit' && $job_edit['status'] == 1)) ? 'checked' : ''; ?>>
                    <label class="form-check-label small fw-bold text-primary" for="status">Buka Lowongan Pekerjaan (Tampilkan di Halaman Karir)</label>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary px-4 py-2"><i class="bi-check-circle me-1"></i> Simpan Lowongan</button>
            <a href="careers.php" class="btn btn-outline-secondary px-4 py-2 ms-2">Batal</a>
        </form>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
