<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/database.php';

// Instantiate database connection
$dbClass = new Database();
$db = $dbClass->getConnection();

// Load site settings into global array
$settings = [];
try {
    $stmt = $db->query("SELECT key_name, key_value FROM settings");
    while ($row = $stmt->fetch()) {
        $settings[$row['key_name']] = $row['key_value'];
    }
} catch (Exception $e) {
    // Fail silently or handle if table isn't initialized yet
}

/**
 * Get dynamic Base URL of the project
 */
function base_url($path = '') {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    
    $doc_root = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
    $app_root = str_replace('\\', '/', dirname(__DIR__));
    
    $base_dir = str_replace($doc_root, '', $app_root);
    
    $base = $protocol . "://" . $host . rtrim($base_dir, '/') . "/";
    
    return $base . ltrim($path, '/');
}

/**
 * Sanitize text input to prevent XSS
 */
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate SEO clean URL Slug
 */
function slugify($text) {
    // Replace non letter or digits by -
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    // Transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    // Remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);
    // Trim
    $text = trim($text, '-');
    // Remove duplicate -
    $text = preg_replace('~-+~', '-', $text);
    // Lowercase
    $text = strtolower($text);
    
    if (empty($text)) {
        return 'n-a';
    }
    return $text;
}

/**
 * CSRF Protection Helpers
 */
function get_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Generate Breadcrumbs dynamically
 */
function generate_breadcrumbs() {
    $breadcrumbs = [
        ['title' => 'Home', 'url' => base_url()]
    ];
    
    $uri = $_SERVER['REQUEST_URI'];
    // Handle subfolder deployment
    $script_dir = dirname($_SERVER['SCRIPT_NAME']);
    $script_dir = str_replace('\\', '/', $script_dir);
    if ($script_dir !== '/') {
        $uri = str_replace($script_dir, '', $uri);
    }
    
    $uri = parse_url($uri, PHP_URL_PATH);
    $uri = trim($uri, '/');
    
    if (!empty($uri)) {
        $parts = explode('/', $uri);
        $current_path = '';
        foreach ($parts as $key => $part) {
            $current_path .= $part . '/';
            // Translate common filenames to readable title
            $title = ucwords(str_replace(['-', '.php', '_'], [' ', '', ' '], $part));
            if ($part === 'tentang') {
                $title = 'Tentang Kami';
            } elseif ($part === 'portofolio') {
                $title = 'Portofolio';
            } elseif ($part === 'layanan') {
                $title = 'Layanan';
            }
            
            // Limit page titles for detail pages
            if ($key === count($parts) - 1 && isset($_GET['slug'])) {
                // We will handle specific detail titles in the page output itself
                continue;
            }
            
            $breadcrumbs[] = [
                'title' => $title,
                'url' => base_url($current_path)
            ];
        }
    }
    
    return $breadcrumbs;
}

/**
 * Helper to check current active menu
 */
function is_active($page_name) {
    $current_page = basename($_SERVER['PHP_SELF']);
    return ($current_page === $page_name) ? 'active' : '';
}

/**
 * Format Date to Indonesian format
 */
function format_date_id($date_string) {
    if (empty($date_string)) return '-';
    $months = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $time = strtotime($date_string);
    $day = date('d', $time);
    $month = $months[(int)date('m', $time)];
    $year = date('Y', $time);
    return "$day $month $year";
}
?>
