<?php
require_once __DIR__ . '/../config/functions.php';
require_once __DIR__ . '/includes/auth.php';

$msg = '';
$msg_type = 'success';

// CREATE / UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id    = intval($_POST['id'] ?? 0);
    $icon  = trim($_POST['icon'] ?? 'bi-building-fill');
    $name  = trim($_POST['name'] ?? '');
    $order = intval($_POST['sort_order'] ?? 0);

    if ($name) {
        if ($id > 0) {
            $stmt = $db->prepare("UPDATE partners SET icon=?, name=?, sort_order=? WHERE id=?");
            $stmt->execute([$icon, $name, $order, $id]);
            $msg = 'Mitra/Klien berhasil diperbarui.';
        } else {
            $stmt = $db->prepare("INSERT INTO partners (icon, name, sort_order) VALUES (?,?,?)");
            $stmt->execute([$icon, $name, $order]);
            $msg = 'Mitra/Klien baru berhasil ditambahkan.';
        }
    } else {
        $msg = 'Nama Mitra/Klien tidak boleh kosong.';
        $msg_type = 'danger';
    }
}

// DELETE
if (isset($_GET['delete'])) {
    $db->prepare("DELETE FROM partners WHERE id=?")->execute([intval($_GET['delete'])]);
    header("Location: partners.php?deleted=1");
    exit;
}
if (isset($_GET['deleted'])) $msg = 'Mitra/Klien berhasil dihapus.';

// EDIT: load existing
$edit = null;
if (isset($_GET['edit'])) {
    $edit = $db->prepare("SELECT * FROM partners WHERE id=?");
    $edit->execute([intval($_GET['edit'])]);
    $edit = $edit->fetch();
}

// CREATE TABLE if it doesn't exist yet
try {
    $db->exec("CREATE TABLE IF NOT EXISTS `partners` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `icon` VARCHAR(50) NOT NULL DEFAULT 'bi-building-fill',
        `name` VARCHAR(150) NOT NULL,
        `sort_order` INT NOT NULL DEFAULT 0
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Insert dummy data if table is empty
    $check = $db->query("SELECT COUNT(*) FROM partners")->fetchColumn();
    if ($check == 0) {
        $dummies = [
            ['icon'=>'bi-building-fill', 'name'=>'BUMN RI', 'sort_order'=>1],
            ['icon'=>'bi-shield-check',  'name'=>'Adhi Karya', 'sort_order'=>2],
            ['icon'=>'bi-box-seam',      'name'=>'Waskita', 'sort_order'=>3],
            ['icon'=>'bi-gear-wide',     'name'=>'Hastra GP', 'sort_order'=>4],
            ['icon'=>'bi-house-check',   'name'=>'Wijaya Karya', 'sort_order'=>5],
        ];
        foreach ($dummies as $d) {
            $stmt = $db->prepare("INSERT INTO partners (icon, name, sort_order) VALUES (?,?,?)");
            $stmt->execute([$d['icon'], $d['name'], $d['sort_order']]);
        }
    }
} catch(Exception $e) {}

// FETCH ALL
$partners = $db->query("SELECT * FROM partners ORDER BY sort_order ASC, id ASC")->fetchAll();

include __DIR__ . '/includes/header.php';
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
                    <?php echo $edit ? 'Edit Mitra' : 'Tambah Mitra'; ?>
                </h6>
                <?php if ($edit): ?>
                    <a href="partners.php" class="btn-admin-outline btn-admin-sm">
                        <i class="bi-x-lg"></i> Batal
                    </a>
                <?php endif; ?>
            </div>
            <div class="admin-card-body">
                <form method="POST" action="partners.php<?php echo $edit ? '?edit='.$edit['id'] : ''; ?>">
                    <input type="hidden" name="id" value="<?php echo $edit['id'] ?? 0; ?>">

                    <div class="mb-3">
                        <label class="form-label-admin">Ikon Bootstrap</label>
                        <div style="display:flex;gap:.5rem;align-items:center;">
                            <input type="text" name="icon" id="iconInput"
                                   class="form-control-admin"
                                   value="<?php echo sanitize($edit['icon'] ?? 'bi-building-fill'); ?>"
                                   placeholder="bi-building-fill, ...">
                            <div id="iconPreview" style="font-size:1.8rem;width:44px;text-align:center;">
                                <i class="bi <?php echo sanitize($edit['icon'] ?? 'bi-building-fill'); ?>"
                                   style="color:var(--navy);"></i>
                            </div>
                        </div>
                        <div style="font-size:.75rem;color:var(--a-gray);margin-top:.35rem;">
                            Lihat daftar ikon di <a href="https://icons.getbootstrap.com" target="_blank">icons.getbootstrap.com</a>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label-admin">Nama Mitra/Klien <span style="color:red">*</span></label>
                        <input type="text" name="name" class="form-control-admin"
                               value="<?php echo sanitize($edit['name'] ?? ''); ?>"
                               placeholder="Contoh: BUMN RI" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label-admin">Urutan Tampil</label>
                        <input type="number" name="sort_order" class="form-control-admin"
                               value="<?php echo intval($edit['sort_order'] ?? count($partners) + 1); ?>"
                               min="1" placeholder="1">
                    </div>

                    <button type="submit" class="btn-admin-primary w-100">
                        <i class="bi-save"></i>
                        <?php echo $edit ? 'Simpan Perubahan' : 'Tambah Mitra'; ?>
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
                    $icons = ['bi-building-fill','bi-shield-check','bi-box-seam','bi-gear-wide','bi-house-check','bi-globe','bi-briefcase','bi-wallet-fill'];
                    foreach($icons as $ic): ?>
                    <button type="button" onclick="document.getElementById('iconInput').value='<?php echo $ic; ?>';document.getElementById('iconPreview').innerHTML='<i class=\'bi <?php echo $ic; ?>\' style=\'color:var(--navy);\'></i>';"
                        style="border:1px solid var(--a-border);background:var(--a-off);border-radius:8px;padding:.4rem .6rem;cursor:pointer;font-size:1.2rem;transition:all .2s;"
                        title="<?php echo $ic; ?>">
                        <i class="bi <?php echo $ic; ?>" style="color:var(--a-navy);"></i>
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
                    <i class="bi-people-fill"></i> Daftar Mitra / Klien
                </h6>
                <span class="badge-admin badge-gold"><?php echo count($partners); ?> item</span>
            </div>
            <div class="admin-card-body" style="padding:0;">
                <?php if (empty($partners)): ?>
                    <div style="text-align:center;padding:3rem;color:var(--a-gray);">
                        <i class="bi-inbox" style="font-size:2rem;display:block;margin-bottom:.75rem;opacity:.4;"></i>
                        Belum ada data mitra. Tambahkan yang pertama!
                    </div>
                <?php else: ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th width="50">No.</th>
                            <th width="56">Ikon</th>
                            <th>Nama Mitra / Klien</th>
                            <th width="90" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($partners as $i => $p): ?>
                        <tr>
                            <td style="color:var(--a-gray);font-size:.82rem;"><?php echo $p['sort_order'] ?: $i+1; ?></td>
                            <td>
                                <div style="width:40px;height:40px;border-radius:10px;background:rgba(11,31,58,.05);border:1px solid rgba(11,31,58,.1);display:flex;align-items:center;justify-content:center;">
                                    <i class="bi <?php echo sanitize($p['icon']); ?>" style="font-size:1.15rem;color:var(--navy);"></i>
                                </div>
                            </td>
                            <td>
                                <div style="font-weight:700;font-size:.9rem;color:var(--a-navy);"><?php echo sanitize($p['name']); ?></div>
                            </td>
                            <td class="text-center">
                                <div style="display:flex;gap:.4rem;justify-content:center;">
                                    <a href="partners.php?edit=<?php echo $p['id']; ?>" class="btn-admin-outline btn-admin-sm" title="Edit">
                                        <i class="bi-pencil"></i>
                                    </a>
                                    <a href="partners.php?delete=<?php echo $p['id']; ?>"
                                       class="btn-admin-danger btn-admin-sm"
                                       onclick="return confirm('Hapus mitra ini?')" title="Hapus">
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
    </div>

</div>

<script>
// Live preview icon
document.getElementById('iconInput').addEventListener('input', function() {
    const val = this.value.trim();
    const preview = document.getElementById('iconPreview');
    preview.innerHTML = '<i class="bi ' + val + '" style="color:var(--navy);"></i>';
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
