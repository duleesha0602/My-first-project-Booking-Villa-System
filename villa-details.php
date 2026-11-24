<?php
/**
 * ====================================================================
 *     Villa Information Management
 * Coastal Villa & Room Booking Management System
 * 
 * ====================================================================
 */

require_once __DIR__ . '/../includes/header.php';
require_admin_login();

$success_msg = '';
$error_msg = '';

// Handle Villa Information Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $villa_name     = sanitize_input($_POST['villa_name'] ?? '');
    $tagline        = sanitize_input($_POST['tagline'] ?? '');
    $description    = sanitize_input($_POST['description'] ?? '');
    $address        = sanitize_input($_POST['address'] ?? '');
    $location       = sanitize_input($_POST['location'] ?? '');
    $contact_number = sanitize_input($_POST['contact_number'] ?? '');
    $email          = sanitize_input($_POST['email'] ?? '');
    $facilities     = sanitize_input($_POST['facilities'] ?? '');
    $facebook_link  = sanitize_input($_POST['facebook_link'] ?? '');
    $instagram_link = sanitize_input($_POST['instagram_link'] ?? '');
    $twitter_link   = sanitize_input($_POST['twitter_link'] ?? '');

    if (empty($villa_name) || empty($address) || empty($contact_number) || empty($email)) {
        $error_msg = 'Please complete all required fields.';
    } else {
        try {
            $stmt = $pdo->prepare("
                UPDATE villa_details SET 
                    villa_name = ?, tagline = ?, description = ?, address = ?, location = ?, 
                    contact_number = ?, email = ?, facilities = ?, facebook_link = ?, 
                    instagram_link = ?, twitter_link = ?
                WHERE id = 1
            ");
            $stmt->execute([
                $villa_name, $tagline, $description, $address, $location,
                $contact_number, $email, $facilities, $facebook_link,
                $instagram_link, $twitter_link
            ]);
            $success_msg = 'Villa details and contact information have been updated successfully!';
        } catch (PDOException $e) {
            $error_msg = 'Error updating villa details: ' . $e->getMessage();
        }
    }
}

// Fetch current details
$details = get_villa_details($pdo);
?>

<div class="dashboard-wrapper">
    <!-- Admin Sidebar -->
    <?php require_once __DIR__ . '/../includes/admin-sidebar.php'; ?>

    <div class="dashboard-main">
        <!-- Topbar -->
        <div class="dashboard-topbar">
            <div class="dashboard-title">
                <h2><i class="fa-solid fa-hotel me-2 text-primary"></i> Villa Information Management</h2>
                <small class="text-muted">Edit villa branding, contact numbers, address, and social links</small>
            </div>
            <div>
                <a href="<?php echo url('index.php'); ?>" target="_blank" class="btn btn-outline-ocean btn-sm">
                    <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> View Live Site
                </a>
            </div>
        </div>

        <div class="dashboard-content">
            <!-- Flash Message & Local Alerts -->
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

            <div class="card border-0 rounded-4 shadow-sm" style="max-width: 920px;">
                <div class="card-body p-4 p-md-5">
                    <form action="<?php echo url('admin/villa-details.php'); ?>" method="POST">
                        <h5 class="fw-bold text-navy mb-3 pb-2 border-bottom">1. Brand & Description</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="villa_name" class="form-label text-navy fw-bold small">Villa Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="villa_name" name="villa_name" required value="<?php echo htmlspecialchars($details['villa_name']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="tagline" class="form-label text-navy fw-bold small">Tagline</label>
                                <input type="text" class="form-control" id="tagline" name="tagline" value="<?php echo htmlspecialchars($details['tagline']); ?>">
                            </div>
                            <div class="col-12">
                                <label for="description" class="form-label text-navy fw-bold small">Villa Overview Story / Description <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="description" name="description" rows="4" required><?php echo htmlspecialchars($details['description']); ?></textarea>
                            </div>
                            <div class="col-12">
                                <label for="facilities" class="form-label text-navy fw-bold small">Core Villa Facilities Summary</label>
                                <input type="text" class="form-control" id="facilities" name="facilities" value="<?php echo htmlspecialchars($details['facilities']); ?>">
                                <small class="text-muted">Comma-separated general property highlights</small>
                            </div>
                        </div>

                        <h5 class="fw-bold text-navy mb-3 pb-2 border-bottom">2. Contact Information & Location</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="contact_number" class="form-label text-navy fw-bold small">Primary Telephone / Hotline <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="contact_number" name="contact_number" required value="<?php echo htmlspecialchars($details['contact_number']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label text-navy fw-bold small">Reservations Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" required value="<?php echo htmlspecialchars($details['email']); ?>">
                            </div>
                            <div class="col-md-8">
                                <label for="address" class="form-label text-navy fw-bold small">Physical Address <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="address" name="address" required value="<?php echo htmlspecialchars($details['address']); ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="location" class="form-label text-navy fw-bold small">City / Region <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="location" name="location" required value="<?php echo htmlspecialchars($details['location']); ?>">
                            </div>
                        </div>

                        <h5 class="fw-bold text-navy mb-3 pb-2 border-bottom">3. Social Media Presence</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label for="facebook_link" class="form-label text-navy fw-bold small">Facebook URL</label>
                                <input type="url" class="form-control" id="facebook_link" name="facebook_link" value="<?php echo htmlspecialchars($details['facebook_link']); ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="instagram_link" class="form-label text-navy fw-bold small">Instagram URL</label>
                                <input type="url" class="form-control" id="instagram_link" name="instagram_link" value="<?php echo htmlspecialchars($details['instagram_link']); ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="twitter_link" class="form-label text-navy fw-bold small">X / Twitter URL</label>
                                <input type="url" class="form-control" id="twitter_link" name="twitter_link" value="<?php echo htmlspecialchars($details['twitter_link']); ?>">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-navy py-2 px-4 font-semibold">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Save Changes
                        </button>
                    </form>
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
