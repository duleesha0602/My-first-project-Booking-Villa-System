<?php
/**
 * ====================================================================
 *  Edit Room
 * Coastal Villa & Room Booking Management System
 * 
 * ====================================================================
 */

require_once __DIR__ . '/../includes/header.php';
require_admin_login();

$room_id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM rooms WHERE id = ?");
$stmt->execute([$room_id]);
$room = $stmt->fetch();

if (!$room) {
    set_flash('danger', 'Room record not found.');
    header('Location: ' . url('admin/rooms.php'));
    exit();
}
?>

<div class="dashboard-wrapper">
    <!-- Admin Sidebar -->
    <?php require_once __DIR__ . '/../includes/admin-sidebar.php'; ?>

    <div class="dashboard-main">
        <!-- Topbar -->
        <div class="dashboard-topbar">
            <div class="dashboard-title">
                <h2><i class="fa-solid fa-pen-to-square me-2 text-primary"></i> Edit Room #<?php echo $room['id']; ?></h2>
                <small class="text-muted">Update pricing, description, facilities, or active status</small>
            </div>
            <div>
                <a href="<?php echo url('admin/rooms.php'); ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Rooms
                </a>
            </div>
        </div>

        <div class="dashboard-content">
            <!-- Flash Message -->
            <?php display_flash(); ?>

            <div class="card border-0 rounded-4 shadow-sm" style="max-width: 880px;">
                <div class="card-body p-4 p-md-5">
                    <form action="<?php echo url('actions/room-action.php'); ?>" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="room_id" value="<?php echo $room['id']; ?>">

                        <div class="row g-3">
                            <!-- Current Photo Preview -->
                            <div class="col-12 mb-2">
                                <label class="form-label text-navy fw-bold small d-block">Current Photograph</label>
                                <img src="<?php echo url($room['room_image']); ?>" alt="Room Image" class="rounded-3 shadow-sm border" style="width: 140px; height: 95px; object-fit: cover;">
                            </div>

                            <!-- Room Name -->
                            <div class="col-md-8">
                                <label for="room_name" class="form-label text-navy fw-bold small">Room Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="room_name" name="room_name" required value="<?php echo htmlspecialchars($room['room_name']); ?>">
                            </div>

                            <!-- Room Type -->
                            <div class="col-md-4">
                                <label for="room_type" class="form-label text-navy fw-bold small">Room Category / Type <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="room_type" name="room_type" required value="<?php echo htmlspecialchars($room['room_type']); ?>">
                            </div>

                            <!-- Nightly Price -->
                            <div class="col-md-4">
                                <label for="price_per_night" class="form-label text-navy fw-bold small">Price per Night (LKR) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-navy fw-bold">LKR</span>
                                    <input type="number" step="0.01" class="form-control" id="price_per_night" name="price_per_night" required value="<?php echo $room['price_per_night']; ?>">
                                </div>
                            </div>

                            <!-- Capacity -->
                            <div class="col-md-4">
                                <label for="max_guests" class="form-label text-navy fw-bold small">Max Guest Capacity <span class="text-danger">*</span></label>
                                <input type="number" min="1" max="10" class="form-control" id="max_guests" name="max_guests" required value="<?php echo $room['max_guests']; ?>">
                            </div>

                            <!-- Status -->
                            <div class="col-md-4">
                                <label for="status" class="form-label text-navy fw-bold small">Status <span class="text-danger">*</span></label>
                                <select class="form-select" id="status" name="status">
                                    <option value="active" <?php echo ($room['status'] === 'active') ? 'selected' : ''; ?>>Active (Bookable)</option>
                                    <option value="inactive" <?php echo ($room['status'] === 'inactive') ? 'selected' : ''; ?>>Inactive (Hidden)</option>
                                </select>
                            </div>

                            <!-- Replace Image -->
                            <div class="col-12">
                                <label for="room_image" class="form-label text-navy fw-bold small">Upload Replacement Photograph (Optional)</label>
                                <input type="file" class="form-control" id="room_image" name="room_image" accept="image/png, image/jpeg, image/webp">
                                <small class="text-muted">Leave empty to keep current photograph.</small>
                            </div>

                            <!-- Facilities -->
                            <div class="col-12">
                                <label for="facilities" class="form-label text-navy fw-bold small">Room Amenities / Facilities <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="facilities" name="facilities" required value="<?php echo htmlspecialchars($room['facilities']); ?>">
                                <small class="text-muted">Comma-separated list of amenities</small>
                            </div>

                            <!-- Description -->
                            <div class="col-12">
                                <label for="description" class="form-label text-navy fw-bold small">Detailed Room Description <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="description" name="description" rows="4" required><?php echo htmlspecialchars($room['description']); ?></textarea>
                            </div>

                            <!-- Business Rule Info Box -->
                            <div class="col-12">
                                <div class="p-3 bg-light rounded-3 small text-muted border">
                                    <i class="fa-solid fa-circle-info text-primary me-1"></i>
                                    <strong>Assignment Rule (BR16):</strong> If a room has past or upcoming reservations, changing status to <code>Inactive</code> stops future bookings while preserving all existing booking history.
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="col-12 pt-2">
                                <button type="submit" class="btn btn-navy py-2 px-4 me-2">
                                    <i class="fa-solid fa-floppy-disk me-1"></i> Update Room Details
                                </button>
                                <a href="<?php echo url('admin/rooms.php'); ?>" class="btn btn-outline-secondary py-2 px-3">
                                    Cancel
                                </a>
                            </div>
                        </div>
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
