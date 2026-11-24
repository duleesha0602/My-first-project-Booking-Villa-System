<?php
/**
 * ====================================================================
 *       Booking Details 
 * Coastal Villa & Room Booking Management System
 * 
 * ====================================================================
 */

require_once __DIR__ . '/../includes/header.php';
require_guest_login();

$booking_id = (int)($_GET['id'] ?? 0);
$user_id = $_SESSION['user_id'];

// Retrieve booking and ensure ownership (BR09)
$stmt = $pdo->prepare("
    SELECT b.*, r.room_name, r.room_type, r.room_image, r.facilities, u.full_name, u.email, u.contact_number
    FROM bookings b
    JOIN rooms r ON b.room_id = r.id
    JOIN users u ON b.user_id = u.id
    WHERE b.id = ? AND b.user_id = ?
    LIMIT 1
");
$stmt->execute([$booking_id, $user_id]);
$booking = $stmt->fetch();

if (!$booking) {
    set_flash('danger', 'Booking details not found or access denied.');
    header('Location: ' . url('guest/my-bookings.php'));
    exit();
}
?>

<div class="dashboard-wrapper">
    <!-- Guest Sidebar -->
    <?php require_once __DIR__ . '/../includes/guest-sidebar.php'; ?>

    <div class="dashboard-main">
        <!-- Topbar -->
        <div class="dashboard-topbar">
            <div class="dashboard-title">
                <h2>Reservation Details</h2>
                <small class="text-muted">Reference: <?php echo htmlspecialchars($booking['booking_reference']); ?></small>
            </div>
            <div>
                <a href="<?php echo url('guest/my-bookings.php'); ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to My Bookings
                </a>
            </div>
        </div>

        <div class="dashboard-content">
            <!-- Flash Alert -->
            <?php display_flash(); ?>

            <div class="row g-4">
                <!-- Left: Booking & Room Details -->
                <div class="col-lg-8">
                    <div class="card border-0 rounded-4 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <!-- Status Header -->
                            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom flex-wrap gap-2">
                                <div>
                                    <span class="text-muted small">Status</span>
                                    <div class="mt-1"><?php echo get_status_badge($booking['booking_status']); ?></div>
                                </div>
                                <div class="text-sm-end">
                                    <span class="text-muted small">Submitted On</span>
                                    <div class="fw-semibold text-navy"><?php echo date('M d, Y h:i A', strtotime($booking['booking_date'])); ?></div>
                                </div>
                            </div>

                            <!-- Room Details -->
                            <h5 class="fw-bold text-navy mb-3">Room Information</h5>
                            <div class="d-flex gap-3 mb-4">
                                <img src="<?php echo url($booking['room_image']); ?>" alt="Room" class="rounded-3 shadow-sm" style="width: 120px; height: 90px; object-fit: cover;">
                                <div>
                                    <h5 class="fw-bold text-navy mb-1"><?php echo htmlspecialchars($booking['room_name']); ?></h5>
                                    <span class="badge bg-light text-navy border mb-2"><?php echo htmlspecialchars($booking['room_type']); ?></span>
                                    <div class="small text-muted"><i class="fa-solid fa-sparkles text-primary me-1"></i> <?php echo htmlspecialchars($booking['facilities']); ?></div>
                                </div>
                            </div>

                            <!-- Reservation Schedule -->
                            <h5 class="fw-bold text-navy mb-3">Stay Schedule</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-sm-6">
                                    <div class="p-3 bg-light rounded-3">
                                        <span class="text-muted small">Check-In Date</span>
                                        <h6 class="fw-bold text-navy mb-0 mt-1"><?php echo format_date($booking['check_in']); ?></h6>
                                        <small class="text-muted">Standard Check-In from 2:00 PM</small>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="p-3 bg-light rounded-3">
                                        <span class="text-muted small">Check-Out Date</span>
                                        <h6 class="fw-bold text-navy mb-0 mt-1"><?php echo format_date($booking['check_out']); ?></h6>
                                        <small class="text-muted">Standard Check-Out by 11:00 AM</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Special Requests -->
                            <?php if (!empty($booking['special_requests'])): ?>
                                <h5 class="fw-bold text-navy mb-2">Special Requests</h5>
                                <div class="p-3 bg-light rounded-3 mb-4 text-muted small">
                                    <?php echo nl2br(htmlspecialchars($booking['special_requests'])); ?>
                                </div>
                            <?php endif; ?>

                            <!-- Cancellation Notice / Action -->
                            <div class="pt-3 border-top">
                                <?php if ($booking['booking_status'] === 'Pending'): ?>
                                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                        <span class="text-muted small">Need to change your plans? You can cancel this pending request.</span>
                                        <form action="<?php echo url('actions/cancel-booking.php'); ?>" method="POST" onsubmit="return confirm('Are you sure you want to cancel this pending booking? This cannot be undone.');">
                                            <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                                <i class="fa-solid fa-ban me-1"></i> Cancel Reservation
                                            </button>
                                        </form>
                                    </div>
                                <?php elseif ($booking['booking_status'] === 'Confirmed'): ?>
                                    <div class="alert alert-success mb-0 small">
                                        <i class="fa-solid fa-circle-check me-1"></i>
                                        Your booking is confirmed! If you need to modify dates or have special requests, please call our front desk directly at <strong>+94 41 223 8899</strong>.
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-secondary mb-0 small">
                                        <i class="fa-solid fa-circle-xmark me-1"></i>
                                        This reservation has been cancelled.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Price Breakdown & Guest Info -->
                <div class="col-lg-4">
                    <!-- Price Breakdown Card -->
                    <div class="card border-0 rounded-4 shadow-sm mb-4">
                        <div class="card-header bg-navy text-white p-3 rounded-top-4" style="background-color: var(--navy-blue);">
                            <h6 class="mb-0 fw-bold">Payment Summary</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between mb-2 small text-muted">
                                <span>Nightly Rate:</span>
                                <span class="fw-semibold text-navy"><?php echo format_currency($booking['price_per_night']); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 small text-muted">
                                <span>Duration:</span>
                                <span class="fw-semibold text-navy"><?php echo $booking['total_nights']; ?> Night(s)</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 small text-muted">
                                <span>Guests:</span>
                                <span class="fw-semibold text-navy"><?php echo $booking['guests']; ?> Guest(s)</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between align-items-baseline mb-0">
                                <span class="fw-bold text-navy">Total Cost:</span>
                                <span class="fw-bold text-ocean fs-4"><?php echo format_currency($booking['total_amount']); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Guest Contact Details Card -->
                    <div class="card border-0 rounded-4 shadow-sm">
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-navy mb-3">Primary Guest Details</h6>
                            <p class="mb-1 text-navy fw-semibold"><?php echo htmlspecialchars($booking['full_name']); ?></p>
                            <p class="small text-muted mb-1"><i class="fa-solid fa-envelope me-1"></i> <?php echo htmlspecialchars($booking['email']); ?></p>
                            <p class="small text-muted mb-0"><i class="fa-solid fa-phone me-1"></i> <?php echo htmlspecialchars($booking['contact_number']); ?></p>
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
