<?php
/**
 * ====================================================================
 * Administrator Sidebar Navigation
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
        <a href="<?php echo url('admin/dashboard.php'); ?>">COASTAL ADMIN</a>
    </div>

    <!-- Active User Profile Card -->
    <div class="sidebar-user">
        <div class="sidebar-avatar">
            <i class="fa-solid fa-user-shield"></i>
        </div>
        <div>
            <div class="sidebar-user-name"><?php echo htmlspecialchars($logged_user['name'] ?? 'Admin'); ?></div>
            <div class="sidebar-user-role">Administrator</div>
        </div>
    </div>

    <!-- Admin Navigation Links -->
    <ul class="sidebar-menu">
        <li>
            <a href="<?php echo url('admin/dashboard.php'); ?>" class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-chart-line"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="<?php echo url('admin/rooms.php'); ?>" class="<?php echo ($current_page == 'rooms.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-bed"></i> Room Management
            </a>
        </li>
        <li>
            <a href="<?php echo url('admin/add-room.php'); ?>" class="<?php echo ($current_page == 'add-room.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-square-plus"></i> Add New Room
            </a>
        </li>
        <li>
            <a href="<?php echo url('admin/bookings.php'); ?>" class="<?php echo ($current_page == 'bookings.php' || $current_page == 'booking-details.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-calendar-check"></i> Bookings
            </a>
        </li>
        <li>
            <a href="<?php echo url('admin/guests.php'); ?>" class="<?php echo ($current_page == 'guests.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-users"></i> Guest Management
            </a>
        </li>
        <li>
            <a href="<?php echo url('admin/villa-details.php'); ?>" class="<?php echo ($current_page == 'villa-details.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-hotel"></i> Villa Information
            </a>
        </li>
        <li>
            <a href="<?php echo url('admin/gallery.php'); ?>" class="<?php echo ($current_page == 'gallery.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-images"></i> Gallery
            </a>
        </li>
        <li>
            <a href="<?php echo url('admin/enquiries.php'); ?>" class="<?php echo ($current_page == 'enquiries.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-envelope-open-text"></i> Enquiries
            </a>
        </li>
        <li>
            <a href="<?php echo url('admin/reports.php'); ?>" class="<?php echo ($current_page == 'reports.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-file-invoice-dollar"></i> Reports
            </a>
        </li>
        <li>
            <a href="<?php echo url('admin/profile.php'); ?>" class="<?php echo ($current_page == 'profile.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-user-gear"></i> Admin Profile
            </a>
        </li>
        <li>
            <a href="<?php echo url('index.php'); ?>" target="_blank">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> View Website
            </a>
        </li>
    </ul>

    <!-- Logout -->
    <div class="sidebar-footer">
        <a href="<?php echo url('admin/logout.php'); ?>" class="btn btn-danger w-100 btn-sm">
            <i class="fa-solid fa-arrow-right-from-bracket me-1"></i> Logout
        </a>
    </div>
</aside>
