<?php
/**
 * ====================================================================
 *  Add New Room
 * Coastal Villa & Room Booking Management System
 * 
 * ====================================================================
 */

require_once __DIR__ . '/../includes/header.php';
require_admin_login();
?>

<div class="dashboard-wrapper">
    <!-- Admin Sidebar -->
    <?php require_once __DIR__ . '/../includes/admin-sidebar.php'; ?>

    <div class="dashboard-main">
        <!-- Topbar -->
        <div class="dashboard-topbar">
            <div class="dashboard-title">
                <h2><i class="fa-solid fa-square-plus me-2 text-primary"></i> Add New Room</h2>
                <small class="text-muted">Register a new accommodation option to the villa booking inventory</small>
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
                        <input type="hidden" name="action" value="add">

                        <div class="row g-3">
                            <!-- Room Name -->
                            <div class="col-md-8">
                                <label for="room_name" class="form-label text-navy fw-bold small">Room Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="room_name" name="room_name" required placeholder="e.g. Superior Ocean Balcony Suite">
                            </div>

                            <!-- Room Type -->
                            <div class="col-md-4">
                                <label for="room_type" class="form-label text-navy fw-bold small">Room Category / Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="room_type" name="room_type" required>
                                    <option value="Deluxe Ocean">Deluxe Ocean</option>
                                    <option value="Luxury Suite">Luxury Suite</option>
                                    <option value="Family Villa">Family Villa</option>
                                    <option value="Standard Beach">Standard Beach</option>
                                    <option value="Executive Beachfront">Executive Beachfront</option>
                                </select>
                            </div>

                            <!-- Nightly Price -->
                            <div class="col-md-4">
                                <label for="price_per_night" class="form-label text-navy fw-bold small">Price per Night (LKR) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-navy fw-bold">LKR</span>
                                    <input type="number" step="0.01" class="form-control" id="price_per_night" name="price_per_night" required placeholder="25000.00">
                                </div>
                            </div>

                            <!-- Capacity -->
                            <div class="col-md-4">
                                <label for="max_guests" class="form-label text-navy fw-bold small">Max Guest Capacity <span class="text-danger">*</span></label>
                                <input type="number" min="1" max="10" class="form-control" id="max_guests" name="max_guests" value="2" required>
                            </div>

                            <!-- Status -->
                            <div class="col-md-4">
                                <label for="status" class="form-label text-navy fw-bold small">Initial Status <span class="text-danger">*</span></label>
                                <select class="form-select" id="status" name="status">
                                    <option value="active" selected>Active (Bookable)</option>
                                    <option value="inactive">Inactive (Hidden)</option>
                                </select>
                            </div>

                            <!-- Room Image Upload -->
                            <div class="col-12">
                                <label for="room_image" class="form-label text-navy fw-bold small">Room Photograph</label>
                                <input type="file" class="form-control" id="room_image" name="room_image" accept="image/png, image/jpeg, image/webp">
                                <small class="text-muted">Accepted formats: JPG, PNG, WEBP. If empty, a default coastal room photograph is assigned.</small>
                            </div>

                            <!-- Facilities -->
                            <div class="col-12">
                                <label for="facilities" class="form-label text-navy fw-bold small">Room Amenities / Facilities <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="facilities" name="facilities" required placeholder="e.g. Free Wi-Fi, Air Conditioning, Ocean Balcony, King Bed, Mini Bar">
                                <small class="text-muted">Comma-separated list of amenities</small>
                            </div>

                            <!-- Description -->
                            <div class="col-12">
                                <label for="description" class="form-label text-navy fw-bold small">Detailed Room Description <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="description" name="description" rows="4" required placeholder="Provide a rich description of room interior, sea views, bed types, and bathroom features..."></textarea>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="col-12 pt-3">
                                <button type="submit" class="btn btn-navy py-2 px-4 me-2">
                                    <i class="fa-solid fa-save me-1"></i> Save New Room
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
