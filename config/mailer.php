<?php
/**
 * Simple SMTP Mailer — tanpa library eksternal
 * Menggunakan kredensial dari tabel settings database
 */

function send_smtp_mail(array $settings, string $to, string $subject, string $body, string $reply_to_name = '', string $reply_to_email = ''): bool {
    $host       = $settings['smtp_host']       ?? 'smtp.hostinger.com';
    $port       = (int)($settings['smtp_port'] ?? 587);
    $username   = $settings['smtp_username']   ?? '';
    $password   = $settings['smtp_password']   ?? '';
    $from_name  = $settings['site_name']       ?? 'PT. Hastra Karya Persada';
    $from_email = $settings['smtp_username']   ?? ($settings['email'] ?? 'info@hastrakaryapersada.com');
    $encryption = $settings['smtp_encryption'] ?? 'tls'; // 'tls' atau 'ssl'

    if (empty($username) || empty($password)) {
        // Fallback ke mail() jika SMTP belum dikonfigurasi
        $headers  = "From: {$from_name} <{$from_email}>\r\n";
        if (!empty($reply_to_email)) {
            $headers .= "Reply-To: {$reply_to_name} <{$reply_to_email}>\r\n";
        }
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();
        return @mail($to, $subject, $body, $headers);
    }

    try {
        // Buka koneksi ke SMTP server
        $context = stream_context_create();

        if ($encryption === 'ssl') {
            $socket = @stream_socket_client(
                "ssl://{$host}:{$port}",
                $errno, $errstr, 30,
                STREAM_CLIENT_CONNECT, $context
            );
        } else {
            // TLS (STARTTLS) — mulai dengan koneksi plain lalu upgrade
            $socket = @stream_socket_client(
                "tcp://{$host}:{$port}",
                $errno, $errstr, 30,
                STREAM_CLIENT_CONNECT, $context
            );
        }

        if (!$socket) {
            error_log("SMTP Connect Error: [{$errno}] {$errstr}");
            return false;
        }

        stream_set_timeout($socket, 30);

        // Helper: baca respons SMTP
        $read = function() use ($socket) {
            $response = '';
            while ($line = fgets($socket, 512)) {
                $response .= $line;
                if (substr($line, 3, 1) === ' ') break; // Baris terakhir
            }
            return $response;
        };

        // Helper: kirim perintah SMTP
        $send = function($cmd) use ($socket) {
            fwrite($socket, $cmd . "\r\n");
        };

        // Baca greeting server
        $read();

        // EHLO
        $send("EHLO {$host}");
        $ehlo_response = $read();

        // STARTTLS jika TLS
        if ($encryption === 'tls') {
            $send("STARTTLS");
            $read();
            stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
            // EHLO ulang setelah TLS
            $send("EHLO {$host}");
            $read();
        }

        // AUTH LOGIN
        $send("AUTH LOGIN");
        $read();
        $send(base64_encode($username));
        $read();
        $send(base64_encode($password));
        $auth_resp = $read();

        if (strpos($auth_resp, '235') === false) {
            error_log("SMTP Auth Failed: " . $auth_resp);
            fclose($socket);
            return false;
        }

        // MAIL FROM
        $send("MAIL FROM:<{$from_email}>");
        $read();

        // RCPT TO
        $send("RCPT TO:<{$to}>");
        $read();

        // DATA
        $send("DATA");
        $read();

        // Susun header email
        $reply_to_header = '';
        if (!empty($reply_to_email)) {
            $reply_to_header = "Reply-To: =?UTF-8?B?" . base64_encode($reply_to_name) . "?= <{$reply_to_email}>\r\n";
        }

        $encoded_from    = "=?UTF-8?B?" . base64_encode($from_name) . "?=";
        $encoded_subject = "=?UTF-8?B?" . base64_encode($subject) . "?=";
        $date            = date('r');

        $message  = "Date: {$date}\r\n";
        $message .= "From: {$encoded_from} <{$from_email}>\r\n";
        $message .= "To: <{$to}>\r\n";
        $message .= $reply_to_header;
        $message .= "Subject: {$encoded_subject}\r\n";
        $message .= "MIME-Version: 1.0\r\n";
        $message .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $message .= "Content-Transfer-Encoding: base64\r\n";
        $message .= "\r\n";
        $message .= chunk_split(base64_encode($body));
        $message .= "\r\n.";

        $send($message);
        $data_resp = $read();

        // QUIT
        $send("QUIT");
        fclose($socket);

        return strpos($data_resp, '250') !== false;

    } catch (Exception $e) {
        error_log("SMTP Exception: " . $e->getMessage());
        return false;
    }
}
