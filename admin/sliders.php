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
        $subheadline = sanitize($_POST['subheadline']);
        $link_text = sanitize($_POST['link_text']);
        $link_url = sanitize($_POST['link_url']);
        $status = isset($_POST['status']) ? 1 : 0;
        
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
                $image_name = 'slider_' . time() . '_' . uniqid() . '.' . $file_ext;
                $dest_path = __DIR__ . '/../assets/uploads/sliders/' . $image_name;
                
                if (!move_uploaded_file($file_tmp, $dest_path)) {
                    $error_msg = 'Gagal mengunggah gambar ke server.';
                    $upload_ok = false;
                }
            }
        }
        
        if ($upload_ok) {
            if ($action === 'add') {
                try {
                    $stmt = $db->prepare("INSERT INTO sliders (title, subheadline, image, link_text, link_url, status) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$title, $subheadline, $image_name, $link_text, $link_url, $status]);
                    $success_msg = 'Slider baru berhasil ditambahkan!';
                    $action = 'list';
                } catch (Exception $e) {
                    $error_msg = 'Terjadi kesalahan database: ' . $e->getMessage();
                }
            } elseif ($action === 'edit' && $id > 0) {
                try {
                    if (!empty($image_name)) {
                        // Delete old image first
                        $old_img = $db->prepare("SELECT image FROM sliders WHERE id = ?");
                        $old_img->execute([$id]);
                        $old_img_name = $old_img->fetchColumn();
                        if (!empty($old_img_name) && file_exists(__DIR__ . '/../assets/uploads/sliders/' . $old_img_name)) {
                            unlink(__DIR__ . '/../assets/uploads/sliders/' . $old_img_name);
                        }
                        
                        $stmt = $db->prepare("UPDATE sliders SET title = ?, subheadline = ?, image = ?, link_text = ?, link_url = ?, status = ? WHERE id = ?");
                        $stmt->execute([$title, $subheadline, $image_name, $link_text, $link_url, $status, $id]);
                    } else {
                        $stmt = $db->prepare("UPDATE sliders SET title = ?, subheadline = ?, link_text = ?, link_url = ?, status = ? WHERE id = ?");
                        $stmt->execute([$title, $subheadline, $link_text, $link_url, $status, $id]);
                    }
                    $success_msg = 'Slider berhasil diperbarui!';
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
        $old_img = $db->prepare("SELECT image FROM sliders WHERE id = ?");
        $old_img->execute([$id]);
        $old_img_name = $old_img->fetchColumn();
        if (!empty($old_img_name) && file_exists(__DIR__ . '/../assets/uploads/sliders/' . $old_img_name)) {
            unlink(__DIR__ . '/../assets/uploads/sliders/' . $old_img_name);
        }
        
        // Delete from database
        $stmt = $db->prepare("DELETE FROM sliders WHERE id = ?");
        $stmt->execute([$id]);
        $success_msg = 'Slider berhasil dihapus!';
    } catch (Exception $e) {
        $error_msg = 'Gagal menghapus data: ' . $e->getMessage();
    }
    $action = 'list';
}

// Fetch single slider for edit
$slider_edit = null;
if ($action === 'edit' && $id > 0) {
    try {
        $stmt = $db->prepare("SELECT * FROM sliders WHERE id = ?");
        $stmt->execute([$id]);
        $slider_edit = $stmt->fetch();
    } catch (Exception $e) {}
}

// Fetch all sliders
$sliders_list = [];
try {
    $sliders_list = $db->query("SELECT * FROM sliders ORDER BY id DESC")->fetchAll();
} catch (Exception $e) {}
?>

<!-- Header Section -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold text-primary mb-0">Kelola Slider Beranda</h1>
        <span class="small text-muted">Kelola gambar hero carousel beserta teks promo utama di halaman beranda.</span>
    </div>
    <?php if ($action === 'list'): ?>
        <a href="sliders.php?action=add" class="btn btn-primary"><i class="bi-plus-circle me-1"></i> Tambah Slider</a>
    <?php else: ?>
        <a href="sliders.php" class="btn btn-outline-secondary"><i class="bi-arrow-left me-1"></i> Kembali ke Daftar</a>
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
        <div class="card-header fw-bold">Daftar Slider Carousel</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 small text-muted align-middle">
                    <thead class="table-primary text-primary">
                        <tr>
                            <th class="py-3 ps-4" style="width: 15%;">Preview Gambar</th>
                            <th class="py-3">Headline (Judul Utama)</th>
                            <th class="py-3">Subheadline</th>
                            <th class="py-3">Tombol Teks</th>
                            <th class="py-3">Status</th>
                            <th class="py-3 pe-4 text-end" style="width: 15%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($sliders_list) > 0): ?>
                            <?php foreach ($sliders_list as $sl): 
                                $preview_img = $sl['image'];
                                if (empty($preview_img) || !file_exists(__DIR__ . '/../assets/uploads/sliders/' . $preview_img)) {
                                    $preview_img = "https://images.unsplash.com/photo-1541888946425-d81bb19240f5?auto=format&fit=crop&w=150&q=80";
                                } else {
                                    $preview_img = base_url('assets/uploads/sliders/' . $preview_img);
                                }
                            ?>
                                <tr>
                                    <td class="py-3 ps-4">
                                        <img src="<?php echo $preview_img; ?>" class="img-thumbnail rounded" style="width: 100px; height: 60px; object-fit: cover;">
                                    </td>
                                    <td class="py-3 fw-bold text-primary"><?php echo sanitize($sl['title']); ?></td>
                                    <td class="py-3"><?php echo sanitize(substr($sl['subheadline'], 0, 70)) . '...'; ?></td>
                                    <td class="py-3"><span class="badge bg-light text-primary border border-secondary-subtle px-2 py-1"><?php echo sanitize($sl['link_text']); ?></span></td>
                                    <td class="py-3">
                                        <?php if ($sl['status'] == 1): ?>
                                            <span class="badge bg-success">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Nonaktif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 pe-4 text-end">
                                        <a href="sliders.php?action=edit&id=<?php echo $sl['id']; ?>" class="btn btn-sm btn-outline-primary me-1"><i class="bi-pencil"></i></a>
                                        <a href="sliders.php?action=delete&id=<?php echo $sl['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus slider ini?')"><i class="bi-trash"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Belum ada slider yang ditambahkan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php elseif ($action === 'add' || ($action === 'edit' && $slider_edit)): ?>
    <!-- ADD & EDIT VIEW FORM -->
    <div class="card border-0 shadow-sm bg-white p-4" style="border-top: 4px solid var(--color-secondary);">
        <h3 class="fw-bold text-primary mb-3 h5"><?php echo ($action === 'add') ? 'Tambah Slider Baru' : 'Edit Slider'; ?></h3>
        
        <form action="?action=<?php echo $action; ?><?php echo $id > 0 ? '&id='.$id : ''; ?>" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">
            
            <div class="mb-3">
                <label for="title" class="form-label small fw-bold text-primary">Headline (Judul Utama)</label>
                <input type="text" class="form-control" id="title" name="title" required value="<?php echo ($action === 'edit') ? sanitize($slider_edit['title']) : ''; ?>" placeholder="Masukkan headline utama promo slider">
            </div>
            
            <div class="mb-3">
                <label for="subheadline" class="form-label small fw-bold text-primary">Subheadline (Deskripsi Pendek)</label>
                <textarea class="form-control" id="subheadline" name="subheadline" rows="3" required placeholder="Masukkan penjelasan ringkas slider"><?php echo ($action === 'edit') ? sanitize($slider_edit['subheadline']) : ''; ?></textarea>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="link_text" class="form-label small fw-bold text-primary">Teks Tombol CTA</label>
                    <input type="text" class="form-control" id="link_text" name="link_text" required value="<?php echo ($action === 'edit') ? sanitize($slider_edit['link_text']) : 'Tentang Perusahaan'; ?>" placeholder="Contoh: Hubungi Kami">
                </div>
                <div class="col-md-6">
                    <label for="link_url" class="form-label small fw-bold text-primary">URL Link Halaman (Tujuan Tombol)</label>
                    <input type="text" class="form-control" id="link_url" name="link_url" required value="<?php echo ($action === 'edit') ? sanitize($slider_edit['link_url']) : 'tentang.php'; ?>" placeholder="Contoh: hubungi-kami.php">
                </div>
            </div>

            <div class="mb-3">
                <label for="image" class="form-label small fw-bold text-primary">File Gambar Slider (Format: JPG, JPEG, PNG, WEBP)</label>
                <input type="file" class="form-control" id="image" name="image" <?php echo ($action === 'add') ? 'required' : ''; ?>>
                <?php if ($action === 'edit' && !empty($slider_edit['image'])): ?>
                    <div class="mt-2">
                        <span class="small text-muted d-block mb-1">Gambar saat ini:</span>
                        <img src="<?php echo base_url('assets/uploads/sliders/' . $slider_edit['image']); ?>" class="img-thumbnail rounded" style="max-height: 100px;">
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="mb-4">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="status" name="status" <?php echo ($action === 'add' || ($action === 'edit' && $slider_edit['status'] == 1)) ? 'checked' : ''; ?>>
                    <label class="form-check-label small fw-bold text-primary" for="status">Aktifkan Slider (Langsung Tampil di Beranda)</label>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary px-4 py-2"><i class="bi-check-circle me-1"></i> Simpan Data</button>
            <a href="sliders.php" class="btn btn-outline-secondary px-4 py-2 ms-2">Batal</a>
        </form>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
