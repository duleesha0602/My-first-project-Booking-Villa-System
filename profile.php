<?php
/**
 * ====================================================================
 *     My Profile (Guest Profile & Password Management)
 * Coastal Villa & Room Booking Management System
 * 
 * ====================================================================
 */

require_once __DIR__ . '/../includes/header.php';
require_guest_login();

$user_id = $_SESSION['user_id'];
$success_msg = '';
$error_msg = '';

// Handle Profile Update POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $full_name = sanitize_input($_POST['full_name'] ?? '');
        $contact   = sanitize_input($_POST['contact_number'] ?? '');

        if (empty($full_name) || empty($contact)) {
            $error_msg = 'Name and contact number cannot be empty.';
        } else {
            $update_stmt = $pdo->prepare("UPDATE users SET full_name = ?, contact_number = ? WHERE id = ?");
            $update_stmt->execute([$full_name, $contact, $user_id]);
            $_SESSION['user_name'] = $full_name;
            $_SESSION['user_phone'] = $contact;
            $success_msg = 'Your profile details have been successfully updated.';
        }
    } elseif (isset($_POST['change_password'])) {
        $current_pwd = $_POST['current_password'] ?? '';
        $new_pwd     = $_POST['new_password'] ?? '';
        $confirm_pwd = $_POST['confirm_password'] ?? '';

        // Verify current password
        $pwd_stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $pwd_stmt->execute([$user_id]);
        $user_row = $pwd_stmt->fetch();

        if (!$user_row || !password_verify($current_pwd, $user_row['password'])) {
            $error_msg = 'The current password you entered is incorrect.';
        } elseif (strlen($new_pwd) < 6) {
            $error_msg = 'New password must be at least 6 characters long.';
        } elseif ($new_pwd !== $confirm_pwd) {
            $error_msg = 'New passwords do not match. Please re-enter.';
        } else {
            $new_hash = password_hash($new_pwd, PASSWORD_BCRYPT);
            $up_stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $up_stmt->execute([$new_hash, $user_id]);
            $success_msg = 'Your password has been successfully updated.';
        }
    }
}

// Fetch latest user record
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>

<div class="dashboard-wrapper">
    <!-- Guest Sidebar -->
    <?php require_once __DIR__ . '/../includes/guest-sidebar.php'; ?>

    <div class="dashboard-main">
        <!-- Topbar -->
        <div class="dashboard-topbar">
            <div class="dashboard-title">
                <h2><i class="fa-solid fa-id-card me-2 text-primary"></i> My Profile</h2>
                <small class="text-muted">Manage your personal account information and security settings</small>
            </div>
        </div>

        <div class="dashboard-content">
            <!-- Flash & Local Alerts -->
            <?php display_flash(); ?>

            <?php if (!empty($success_msg)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i> <?php echo $success_msg; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($error_msg)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-circle-exclamation me-2"></i> <?php echo $error_msg; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="row g-4">
                <!-- Left: Profile Information Form -->
                <div class="col-lg-6">
                    <div class="card border-0 rounded-4 shadow-sm h-100">
                        <div class="card-header bg-white py-3">
                            <h5 class="fw-bold text-navy mb-0"><i class="fa-solid fa-user-pen me-2 text-primary"></i> Personal Information</h5>
                        </div>
                        <div class="card-body p-4">
                            <form action="<?php echo url('guest/profile.php'); ?>" method="POST">
                                <input type="hidden" name="update_profile" value="1">

                                <div class="mb-3">
                                    <label class="form-label text-muted small">Registered Email Address</label>
                                    <input type="email" class="form-control bg-light" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                                    <small class="text-muted">Email address cannot be changed once registered.</small>
                                </div>

                                <div class="mb-3">
                                    <label for="full_name" class="form-label text-navy fw-bold small">Full Name</label>
                                    <input type="text" class="form-control" id="full_name" name="full_name" required value="<?php echo htmlspecialchars($user['full_name']); ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="contact_number" class="form-label text-navy fw-bold small">Contact Phone Number</label>
                                    <input type="text" class="form-control" id="contact_number" name="contact_number" required value="<?php echo htmlspecialchars($user['contact_number']); ?>">
                                </div>

                                <div class="mb-4">
                                    <label class="form-label text-muted small">Member Since</label>
                                    <div class="fw-semibold text-navy"><?php echo date('F d, Y', strtotime($user['created_at'])); ?></div>
                                </div>

                                <button type="submit" class="btn btn-ocean">
                                    <i class="fa-solid fa-floppy-disk me-1"></i> Save Profile Details
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Right: Password Security Form -->
                <div class="col-lg-6">
                    <div class="card border-0 rounded-4 shadow-sm h-100">
                        <div class="card-header bg-white py-3">
                            <h5 class="fw-bold text-navy mb-0"><i class="fa-solid fa-key me-2 text-primary"></i> Change Password</h5>
                        </div>
                        <div class="card-body p-4">
                            <form action="<?php echo url('guest/profile.php'); ?>" method="POST">
                                <input type="hidden" name="change_password" value="1">

                                <div class="mb-3">
                                    <label for="current_password" class="form-label text-navy fw-bold small">Current Password <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="current_password" name="current_password" required placeholder="Enter current password">
                                        <button type="button" class="btn btn-outline-secondary toggle-password-btn" data-target="current_password">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="new_password" class="form-label text-navy fw-bold small">New Password <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="new_password" name="new_password" required placeholder="Minimum 6 characters">
                                        <button type="button" class="btn btn-outline-secondary toggle-password-btn" data-target="new_password">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="confirm_password" class="form-label text-navy fw-bold small">Confirm New Password <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required placeholder="Re-enter new password">
                                        <button type="button" class="btn btn-outline-secondary toggle-password-btn" data-target="confirm_password">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-navy">
                                    <i class="fa-solid fa-shield-halved me-1"></i> Update Password
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo url('assets/js/main.js'); ?>"></script>
</body>
</html>
