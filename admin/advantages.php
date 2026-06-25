<?php
include __DIR__ . '/includes/header.php';

$msg = '';
$msg_type = 'success';

// CREATE / UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id    = intval($_POST['id'] ?? 0);
    $icon  = trim($_POST['icon'] ?? 'bi-star');
    $title = trim($_POST['title'] ?? '');
    $desc  = trim($_POST['description'] ?? '');
    $order = intval($_POST['sort_order'] ?? 0);

    if ($title && $desc) {
        if ($id > 0) {
            $stmt = $db->prepare("UPDATE advantages SET icon=?, title=?, description=?, sort_order=? WHERE id=?");
            $stmt->execute([$icon, $title, $desc, $order, $id]);
            $msg = 'Keunggulan berhasil diperbarui.';
        } else {
            $stmt = $db->prepare("INSERT INTO advantages (icon, title, description, sort_order) VALUES (?,?,?,?)");
            $stmt->execute([$icon, $title, $desc, $order]);
            $msg = 'Keunggulan baru berhasil ditambahkan.';
        }
    } else {
        $msg = 'Judul dan deskripsi tidak boleh kosong.';
        $msg_type = 'danger';
    }
}

// DELETE
if (isset($_GET['delete'])) {
    $db->prepare("DELETE FROM advantages WHERE id=?")->execute([intval($_GET['delete'])]);
    header("Location: advantages.php?deleted=1");
    exit;
}
if (isset($_GET['deleted'])) $msg = 'Keunggulan berhasil dihapus.';

// EDIT: load existing
$edit = null;
if (isset($_GET['edit'])) {
    $edit = $db->prepare("SELECT * FROM advantages WHERE id=?");
    $edit->execute([intval($_GET['edit'])]);
    $edit = $edit->fetch();
}

// FETCH ALL
$advantages = $db->query("SELECT * FROM advantages ORDER BY sort_order ASC, id ASC")->fetchAll();

// CREATE TABLE if it doesn't exist yet (for existing installations)
try {
    $db->exec("CREATE TABLE IF NOT EXISTS `advantages` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `icon` VARCHAR(50) NOT NULL DEFAULT 'bi-star',
        `title` VARCHAR(150) NOT NULL,
        `description` TEXT NOT NULL,
        `sort_order` INT NOT NULL DEFAULT 0
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
} catch(Exception $e) {}
?>

<?php if ($msg): ?>
<div class="alert-admin alert-admin-<?php echo $msg_type; ?> mb-4">
    <i class="bi-<?php echo $msg_type === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill'; ?>"></i>
    <?php echo sanitize($msg); ?>
</div>
<?php endif; ?>

<div class="row g-4">

    <!-- ── FORM ADD / EDIT ── -->
    <div class="col-lg-4">
        <div class="admin-card">
            <div class="admin-card-header">
                <h6 class="admin-card-title">
                    <i class="bi-<?php echo $edit ? 'pencil-square' : 'plus-circle-fill'; ?>"></i>
                    <?php echo $edit ? 'Edit Keunggulan' : 'Tambah Keunggulan'; ?>
                </h6>
                <?php if ($edit): ?>
                    <a href="advantages.php" class="btn-admin-outline btn-admin-sm">
                        <i class="bi-x-lg"></i> Batal
                    </a>
                <?php endif; ?>
            </div>
            <div class="admin-card-body">
                <form method="POST" action="advantages.php<?php echo $edit ? '?edit='.$edit['id'] : ''; ?>">
                    <input type="hidden" name="id" value="<?php echo $edit['id'] ?? 0; ?>">

                    <div class="mb-3">
                        <label class="form-label-admin">Ikon Bootstrap Icons</label>
                        <div style="display:flex;gap:.5rem;align-items:center;">
                            <input type="text" name="icon" id="iconInput"
                                   class="form-control-admin"
                                   value="<?php echo sanitize($edit['icon'] ?? 'bi-star'); ?>"
                                   placeholder="bi-star, bi-shield-check, ...">
                            <div id="iconPreview" style="font-size:1.8rem;width:44px;text-align:center;">
                                <i class="bi <?php echo sanitize($edit['icon'] ?? 'bi-star'); ?>"
                                   style="background:var(--grad-gold);-webkit-background-clip:text;-webkit-text-fill-color:transparent;"></i>
                            </div>
                        </div>
                        <div style="font-size:.75rem;color:var(--a-gray);margin-top:.35rem;">
                            Lihat daftar ikon di <a href="https://icons.getbootstrap.com" target="_blank">icons.getbootstrap.com</a>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label-admin">Judul <span style="color:red">*</span></label>
                        <input type="text" name="title" class="form-control-admin"
                               value="<?php echo sanitize($edit['title'] ?? ''); ?>"
                               placeholder="Contoh: Profesional Tersertifikasi" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label-admin">Deskripsi <span style="color:red">*</span></label>
                        <textarea name="description" class="form-control-admin" rows="3"
                                  placeholder="Deskripsi singkat keunggulan ini..." required><?php echo sanitize($edit['description'] ?? ''); ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label-admin">Urutan Tampil</label>
                        <input type="number" name="sort_order" class="form-control-admin"
                               value="<?php echo intval($edit['sort_order'] ?? count($advantages) + 1); ?>"
                               min="1" placeholder="1">
                    </div>

                    <button type="submit" class="btn-admin-primary w-100">
                        <i class="bi-save"></i>
                        <?php echo $edit ? 'Simpan Perubahan' : 'Tambah Keunggulan'; ?>
                    </button>
                </form>
            </div>
        </div>

        <!-- Icon Reference -->
        <div class="admin-card mt-3">
            <div class="admin-card-header">
                <h6 class="admin-card-title"><i class="bi-stars"></i> Ikon Populer</h6>
            </div>
            <div class="admin-card-body">
                <div style="display:flex;flex-wrap:wrap;gap:.5rem;">
                    <?php
                    $icons = ['bi-shield-check','bi-graph-up-arrow','bi-gem','bi-clock-history','bi-award','bi-people-fill','bi-tools','bi-star-fill','bi-check-circle-fill','bi-lightning-charge-fill','bi-briefcase-fill','bi-building','bi-patch-check-fill'];
                    foreach($icons as $ic): ?>
                    <button type="button" onclick="document.getElementById('iconInput').value='<?php echo $ic; ?>';document.getElementById('iconPreview').innerHTML='<i class=\'bi <?php echo $ic; ?>\' style=\'background:var(--grad-gold);-webkit-background-clip:text;-webkit-text-fill-color:transparent;\'></i>';"
                        style="border:1px solid var(--a-border);background:var(--a-off);border-radius:8px;padding:.4rem .6rem;cursor:pointer;font-size:1.2rem;transition:all .2s;"
                        title="<?php echo $ic; ?>">
                        <i class="bi <?php echo $ic; ?>" style="color:var(--a-gold);"></i>
                    </button>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- ── LIST ── -->
    <div class="col-lg-8">
        <div class="admin-card">
            <div class="admin-card-header">
                <h6 class="admin-card-title">
                    <i class="bi-list-stars"></i> Daftar Keunggulan
                </h6>
                <span class="badge-admin badge-gold"><?php echo count($advantages); ?> item</span>
            </div>
            <div class="admin-card-body" style="padding:0;">
                <?php if (empty($advantages)): ?>
                    <div style="text-align:center;padding:3rem;color:var(--a-gray);">
                        <i class="bi-inbox" style="font-size:2rem;display:block;margin-bottom:.75rem;opacity:.4;"></i>
                        Belum ada data keunggulan. Tambahkan yang pertama!
                    </div>
                <?php else: ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th width="50">No.</th>
                            <th width="56">Ikon</th>
                            <th>Judul & Deskripsi</th>
                            <th width="90" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($advantages as $i => $adv): ?>
                        <tr>
                            <td style="color:var(--a-gray);font-size:.82rem;"><?php echo $adv['sort_order'] ?: $i+1; ?></td>
                            <td>
                                <div style="width:40px;height:40px;border-radius:10px;background:rgba(201,162,39,.1);border:1px solid rgba(201,162,39,.2);display:flex;align-items:center;justify-content:center;">
                                    <i class="bi <?php echo sanitize($adv['icon']); ?>" style="font-size:1.15rem;background:var(--grad-gold);-webkit-background-clip:text;-webkit-text-fill-color:transparent;"></i>
                                </div>
                            </td>
                            <td>
                                <div style="font-weight:700;font-size:.9rem;color:var(--a-navy);margin-bottom:.2rem;"><?php echo sanitize($adv['title']); ?></div>
                                <div style="font-size:.8rem;color:var(--a-gray);line-height:1.4;"><?php echo sanitize($adv['description']); ?></div>
                            </td>
                            <td class="text-center">
                                <div style="display:flex;gap:.4rem;justify-content:center;">
                                    <a href="advantages.php?edit=<?php echo $adv['id']; ?>" class="btn-admin-outline btn-admin-sm" title="Edit">
                                        <i class="bi-pencil"></i>
                                    </a>
                                    <a href="advantages.php?delete=<?php echo $adv['id']; ?>"
                                       class="btn-admin-danger btn-admin-sm"
                                       onclick="return confirm('Hapus keunggulan ini?')" title="Hapus">
                                        <i class="bi-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- Preview Info -->
        <div class="admin-card mt-3" style="border-color:rgba(201,162,39,.2);">
            <div class="admin-card-body" style="display:flex;align-items:center;gap:1rem;padding:1.25rem 1.5rem;">
                <i class="bi-info-circle-fill" style="font-size:1.4rem;color:var(--a-gold);flex-shrink:0;"></i>
                <div style="font-size:.85rem;color:var(--a-gray);">
                    Keunggulan yang ditampilkan di homepage hanya <strong>4 item pertama</strong> berdasarkan urutan. Anda bisa mengatur nomor urutan pada form di sebelah kiri.
                </div>
            </div>
        </div>
    </div>

</div>

<script>
// Live preview icon
document.getElementById('iconInput').addEventListener('input', function() {
    const val = this.value.trim();
    const preview = document.getElementById('iconPreview');
    preview.innerHTML = '<i class="bi ' + val + '" style="background:var(--grad-gold);-webkit-background-clip:text;-webkit-text-fill-color:transparent;"></i>';
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
