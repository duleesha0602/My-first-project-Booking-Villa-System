<?php
/**
 * ====================================================================
 *       My Bookings
 * Coastal Villa & Room Booking Management System
 * 
 * ====================================================================
 */

require_once __DIR__ . '/../includes/header.php';
require_guest_login();

$user_id = $_SESSION['user_id'];
$status_filter = sanitize_input($_GET['status'] ?? '');
$search_query = sanitize_input($_GET['search'] ?? '');

// Build query for logged-in guest bookings (BR09)
$sql = "
    SELECT b.*, r.room_name, r.room_type, r.room_image 
    FROM bookings b
    JOIN rooms r ON b.room_id = r.id
    WHERE b.user_id = :user_id
";
$params = [':user_id' => $user_id];

if (!empty($status_filter)) {
    $sql .= " AND b.booking_status = :status";
    $params[':status'] = $status_filter;
}

if (!empty($search_query)) {
    $sql .= " AND (b.booking_reference LIKE :search OR r.room_name LIKE :search)";
    $params[':search'] = "%{$search_query}%";
}

$sql .= " ORDER BY b.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$bookings = $stmt->fetchAll();
?>

<div class="dashboard-wrapper">
    <!-- Guest Sidebar -->
    <?php require_once __DIR__ . '/../includes/guest-sidebar.php'; ?>

    <div class="dashboard-main">
        <!-- Topbar -->
        <div class="dashboard-topbar">
            <div class="dashboard-title">
                <h2><i class="fa-solid fa-calendar-check me-2 text-primary"></i> My Bookings</h2>
                <small class="text-muted">Review, track, and manage your villa reservations</small>
            </div>
            <div>
                <a href="<?php echo url('guest/availability.php'); ?>" class="btn btn-ocean btn-sm">
                    <i class="fa-solid fa-plus me-1"></i> New Booking
                </a>
            </div>
        </div>

        <div class="dashboard-content">
            <!-- Flash Alert -->
            <?php display_flash(); ?>

            <!-- Filter & Search Toolbar -->
            <div class="card border-0 rounded-4 shadow-sm mb-4">
                <div class="card-body p-3">
                    <form action="<?php echo url('guest/my-bookings.php'); ?>" method="GET" class="row g-2 align-items-center">
                        <!-- Status Filter Buttons -->
                        <div class="col-lg-6 col-md-12">
                            <div class="btn-group w-100" role="group">
                                <a href="<?php echo url('guest/my-bookings.php'); ?>" class="btn btn-sm <?php echo empty($status_filter) ? 'btn-ocean' : 'btn-outline-ocean'; ?>">
                                    All Bookings
                                </a>
                                <a href="<?php echo url('guest/my-bookings.php?status=Pending'); ?>" class="btn btn-sm <?php echo ($status_filter === 'Pending') ? 'btn-ocean' : 'btn-outline-ocean'; ?>">
                                    Pending
                                </a>
                                <a href="<?php echo url('guest/my-bookings.php?status=Confirmed'); ?>" class="btn btn-sm <?php echo ($status_filter === 'Confirmed') ? 'btn-ocean' : 'btn-outline-ocean'; ?>">
                                    Confirmed
                                </a>
                                <a href="<?php echo url('guest/my-bookings.php?status=Cancelled'); ?>" class="btn btn-sm <?php echo ($status_filter === 'Cancelled') ? 'btn-ocean' : 'btn-outline-ocean'; ?>">
                                    Cancelled
                                </a>
                            </div>
                        </div>

                        <!-- Search Box -->
                        <div class="col-lg-6 col-md-12">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by Reference ID (e.g. CV-2026-1001)..." value="<?php echo htmlspecialchars($search_query); ?>">
                                <?php if (!empty($status_filter)): ?>
                                    <input type="hidden" name="status" value="<?php echo htmlspecialchars($status_filter); ?>">
                                <?php endif; ?>
                                <button type="submit" class="btn btn-ocean btn-sm">
                                    <i class="fa-solid fa-magnifying-glass"></i> Search
                                </button>
                                <?php if (!empty($search_query) || !empty($status_filter)): ?>
                                    <a href="<?php echo url('guest/my-bookings.php'); ?>" class="btn btn-outline-secondary btn-sm">
                                        <i class="fa-solid fa-rotate-left"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Bookings List Table -->
            <div class="card border-0 rounded-4 shadow-sm overflow-hidden">
                <div class="card-body p-0">
                    <?php if (empty($bookings)): ?>
                        <div class="p-5 text-center text-muted">
                            <i class="fa-solid fa-calendar-xmark fa-3x mb-3 text-muted"></i>
                            <h5>No Reservations Found</h5>
                            <p class="small mb-3">No bookings matched your filter or search criteria.</p>
                            <a href="<?php echo url('guest/availability.php'); ?>" class="btn btn-ocean btn-sm">Make a Booking</a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Ref ID</th>
                                        <th>Room</th>
                                        <th>Check-In</th>
                                        <th>Check-Out</th>
                                        <th>Duration</th>
                                        <th>Total Cost</th>
                                        <th>Status</th>
                                        <th>Booking Date</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($bookings as $b): ?>
                                        <tr>
                                            <td>
                                                <strong class="text-navy"><?php echo htmlspecialchars($b['booking_reference']); ?></strong>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <img src="<?php echo url($b['room_image']); ?>" alt="Room" class="rounded" style="width: 44px; height: 35px; object-fit: cover;">
                                                    <div>
                                                        <div class="fw-semibold small text-navy"><?php echo htmlspecialchars($b['room_name']); ?></div>
                                                        <small class="text-muted"><?php echo $b['guests']; ?> Guest(s)</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="small"><?php echo format_date($b['check_in']); ?></td>
                                            <td class="small"><?php echo format_date($b['check_out']); ?></td>
                                            <td class="small"><?php echo $b['total_nights']; ?> night(s)</td>
                                            <td class="small fw-bold text-ocean"><?php echo format_currency($b['total_amount']); ?></td>
                                            <td><?php echo get_status_badge($b['booking_status']); ?></td>
                                            <td class="small text-muted"><?php echo format_date($b['booking_date']); ?></td>
                                            <td class="text-end">
                                                <div class="d-inline-flex gap-1">
                                                    <a href="<?php echo url('guest/booking-details.php?id=' . $b['id']); ?>" class="btn btn-light btn-sm text-primary" title="View Details">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </a>

                                                    <!-- Only allow cancelling PENDING bookings (BR10) -->
                                                    <?php if ($b['booking_status'] === 'Pending'): ?>
                                                        <form action="<?php echo url('actions/cancel-booking.php'); ?>" method="POST" onsubmit="return confirm('Are you sure you want to cancel this pending booking?');">
                                                            <input type="hidden" name="booking_id" value="<?php echo $b['id']; ?>">
                                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Cancel Booking">
                                                                <i class="fa-solid fa-ban"></i>
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
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
