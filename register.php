<?php
/**
 * ====================================================================
 *     Guest Registration
 * Coastal Villa & Room Booking Management System
 * 
 * ====================================================================
 */

$page_title = 'Guest Registration - Coastal Villa';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';

// If already logged in as guest, redirect to dashboard
if (is_guest()) {
    header('Location: ' . url('guest/dashboard.php'));
    exit();
}
?>

<div class="container py-5">
    <div class="auth-card">
        <div class="auth-header">
            <i class="fa-solid fa-user-plus fa-2x mb-2 text-info"></i>
            <h3>Create Guest Account</h3>
            <p class="text-light-50 small mb-0">Register to check availability, book rooms, and manage reservations</p>
        </div>

        <div class="auth-body">
            <form action="<?php echo url('actions/register-action.php'); ?>" method="POST" onsubmit="return validateRegisterForm()">
                <!-- Full Name -->
                <div class="mb-3">
                    <label for="full_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-user"></i></span>
                        <input type="text" class="form-control" id="full_name" name="full_name" required placeholder="e.g. Kasun Perera">
                    </div>
                </div>

                <!-- Email Address -->
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-envelope"></i></span>
                        <input type="email" class="form-control" id="email" name="email" required placeholder="name@example.com">
                    </div>
                </div>

                <!-- Contact Number -->
                <div class="mb-3">
                    <label for="contact_number" class="form-label">Contact Phone Number <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-phone"></i></span>
                        <input type="text" class="form-control" id="contact_number" name="contact_number" required placeholder="+94 77 123 4567">
                    </div>
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" required placeholder="At least 6 characters">
                        <button type="button" class="btn btn-outline-secondary toggle-password-btn" data-target="password">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="mb-4">
                    <label for="confirm_password" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-lock"></i></span>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required placeholder="Re-type your password">
                        <button type="button" class="btn btn-outline-secondary toggle-password-btn" data-target="confirm_password">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-ocean w-100 py-2 mb-3">
                    <i class="fa-solid fa-user-check me-2"></i> Register Account
                </button>

                <!-- Login Link -->
                <div class="text-center small text-muted">
                    Already have an account? <a href="<?php echo url('auth/login.php'); ?>" class="text-ocean fw-bold">Login here</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
