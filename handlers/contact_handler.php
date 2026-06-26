<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/functions.php';

$response = [
    'success' => false,
    'message' => 'Metode request tidak diizinkan.'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        $response['message'] = 'Token CSRF tidak valid. Silakan muat ulang halaman.';
    } else {
        $name    = isset($_POST['name'])    ? trim(strip_tags($_POST['name']))    : '';
        $email   = isset($_POST['email'])   ? trim(strip_tags($_POST['email']))   : '';
        $subject = isset($_POST['subject']) ? trim(strip_tags($_POST['subject'])) : '';
        $message = isset($_POST['message']) ? trim(strip_tags($_POST['message'])) : '';

        if (empty($name) || empty($email) || empty($subject) || empty($message)) {
            $response['message'] = 'Semua kolom wajib diisi.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response['message'] = 'Format email tidak valid.';
        } else {

            // === 1. SIMPAN KE DATABASE ===
            $saved = false;
            try {
                $stmt = $db->prepare(
                    "INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)"
                );
                $saved = $stmt->execute([$name, $email, $subject, $message]);
            } catch (Exception $e) {
                // Log error tapi jangan tampilkan ke user
                error_log('Contact DB Error: ' . $e->getMessage());
            }

            // === 2. KIRIM EMAIL NOTIFIKASI KE ADMIN ===
            $admin_email = $settings['email'] ?? 'info@hastrakarya.co.id';
            $site_name   = $settings['site_name'] ?? 'PT. Hastra Karya Persada';

            $mail_subject = "[Pesan Baru] $subject — dari $name";

            $mail_body  = "Anda menerima pesan baru melalui form kontak website $site_name.\n\n";
            $mail_body .= "======================================\n";
            $mail_body .= "Nama    : $name\n";
            $mail_body .= "Email   : $email\n";
            $mail_body .= "Subjek  : $subject\n";
            $mail_body .= "Pesan   :\n$message\n";
            $mail_body .= "======================================\n\n";
            $mail_body .= "Waktu   : " . date('d-m-Y H:i:s') . "\n";
            $mail_body .= "Balas langsung ke pengirim: $email\n";

            $mail_headers  = "From: {$site_name} <{$admin_email}>\r\n";
            $mail_headers .= "Reply-To: {$name} <{$email}>\r\n";
            $mail_headers .= "X-Mailer: PHP/" . phpversion();

            $mail_sent = @mail($admin_email, $mail_subject, $mail_body, $mail_headers);

            if ($saved || $mail_sent) {
                $response['success'] = true;
                $response['message'] = "Terima kasih, <strong>$name</strong>! Pesan Anda mengenai <em>\"$subject\"</em> telah terkirim. Tim humas kami akan merespons melalui email (<strong>$email</strong>) dalam 1x24 jam kerja.";
            } else {
                $response['message'] = 'Terjadi kesalahan saat mengirim pesan. Silakan coba lagi atau hubungi kami langsung via WhatsApp.';
            }
        }
    }
}

echo json_encode($response);
exit();
