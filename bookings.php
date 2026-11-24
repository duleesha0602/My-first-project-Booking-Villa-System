<?php
/**
 * ====================================================================
 *  Booking Management
 * Coastal Villa & Room Booking Management System
 * 
 * ====================================================================
 */

require_once __DIR__ . '/../includes/header.php';
require_admin_login();

$status_filter = sanitize_input($_GET['status'] ?? '');
$search_query  = sanitize_input($_GET['search'] ?? '');
$date_filter   = sanitize_input($_GET['date'] ?? '');

$sql = "
    SELECT b.*, r.room_name, r.room_image, u.full_name, u.email, u.contact_number
    FROM bookings b
    JOIN rooms r ON b.room_id = r.id
    JOIN users u ON b.user_id = u.id
    WHERE 1=1
";
$params = [];

if (!empty($status_filter)) {
    $sql .= " AND b.booking_status = :status";
    $params[':status'] = $status_filter;
}

if (!empty($search_query)) {
    $sql .= " AND (b.booking_reference LIKE :search OR u.full_name LIKE :search OR r.room_name LIKE :search OR u.email LIKE :search)";
    $params[':search'] = "%{$search_query}%";
}

if (!empty($date_filter)) {
    $sql .= " AND :date BETWEEN b.check_in AND b.check_out";
    $params[':date'] = $date_filter;
}

$sql .= " ORDER BY b.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$bookings = $stmt->fetchAll();
?>

<div class="dashboard-wrapper">
    <!-- Admin Sidebar -->
    <?php require_once __DIR__ . '/../includes/admin-sidebar.php'; ?>

    <div class="dashboard-main">
        <!-- Topbar -->
        <div class="dashboard-topbar">
            <div class="dashboard-title">
                <h2><i class="fa-solid fa-calendar-check me-2 text-primary"></i> Booking Management</h2>
                <small class="text-muted">Review, approve, and track guest accommodation bookings</small>
            </div>
            <div>
                <a href="<?php echo url('admin/reports.php'); ?>" class="btn btn-outline-ocean btn-sm">
                    <i class="fa-solid fa-chart-pie me-1"></i> Booking Reports
                </a>
            </div>
        </div>

        <div class="dashboard-content">
            <!-- Flash Alert -->
            <?php display_flash(); ?>

            <!-- Status Tabs & Filter Bar -->
            <div class="card border-0 rounded-4 shadow-sm mb-4">
                <div class="card-body p-3">
                    <div class="row g-3 align-items-center">
                        <!-- Status Tabs -->
                        <div class="col-lg-6">
                            <div class="btn-group w-100" role="group">
                                <a href="<?php echo url('admin/bookings.php'); ?>" class="btn btn-sm <?php echo empty($status_filter) ? 'btn-ocean' : 'btn-outline-ocean'; ?>">
                                    All Bookings
                                </a>
                                <a href="<?php echo url('admin/bookings.php?status=Pending'); ?>" class="btn btn-sm <?php echo ($status_filter === 'Pending') ? 'btn-ocean' : 'btn-outline-ocean'; ?>">
                                    Pending Requests
                                </a>
                                <a href="<?php echo url('admin/bookings.php?status=Confirmed'); ?>" class="btn btn-sm <?php echo ($status_filter === 'Confirmed') ? 'btn-ocean' : 'btn-outline-ocean'; ?>">
                                    Confirmed
                                </a>
                                <a href="<?php echo url('admin/bookings.php?status=Cancelled'); ?>" class="btn btn-sm <?php echo ($status_filter === 'Cancelled') ? 'btn-ocean' : 'btn-outline-ocean'; ?>">
                                    Cancelled
                                </a>
                            </div>
                        </div>

                        <!-- Search and Date Filter -->
                        <div class="col-lg-6">
                            <form action="<?php echo url('admin/bookings.php'); ?>" method="GET" class="d-flex gap-2">
                                <?php if (!empty($status_filter)): ?>
                                    <input type="hidden" name="status" value="<?php echo htmlspecialchars($status_filter); ?>">
                                <?php endif; ?>
                                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search ref, guest, room..." value="<?php echo htmlspecialchars($search_query); ?>">
                                <input type="date" name="date" class="form-control form-control-sm" title="Active on date" value="<?php echo htmlspecialchars($date_filter); ?>">
                                <button type="submit" class="btn btn-ocean btn-sm">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </button>
                                <?php if (!empty($search_query) || !empty($date_filter) || !empty($status_filter)): ?>
                                    <a href="<?php echo url('admin/bookings.php'); ?>" class="btn btn-outline-secondary btn-sm" title="Reset">
                                        <i class="fa-solid fa-rotate-left"></i>
                                    </a>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bookings Table -->
            <div class="card border-0 rounded-4 shadow-sm overflow-hidden">
                <div class="card-body p-0">
                    <?php if (empty($bookings)): ?>
                        <div class="p-5 text-center text-muted">
                            <i class="fa-solid fa-calendar-xmark fa-3x mb-3 text-muted"></i>
                            <h5>No Reservations Found</h5>
                            <p class="small">No booking records match the selected status or search filter.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Ref ID</th>
                                        <th>Guest Name</th>
                                        <th>Room</th>
                                        <th>Dates</th>
                                        <th>Guests</th>
                                        <th>Total</th>
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
                                                <div class="fw-semibold text-navy small"><?php echo htmlspecialchars($b['full_name']); ?></div>
                                                <small class="text-muted"><?php echo htmlspecialchars($b['contact_number']); ?></small>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <img src="<?php echo url($b['room_image']); ?>" alt="Room" class="rounded" style="width: 38px; height: 30px; object-fit: cover;">
                                                    <span class="small"><?php echo htmlspecialchars($b['room_name']); ?></span>
                                                </div>
                                            </td>
                                            <td class="small">
                                                <div><?php echo format_date($b['check_in']); ?> &rarr; <?php echo format_date($b['check_out']); ?></div>
                                                <span class="text-muted"><?php echo $b['total_nights']; ?> night(s)</span>
                                            </td>
                                            <td class="small text-center"><?php echo $b['guests']; ?></td>
                                            <td class="small fw-bold text-ocean"><?php echo format_currency($b['total_amount']); ?></td>
                                            <td><?php echo get_status_badge($b['booking_status']); ?></td>
                                            <td class="small text-muted"><?php echo format_date($b['booking_date']); ?></td>
                                            <td class="text-end">
                                                <div class="d-inline-flex gap-1">
                                                    <!-- View Details -->
                                                    <a href="<?php echo url('admin/booking-details.php?id=' . $b['id']); ?>" class="btn btn-light btn-sm text-primary" title="View Details">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </a>

                                                    <!-- Confirm (if pending) -->
                                                    <?php if ($b['booking_status'] === 'Pending'): ?>
                                                        <a href="<?php echo url('actions/booking-status.php?action=confirm&id=' . $b['id']); ?>" class="btn btn-light btn-sm text-success" title="Confirm Booking" onclick="return confirm('Confirm this reservation? The system will verify there are no overlapping confirmed bookings.');">
                                                            <i class="fa-solid fa-check"></i>
                                                        </a>
                                                    <?php endif; ?>

                                                    <!-- Cancel (if not already cancelled) -->
                                                    <?php if ($b['booking_status'] !== 'Cancelled'): ?>
                                                        <a href="<?php echo url('actions/booking-status.php?action=cancel&id=' . $b['id']); ?>" class="btn btn-light btn-sm text-danger" title="Cancel Booking" onclick="return confirm('Cancel this reservation?');">
                                                            <i class="fa-solid fa-xmark"></i>
                                                        </a>
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
