<?php
require_once __DIR__ . '/../config/functions.php';
require_once __DIR__ . '/includes/auth.php';

$success_msg = '';
$error_msg = '';
$edit_data = null;

// --- CREATE org_structure table if not exists (migration safety) ---
try {
    $db->exec("CREATE TABLE IF NOT EXISTS `org_structure` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(150) NOT NULL,
        `position` VARCHAR(150) NOT NULL,
        `parent_id` INT NULL DEFAULT NULL,
        `photo` VARCHAR(255) NULL DEFAULT NULL,
        `sort_order` INT NOT NULL DEFAULT 0
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
} catch (Exception $e) {}

$upload_dir = __DIR__ . '/../assets/uploads/org/';
if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

// --- HANDLE DELETE ---
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $del_id = (int)$_GET['delete'];
    try {
        // Set children's parent to null first
        $db->prepare("UPDATE org_structure SET parent_id = NULL WHERE parent_id = ?")->execute([$del_id]);
        $row = $db->query("SELECT photo FROM org_structure WHERE id = $del_id")->fetch();
        if ($row && $row['photo'] && file_exists($upload_dir . $row['photo'])) {
            unlink($upload_dir . $row['photo']);
        }
        $db->prepare("DELETE FROM org_structure WHERE id = ?")->execute([$del_id]);
        $success_msg = 'Anggota berhasil dihapus.';
    } catch (Exception $e) {
        $error_msg = 'Gagal menghapus: ' . $e->getMessage();
    }
}

// --- HANDLE DELETE PHOTO ---
if (isset($_GET['delete_photo']) && is_numeric($_GET['delete_photo'])) {
    $del_id = (int)$_GET['delete_photo'];
    try {
        $row = $db->query("SELECT photo FROM org_structure WHERE id = $del_id")->fetch();
        if ($row && !empty($row['photo']) && file_exists($upload_dir . $row['photo'])) {
            unlink($upload_dir . $row['photo']);
        }
        $db->prepare("UPDATE org_structure SET photo = NULL WHERE id = ?")->execute([$del_id]);
        header("Location: org_structure.php?edit=" . $del_id . "&msg=photo_deleted");
        exit;
    } catch (Exception $e) {
        $error_msg = 'Gagal menghapus foto: ' . $e->getMessage();
    }
}
if (isset($_GET['msg']) && $_GET['msg'] === 'photo_deleted') {
    $success_msg = 'Foto berhasil dihapus.';
}

// --- HANDLE EDIT FETCH ---
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $edit_data = $db->query("SELECT * FROM org_structure WHERE id = " . (int)$_GET['edit'])->fetch();
}

// --- HANDLE SAVE (Add/Edit) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_org'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        $error_msg = 'Token CSRF tidak valid.';
    } else {
        $name      = trim($_POST['name']);
        $position  = trim($_POST['position']);
        $parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
        $sort_order = (int)($_POST['sort_order'] ?? 0);
        $edit_id   = !empty($_POST['edit_id']) ? (int)$_POST['edit_id'] : null;

        // Handle photo upload
        $photo_name = $_POST['existing_photo'] ?? null;
        if (!empty($_FILES['photo']['name'])) {
            $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg','jpeg','png','webp'])) {
                $error_msg = 'Format foto harus JPG, PNG, atau WEBP.';
            } elseif ($_FILES['photo']['size'] > 2 * 1024 * 1024) {
                $error_msg = 'Ukuran foto maksimal 2MB.';
            } else {
                $new_name = 'org_' . time() . '_' . rand(100,999) . '.' . $ext;
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_dir . $new_name)) {
                    // Delete old photo
                    if ($photo_name && file_exists($upload_dir . $photo_name)) unlink($upload_dir . $photo_name);
                    $photo_name = $new_name;
                } else {
                    $error_msg = 'Gagal mengupload foto.';
                }
            }
        }

        if (empty($error_msg)) {
            try {
                if ($edit_id) {
                    $stmt = $db->prepare("UPDATE org_structure SET name=?, position=?, parent_id=?, photo=?, sort_order=? WHERE id=?");
                    $stmt->execute([$name, $position, $parent_id, $photo_name, $sort_order, $edit_id]);
                    $success_msg = 'Data anggota berhasil diperbarui.';
                } else {
                    $stmt = $db->prepare("INSERT INTO org_structure (name, position, parent_id, photo, sort_order) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$name, $position, $parent_id, $photo_name, $sort_order]);
                    $success_msg = 'Anggota baru berhasil ditambahkan.';
                }
                $edit_data = null;
            } catch (Exception $e) {
                $error_msg = 'Gagal menyimpan: ' . $e->getMessage();
            }
        }
    }
}

// --- Fetch all members ---
$members = $db->query("SELECT * FROM org_structure ORDER BY sort_order ASC, id ASC")->fetchAll();
$all_ids = array_column($members, 'id');

include __DIR__ . '/includes/header.php';
?>

<!-- Header -->
<div class="mb-4">
    <h1 class="h3 fw-bold mb-0" style="color:var(--a-navy);">Struktur Organisasi</h1>
    <span class="small text-muted">Kelola nama, jabatan, foto, dan hirarki anggota struktur perusahaan.</span>
</div>

<!-- Alerts -->
<?php if ($success_msg): ?>
<div class="alert-admin alert-admin-success mb-4"><i class="bi-check-circle-fill"></i> <?php echo $success_msg; ?></div>
<?php endif; ?>
<?php if ($error_msg): ?>
<div class="alert-admin alert-admin-danger mb-4"><i class="bi-exclamation-triangle-fill"></i> <?php echo $error_msg; ?></div>
<?php endif; ?>

<div class="row g-4">

    <!-- FORM ADD/EDIT -->
    <div class="col-lg-4">
        <div class="admin-card">
            <div class="admin-card-header">
                <h6 class="admin-card-title">
                    <i class="bi-person-plus-fill"></i>
                    <?php echo $edit_data ? 'Edit Anggota' : 'Tambah Anggota Baru'; ?>
                </h6>
                <?php if ($edit_data): ?>
                <a href="org_structure.php" class="btn-admin-outline btn-admin-sm">Batal Edit</a>
                <?php endif; ?>
            </div>
            <div class="admin-card-body">
                <form action="org_structure.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">
                    <input type="hidden" name="save_org" value="1">
                    <input type="hidden" name="edit_id" value="<?php echo $edit_data ? $edit_data['id'] : ''; ?>">
                    <input type="hidden" name="existing_photo" value="<?php echo sanitize($edit_data['photo'] ?? ''); ?>">

                    <div class="mb-3">
                        <label class="form-label-admin">Nama Lengkap & Gelar <span class="text-danger">*</span></label>
                        <input type="text" class="form-control-admin" name="name" required
                               value="<?php echo sanitize($edit_data['name'] ?? ''); ?>" placeholder="Contoh: Muhammad Akib, ST">
                    </div>
                    <div class="mb-3">
                        <label class="form-label-admin">Jabatan / Posisi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control-admin" name="position" required
                               value="<?php echo sanitize($edit_data['position'] ?? ''); ?>" placeholder="Contoh: Director">
                    </div>
                    <div class="mb-3">
                        <label class="form-label-admin">Atasan Langsung (Parent)</label>
                        <select class="form-control-admin" name="parent_id">
                            <option value="">-- Tidak Ada (Level Teratas) --</option>
                            <?php foreach ($members as $m): ?>
                                <?php if ($edit_data && $m['id'] == $edit_data['id']) continue; ?>
                                <option value="<?php echo $m['id']; ?>"
                                    <?php echo ($edit_data && $edit_data['parent_id'] == $m['id']) ? 'selected' : ''; ?>>
                                    <?php echo sanitize($m['name']); ?> — <?php echo sanitize($m['position']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label-admin">Urutan Tampil</label>
                        <input type="number" class="form-control-admin" name="sort_order" min="0"
                               value="<?php echo (int)($edit_data['sort_order'] ?? 0); ?>" placeholder="0">
                        <span class="text-muted" style="font-size:.72rem;">Angka kecil tampil lebih dulu (dalam level yang sama)</span>
                    </div>
                    <div class="mb-4">
                        <label class="form-label-admin">Foto (JPG/PNG/WEBP, maks. 2MB)</label>
                        <?php if (!empty($edit_data['photo'])): ?>
                        <div class="mb-2 d-flex align-items-end gap-3">
                            <div>
                                <img src="<?php echo base_url('assets/uploads/org/' . $edit_data['photo']); ?>"
                                     style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid rgba(201,162,39,.4);" alt="Foto">
                            </div>
                            <div>
                                <a href="org_structure.php?delete_photo=<?php echo $edit_data['id']; ?>" class="btn btn-sm btn-outline-danger" style="border-radius:100px;font-size:0.75rem;" id="deletePhotoBtn">
                                    <i class="bi-trash"></i> Hapus Foto
                                </a>
                            </div>
                        </div>
                        <?php endif; ?>
                        <input type="file" class="form-control-admin" name="photo" accept="image/jpeg,image/png,image/webp">
                    </div>

                    <button type="submit" class="btn-admin-primary w-100">
                        <i class="bi-check-all"></i> <?php echo $edit_data ? 'Simpan Perubahan' : 'Tambah Anggota'; ?>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- TABLE -->
    <div class="col-lg-8">
        <div class="admin-card">
            <div class="admin-card-header">
                <h6 class="admin-card-title"><i class="bi-diagram-3-fill"></i> Daftar Anggota Struktur</h6>
                <span class="badge-admin badge-navy"><?php echo count($members); ?> anggota</span>
            </div>
            <div style="overflow-x:auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>Nama & Jabatan</th>
                            <th>Atasan</th>
                            <th>Urutan</th>
                            <th style="text-align:right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($members)): ?>
                        <tr><td colspan="5" style="text-align:center;padding:3rem;color:var(--a-gray);">
                            <i class="bi-diagram-3" style="font-size:2rem;display:block;margin-bottom:.75rem;"></i>
                            Belum ada anggota. Tambahkan menggunakan form di samping.
                        </td></tr>
                        <?php else: ?>
                        <?php
                        // Build lookup for parent name
                        $member_map = array_column($members, null, 'id');
                        foreach ($members as $m):
                            $parent_name = $m['parent_id'] ? sanitize($member_map[$m['parent_id']]['name'] ?? '-') . '<br><small style="color:var(--a-gray);">' . sanitize($member_map[$m['parent_id']]['position'] ?? '') . '</small>' : '<span style="color:var(--a-gold);font-weight:700;">Teratas</span>';
                        ?>
                        <tr>
                            <td>
                                <?php if ($m['photo']): ?>
                                <img src="<?php echo base_url('assets/uploads/org/' . $m['photo']); ?>"
                                     style="width:46px;height:46px;border-radius:50%;object-fit:cover;border:2px solid rgba(201,162,39,.4);" alt="">
                                <?php else: ?>
                                <div style="width:46px;height:46px;border-radius:50%;background:var(--a-navy-light);display:flex;align-items:center;justify-content:center;">
                                    <i class="bi-person-fill" style="color:var(--a-navy);font-size:1.2rem;"></i>
                                </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="font-weight:700;color:var(--a-navy);"><?php echo sanitize($m['name']); ?></div>
                                <div style="font-size:.78rem;color:var(--a-gray);"><?php echo sanitize($m['position']); ?></div>
                            </td>
                            <td><?php echo $parent_name; ?></td>
                            <td><span class="badge-admin badge-navy"><?php echo $m['sort_order']; ?></span></td>
                            <td style="text-align:right;">
                                <a href="org_structure.php?edit=<?php echo $m['id']; ?>" class="btn-admin-outline btn-admin-sm">
                                    <i class="bi-pencil-fill"></i> Edit
                                </a>
                                <a href="org_structure.php?delete=<?php echo $m['id']; ?>"
                                   class="btn-admin-danger btn-admin-sm ms-1"
                                   onclick="return confirm('Hapus <?php echo sanitize($m['name']); ?>? Anggota di bawahnya akan kehilangan hierarki.')">
                                    <i class="bi-trash3-fill"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Preview Struktur -->
        <div class="admin-card mt-3">
            <div class="admin-card-header">
                <h6 class="admin-card-title"><i class="bi-eye-fill"></i> Preview Hierarki</h6>
                <a href="<?php echo base_url('tentang.php'); ?>#struktur" target="_blank" class="btn-admin-outline btn-admin-sm">Lihat di Website</a>
            </div>
            <div class="admin-card-body" style="padding:1rem;">
                <?php
                // Build tree
                function buildTree($items, $parent_id = null) {
                    $tree = [];
                    foreach ($items as $item) {
                        if ($item['parent_id'] == $parent_id) {
                            $item['children'] = buildTree($items, $item['id']);
                            $tree[] = $item;
                        }
                    }
                    return $tree;
                }
                function renderTree($nodes, $level = 0) {
                    foreach ($nodes as $node) {
                        $indent = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level);
                        $icon = $level === 0 ? '🏛' : ($level === 1 ? '👤' : ($level === 2 ? '📋' : '🔧'));
                        echo "<div style='padding:.4rem 0;border-bottom:1px solid rgba(0,0,0,.04);'>";
                        echo $indent . $icon . " <strong style='color:var(--a-navy);font-size:.88rem;'>" . htmlspecialchars($node['name']) . "</strong>";
                        echo " <span style='color:var(--a-gray);font-size:.75rem;'>— " . htmlspecialchars($node['position']) . "</span>";
                        echo "</div>";
                        if (!empty($node['children'])) renderTree($node['children'], $level + 1);
                    }
                }
                $tree = buildTree($members);
                if (empty($tree)) {
                    echo '<p class="text-muted text-center py-3">Belum ada data struktur.</p>';
                } else {
                    renderTree($tree);
                }
                ?>
            </div>
        </div>
    </div>
</div>

<!-- ===== DELETE PHOTO CONFIRMATION MODAL ===== -->
<div id="deletePhotoModal" style="
    display:none;
    position:fixed;inset:0;z-index:99999;
    background:rgba(6,16,31,.65);
    backdrop-filter:blur(8px);
    -webkit-backdrop-filter:blur(8px);
    align-items:center;justify-content:center;
">
    <div style="
        background:#fff;
        border-radius:20px;
        padding:2.5rem 2.25rem;
        max-width:380px;width:90%;
        box-shadow:0 32px 80px rgba(6,16,31,.35);
        border:1px solid rgba(220,38,38,.15);
        text-align:center;
        animation:modalPop .3s cubic-bezier(.34,1.56,.64,1);
        position:relative;
    ">
        <!-- Icon -->
        <div style="
            width:68px;height:68px;border-radius:50%;
            background:rgba(239,68,68,.08);
            border:2px solid rgba(239,68,68,.2);
            display:flex;align-items:center;justify-content:center;
            margin:0 auto 1.5rem;
            box-shadow:0 8px 24px rgba(239,68,68,.15);
        ">
            <i class="bi-trash3" style="font-size:1.75rem;color:#dc2626;"></i>
        </div>

        <!-- Text -->
        <h5 style="font-family:'Outfit',sans-serif;font-weight:800;color:#0B1F3A;margin-bottom:.5rem;font-size:1.2rem;">Konfirmasi Hapus Foto</h5>
        <p style="color:#64748B;font-size:.9rem;margin-bottom:2rem;line-height:1.6;">Apakah Anda yakin ingin menghapus foto dari<br><strong style="color:#0B1F3A;">anggota ini?</strong></p>

        <!-- Buttons -->
        <div style="display:flex;gap:.75rem;">
            <button id="deletePhotoCancel" style="
                flex:1;padding:.75rem;border-radius:100px;
                border:1.5px solid rgba(11,31,58,.15);
                background:transparent;color:#0B1F3A;
                font-size:.9rem;font-weight:600;
                cursor:pointer;font-family:'Outfit',sans-serif;
                transition:all .25s;
            " onmouseover="this.style.background='#F4F6FA'" onmouseout="this.style.background='transparent'">
                Batal
            </button>
            <button id="deletePhotoConfirm" style="
                flex:1;padding:.75rem;border-radius:100px;
                background:linear-gradient(135deg,#dc2626,#ef4444);
                color:#ffffff;
                font-size:.9rem;font-weight:700;
                border:none;cursor:pointer;display:flex;
                align-items:center;justify-content:center;gap:.45rem;
                box-shadow:0 4px 14px rgba(220,38,38,.35);
                transition:all .3s;font-family:'Outfit',sans-serif;
            ">
                <i class="bi-trash3-fill"></i> Ya, Hapus
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const deletePhotoBtn = document.getElementById('deletePhotoBtn');
    const deletePhotoModal = document.getElementById('deletePhotoModal');
    const deletePhotoCancel = document.getElementById('deletePhotoCancel');
    const deletePhotoConfirm = document.getElementById('deletePhotoConfirm');

    if (deletePhotoBtn && deletePhotoModal) {
        deletePhotoBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const deleteUrl = this.getAttribute('href');
            deletePhotoConfirm.onclick = function() {
                window.location.href = deleteUrl;
            };
            deletePhotoModal.style.display = 'flex';
        });
    }

    if (deletePhotoCancel) {
        deletePhotoCancel.addEventListener('click', function() {
            deletePhotoModal.style.display = 'none';
        });
    }

    if (deletePhotoModal) {
        deletePhotoModal.addEventListener('click', function(e) {
            if (e.target === deletePhotoModal) {
                deletePhotoModal.style.display = 'none';
            }
        });
    }
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
