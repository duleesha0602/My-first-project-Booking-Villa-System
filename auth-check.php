<?php
/**
 * ====================================================================
 *    Role-Based Access Control & Session Verification
 * Coastal Villa & Room Booking Management System
 * 
 * ====================================================================
 
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';

/**
 * Check if any user (Guest or Admin) is currently logged in
 */
function is_logged_in() {
    return !empty($_SESSION['user_id']);
}

/**
 * Check if the currently logged-in user is an Administrator
 */
function is_admin() {
    return (!empty($_SESSION['user_id']) && !empty($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin');
}

/**
 * Check if the currently logged-in user is a Guest
 */
function is_guest() {
    return (!empty($_SESSION['user_id']) && !empty($_SESSION['user_role']) && $_SESSION['user_role'] === 'guest');
}

/**
 * Retrieve the active user's session data
 */
function get_logged_user() {
    if (!is_logged_in()) {
        return null;
    }
    return [
        'id'        => $_SESSION['user_id'] ?? null,
        'name'      => $_SESSION['user_name'] ?? 'Guest',
        'email'     => $_SESSION['user_email'] ?? '',
        'role'      => $_SESSION['user_role'] ?? 'guest',
        'phone'     => $_SESSION['user_phone'] ?? ''
    ];
}

/**
 * Protect Guest Pages (Require guest login)
 
 */
function require_guest_login() {
    if (!is_logged_in()) {
        set_flash('warning', 'Please login to your guest account to access this page.');
        header('Location: ' . url('auth/login.php'));
        exit();
    }
}

/**
 * Protect Administrator Pages (Require admin role)
 
 */
function require_admin_login() {
    if (!is_admin()) {
        set_flash('danger', 'Access denied. You must be an administrator to access the admin panel.');
        header('Location: ' . url('admin/login.php'));
        exit();
    }
}
?>