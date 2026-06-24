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
        $slug = slugify($title); // Auto generated slug
        $icon = sanitize($_POST['icon']);
        $short_description = sanitize($_POST['short_description']);
        $description = $_POST['description']; // Don't strip tags to allow formatting
        $advantages = sanitize($_POST['advantages']);
        $workflow = sanitize($_POST['workflow']);
        
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
                $image_name = 'svc_' . time() . '_' . uniqid() . '.' . $file_ext;
                $dest_path = __DIR__ . '/../assets/uploads/services/' . $image_name;
                
                if (!move_uploaded_file($file_tmp, $dest_path)) {
                    $error_msg = 'Gagal mengunggah gambar ke server.';
                    $upload_ok = false;
                }
            }
        }
        
        if ($upload_ok) {
            if ($action === 'add') {
                try {
                    $stmt = $db->prepare("INSERT INTO services (title, slug, icon, short_description, description, advantages, workflow, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$title, $slug, $icon, $short_description, $description, $advantages, $workflow, $image_name]);
                    $success_msg = 'Layanan baru berhasil ditambahkan!';
                    $action = 'list';
                } catch (Exception $e) {
                    if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                        $error_msg = 'Layanan dengan judul serupa sudah ada!';
                    } else {
                        $error_msg = 'Terjadi kesalahan database: ' . $e->getMessage();
                    }
                }
            } elseif ($action === 'edit' && $id > 0) {
                try {
                    if (!empty($image_name)) {
                        // Delete old image first
                        $old_img = $db->prepare("SELECT image FROM services WHERE id = ?");
                        $old_img->execute([$id]);
                        $old_img_name = $old_img->fetchColumn();
                        if (!empty($old_img_name) && file_exists(__DIR__ . '/../assets/uploads/services/' . $old_img_name)) {
                            unlink(__DIR__ . '/../assets/uploads/services/' . $old_img_name);
                        }
                        
                        $stmt = $db->prepare("UPDATE services SET title = ?, slug = ?, icon = ?, short_description = ?, description = ?, advantages = ?, workflow = ?, image = ? WHERE id = ?");
                        $stmt->execute([$title, $slug, $icon, $short_description, $description, $advantages, $workflow, $image_name, $id]);
                    } else {
                        $stmt = $db->prepare("UPDATE services SET title = ?, slug = ?, icon = ?, short_description = ?, description = ?, advantages = ?, workflow = ? WHERE id = ?");
                        $stmt->execute([$title, $slug, $icon, $short_description, $description, $advantages, $workflow, $id]);
                    }
                    $success_msg = 'Layanan berhasil diperbarui!';
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
        // Delete physical image file
        $old_img = $db->prepare("SELECT image FROM services WHERE id = ?");
        $old_img->execute([$id]);
        $old_img_name = $old_img->fetchColumn();
        if (!empty($old_img_name) && file_exists(__DIR__ . '/../assets/uploads/services/' . $old_img_name)) {
            unlink(__DIR__ . '/../assets/uploads/services/' . $old_img_name);
        }
        
        // Delete from database
        $stmt = $db->prepare("DELETE FROM services WHERE id = ?");
        $stmt->execute([$id]);
        $success_msg = 'Layanan berhasil dihapus!';
    } catch (Exception $e) {
        $error_msg = 'Gagal menghapus data: ' . $e->getMessage();
    }
    $action = 'list';
}

// Fetch single service for edit
$svc_edit = null;
if ($action === 'edit' && $id > 0) {
    try {
        $stmt = $db->prepare("SELECT * FROM services WHERE id = ?");
        $stmt->execute([$id]);
        $svc_edit = $stmt->fetch();
    } catch (Exception $e) {}
}

// Fetch all services
$services_list = [];
try {
    $services_list = $db->query("SELECT * FROM services ORDER BY id DESC")->fetchAll();
} catch (Exception $e) {}
?>

<!-- Header Section -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
<div class="mb-4">
    <h1 class="h3 fw-bold text-primary mb-0" style="color: var(--a-navy) !important;">Kelola Layanan</h1>
    <span class="small text-muted" style="color: var(--a-gray) !important;">Tambah, ubah, dan hapus layanan/bidang usaha perusahaan.</span>
</div>

<!-- Status Alerts -->
<?php if (!empty($success_msg)): ?>
    <div class="alert-admin alert-admin-success mb-4"><i class="bi-check-circle-fill"></i> <?php echo $success_msg; ?></div>
<?php endif; ?>
<?php if (!empty($error_msg)): ?>
    <div class="alert-admin alert-admin-danger mb-4"><i class="bi-exclamation-triangle-fill"></i> <?php echo $error_msg; ?></div>
<?php endif; ?>

<?php if ($action === 'list'): ?>
    <!-- LIST VIEW -->
    <div class="card admin-table-card border-0 shadow-sm">
        <div class="card-header fw-bold d-flex justify-content-between align-items-center">
            Daftar Layanan Perusahaan
            <a href="services.php?action=add" class="btn-admin-primary btn-sm"><i class="bi-plus-circle me-1"></i> Tambah Layanan</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 small text-muted align-middle">
                    <thead class="table-primary text-primary">
                        <tr>
                            <th class="py-3 ps-4" style="width: 10%;">Gambar</th>
                            <th class="py-3">Icon</th>
                            <th class="py-3">Judul Layanan</th>
                            <th class="py-3">Slug SEO</th>
                            <th class="py-3">Deskripsi Singkat</th>
                            <th class="py-3 pe-4 text-end" style="width: 15%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($services_list) > 0): ?>
                            <?php foreach ($services_list as $s): 
                                $preview_img = $s['image'];
                                if (empty($preview_img) || !file_exists(__DIR__ . '/../assets/uploads/services/' . $preview_img)) {
                                    $preview_img = "https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&w=150&q=80";
                                } else {
                                    $preview_img = base_url('assets/uploads/services/' . $preview_img);
                                }
                            ?>
                                <tr>
                                    <td class="py-3 ps-4">
                                        <img src="<?php echo $preview_img; ?>" class="img-thumbnail rounded" style="width: 70px; height: 50px; object-fit: cover;">
                                    </td>
                                    <td class="py-3"><i class="bi <?php echo sanitize($s['icon']); ?> fs-4 text-gold"></i></td>
                                    <td class="py-3 fw-bold text-primary"><?php echo sanitize($s['title']); ?></td>
                                    <td class="py-3"><code class="small"><?php echo sanitize($s['slug']); ?></code></td>
                                    <td class="py-3"><?php echo sanitize(substr($s['short_description'], 0, 80)) . '...'; ?></td>
                                    <td class="py-3 pe-4 text-end">
                                        <a href="services.php?action=edit&id=<?php echo $s['id']; ?>" class="btn btn-sm btn-outline-primary me-1"><i class="bi-pencil"></i></a>
                                        <a href="services.php?action=delete&id=<?php echo $s['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus layanan ini? Seluruh data terkait akan hilang.')"><i class="bi-trash"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Belum ada layanan yang didaftarkan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php elseif ($action === 'add' || ($action === 'edit' && $edit_data)): ?>
<!-- Add/Edit Form -->
<div class="admin-card mb-5" id="form-container">
    <div class="admin-card-header">
        <h5 class="mb-0 fw-bold" style="color: var(--a-navy);">
            <?php echo $action === 'edit' ? 'Edit Layanan' : 'Tambah Layanan Baru'; ?>
        </h5>
    </div>
    <div class="admin-card-body">
        <form action="?action=<?php echo $action; ?><?php echo $id > 0 ? '&id='.$id : ''; ?>" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">
            
            <div class="row g-4 mb-4">
                <div class="col-md-8">
                    <label for="title" class="form-label-admin">Judul Layanan</label>
                    <input type="text" class="form-control-admin" id="title" name="title" required value="<?php echo sanitize($edit_data['title'] ?? ''); ?>">
                </div>
                <div class="col-md-4">
                    <label for="icon" class="form-label-admin">Ikon (Bootstrap Icons Class)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="<?php echo sanitize($edit_data['icon'] ?? 'bi-star'); ?>"></i></span>
                        <input type="text" class="form-control-admin" id="icon" name="icon" placeholder="Contoh: bi-building" required value="<?php echo sanitize($edit_data['icon'] ?? ''); ?>">
                    </div>
                    <span class="small text-muted d-block mt-1">Referensi: <a href="https://icons.getbootstrap.com/" target="_blank">Bootstrap Icons</a></span>
                </div>
            </div>

            <div class="mb-4">
                <label for="short_description" class="form-label-admin">Deskripsi Singkat (Ditampilkan di Beranda & Kartu Layanan)</label>
                <textarea class="form-control-admin" id="short_description" name="short_description" rows="2" maxlength="255" required><?php echo sanitize($edit_data['short_description'] ?? ''); ?></textarea>
            </div>

            <div class="mb-4">
                <label for="description" class="form-label-admin">Deskripsi Lengkap (Mendukung HTML)</label>
                <textarea class="form-control-admin" id="description" name="description" rows="6" required><?php echo $edit_data['description'] ?? ''; ?></textarea>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <label for="advantages" class="form-label-admin">Keunggulan Layanan (Pisahkan dengan baris baru)</label>
                    <textarea class="form-control-admin" id="advantages" name="advantages" rows="5"><?php echo sanitize($edit_data['advantages'] ?? ''); ?></textarea>
                </div>
                <div class="col-md-6">
                    <label for="workflow" class="form-label-admin">Proses Kerja / Tahapan (Pisahkan dengan baris baru)</label>
                    <textarea class="form-control-admin" id="workflow" name="workflow" rows="5"><?php echo sanitize($edit_data['workflow'] ?? ''); ?></textarea>
                </div>
            </div>

            <div class="mb-4">
                <label for="image" class="form-label-admin">Gambar Layanan / Cover (Format: JPG, PNG, WEBP)</label>
                <input type="file" class="form-control-admin" style="background:#fff;" id="image" name="image" <?php echo $action === 'add' ? 'required' : ''; ?>>
                <?php if ($action === 'edit' && !empty($edit_data['image'])): ?>
                    <div class="mt-3">
                        <span class="small d-block mb-2" style="color:var(--a-gray);font-weight:600;">Gambar saat ini:</span>
                        <img src="<?php echo base_url('assets/uploads/services/' . $edit_data['image']); ?>" style="max-height: 120px; border-radius:var(--a-radius-sm); border:1px solid var(--a-border); box-shadow:0 2px 8px rgba(0,0,0,0.05);">
                    </div>
                <?php endif; ?>
            </div>

            <div class="d-flex gap-2 pt-4 border-top" style="border-color: var(--a-border) !important;">
                <button type="submit" class="btn-admin-primary px-4"><i class="bi-save"></i> <?php echo $action === 'edit' ? 'Simpan Perubahan' : 'Tambah Layanan'; ?></button>
                <a href="?action=list" class="btn btn-light px-4 border" style="border-radius: var(--a-radius-sm); font-weight: 600; color: var(--a-gray);">Batal</a>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
