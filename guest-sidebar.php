<?php
/**
 * ====================================================================
 * Guest Dashboard Sidebar Navigation
 * Coastal Villa & Room Booking Management System
 * 
 * ====================================================================
 */

$current_page = basename($_SERVER['PHP_SELF']);
$logged_user = get_logged_user();
?>
<aside class="dashboard-sidebar">
    <!-- Brand -->
    <div class="sidebar-brand">
        <i class="fa-solid fa-umbrella-beach text-info"></i>
        <a href="<?php echo url('guest/dashboard.php'); ?>">MY ACCOUNT</a>
    </div>

    <!-- Guest User Profile Card -->
    <div class="sidebar-user">
        <div class="sidebar-avatar">
            <i class="fa-solid fa-user"></i>
        </div>
        <div>
            <div class="sidebar-user-name"><?php echo htmlspecialchars($logged_user['name'] ?? 'Guest'); ?></div>
            <div class="sidebar-user-role">Registered Guest</div>
        </div>
    </div>

    <!-- Guest Navigation Links -->
    <ul class="sidebar-menu">
        <li>
            <a href="<?php echo url('guest/dashboard.php'); ?>" class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-gauge"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="<?php echo url('guest/availability.php'); ?>" class="<?php echo ($current_page == 'availability.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-magnifying-glass"></i> Search Rooms
            </a>
        </li>
        <li>
            <a href="<?php echo url('guest/my-bookings.php'); ?>" class="<?php echo ($current_page == 'my-bookings.php' || $current_page == 'booking-details.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-calendar-check"></i> My Bookings
            </a>
        </li>
        <li>
            <a href="<?php echo url('guest/profile.php'); ?>" class="<?php echo ($current_page == 'profile.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-user-pen"></i> My Profile
            </a>
        </li>
        <li>
            <a href="<?php echo url('contact.php'); ?>">
                <i class="fa-solid fa-headset"></i> Contact Us
            </a>
        </li>
        <li>
            <a href="<?php echo url('index.php'); ?>" target="_blank">
                <i class="fa-solid fa-house"></i> View Website
            </a>
        </li>
    </ul>

    <!-- Logout -->
    <div class="sidebar-footer">
        <a href="<?php echo url('auth/logout.php'); ?>" class="btn btn-outline-light w-100 btn-sm">
            <i class="fa-solid fa-arrow-right-from-bracket me-1"></i> Logout
        </a>
    </div>
</aside>
