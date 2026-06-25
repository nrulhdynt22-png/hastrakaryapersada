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
        $category = sanitize($_POST['category']);
        $location = sanitize($_POST['location']);
        $year = sanitize($_POST['year']);
        $client = sanitize($_POST['client']);
        $description = $_POST['description']; // Allow rich text/formatted content
        
        // Handle image upload
        $image_name = '';
        $upload_ok = true;
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['image']['tmp_name'];
            $file_orig_name = $_FILES['image']['name'];
            $file_ext = strtolower(pathinfo($file_orig_name, PATHINFO_EXTENSION));
            $allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];
            
            if (!in_array($file_ext, $allowed_exts)) {
                $error_msg = 'Format file gambar tidak diperbolehkan (hanya JPG, JPEG, PNG, WEBP).';
                $upload_ok = false;
            } else {
                $image_name = 'port_' . time() . '_' . uniqid() . '.' . $file_ext;
                $dest_path = __DIR__ . '/../assets/uploads/portfolio/' . $image_name;
                
                if (!move_uploaded_file($file_tmp, $dest_path)) {
                    $error_msg = 'Gagal mengunggah gambar ke server.';
                    $upload_ok = false;
                }
            }
        }
        
        if ($upload_ok) {
            if ($action === 'add') {
                try {
                    $stmt = $db->prepare("INSERT INTO portfolio (title, slug, category, location, year, client, description, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$title, $slug, $category, $location, $year, $client, $description, $image_name]);
                    $success_msg = 'Proyek baru berhasil ditambahkan ke portofolio!';
                    $action = 'list';
                } catch (Exception $e) {
                    if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                        $error_msg = 'Proyek dengan judul serupa sudah terdaftar!';
                    } else {
                        $error_msg = 'Terjadi kesalahan database: ' . $e->getMessage();
                    }
                }
            } elseif ($action === 'edit' && $id > 0) {
                try {
                    if (!empty($image_name)) {
                        // Delete old image
                        $old_img = $db->prepare("SELECT image FROM portfolio WHERE id = ?");
                        $old_img->execute([$id]);
                        $old_img_name = $old_img->fetchColumn();
                        if (!empty($old_img_name) && file_exists(__DIR__ . '/../assets/uploads/portfolio/' . $old_img_name)) {
                            unlink(__DIR__ . '/../assets/uploads/portfolio/' . $old_img_name);
                        }
                        
                        $stmt = $db->prepare("UPDATE portfolio SET title = ?, slug = ?, category = ?, location = ?, year = ?, client = ?, description = ?, image = ? WHERE id = ?");
                        $stmt->execute([$title, $slug, $category, $location, $year, $client, $description, $image_name, $id]);
                    } else {
                        $stmt = $db->prepare("UPDATE portfolio SET title = ?, slug = ?, category = ?, location = ?, year = ?, client = ?, description = ? WHERE id = ?");
                        $stmt->execute([$title, $slug, $category, $location, $year, $client, $description, $id]);
                    }
                    $success_msg = 'Portofolio proyek berhasil diperbarui!';
                    $action = 'list';
                } catch (Exception $e) {
                    $error_msg = 'Terjadi kesalahan database: ' . $e->getMessage();
                }
            }
        }
    }
}

// Process Delete Request
if ($action === 'delete' && $id > 0) {
    try {
        // Delete image file
        $old_img = $db->prepare("SELECT image FROM portfolio WHERE id = ?");
        $old_img->execute([$id]);
        $old_img_name = $old_img->fetchColumn();
        if (!empty($old_img_name) && file_exists(__DIR__ . '/../assets/uploads/portfolio/' . $old_img_name)) {
            unlink(__DIR__ . '/../assets/uploads/portfolio/' . $old_img_name);
        }
        
        // Delete database record
        $stmt = $db->prepare("DELETE FROM portfolio WHERE id = ?");
        $stmt->execute([$id]);
        $success_msg = 'Proyek berhasil dihapus dari portofolio!';
    } catch (Exception $e) {
        $error_msg = 'Gagal menghapus data: ' . $e->getMessage();
    }
    $action = 'list';
}

// Fetch single portfolio for edit
$port_edit = null;
if ($action === 'edit' && $id > 0) {
    try {
        $stmt = $db->prepare("SELECT * FROM portfolio WHERE id = ?");
        $stmt->execute([$id]);
        $port_edit = $stmt->fetch();
    } catch (Exception $e) {}
}

// Fetch all portfolios
$portfolios_list = [];
try {
    $portfolios_list = $db->query("SELECT * FROM portfolio ORDER BY id DESC")->fetchAll();
} catch (Exception $e) {}
?>

<!-- Header Section -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold text-primary mb-0">Kelola Portofolio Proyek</h1>
        <span class="small text-muted">Kelola rekam jejak pelaksanaan proyek korporasi PT. Hastra Karya Persada.</span>
    </div>
    <?php if ($action === 'list'): ?>
        <a href="portfolio.php?action=add" class="btn btn-primary"><i class="bi-plus-circle me-1"></i> Tambah Proyek</a>
    <?php else: ?>
        <a href="portfolio.php" class="btn btn-outline-secondary"><i class="bi-arrow-left me-1"></i> Kembali ke Daftar</a>
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
    <!-- LIST VIEW -->
    <div class="card admin-table-card border-0 shadow-sm">
        <div class="card-header fw-bold">Daftar Portofolio Proyek</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 small text-muted align-middle">
                    <thead class="table-primary text-primary">
                        <tr>
                            <th class="py-3 ps-4" style="width: 10%;">Gambar</th>
                            <th class="py-3">Nama Proyek</th>
                            <th class="py-3">Kategori</th>
                            <th class="py-3">Klien</th>
                            <th class="py-3">Lokasi</th>
                            <th class="py-3">Tahun</th>
                            <th class="py-3 pe-4 text-end" style="width: 15%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($portfolios_list) > 0): ?>
                            <?php foreach ($portfolios_list as $p): 
                                $preview_img = $p['image'];
                                if (empty($preview_img) || !file_exists(__DIR__ . '/../assets/uploads/portfolio/' . $preview_img)) {
                                    if ($p['slug'] === 'pembangunan-gedung-perkantoran-menara-hastra') {
                                        $preview_img = "https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=150&q=80";
                                    } elseif ($p['slug'] === 'pengadaan-alat-berat-proyek-jalan-tol-sumatera') {
                                        $preview_img = "https://images.unsplash.com/photo-1578328819058-b69f3a3b0f6b?auto=format&fit=crop&w=150&q=80";
                                    } else {
                                        $preview_img = "https://images.unsplash.com/photo-1436491865332-7a61a109cc05?auto=format&fit=crop&w=150&q=80";
                                    }
                                } else {
                                    $preview_img = base_url('assets/uploads/portfolio/' . $preview_img);
                                }
                            ?>
                                <tr>
                                    <td class="py-3 ps-4">
                                        <img src="<?php echo $preview_img; ?>" class="img-thumbnail rounded" style="width: 70px; height: 50px; object-fit: cover;">
                                    </td>
                                    <td class="py-3 fw-bold text-primary"><?php echo sanitize($p['title']); ?></td>
                                    <td class="py-3"><span class="badge bg-light text-primary border border-secondary-subtle px-2 py-1"><?php echo sanitize($p['category']); ?></span></td>
                                    <td class="py-3"><?php echo sanitize($p['client']); ?></td>
                                    <td class="py-3"><?php echo sanitize($p['location']); ?></td>
                                    <td class="py-3"><?php echo sanitize($p['year']); ?></td>
                                    <td class="py-3 pe-4 text-end">
                                        <a href="portfolio.php?action=edit&id=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-primary me-1"><i class="bi-pencil"></i></a>
                                        <a href="portfolio.php?action=delete&id=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus portofolio ini?')"><i class="bi-trash"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Belum ada portofolio proyek yang terdaftar.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php elseif ($action === 'add' || ($action === 'edit' && $port_edit)): ?>
    <!-- ADD & EDIT FORM -->
    <div class="card border-0 shadow-sm bg-white p-4" style="border-top: 4px solid var(--color-secondary);">
        <h3 class="fw-bold text-primary mb-3 h5"><?php echo ($action === 'add') ? 'Tambah Proyek Baru' : 'Edit Proyek'; ?></h3>
        
        <form action="?action=<?php echo $action; ?><?php echo $id > 0 ? '&id='.$id : ''; ?>" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">
            
            <div class="mb-3">
                <label for="title" class="form-label small fw-bold text-primary">Nama Proyek</label>
                <input type="text" class="form-control" id="title" name="title" required value="<?php echo ($action === 'edit') ? sanitize($port_edit['title']) : ''; ?>" placeholder="Masukkan nama proyek lengkap">
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="category" class="form-label small fw-bold text-primary">Kategori Proyek</label>
                    <select class="form-select" id="category" name="category" required>
                        <option value="Konstruksi" <?php echo ($action === 'edit' && $port_edit['category'] === 'Konstruksi') ? 'selected' : ''; ?>>Konstruksi & Infrastruktur</option>
                        <option value="Pengadaan" <?php echo ($action === 'edit' && $port_edit['category'] === 'Pengadaan') ? 'selected' : ''; ?>>Pengadaan (Procurement)</option>
                        <option value="Konsultansi" <?php echo ($action === 'edit' && $port_edit['category'] === 'Konsultansi') ? 'selected' : ''; ?>>Konsultansi Manajemen Proyek</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="client" class="form-label small fw-bold text-primary">Nama Klien / Mitra Bisnis</label>
                    <input type="text" class="form-control" id="client" name="client" required value="<?php echo ($action === 'edit') ? sanitize($port_edit['client']) : ''; ?>" placeholder="Contoh: PT. Wijaya Karya Tbk">
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="location" class="form-label small fw-bold text-primary">Lokasi Pekerjaan</label>
                    <input type="text" class="form-control" id="location" name="location" required value="<?php echo ($action === 'edit') ? sanitize($port_edit['location']) : ''; ?>" placeholder="Contoh: Tangerang, Banten">
                </div>
                <div class="col-md-6">
                    <label for="year" class="form-label small fw-bold text-primary">Tahun Pelaksanaan / Selesai</label>
                    <input type="text" class="form-control" id="year" name="year" required value="<?php echo ($action === 'edit') ? sanitize($port_edit['year']) : ''; ?>" placeholder="Contoh: 2025">
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label small fw-bold text-primary">Deskripsi Proyek</label>
                <textarea class="form-control" id="description" name="description" rows="5" required placeholder="Masukkan penjelasan rincian proyek..."><?php echo ($action === 'edit') ? $port_edit['description'] : ''; ?></textarea>
            </div>

            <div class="mb-4">
                <label for="image" class="form-label small fw-bold text-primary">File Gambar Proyek (Format: JPG, JPEG, PNG, WEBP)</label>
                <input type="file" class="form-control" id="image" name="image" <?php echo ($action === 'add') ? 'required' : ''; ?>>
                <?php if ($action === 'edit' && !empty($port_edit['image'])): ?>
                    <div class="mt-2">
                        <span class="small text-muted d-block mb-1">Gambar saat ini:</span>
                        <img src="<?php echo base_url('assets/uploads/portfolio/' . $port_edit['image']); ?>" class="img-thumbnail rounded" style="max-height: 100px;">
                    </div>
                <?php endif; ?>
            </div>
            
            <button type="submit" class="btn btn-primary px-4 py-2"><i class="bi-check-circle me-1"></i> Simpan Proyek</button>
            <a href="portfolio.php" class="btn btn-outline-secondary px-4 py-2 ms-2">Batal</a>
        </form>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
