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
        $category = sanitize($_POST['category']);
        
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
                $image_name = 'gal_' . time() . '_' . uniqid() . '.' . $file_ext;
                $dest_path = __DIR__ . '/../assets/uploads/gallery/' . $image_name;
                
                if (!move_uploaded_file($file_tmp, $dest_path)) {
                    $error_msg = 'Gagal mengunggah gambar ke server.';
                    $upload_ok = false;
                }
            }
        }
        
        if ($upload_ok) {
            if ($action === 'add') {
                try {
                    $stmt = $db->prepare("INSERT INTO gallery (title, category, image) VALUES (?, ?, ?)");
                    $stmt->execute([$title, $category, $image_name]);
                    $success_msg = 'Foto baru berhasil ditambahkan ke galeri!';
                    $action = 'list';
                } catch (Exception $e) {
                    $error_msg = 'Terjadi kesalahan database: ' . $e->getMessage();
                }
            } elseif ($action === 'edit' && $id > 0) {
                try {
                    if (!empty($image_name)) {
                        // Delete old image file
                        $old_img = $db->prepare("SELECT image FROM gallery WHERE id = ?");
                        $old_img->execute([$id]);
                        $old_img_name = $old_img->fetchColumn();
                        if (!empty($old_img_name) && strpos($old_img_name, 'http') !== 0 && file_exists(__DIR__ . '/../assets/uploads/gallery/' . $old_img_name)) {
                            unlink(__DIR__ . '/../assets/uploads/gallery/' . $old_img_name);
                        }
                        
                        $stmt = $db->prepare("UPDATE gallery SET title = ?, category = ?, image = ? WHERE id = ?");
                        $stmt->execute([$title, $category, $image_name, $id]);
                    } else {
                        $stmt = $db->prepare("UPDATE gallery SET title = ?, category = ? WHERE id = ?");
                        $stmt->execute([$title, $category, $id]);
                    }
                    $success_msg = 'Foto galeri berhasil diperbarui!';
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
        $old_img = $db->prepare("SELECT image FROM gallery WHERE id = ?");
        $old_img->execute([$id]);
        $old_img_name = $old_img->fetchColumn();
        if (!empty($old_img_name) && strpos($old_img_name, 'http') !== 0 && file_exists(__DIR__ . '/../assets/uploads/gallery/' . $old_img_name)) {
            unlink(__DIR__ . '/../assets/uploads/gallery/' . $old_img_name);
        }
        
        // Delete database record
        $stmt = $db->prepare("DELETE FROM gallery WHERE id = ?");
        $stmt->execute([$id]);
        $success_msg = 'Foto berhasil dihapus dari galeri!';
    } catch (Exception $e) {
        $error_msg = 'Gagal menghapus data: ' . $e->getMessage();
    }
    $action = 'list';
}

// Fetch single gallery for edit
$gal_edit = null;
if ($action === 'edit' && $id > 0) {
    try {
        $stmt = $db->prepare("SELECT * FROM gallery WHERE id = ?");
        $stmt->execute([$id]);
        $gal_edit = $stmt->fetch();
    } catch (Exception $e) {}
}

// Fetch all gallery items
$gallery_list = [];
try {
    $gallery_list = $db->query("SELECT * FROM gallery ORDER BY id DESC")->fetchAll();
} catch (Exception $e) {}
?>

<!-- Header Section -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold text-primary mb-0">Kelola Galeri Dokumentasi</h1>
        <span class="small text-muted">Kelola arsip dokumentasi proyek, kegiatan karyawan, dan event korporasi.</span>
    </div>
    <?php if ($action === 'list'): ?>
        <a href="gallery.php?action=add" class="btn btn-primary"><i class="bi-plus-circle me-1"></i> Unggah Foto</a>
    <?php else: ?>
        <a href="gallery.php" class="btn btn-outline-secondary"><i class="bi-arrow-left me-1"></i> Kembali ke Daftar</a>
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
        <div class="card-header fw-bold">Daftar Foto Galeri</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 small text-muted align-middle">
                    <thead class="table-primary text-primary">
                        <tr>
                            <th class="py-3 ps-4" style="width: 15%;">Preview Gambar</th>
                            <th class="py-3">Keterangan Foto</th>
                            <th class="py-3">Kategori</th>
                            <th class="py-3 pe-4 text-end" style="width: 15%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($gallery_list) > 0): ?>
                            <?php foreach ($gallery_list as $g): 
                                $preview_img = $g['image'];
                                if (strpos($preview_img, 'http') !== 0) {
                                    if (empty($preview_img) || !file_exists(__DIR__ . '/../assets/uploads/gallery/' . $preview_img)) {
                                        $preview_img = "https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=150&q=80";
                                    } else {
                                        $preview_img = base_url('assets/uploads/gallery/' . $preview_img);
                                    }
                                }
                            ?>
                                <tr>
                                    <td class="py-3 ps-4">
                                        <img src="<?php echo $preview_img; ?>" class="img-thumbnail rounded" style="width: 80px; height: 50px; object-fit: cover;">
                                    </td>
                                    <td class="py-3 fw-bold text-primary"><?php echo sanitize($g['title']); ?></td>
                                    <td class="py-3">
                                        <span class="badge bg-light text-primary border border-secondary-subtle px-2 py-1">
                                            <?php 
                                            if ($g['category'] === 'kegiatan') echo 'Kegiatan';
                                            elseif ($g['category'] === 'proyek') echo 'Dokumentasi Proyek';
                                            elseif ($g['category'] === 'event') echo 'Event';
                                            ?>
                                        </span>
                                    </td>
                                    <td class="py-3 pe-4 text-end">
                                        <a href="gallery.php?action=edit&id=<?php echo $g['id']; ?>" class="btn btn-sm btn-outline-primary me-1"><i class="bi-pencil"></i></a>
                                        <a href="gallery.php?action=delete&id=<?php echo $g['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus foto ini?')"><i class="bi-trash"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Belum ada foto galeri yang diunggah.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php elseif ($action === 'add' || ($action === 'edit' && $gal_edit)): ?>
    <!-- ADD & EDIT FORM -->
    <div class="card border-0 shadow-sm bg-white p-4" style="border-top: 4px solid var(--color-secondary);">
        <h3 class="fw-bold text-primary mb-3 h5"><?php echo ($action === 'add') ? 'Unggah Foto Baru' : 'Edit Foto'; ?></h3>
        
        <form action="?action=<?php echo $action; ?><?php echo $id > 0 ? '&id='.$id : ''; ?>" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">
            
            <div class="mb-3">
                <label for="title" class="form-label small fw-bold text-primary">Keterangan / Judul Foto</label>
                <input type="text" class="form-control" id="title" name="title" required value="<?php echo ($action === 'edit') ? sanitize($gal_edit['title']) : ''; ?>" placeholder="Masukkan deskripsi pendek foto">
            </div>
            
            <div class="mb-3">
                <label for="category" class="form-label small fw-bold text-primary">Kategori Galeri</label>
                <select class="form-select" id="category" name="category" required>
                    <option value="kegiatan" <?php echo ($action === 'edit' && $gal_edit['category'] === 'kegiatan') ? 'selected' : ''; ?>>Kegiatan Perusahaan</option>
                    <option value="proyek" <?php echo ($action === 'edit' && $gal_edit['category'] === 'proyek') ? 'selected' : ''; ?>>Dokumentasi Proyek</option>
                    <option value="event" <?php echo ($action === 'edit' && $gal_edit['category'] === 'event') ? 'selected' : ''; ?>>Event & Acara</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="image" class="form-label small fw-bold text-primary">File Gambar Foto (Format: JPG, JPEG, PNG, WEBP)</label>
                <input type="file" class="form-control" id="image" name="image" <?php echo ($action === 'add') ? 'required' : ''; ?>>
                <?php if ($action === 'edit' && !empty($gal_edit['image'])): 
                    $preview_img = $gal_edit['image'];
                    if (strpos($preview_img, 'http') !== 0) {
                        $preview_img = base_url('assets/uploads/gallery/' . $preview_img);
                    }
                ?>
                    <div class="mt-2">
                        <span class="small text-muted d-block mb-1">Gambar saat ini:</span>
                        <img src="<?php echo $preview_img; ?>" class="img-thumbnail rounded" style="max-height: 100px;">
                    </div>
                <?php endif; ?>
            </div>
            
            <button type="submit" class="btn btn-primary px-4 py-2"><i class="bi-check-circle me-1"></i> Simpan Foto</button>
            <a href="gallery.php" class="btn btn-outline-secondary px-4 py-2 ms-2">Batal</a>
        </form>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
