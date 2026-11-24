<?php
/**
 * ====================================================================
 *      Room Availability Search
 * Coastal Villa & Room Booking Management System
 * 
 * ====================================================================
 */

require_once __DIR__ . '/../includes/header.php';

// Form inputs
$check_in  = sanitize_input($_GET['check_in'] ?? date('Y-m-d'));
$check_out = sanitize_input($_GET['check_out'] ?? date('Y-m-d', strtotime('+1 day')));
$guests    = (int)($_GET['guests'] ?? 2);

$validation_error = '';
$available_rooms = [];
$total_nights = 0;

// Execute search if both dates are provided
if (!empty($check_in) && !empty($check_out)) {
    $today = date('Y-m-d');

    // Rule BR05: Check-in not in past, check-out after check-in
    if ($check_in < $today) {
        $validation_error = 'Check-in date cannot be in the past.';
    } elseif ($check_out <= $check_in) {
        $validation_error = 'Check-out date must be strictly after the check-in date.';
    } else {
        $total_nights = calculate_nights($check_in, $check_out);

        // Core Availability Query:
        // Room must be active, have enough capacity, and NOT have an overlapping Confirmed booking
        // Overlap Condition: existing.check_in < :req_check_out AND existing.check_out > :req_check_in
        $sql = "
            SELECT r.* 
            FROM rooms r
            WHERE r.status = 'active'
              AND r.max_guests >= :guests
              AND r.id NOT IN (
                  SELECT b.room_id 
                  FROM bookings b 
                  WHERE b.booking_status = 'Confirmed'
                    AND b.check_in < :check_out 
                    AND b.check_out > :check_in
              )
            ORDER BY r.price_per_night ASC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':guests'    => $guests,
            ':check_in'  => $check_in,
            ':check_out' => $check_out
        ]);
        $available_rooms = $stmt->fetchAll();
    }
}
?>

<div class="dashboard-wrapper">
    <!-- Guest or Admin Sidebar (or guest sidebar if logged in) -->
    <?php if (is_guest()): ?>
        <?php require_once __DIR__ . '/../includes/guest-sidebar.php'; ?>
    <?php endif; ?>

    <div class="dashboard-main">
        <!-- Topbar -->
        <div class="dashboard-topbar">
            <div class="dashboard-title">
                <h2><i class="fa-solid fa-magnifying-glass me-2 text-primary"></i> Search Room Availability</h2>
                <small class="text-muted">Find open rooms matching your stay dates and party size</small>
            </div>
            <div>
                <a href="<?php echo url('rooms.php'); ?>" class="btn btn-outline-ocean btn-sm">
                    <i class="fa-solid fa-bed me-1"></i> View All Rooms
                </a>
            </div>
        </div>

        <div class="dashboard-content">
            <!-- Flash Alert -->
            <?php display_flash(); ?>

            <!-- Availability Search Form Box -->
            <div class="card border-0 rounded-4 shadow-sm mb-4">
                <div class="card-body p-4">
                    <form action="<?php echo url('guest/availability.php'); ?>" method="GET" class="row g-3 align-items-end">
                        <div class="col-md-3 col-sm-6">
                            <label for="check_in" class="form-label text-navy fw-bold mb-1">
                                <i class="fa-solid fa-calendar-days text-primary me-1"></i> Check-In Date
                            </label>
                            <input type="date" class="form-control" id="check_in" name="check_in" required value="<?php echo htmlspecialchars($check_in); ?>" min="<?php echo date('Y-m-d'); ?>">
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <label for="check_out" class="form-label text-navy fw-bold mb-1">
                                <i class="fa-solid fa-calendar-days text-primary me-1"></i> Check-Out Date
                            </label>
                            <input type="date" class="form-control" id="check_out" name="check_out" required value="<?php echo htmlspecialchars($check_out); ?>" min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <label for="guests" class="form-label text-navy fw-bold mb-1">
                                <i class="fa-solid fa-user-group text-primary me-1"></i> Guests
                            </label>
                            <select class="form-select" id="guests" name="guests">
                                <option value="1" <?php echo ($guests === 1) ? 'selected' : ''; ?>>1 Guest</option>
                                <option value="2" <?php echo ($guests === 2) ? 'selected' : ''; ?>>2 Guests</option>
                                <option value="3" <?php echo ($guests === 3) ? 'selected' : ''; ?>>3 Guests</option>
                                <option value="4" <?php echo ($guests === 4) ? 'selected' : ''; ?>>4 Guests</option>
                                <option value="5" <?php echo ($guests >= 5) ? 'selected' : ''; ?>>5+ Guests</option>
                            </select>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <button type="submit" class="btn btn-ocean w-100 py-2">
                                <i class="fa-solid fa-search me-1"></i> Check Availability
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Validation Error Alert -->
            <?php if (!empty($validation_error)): ?>
                <div class="alert alert-danger rounded-3 shadow-sm mb-4" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i> <?php echo $validation_error; ?>
                </div>
            <?php endif; ?>

            <!-- Search Results Section -->
            <?php if (empty($validation_error) && !empty($check_in) && !empty($check_out)): ?>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold text-navy mb-0">
                            Available Rooms for <?php echo format_date($check_in); ?> &rarr; <?php echo format_date($check_out); ?>
                        </h5>
                        <span class="badge bg-light text-navy border mt-1">Duration: <?php echo $total_nights; ?> Night(s)</span>
                    </div>
                    <span class="text-muted small"><?php echo count($available_rooms); ?> room(s) available</span>
                </div>

                <?php if (empty($available_rooms)): ?>
                    <div class="card p-5 text-center border-0 rounded-4 shadow-sm my-4">
                        <i class="fa-solid fa-calendar-xmark text-danger fa-3x mb-3"></i>
                        <h4 class="text-navy fw-bold">No Rooms Available</h4>
                        <p class="text-muted max-w-500 mx-auto">
                            All rooms are currently booked or exceed capacity for your requested dates (<?php echo format_date($check_in); ?> to <?php echo format_date($check_out); ?>). Please choose alternative dates.
                        </p>
                    </div>
                <?php else: ?>
                    <div class="row g-4">
                        <?php foreach ($available_rooms as $room): ?>
                            <?php $est_total = $room['price_per_night'] * $total_nights; ?>
                            <div class="col-lg-4 col-md-6">
                                <div class="room-card h-100">
                                    <div class="room-card-img-wrap">
                                        <img src="<?php echo url($room['room_image']); ?>" alt="<?php echo htmlspecialchars($room['room_name']); ?>" class="room-card-img">
                                        <span class="room-badge-type"><?php echo htmlspecialchars($room['room_type']); ?></span>
                                    </div>
                                    <div class="room-card-body">
                                        <h4 class="room-title"><?php echo htmlspecialchars($room['room_name']); ?></h4>
                                        <p class="text-muted small mb-2">
                                            <?php echo htmlspecialchars(substr($room['description'], 0, 110)); ?>...
                                        </p>
                                        <div class="room-meta-icons">
                                            <span><i class="fa-solid fa-user-group me-1 text-primary"></i> Up to <?php echo $room['max_guests']; ?> Guests</span>
                                            <span><i class="fa-solid fa-moon me-1 text-primary"></i> <?php echo $total_nights; ?> Nights</span>
                                        </div>

                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between align-items-baseline">
                                                <span class="text-muted small">Per Night:</span>
                                                <span class="fw-bold text-navy"><?php echo format_currency($room['price_per_night']); ?></span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-baseline">
                                                <span class="text-muted small">Estimated Total:</span>
                                                <span class="fw-bold text-ocean fs-5"><?php echo format_currency($est_total); ?></span>
                                            </div>
                                        </div>

                                        <div class="d-flex gap-2 mt-auto">
                                            <a href="<?php echo url('room-details.php?id=' . $room['id']); ?>" class="btn btn-outline-ocean btn-sm w-50">
                                                View Details
                                            </a>
                                            <a href="<?php echo url('guest/booking.php?room_id=' . $room['id'] . '&check_in=' . $check_in . '&check_out=' . $check_out . '&guests=' . $guests); ?>" class="btn btn-ocean btn-sm w-50">
                                                <i class="fa-solid fa-calendar-plus me-1"></i> Book Now
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo url('assets/js/main.js'); ?>"></script>
</body>
</html>
