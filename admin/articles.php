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
        $content = $_POST['content']; // Preserve HTML structures for blog formatting
        $tags = sanitize($_POST['tags']);
        $author = sanitize($_POST['author']);
        $status = sanitize($_POST['status']);
        
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
                $image_name = 'news_' . time() . '_' . uniqid() . '.' . $file_ext;
                $dest_path = __DIR__ . '/../assets/uploads/articles/' . $image_name;
                
                if (!move_uploaded_file($file_tmp, $dest_path)) {
                    $error_msg = 'Gagal mengunggah gambar ke server.';
                    $upload_ok = false;
                }
            }
        }
        
        if ($upload_ok) {
            if ($action === 'add') {
                try {
                    $stmt = $db->prepare("INSERT INTO articles (title, slug, content, image, tags, author, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$title, $slug, $content, $image_name, $tags, $author, $status]);
                    $success_msg = 'Artikel berita baru berhasil diterbitkan!';
                    $action = 'list';
                } catch (Exception $e) {
                    if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                        $error_msg = 'Artikel dengan judul serupa sudah pernah diterbitkan!';
                    } else {
                        $error_msg = 'Terjadi kesalahan database: ' . $e->getMessage();
                    }
                }
            } elseif ($action === 'edit' && $id > 0) {
                try {
                    if (!empty($image_name)) {
                        // Delete old image
                        $old_img = $db->prepare("SELECT image FROM articles WHERE id = ?");
                        $old_img->execute([$id]);
                        $old_img_name = $old_img->fetchColumn();
                        if (!empty($old_img_name) && file_exists(__DIR__ . '/../assets/uploads/articles/' . $old_img_name)) {
                            unlink(__DIR__ . '/../assets/uploads/articles/' . $old_img_name);
                        }
                        
                        $stmt = $db->prepare("UPDATE articles SET title = ?, slug = ?, content = ?, image = ?, tags = ?, author = ?, status = ? WHERE id = ?");
                        $stmt->execute([$title, $slug, $content, $image_name, $tags, $author, $status, $id]);
                    } else {
                        $stmt = $db->prepare("UPDATE articles SET title = ?, slug = ?, content = ?, tags = ?, author = ?, status = ? WHERE id = ?");
                        $stmt->execute([$title, $slug, $content, $tags, $author, $status, $id]);
                    }
                    $success_msg = 'Artikel berita berhasil diperbarui!';
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
        // Delete physical file
        $old_img = $db->prepare("SELECT image FROM articles WHERE id = ?");
        $old_img->execute([$id]);
        $old_img_name = $old_img->fetchColumn();
        if (!empty($old_img_name) && file_exists(__DIR__ . '/../assets/uploads/articles/' . $old_img_name)) {
            unlink(__DIR__ . '/../assets/uploads/articles/' . $old_img_name);
        }
        
        // Delete database record
        $stmt = $db->prepare("DELETE FROM articles WHERE id = ?");
        $stmt->execute([$id]);
        $success_msg = 'Artikel berita berhasil dihapus!';
    } catch (Exception $e) {
        $error_msg = 'Gagal menghapus data: ' . $e->getMessage();
    }
    $action = 'list';
}

// Fetch single article for edit
$art_edit = null;
if ($action === 'edit' && $id > 0) {
    try {
        $stmt = $db->prepare("SELECT * FROM articles WHERE id = ?");
        $stmt->execute([$id]);
        $art_edit = $stmt->fetch();
    } catch (Exception $e) {}
}

// Fetch all articles
$articles_list = [];
try {
    $articles_list = $db->query("SELECT * FROM articles ORDER BY created_at DESC")->fetchAll();
} catch (Exception $e) {}
?>

<!-- Header Section -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold text-primary mb-0">Kelola Berita & Artikel</h1>
        <span class="small text-muted">Kelola berita kegiatan perusahaan, rilis pers, serta publikasi artikel.</span>
    </div>
    <?php if ($action === 'list'): ?>
        <a href="articles.php?action=add" class="btn btn-primary"><i class="bi-plus-circle me-1"></i> Tulis Artikel</a>
    <?php else: ?>
        <a href="articles.php" class="btn btn-outline-secondary"><i class="bi-arrow-left me-1"></i> Kembali ke Daftar</a>
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
        <div class="card-header fw-bold">Daftar Artikel Diterbitkan</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 small text-muted align-middle">
                    <thead class="table-primary text-primary">
                        <tr>
                            <th class="py-3 ps-4" style="width: 10%;">Gambar</th>
                            <th class="py-3">Judul Artikel</th>
                            <th class="py-3">Penulis</th>
                            <th class="py-3">Tagar</th>
                            <th class="py-3">Tanggal Dibuat</th>
                            <th class="py-3">Status</th>
                            <th class="py-3 pe-4 text-end" style="width: 15%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($articles_list) > 0): ?>
                            <?php foreach ($articles_list as $a): 
                                $preview_img = $a['image'];
                                if (empty($preview_img) || !file_exists(__DIR__ . '/../assets/uploads/articles/' . $preview_img)) {
                                    if ($a['slug'] === 'pt-hastra-karya-persada-raih-sertifikasi-iso-9001-2015-sistem-manajemen-mutu') {
                                        $preview_img = "https://images.unsplash.com/photo-1590283603385-17ffb3a7f29f?auto=format&fit=crop&w=150&q=80";
                                    } else {
                                        $preview_img = "https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=150&q=80";
                                    }
                                } else {
                                    $preview_img = base_url('assets/uploads/articles/' . $preview_img);
                                }
                            ?>
                                <tr>
                                    <td class="py-3 ps-4">
                                        <img src="<?php echo $preview_img; ?>" class="img-thumbnail rounded" style="width: 70px; height: 50px; object-fit: cover;">
                                    </td>
                                    <td class="py-3 fw-bold text-primary"><?php echo sanitize($a['title']); ?></td>
                                    <td class="py-3"><?php echo sanitize($a['author']); ?></td>
                                    <td class="py-3"><span class="small text-muted"><?php echo sanitize($a['tags']); ?></span></td>
                                    <td class="py-3"><?php echo format_date_id($a['created_at']); ?></td>
                                    <td class="py-3">
                                        <?php if ($a['status'] === 'published'): ?>
                                            <span class="badge bg-success">Diterbitkan</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Draft</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 pe-4 text-end">
                                        <a href="articles.php?action=edit&id=<?php echo $a['id']; ?>" class="btn btn-sm btn-outline-primary me-1"><i class="bi-pencil"></i></a>
                                        <a href="articles.php?action=delete&id=<?php echo $a['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus artikel ini?')"><i class="bi-trash"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Belum ada artikel berita yang dipublikasikan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php elseif ($action === 'add' || ($action === 'edit' && $art_edit)): ?>
    <!-- ADD & EDIT FORM -->
    <div class="card border-0 shadow-sm bg-white p-4" style="border-top: 4px solid var(--color-secondary);">
        <h3 class="fw-bold text-primary mb-3 h5"><?php echo ($action === 'add') ? 'Tulis Artikel Baru' : 'Edit Artikel'; ?></h3>
        
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">
            
            <div class="mb-3">
                <label for="title" class="form-label small fw-bold text-primary">Judul Artikel</label>
                <input type="text" class="form-control" id="title" name="title" required value="<?php echo ($action === 'edit') ? sanitize($art_edit['title']) : ''; ?>" placeholder="Masukkan judul berita utama">
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="author" class="form-label small fw-bold text-primary">Penulis / Author</label>
                    <input type="text" class="form-control" id="author" name="author" required value="<?php echo ($action === 'edit') ? sanitize($art_edit['author']) : 'Humas Perusahaan'; ?>" placeholder="Contoh: Humas PT. Hastra">
                </div>
                <div class="col-md-6">
                    <label for="tags" class="form-label small fw-bold text-primary">Tagar / Kategori (Pisahkan dengan koma)</label>
                    <input type="text" class="form-control" id="tags" name="tags" value="<?php echo ($action === 'edit') ? sanitize($art_edit['tags']) : 'Konstruksi,Berita,Kegiatan'; ?>" placeholder="Contoh: Sertifikasi,ISO,Proyek">
                </div>
            </div>

            <!-- Content HTML Editor (We will use direct textarea, very robust and doesn't load external heavy libraries, let's format content using paragraph inputs) -->
            <div class="mb-3">
                <label for="content" class="form-label small fw-bold text-primary">Konten Artikel (Format HTML didukung, misal menggunakan tag &lt;p&gt; untuk alinea)</label>
                <textarea class="form-control" id="content" name="content" rows="12" required placeholder="Tuliskan berita lengkap Anda di sini..."><?php echo ($action === 'edit') ? $art_edit['content'] : ''; ?></textarea>
            </div>

            <div class="row mb-4 align-items-end">
                <div class="col-md-8">
                    <label for="image" class="form-label small fw-bold text-primary">File Cover Banner Gambar (Format: JPG, JPEG, PNG, WEBP)</label>
                    <input type="file" class="form-control" id="image" name="image" <?php echo ($action === 'add') ? 'required' : ''; ?>>
                    <?php if ($action === 'edit' && !empty($art_edit['image'])): ?>
                        <div class="mt-2">
                            <span class="small text-muted d-block mb-1">Gambar saat ini:</span>
                            <img src="<?php echo base_url('assets/uploads/articles/' . $art_edit['image']); ?>" class="img-thumbnail rounded" style="max-height: 80px;">
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-4">
                    <label for="status" class="form-label small fw-bold text-primary">Status Publikasi</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="published" <?php echo ($action === 'edit' && $art_edit['status'] === 'published') ? 'selected' : ''; ?>>Diterbitkan (Published)</option>
                        <option value="draft" <?php echo ($action === 'edit' && $art_edit['status'] === 'draft') ? 'selected' : ''; ?>>Draft (Simpan Sementara)</option>
                    </select>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary px-4 py-2"><i class="bi-check-circle me-1"></i> Simpan Artikel</button>
            <a href="articles.php" class="btn btn-outline-secondary px-4 py-2 ms-2">Batal</a>
        </form>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
