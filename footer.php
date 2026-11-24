<?php
/**
 * ====================================================================
 * Public Footer Template
 * Coastal Villa & Room Booking Management System
 * 
 * ====================================================================
 */

$villa = $villa ?? get_villa_details($pdo);
?>
<!-- Footer Section -->
<footer class="footer-coastal">
    <div class="container">
        <div class="row g-4">
            <!-- Brand & Intro -->
            <div class="col-lg-4 col-md-6">
                <div class="footer-brand">
                    <i class="fa-solid fa-umbrella-beach text-info me-2"></i>
                    <span>COASTAL VILLA</span>
                </div>
                <p class="text-light-50 small">
                    <?php echo htmlspecialchars($villa['tagline']); ?>. An idyllic luxury sanctuary along the southern coast offering memorable oceanfront stays and true hospitality.
                </p>
                <div class="d-flex gap-3 mt-3">
                    <a href="<?php echo htmlspecialchars($villa['facebook_link']); ?>" target="_blank" class="btn btn-outline-light btn-sm rounded-circle" style="width: 36px; height: 36px; line-height: 22px;">
                        <i class="fa-brands fa-facebook-f"></i>
                    </a>
                    <a href="<?php echo htmlspecialchars($villa['instagram_link']); ?>" target="_blank" class="btn btn-outline-light btn-sm rounded-circle" style="width: 36px; height: 36px; line-height: 22px;">
                        <i class="fa-brands fa-instagram"></i>
                    </a>
                    <a href="<?php echo htmlspecialchars($villa['twitter_link']); ?>" target="_blank" class="btn btn-outline-light btn-sm rounded-circle" style="width: 36px; height: 36px; line-height: 22px;">
                        <i class="fa-brands fa-x-twitter"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Navigation Links -->
            <div class="col-lg-2 col-md-6 col-6">
                <h5 class="text-white mb-3 font-semibold">Explore</h5>
                <ul class="footer-links">
                    <li><a href="<?php echo url('index.php'); ?>"><i class="fa-solid fa-angle-right me-1 small"></i> Home</a></li>
                    <li><a href="<?php echo url('about.php'); ?>"><i class="fa-solid fa-angle-right me-1 small"></i> About Us</a></li>
                    <li><a href="<?php echo url('rooms.php'); ?>"><i class="fa-solid fa-angle-right me-1 small"></i> Rooms & Suites</a></li>
                    <li><a href="<?php echo url('gallery.php'); ?>"><i class="fa-solid fa-angle-right me-1 small"></i> Photo Gallery</a></li>
                    <li><a href="<?php echo url('contact.php'); ?>"><i class="fa-solid fa-angle-right me-1 small"></i> Location & Contact</a></li>
                </ul>
            </div>

            <!-- Guest & Booking Links -->
            <div class="col-lg-2 col-md-6 col-6">
                <h5 class="text-white mb-3 font-semibold">Guest Area</h5>
                <ul class="footer-links">
                    <li><a href="<?php echo url('guest/availability.php'); ?>"><i class="fa-solid fa-angle-right me-1 small"></i> Check Availability</a></li>
                    <li><a href="<?php echo url('auth/login.php'); ?>"><i class="fa-solid fa-angle-right me-1 small"></i> Guest Login</a></li>
                    <li><a href="<?php echo url('auth/register.php'); ?>"><i class="fa-solid fa-angle-right me-1 small"></i> Create Account</a></li>
                    <li><a href="<?php echo url('guest/my-bookings.php'); ?>"><i class="fa-solid fa-angle-right me-1 small"></i> My Bookings</a></li>
                    <li><a href="<?php echo url('admin/login.php'); ?>" class="text-warning"><i class="fa-solid fa-lock me-1 small"></i> Admin Login</a></li>
                </ul>
            </div>

            <!-- Contact Information -->
            <div class="col-lg-4 col-md-6">
                <h5 class="text-white mb-3 font-semibold">Contact Us</h5>
                <ul class="list-unstyled text-light-50 small">
                    <li class="mb-2">
                        <i class="fa-solid fa-location-dot text-info me-2"></i>
                        <span><?php echo htmlspecialchars($villa['address']); ?></span>
                    </li>
                    <li class="mb-2">
                        <i class="fa-solid fa-phone text-info me-2"></i>
                        <span><?php echo htmlspecialchars($villa['contact_number']); ?></span>
                    </li>
                    <li class="mb-2">
                        <i class="fa-solid fa-envelope text-info me-2"></i>
                        <span><?php echo htmlspecialchars($villa['email']); ?></span>
                    </li>
                    <li class="mb-2">
                        <i class="fa-solid fa-clock text-info me-2"></i>
                        <span>Check-In: 2:00 PM | Check-Out: 11:00 AM</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Copyright -->
        <div class="footer-bottom text-center">
            <p class="mb-0">
                &copy; <?php echo date('Y'); ?> <strong>Coastal Villa</strong>. All Rights Reserved.
            </p>
        </div>
    </div>
</footer>

<!-- Bootstrap 5 Bundle JS (CDN) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JS files -->
<script src="<?php echo url('assets/js/main.js'); ?>"></script>
<script src="<?php echo url('assets/js/validation.js'); ?>"></script>
</body>
</html>
