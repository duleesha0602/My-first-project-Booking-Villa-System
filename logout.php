<?php
/**
 * ====================================================================
 *  User Logout
 * Coastal Villa & Room Booking Management System
 *
 * ====================================================================
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Unset all session values and destroy session
$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

// Start a fresh session to deliver flash notice
session_start();
set_flash('info', 'You have been successfully logged out. See you again soon!');
header('Location: ' . url('auth/login.php'));
exit();
?>