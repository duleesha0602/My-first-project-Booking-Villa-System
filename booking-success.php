<?php
/**
 * ====================================================================
 *      Booking Confirmation / Success Page
 * Coastal Villa & Room Booking Management System
 * 
 * ====================================================================
 */

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
require_guest_login();

$booking_id = (int)($_GET['id'] ?? 0);
$user_id = $_SESSION['user_id'];

// Retrieve saved booking details for the logged-in guest (BR09)
$stmt = $pdo->prepare("
    SELECT b.*, r.room_name, r.room_type, r.room_image, u.full_name, u.email, u.contact_number
    FROM bookings b
    JOIN rooms r ON b.room_id = r.id
    JOIN users u ON b.user_id = u.id
    WHERE b.id = ? AND b.user_id = ?
    LIMIT 1
");
$stmt->execute([$booking_id, $user_id]);
$booking = $stmt->fetch();

if (!$booking) {
    echo "<div class='container my-5'><div class='alert alert-warning'>Booking details not found. <a href='" . url('guest/dashboard.php') . "'>Return to Dashboard</a></div></div>";
    require_once __DIR__ . '/../includes/footer.php';
    exit();
}
?>

<div class="container py-5">
    <div class="card border-0 rounded-4 shadow-sm mx-auto" style="max-width: 720px; overflow: hidden;">
        <!-- Success Banner -->
        <div class="p-4 text-center text-white" style="background: linear-gradient(135deg, var(--navy-blue), var(--ocean-blue));">
            <div class="mb-2">
                <i class="fa-solid fa-circle-check fa-3x text-info"></i>
            </div>
            <h2 class="fw-bold mb-1">Reservation Request Received!</h2>
            <p class="text-light-50 mb-0 small">Thank you, <?php echo htmlspecialchars($booking['full_name']); ?>. Your booking request has been successfully recorded.</p>
        </div>

        <div class="card-body p-4 p-md-5">
            <!-- Reference Tag Box -->
            <div class="p-3 bg-light rounded-3 text-center mb-4 border">
                <span class="text-muted small text-uppercase">Your Booking Reference Number</span>
                <div class="fs-3 fw-bold text-navy"><?php echo htmlspecialchars($booking['booking_reference']); ?></div>
                <div class="mt-1">
                    Status: <?php echo get_status_badge($booking['booking_status']); ?>
                </div>
            </div>

            <!-- Booking Summary Details -->
            <div class="row g-3 mb-4">
                <div class="col-sm-6">
                    <span class="text-muted small">Selected Room</span>
                    <h6 class="fw-bold text-navy mb-0"><?php echo htmlspecialchars($booking['room_name']); ?></h6>
                    <small class="text-muted"><?php echo htmlspecialchars($booking['room_type']); ?></small>
                </div>
                <div class="col-sm-6">
                    <span class="text-muted small">Guests</span>
                    <h6 class="fw-bold text-navy mb-0"><?php echo $booking['guests']; ?> Guest(s)</h6>
                </div>
                <div class="col-sm-6">
                    <span class="text-muted small">Check-In Date</span>
                    <h6 class="fw-bold text-navy mb-0"><?php echo format_date($booking['check_in']); ?> (from 2:00 PM)</h6>
                </div>
                <div class="col-sm-6">
                    <span class="text-muted small">Check-Out Date</span>
                    <h6 class="fw-bold text-navy mb-0"><?php echo format_date($booking['check_out']); ?> (until 11:00 AM)</h6>
                </div>
                <div class="col-sm-6">
                    <span class="text-muted small">Duration</span>
                    <h6 class="fw-bold text-navy mb-0"><?php echo $booking['total_nights']; ?> Night(s)</h6>
                </div>
                <div class="col-sm-6">
                    <span class="text-muted small">Total Estimated Cost</span>
                    <h5 class="fw-bold text-ocean mb-0"><?php echo format_currency($booking['total_amount']); ?></h5>
                </div>
            </div>

            <?php if (!empty($booking['special_requests'])): ?>
                <div class="p-3 bg-light rounded-3 mb-4 small">
                    <strong>Special Requests:</strong><br>
                    <span class="text-muted"><?php echo nl2br(htmlspecialchars($booking['special_requests'])); ?></span>
                </div>
            <?php endif; ?>

            <!-- Informational Note -->
            <div class="alert alert-info border-0 rounded-3 small mb-4">
                <i class="fa-solid fa-circle-info me-1"></i>
                <strong>What happens next?</strong> The villa administrator will review room availability and confirm your booking. You can check the current status anytime under <strong>My Bookings</strong>.
            </div>

            <!-- Navigation Buttons -->
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="<?php echo url('guest/my-bookings.php'); ?>" class="btn btn-ocean">
                    <i class="fa-solid fa-calendar-check me-1"></i> Go to My Bookings
                </a>
                <a href="<?php echo url('guest/dashboard.php'); ?>" class="btn btn-outline-secondary">
                    <i class="fa-solid fa-house me-1"></i> Guest Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
