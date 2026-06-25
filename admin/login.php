<?php
require_once __DIR__ . '/../config/functions.php';

// Redirect if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: index.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        $error = 'Token CSRF tidak valid. Silakan muat ulang halaman.';
    } else {
        $username = sanitize($_POST['username']);
        $password = $_POST['password'];
        
        try {
            $stmt = $db->prepare("SELECT * FROM admins WHERE username = ?");
            $stmt->execute([$username]);
            $admin = $stmt->fetch();
            
            if ($admin && password_verify($password, $admin['password'])) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id']        = $admin['id'];
                $_SESSION['admin_username']  = $admin['username'];
                $_SESSION['admin_email']     = $admin['email'];
                
                $update_stmt = $db->prepare("UPDATE admins SET last_login = NOW() WHERE id = ?");
                $update_stmt->execute([$admin['id']]);
                
                header("Location: index.php");
                exit();
            } else {
                $error = 'Username atau Password tidak sesuai. Silakan coba lagi.';
            }
        } catch (Exception $e) {
            $error = 'Terjadi kesalahan sistem: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin | PT. Hastra Karya Persada</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?php echo base_url('assets/css/admin.css'); ?>" rel="stylesheet">
</head>
<body class="admin-login-body">

    <div class="login-card-admin">

        <!-- Logo -->
        <div style="text-align:center;margin-bottom:1.5rem;">
            <img src="<?php echo base_url('assets/img/logo.png'); ?>" alt="Logo PT. Hastra Karya Persada" style="height:72px;width:auto;object-fit:contain;">
        </div>

        <!-- Brand name -->
        <div class="login-logo">PT. HASTRA KARYA <span>PERSADA</span></div>
        <div class="login-sub">Sistem Manajemen Konten · Masuk Administrator</div>

        <!-- Error Alert -->
        <?php if (!empty($error)): ?>
            <div class="alert-admin alert-admin-danger">
                <i class="bi-exclamation-triangle-fill"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <!-- Login Form -->
        <form action="" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">

            <label class="form-label-admin">Username</label>
            <div class="login-input-wrap">
                <i class="bi-person login-input-icon"></i>
                <input type="text" class="login-input" id="username" name="username" required autocomplete="username" placeholder="Masukkan username">
            </div>

            <label class="form-label-admin">Password</label>
            <div class="login-input-wrap">
                <i class="bi-lock login-input-icon"></i>
                <input type="password" class="login-input" id="password" name="password" required autocomplete="current-password" placeholder="Masukkan password">
            </div>

            <button type="submit" name="login" class="login-btn">
                <i class="bi-box-arrow-in-right me-1"></i> Masuk Dashboard
            </button>
        </form>

        <a href="<?php echo base_url(); ?>" class="login-back">
            <i class="bi-arrow-left me-1"></i> Kembali ke Website
        </a>
    </div>

</body>
</html>
