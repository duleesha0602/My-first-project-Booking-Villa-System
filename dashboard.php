<?php
/**
 * ====================================================================
 *      Guest Dashboard
 * Coastal Villa & Room Booking Management System
 * 
 * ====================================================================
 */

require_once __DIR__ . '/../includes/header.php';
require_guest_login(); // Ensure user is logged in as guest

$user_id = $_SESSION['user_id'];
$guest_name = $_SESSION['user_name'];

// 1. Fetch booking statistics for the logged-in guest
$stats_stmt = $pdo->prepare("
    SELECT 
        COUNT(*) AS total_bookings,
        SUM(CASE WHEN booking_status = 'Pending' THEN 1 ELSE 0 END) AS pending_count,
        SUM(CASE WHEN booking_status = 'Confirmed' THEN 1 ELSE 0 END) AS confirmed_count,
        SUM(CASE WHEN booking_status = 'Cancelled' THEN 1 ELSE 0 END) AS cancelled_count
    FROM bookings
    WHERE user_id = ?
");
$stats_stmt->execute([$user_id]);
$stats = $stats_stmt->fetch();

// 2. Fetch upcoming reservation (next confirmed reservation)
$upcoming_stmt = $pdo->prepare("
    SELECT b.*, r.room_name, r.room_type, r.room_image 
    FROM bookings b
    JOIN rooms r ON b.room_id = r.id
    WHERE b.user_id = ? AND b.booking_status = 'Confirmed' AND b.check_in >= CURDATE()
    ORDER BY b.check_in ASC
    LIMIT 1
");
$upcoming_stmt->execute([$user_id]);
$upcoming_booking = $upcoming_stmt->fetch();

// 3. Fetch 5 most recent bookings
$recent_stmt = $pdo->prepare("
    SELECT b.*, r.room_name, r.room_image 
    FROM bookings b
    JOIN rooms r ON b.room_id = r.id
    WHERE b.user_id = ?
    ORDER BY b.id DESC
    LIMIT 5
");
$recent_stmt->execute([$user_id]);
$recent_bookings = $recent_stmt->fetchAll();
?>

<div class="dashboard-wrapper">
    <!-- Guest Sidebar -->
    <?php require_once __DIR__ . '/../includes/guest-sidebar.php'; ?>

    <!-- Main Content -->
    <div class="dashboard-main">
        <!-- Topbar -->
        <div class="dashboard-topbar">
            <div class="dashboard-title">
                <h2>Welcome, <?php echo htmlspecialchars($guest_name); ?>!</h2>
                <small class="text-muted">Manage your villa stays and reservation requests</small>
            </div>
            <div>
                <a href="<?php echo url('guest/availability.php'); ?>" class="btn btn-ocean btn-sm">
                    <i class="fa-solid fa-calendar-plus me-1"></i> New Reservation
                </a>
            </div>
        </div>

        <!-- Dashboard Content -->
        <div class="dashboard-content">
            <!-- Flash Alert -->
            <?php display_flash(); ?>

            <!-- Booking Statistics Cards -->
            <div class="row g-3 mb-4">
                <div class="col-xl-3 col-sm-6">
                    <div class="stat-card stat-blue">
                        <div>
                            <div class="stat-label">Total Bookings</div>
                            <div class="stat-number"><?php echo (int)($stats['total_bookings'] ?? 0); ?></div>
                        </div>
                        <div class="stat-icon">
                            <i class="fa-solid fa-receipt"></i>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6">
                    <div class="stat-card stat-orange">
                        <div>
                            <div class="stat-label">Pending Requests</div>
                            <div class="stat-number"><?php echo (int)($stats['pending_count'] ?? 0); ?></div>
                        </div>
                        <div class="stat-icon">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6">
                    <div class="stat-card stat-green">
                        <div>
                            <div class="stat-label">Confirmed Stays</div>
                            <div class="stat-number"><?php echo (int)($stats['confirmed_count'] ?? 0); ?></div>
                        </div>
                        <div class="stat-icon">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6">
                    <div class="stat-card stat-red">
                        <div>
                            <div class="stat-label">Cancelled Bookings</div>
                            <div class="stat-number"><?php echo (int)($stats['cancelled_count'] ?? 0); ?></div>
                        </div>
                        <div class="stat-icon">
                            <i class="fa-solid fa-ban"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Reservation Banner -->
            <?php if ($upcoming_booking): ?>
                <div class="card border-0 rounded-4 shadow-sm mb-4 overflow-hidden" style="border-left: 5px solid var(--ocean-blue) !important;">
                    <div class="card-body p-4">
                        <div class="row align-items-center g-3">
                            <div class="col-md-2 col-sm-3">
                                <img src="<?php echo url($upcoming_booking['room_image']); ?>" alt="Room" class="img-fluid rounded-3 shadow-sm w-100" style="height: 85px; object-fit: cover;">
                            </div>
                            <div class="col-md-7 col-sm-9">
                                <span class="badge bg-success mb-1"><i class="fa-solid fa-circle-check me-1"></i> Upcoming Confirmed Stay</span>
                                <h5 class="fw-bold text-navy mb-1"><?php echo htmlspecialchars($upcoming_booking['room_name']); ?></h5>
                                <div class="text-muted small">
                                    <span class="me-3"><i class="fa-solid fa-calendar me-1 text-primary"></i> <?php echo format_date($upcoming_booking['check_in']); ?> &rarr; <?php echo format_date($upcoming_booking['check_out']); ?></span>
                                    <span><i class="fa-solid fa-hashtag me-1 text-primary"></i> Ref: <?php echo htmlspecialchars($upcoming_booking['booking_reference']); ?></span>
                                </div>
                            </div>
                            <div class="col-md-3 text-md-end">
                                <a href="<?php echo url('guest/booking-details.php?id=' . $upcoming_booking['id']); ?>" class="btn btn-outline-ocean btn-sm">
                                    View Full Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Recent Bookings Table -->
            <div class="card border-0 rounded-4 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-navy mb-0"><i class="fa-solid fa-list-check me-2 text-primary"></i> Recent Reservations</h5>
                    <a href="<?php echo url('guest/my-bookings.php'); ?>" class="btn btn-outline-ocean btn-sm">View All</a>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($recent_bookings)): ?>
                        <div class="p-5 text-center text-muted">
                            <i class="fa-solid fa-calendar-xmark fa-3x mb-3 text-muted"></i>
                            <h6>No reservations recorded yet</h6>
                            <p class="small mb-3">You haven't made any villa reservation requests yet.</p>
                            <a href="<?php echo url('guest/availability.php'); ?>" class="btn btn-ocean btn-sm">Find Available Rooms</a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Ref #</th>
                                        <th>Room</th>
                                        <th>Check-In</th>
                                        <th>Check-Out</th>
                                        <th>Duration</th>
                                        <th>Total Amount</th>
                                        <th>Status</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_bookings as $b): ?>
                                        <tr>
                                            <td><strong class="text-navy"><?php echo htmlspecialchars($b['booking_reference']); ?></strong></td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <img src="<?php echo url($b['room_image']); ?>" alt="Room" class="rounded" style="width: 40px; height: 32px; object-fit: cover;">
                                                    <span class="small fw-semibold"><?php echo htmlspecialchars($b['room_name']); ?></span>
                                                </div>
                                            </td>
                                            <td class="small"><?php echo format_date($b['check_in']); ?></td>
                                            <td class="small"><?php echo format_date($b['check_out']); ?></td>
                                            <td class="small"><?php echo $b['total_nights']; ?> night(s)</td>
                                            <td class="small fw-bold text-ocean"><?php echo format_currency($b['total_amount']); ?></td>
                                            <td><?php echo get_status_badge($b['booking_status']); ?></td>
                                            <td class="text-end">
                                                <a href="<?php echo url('guest/booking-details.php?id=' . $b['id']); ?>" class="btn btn-light btn-sm text-primary">
                                                    <i class="fa-solid fa-eye me-1"></i> Details
                                                </a>
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
