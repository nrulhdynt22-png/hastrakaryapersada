<?php
require_once __DIR__ . '/includes/header.php';

// Handle actions
$action  = $_GET['action'] ?? '';
$msg_id  = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$flash   = '';

// Tandai sudah dibaca
if ($action === 'read' && $msg_id > 0) {
    $db->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?")->execute([$msg_id]);
    header('Location: messages.php?viewed=' . $msg_id);
    exit();
}

// Tandai belum dibaca
if ($action === 'unread' && $msg_id > 0) {
    $db->prepare("UPDATE contact_messages SET is_read = 0 WHERE id = ?")->execute([$msg_id]);
    header('Location: messages.php');
    exit();
}

// Tandai semua sudah dibaca
if ($action === 'read_all') {
    $db->exec("UPDATE contact_messages SET is_read = 1");
    header('Location: messages.php');
    exit();
}

// Hapus pesan
if ($action === 'delete' && $msg_id > 0) {
    $db->prepare("DELETE FROM contact_messages WHERE id = ?")->execute([$msg_id]);
    header('Location: messages.php?deleted=1');
    exit();
}

// Hapus semua pesan yang sudah dibaca
if ($action === 'delete_read') {
    $db->exec("DELETE FROM contact_messages WHERE is_read = 1");
    header('Location: messages.php');
    exit();
}

// Detail pesan
$viewed_msg = null;
$viewed_id  = isset($_GET['viewed']) ? (int)$_GET['viewed'] : 0;
if ($viewed_id > 0) {
    $s = $db->prepare("SELECT * FROM contact_messages WHERE id = ?");
    $s->execute([$viewed_id]);
    $viewed_msg = $s->fetch();
}

// Ambil semua pesan, terbaru dulu
$messages   = $db->query("SELECT * FROM contact_messages ORDER BY created_at DESC")->fetchAll();
$unread_cnt = $db->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0")->fetchColumn();
$total_cnt  = count($messages);
?>

<?php if (isset($_GET['deleted'])): ?>
<div class="alert alert-success alert-dismissible fade show mx-4 mt-3" role="alert">
    <i class="bi-check-circle-fill me-2"></i> Pesan berhasil dihapus.
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="admin-card p-4 text-center">
            <div style="font-size:2rem;font-weight:800;color:var(--a-navy);"><?php echo $total_cnt; ?></div>
            <div class="small text-muted mt-1">Total Pesan</div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="admin-card p-4 text-center" style="border-left:4px solid var(--a-gold);">
            <div style="font-size:2rem;font-weight:800;color:var(--a-gold);"><?php echo $unread_cnt; ?></div>
            <div class="small text-muted mt-1">Belum Dibaca</div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="admin-card p-4 text-center">
            <div style="font-size:2rem;font-weight:800;color:#22c55e;"><?php echo $total_cnt - $unread_cnt; ?></div>
            <div class="small text-muted mt-1">Sudah Dibaca</div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="admin-card p-4 text-center">
            <?php
            $today = $db->query("SELECT COUNT(*) FROM contact_messages WHERE DATE(created_at) = CURDATE()")->fetchColumn();
            ?>
            <div style="font-size:2rem;font-weight:800;color:#6366f1;"><?php echo $today; ?></div>
            <div class="small text-muted mt-1">Masuk Hari Ini</div>
        </div>
    </div>
</div>

<!-- Detail Pesan yang Diklik -->
<?php if ($viewed_msg): ?>
<div class="admin-card p-4 mb-4" style="border-left:4px solid var(--a-gold);">
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h5 class="fw-bold mb-1" style="color:var(--a-navy);">
                <i class="bi-envelope-open-fill me-2" style="color:var(--a-gold);"></i>
                <?php echo htmlspecialchars($viewed_msg['subject']); ?>
            </h5>
            <div class="small text-muted">
                Dari <strong><?php echo htmlspecialchars($viewed_msg['name']); ?></strong>
                &lt;<a href="mailto:<?php echo htmlspecialchars($viewed_msg['email']); ?>"><?php echo htmlspecialchars($viewed_msg['email']); ?></a>&gt;
                &mdash; <?php echo date('d M Y, H:i', strtotime($viewed_msg['created_at'])); ?> WIB
            </div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="mailto:<?php echo htmlspecialchars($viewed_msg['email']); ?>?subject=Re: <?php echo rawurlencode($viewed_msg['subject']); ?>"
               class="btn btn-sm btn-primary" style="background:var(--a-gold);border:none;">
                <i class="bi-reply-fill me-1"></i> Balas Email
            </a>
            <a href="messages.php?action=unread&id=<?php echo $viewed_msg['id']; ?>"
               class="btn btn-sm btn-outline-secondary">
                <i class="bi-envelope me-1"></i> Tandai Belum Dibaca
            </a>
            <a href="messages.php" class="btn btn-sm btn-outline-secondary">
                <i class="bi-x-lg"></i> Tutup
            </a>
        </div>
    </div>
    <hr>
    <div style="white-space:pre-wrap;line-height:1.8;color:#374151;font-size:.95rem;">
        <?php echo htmlspecialchars($viewed_msg['message']); ?>
    </div>
</div>
<?php endif; ?>

<!-- Toolbar -->
<div class="admin-card p-3 mb-3 d-flex flex-wrap gap-2 align-items-center justify-content-between">
    <h6 class="mb-0 fw-bold" style="color:var(--a-navy);">
        <i class="bi-inbox-fill me-2" style="color:var(--a-gold);"></i>
        Semua Pesan
        <?php if ($unread_cnt > 0): ?>
        <span class="badge rounded-pill ms-1" style="background:var(--a-gold);color:#fff;font-size:.7rem;"><?php echo $unread_cnt; ?> baru</span>
        <?php endif; ?>
    </h6>
    <div class="d-flex gap-2 flex-wrap">
        <?php if ($unread_cnt > 0): ?>
        <a href="messages.php?action=read_all" class="btn btn-sm btn-outline-secondary"
           onclick="return confirm('Tandai semua pesan sebagai sudah dibaca?')">
            <i class="bi-check2-all me-1"></i> Tandai Semua Dibaca
        </a>
        <?php endif; ?>
        <a href="messages.php?action=delete_read" class="btn btn-sm btn-outline-danger"
           onclick="return confirm('Hapus semua pesan yang sudah dibaca?')">
            <i class="bi-trash me-1"></i> Hapus Sudah Dibaca
        </a>
    </div>
</div>

<!-- Tabel Pesan -->
<div class="admin-card p-0 overflow-hidden">
    <?php if (empty($messages)): ?>
    <div class="text-center py-5">
        <i class="bi-inbox text-muted" style="font-size:3.5rem;display:block;margin-bottom:1rem;opacity:.3;"></i>
        <p class="text-muted mb-0">Belum ada pesan masuk.</p>
    </div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover mb-0" style="font-size:.9rem;">
            <thead style="background:#f8f9fa;">
                <tr>
                    <th style="padding:1rem 1.25rem;font-weight:700;color:var(--a-navy);border:none;" width="25">Status</th>
                    <th style="padding:1rem;font-weight:700;color:var(--a-navy);border:none;">Pengirim</th>
                    <th style="padding:1rem;font-weight:700;color:var(--a-navy);border:none;">Subjek</th>
                    <th style="padding:1rem;font-weight:700;color:var(--a-navy);border:none;" width="140">Waktu</th>
                    <th style="padding:1rem;font-weight:700;color:var(--a-navy);border:none;text-align:right;" width="120">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($messages as $msg): 
                $is_active = ($viewed_id === (int)$msg['id']);
                $row_bg    = $is_active ? 'background:rgba(212,167,78,.08);' : ($msg['is_read'] ? '' : 'background:rgba(212,167,78,.04);');
            ?>
                <tr style="<?php echo $row_bg; ?>" onclick="window.location='messages.php?action=read&id=<?php echo $msg['id']; ?>'" style="cursor:pointer;">
                    <td style="padding:.85rem 1.25rem;border-top:1px solid #f0f0f0;vertical-align:middle;">
                        <?php if (!$msg['is_read']): ?>
                        <span class="d-inline-block rounded-circle" style="width:10px;height:10px;background:var(--a-gold);"></span>
                        <?php else: ?>
                        <span class="d-inline-block rounded-circle" style="width:10px;height:10px;background:#e5e7eb;"></span>
                        <?php endif; ?>
                    </td>
                    <td style="padding:.85rem 1rem;border-top:1px solid #f0f0f0;vertical-align:middle;">
                        <div class="fw-semibold <?php echo $msg['is_read'] ? 'text-muted' : ''; ?>">
                            <?php echo htmlspecialchars($msg['name']); ?>
                        </div>
                        <div class="small text-muted"><?php echo htmlspecialchars($msg['email']); ?></div>
                    </td>
                    <td style="padding:.85rem 1rem;border-top:1px solid #f0f0f0;vertical-align:middle;max-width:300px;">
                        <div class="<?php echo $msg['is_read'] ? 'text-muted' : 'fw-semibold'; ?> text-truncate">
                            <?php echo htmlspecialchars($msg['subject']); ?>
                        </div>
                        <div class="small text-muted text-truncate" style="max-width:280px;">
                            <?php echo htmlspecialchars(substr($msg['message'], 0, 80)) . (strlen($msg['message']) > 80 ? '...' : ''); ?>
                        </div>
                    </td>
                    <td style="padding:.85rem 1rem;border-top:1px solid #f0f0f0;vertical-align:middle;" class="text-muted small">
                        <?php echo date('d M Y', strtotime($msg['created_at'])); ?><br>
                        <span style="color:#9ca3af;"><?php echo date('H:i', strtotime($msg['created_at'])); ?> WIB</span>
                    </td>
                    <td style="padding:.85rem 1rem;border-top:1px solid #f0f0f0;vertical-align:middle;text-align:right;" onclick="event.stopPropagation()">
                        <a href="mailto:<?php echo htmlspecialchars($msg['email']); ?>?subject=Re: <?php echo rawurlencode($msg['subject']); ?>"
                           class="btn btn-sm btn-outline-secondary me-1" title="Balas Email">
                            <i class="bi-reply"></i>
                        </a>
                        <a href="messages.php?action=delete&id=<?php echo $msg['id']; ?>"
                           class="btn btn-sm btn-outline-danger"
                           title="Hapus"
                           onclick="return confirm('Hapus pesan dari <?php echo addslashes($msg['name']); ?>?')">
                            <i class="bi-trash"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<script>
// Buat baris tabel bisa diklik tanpa konflik dengan tombol aksi
document.querySelectorAll('tbody tr').forEach(function(row) {
    row.style.cursor = 'pointer';
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
