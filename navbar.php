<?php
/**
 * ====================================================================
 * Public Navigation Bar Template
 * Coastal Villa & Room Booking Management System
 * 
 * ====================================================================
 */

$current_page = basename($_SERVER['PHP_SELF']);
$logged_user = get_logged_user();
?>
<!-- Main Top Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark navbar-coastal sticky-top">
    <div class="container">
        <!-- Villa Brand Logo & Name -->
        <a class="navbar-brand" href="<?php echo url('index.php'); ?>">
            <i class="fa-solid fa-umbrella-beach"></i>
            <span>COASTAL VILLA</span>
        </a>

        <!-- Mobile Toggler -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMainContent" aria-controls="navbarMainContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar Links -->
        <div class="collapse navbar-collapse" id="navbarMainContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>" href="<?php echo url('index.php'); ?>">
                        <i class="fa-solid fa-house me-1"></i> Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'about.php') ? 'active' : ''; ?>" href="<?php echo url('about.php'); ?>">
                        <i class="fa-solid fa-circle-info me-1"></i> About Us
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'rooms.php' || $current_page == 'room-details.php') ? 'active' : ''; ?>" href="<?php echo url('rooms.php'); ?>">
                        <i class="fa-solid fa-bed me-1"></i> Rooms
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'gallery.php') ? 'active' : ''; ?>" href="<?php echo url('gallery.php'); ?>">
                        <i class="fa-solid fa-images me-1"></i> Gallery
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'contact.php') ? 'active' : ''; ?>" href="<?php echo url('contact.php'); ?>">
                        <i class="fa-solid fa-location-dot me-1"></i> Location & Contact
                    </a>
                </li>
            </ul>

            <!-- Right-side Auth & Booking Actions -->
            <div class="d-flex align-items-center gap-2">
                <?php if (is_admin()): ?>
                    <!-- Admin Quick Access -->
                    <a href="<?php echo url('admin/dashboard.php'); ?>" class="btn btn-outline-light btn-sm">
                        <i class="fa-solid fa-shield-halved me-1"></i> Admin Panel
                    </a>
                    <a href="<?php echo url('admin/logout.php'); ?>" class="btn btn-danger btn-sm">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    </a>

                <?php elseif (is_guest()): ?>
                    <!-- Guest Account Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-outline-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-user-circle me-1"></i> <?php echo htmlspecialchars($logged_user['name']); ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            <li><a class="dropdown-item" href="<?php echo url('guest/dashboard.php'); ?>"><i class="fa-solid fa-gauge me-2 text-primary"></i> Dashboard</a></li>
                            <li><a class="dropdown-item" href="<?php echo url('guest/my-bookings.php'); ?>"><i class="fa-solid fa-calendar-check me-2 text-primary"></i> My Bookings</a></li>
                            <li><a class="dropdown-item" href="<?php echo url('guest/profile.php'); ?>"><i class="fa-solid fa-id-card me-2 text-primary"></i> My Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?php echo url('auth/logout.php'); ?>"><i class="fa-solid fa-arrow-right-from-bracket me-2"></i> Logout</a></li>
                        </ul>
                    </div>
                    <a href="<?php echo url('rooms.php'); ?>" class="btn btn-ocean btn-sm">
                        <i class="fa-solid fa-calendar-plus me-1"></i> Book Now
                    </a>

                <?php else: ?>
                    <!-- Public Guest Login & Register Links -->
                    <a href="<?php echo url('auth/login.php'); ?>" class="btn btn-outline-light btn-sm">
                        <i class="fa-solid fa-right-to-bracket me-1"></i> Login
                    </a>
                    <a href="<?php echo url('auth/register.php'); ?>" class="btn btn-outline-light btn-sm">
                        <i class="fa-solid fa-user-plus me-1"></i> Register
                    </a>
                    <a href="<?php echo url('rooms.php'); ?>" class="btn btn-ocean btn-sm">
                        <i class="fa-solid fa-calendar-plus me-1"></i> Book Now
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<!-- Container for Flash Notification Messages -->
<div class="container mt-3">
    <?php display_flash(); ?>
</div>
