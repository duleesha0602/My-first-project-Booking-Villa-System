<?php
/**
 * ====================================================================
 *    Guest Login
 * Coastal Villa & Room Booking Management System
 * 
 * ====================================================================
 */

$page_title = 'Guest Login - Coastal Villa';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';

// If already logged in, redirect
if (is_guest()) {
    header('Location: ' . url('guest/dashboard.php'));
    exit();
}
?>

<div class="container py-5">
    <div class="auth-card">
        <div class="auth-header">
            <i class="fa-solid fa-right-to-bracket fa-2x mb-2 text-info"></i>
            <h3>Guest Login</h3>
            <p class="text-light-50 small mb-0">Access your personal coastal villa booking dashboard</p>
        </div>

        <div class="auth-body">
            <form action="<?php echo url('actions/login-action.php'); ?>" method="POST" onsubmit="return validateLoginForm()">
                <!-- Email Address -->
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-envelope"></i></span>
                        <input type="email" class="form-control" id="email" name="email" required placeholder="name@example.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    </div>
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between">
                        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" required placeholder="Enter your password">
                        <button type="button" class="btn btn-outline-secondary toggle-password-btn" data-target="password">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-ocean w-100 py-2 mb-3">
                    <i class="fa-solid fa-arrow-right-to-bracket me-2"></i> Sign In to Account
                </button>

                <!-- Registration Link -->
                <div class="text-center small text-muted">
                    Don't have an account yet? <a href="<?php echo url('auth/register.php'); ?>" class="text-ocean fw-bold">Create Account</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
