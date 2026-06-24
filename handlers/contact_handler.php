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
        $name = isset($_POST['name']) ? sanitize($_POST['name']) : '';
        $email = isset($_POST['email']) ? sanitize($_POST['email']) : '';
        $subject = isset($_POST['subject']) ? sanitize($_POST['subject']) : '';
        $message = isset($_POST['message']) ? sanitize($_POST['message']) : '';

        if (empty($name) || empty($email) || empty($subject) || empty($message)) {
            $response['message'] = 'Semua kolom wajib diisi.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response['message'] = 'Format email tidak valid.';
        } else {
            // Success simulated
            $response['success'] = true;
            $response['message'] = "Terima kasih, $name! Pesan Anda mengenai \"$subject\" telah terkirim. Tim humas kami akan merespons secepatnya.";
        }
    }
}

echo json_encode($response);
exit();
