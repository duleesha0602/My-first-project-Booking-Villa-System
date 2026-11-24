<?php


// Database credentials (default for local XAMPP)
$db_host = 'localhost';
$db_name = 'coastal_villa_db';
$db_user = 'root';
$db_pass = ''; // Default XAMPP MySQL password is empty

try {
    // Create secure PDO connection
    $pdo = new PDO("mysql:host={$db_host};dbname={$db_name};charset=utf8mb4", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on SQL errors
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetch associative arrays by default
        PDO::ATTR_EMULATE_PREPARES   => false,                  // Use real prepared statements
    ]);
} catch (PDOException $e) {
    // Display friendly error message if database is not yet configured
    die("
    <div style='font-family: Arial, sans-serif; max-width: 650px; margin: 40px auto; padding: 25px; border: 1px solid #D9534F; border-radius: 8px; background: #fff5f5;'>
        <h2 style='color: #D9534F; margin-top: 0;'>Database Connection Error</h2>
        <p>Could not connect to the MySQL database <strong>coastal_villa_db</strong>.</p>
        <p><strong>How to resolve:</strong></p>
        <ol>
            <li>Make sure <strong>XAMPP MySQL</strong> service is started.</li>
            <li>Open <a href='http://localhost/phpmyadmin' target='_blank'>phpMyAdmin</a> and create the database <code>coastal_villa_db</code>.</li>
            <li>Import the <code>database/db_structure.sql</code> file into <code>coastal_villa_db</code>.</li>
        </ol>
        <p style='color: #666; font-size: 13px;'>Technical detail: " . htmlspecialchars($e->getMessage()) . "</p>
    </div>");
}

// Start user session if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Automatically detect the root application URL dynamically
if (!defined('BASE_URL')) {
    $script_dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    // If the script is executed inside a subfolder like /admin, /guest, /auth, /actions, /ajax
    $subdirs = ['/admin', '/guest', '/auth', '/actions', '/ajax'];
    foreach ($subdirs as $sub) {
        if (substr($script_dir, -strlen($sub)) === $sub) {
            $script_dir = substr($script_dir, 0, -strlen($sub));
            break;
        }
    }
    $base = ($script_dir === '/' || $script_dir === '\\' || $script_dir === '.') ? '' : rtrim($script_dir, '/');
    define('BASE_URL', $base . '/');
}

/**
 * Helper function to create clean URLs for links, assets, and redirection
 * Example: url('rooms.php') or url('assets/css/style.css')
 */
if (!function_exists('url')) {
    function url($path = '') {
        return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
    }
}
?>