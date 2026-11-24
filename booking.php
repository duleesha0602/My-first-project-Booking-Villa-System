<?php
/**
 * ====================================================================
 *     Booking Form
 * Coastal Villa & Room Booking Management System
 * 
 * ====================================================================
 */

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';

// Guest authentication required (BR02)
require_guest_login();

$user = get_logged_user();
$room_id = (int)($_GET['room_id'] ?? 0);
$check_in_val = sanitize_input($_GET['check_in'] ?? date('Y-m-d'));
$check_out_val = sanitize_input($_GET['check_out'] ?? date('Y-m-d', strtotime('+1 day')));
$guests_val = (int)($_GET['guests'] ?? 2);

// Fetch room details
$stmt = $pdo->prepare("SELECT * FROM rooms WHERE id = ? AND status = 'active' LIMIT 1");
$stmt->execute([$room_id]);
$room = $stmt->fetch();

if (!$room) {
    echo "<div class='container my-5'><div class='alert alert-warning'>Please select a room to proceed with booking. <a href='" . url('rooms.php') . "'>Browse Rooms</a></div></div>";
    require_once __DIR__ . '/../includes/footer.php';
    exit();
}

// Initial calculation for display
$initial_nights = calculate_nights($check_in_val, $check_out_val);
$initial_total = $initial_nights * (float)$room['price_per_night'];
?>

<div class="container py-5">
    <div class="row g-4">
        <!-- Left: Booking Form -->
        <div class="col-lg-8">
            <div class="bg-white p-4 p-md-5 rounded-4 shadow-sm">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge bg-light text-navy border">Step 2 of 2</span>
                    <h3 class="text-navy fw-bold mb-0">Complete Your Reservation</h3>
                </div>
                <p class="text-muted small mb-4">Please verify your booking details below. Your reservation request will be submitted as <strong>Pending</strong> awaiting villa administration confirmation.</p>

                <form action="<?php echo url('actions/booking-action.php'); ?>" method="POST" onsubmit="return validateBookingForm()">
                    <input type="hidden" name="room_id" value="<?php echo $room['id']; ?>">
                    <input type="hidden" id="price_per_night" value="<?php echo $room['price_per_night']; ?>">
                    <input type="hidden" id="max_capacity" value="<?php echo $room['max_guests']; ?>">

                    <!-- Guest Details (Auto-filled from account) -->
                    <h5 class="fw-bold text-navy mb-3 pb-2 border-bottom">1. Guest Information</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small text-muted">Guest Full Name</label>
                            <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($user['name']); ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted">Email Address</label>
                            <input type="email" class="form-control bg-light" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted">Contact Phone</label>
                            <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($user['phone']); ?>" readonly>
                        </div>
                    </div>

                    <!-- Stay Dates & Guests -->
                    <h5 class="fw-bold text-navy mb-3 pb-2 border-bottom">2. Reservation Dates & Party</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label for="check_in" class="form-label small text-navy fw-bold">Check-In Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="check_in" name="check_in" required value="<?php echo htmlspecialchars($check_in_val); ?>" min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="check_out" class="form-label small text-navy fw-bold">Check-Out Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="check_out" name="check_out" required value="<?php echo htmlspecialchars($check_out_val); ?>" min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="guests" class="form-label small text-navy fw-bold">Number of Guests <span class="text-danger">*</span></label>
                            <select class="form-select" id="guests" name="guests" required>
                                <?php for ($g = 1; $g <= $room['max_guests']; $g++): ?>
                                    <option value="<?php echo $g; ?>" <?php echo ($guests_val == $g) ? 'selected' : ''; ?>>
                                        <?php echo $g; ?> Guest<?php echo ($g > 1) ? 's' : ''; ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                            <small class="text-muted">Maximum: <?php echo $room['max_guests']; ?> guests</small>
                        </div>
                    </div>

                    <!-- Special Requests -->
                    <h5 class="fw-bold text-navy mb-3 pb-2 border-bottom">3. Additional Requests (Optional)</h5>
                    <div class="mb-4">
                        <label for="special_requests" class="form-label small text-muted">Dietary preferences, estimated arrival time, airport pickup requests:</label>
                        <textarea class="form-control" id="special_requests" name="special_requests" rows="3" placeholder="Let us know how we can make your stay special..."></textarea>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-ocean btn-lg w-100 py-3 font-semibold">
                        <i class="fa-solid fa-lock me-2"></i> Confirm & Submit Reservation Request
                    </button>
                </form>
            </div>
        </div>

        <!-- Right: Room Summary & Live Cost Calculation -->
        <div class="col-lg-4">
            <div class="card border-0 rounded-4 shadow-sm sticky-top" style="top: 90px;">
                <div class="card-header bg-navy text-white p-3 rounded-top-4" style="background-color: var(--navy-blue);">
                    <h6 class="mb-0 fw-bold"><i class="fa-solid fa-hotel me-2"></i> Reservation Summary</h6>
                </div>
                <div class="card-body p-4">
                    <!-- Room Info -->
                    <div class="d-flex gap-3 mb-3 pb-3 border-bottom">
                        <img src="<?php echo url($room['room_image']); ?>" alt="Room" class="rounded-3 shadow-sm" style="width: 80px; height: 65px; object-fit: cover;">
                        <div>
                            <h6 class="fw-bold text-navy mb-0"><?php echo htmlspecialchars($room['room_name']); ?></h6>
                            <span class="badge bg-light text-navy border small mt-1"><?php echo htmlspecialchars($room['room_type']); ?></span>
                        </div>
                    </div>

                    <!-- Live Calculation Breakdown -->
                    <div class="d-flex justify-content-between mb-2 small text-muted">
                        <span>Price per Night:</span>
                        <span class="fw-bold text-navy"><?php echo format_currency($room['price_per_night']); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 small text-muted">
                        <span>Calculated Duration:</span>
                        <span><strong id="display_nights" class="text-navy"><?php echo $initial_nights; ?></strong> Night(s)</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 small text-muted">
                        <span>Taxes & Service Fee:</span>
                        <span class="text-success fw-bold">Included</span>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between align-items-baseline mb-4">
                        <span class="fw-bold text-navy">Estimated Total:</span>
                        <span id="display_total" class="fw-bold text-ocean fs-4"><?php echo format_currency($initial_total); ?></span>
                    </div>

                    <div class="p-3 bg-light rounded-3 small text-muted">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="fa-solid fa-clock text-warning"></i>
                            <strong>Status will be Pending</strong>
                        </div>
                        <p class="mb-0" style="font-size: 0.82rem;">
                            Villa administration will review your dates and confirm room availability. You can cancel pending bookings anytime from your account.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Booking JavaScript for dynamic recalculation -->
<script src="<?php echo url('assets/js/booking.js'); ?>"></script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
