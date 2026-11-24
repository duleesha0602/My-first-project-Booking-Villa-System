<?php
/**
 * ====================================================================
 *       Reports & Analytics
 * Coastal Villa & Room Booking Management System
 * 
 * ====================================================================
 */

require_once __DIR__ . '/../includes/header.php';
require_admin_login();

$start_date = sanitize_input($_GET['start_date'] ?? date('Y-m-01')); // start of current month
$end_date   = sanitize_input($_GET['end_date'] ?? date('Y-m-t'));   // end of current month
$status     = sanitize_input($_GET['status'] ?? '');

// 1. Overall Filtered Summary Statistics
$stats_sql = "
    SELECT 
        COUNT(*) AS total_bookings,
        SUM(CASE WHEN booking_status = 'Pending' THEN 1 ELSE 0 END) AS pending_count,
        SUM(CASE WHEN booking_status = 'Confirmed' THEN 1 ELSE 0 END) AS confirmed_count,
        SUM(CASE WHEN booking_status = 'Cancelled' THEN 1 ELSE 0 END) AS cancelled_count,
        SUM(CASE WHEN booking_status = 'Confirmed' THEN total_amount ELSE 0 END) AS total_revenue
    FROM bookings
    WHERE DATE(booking_date) BETWEEN :start_date AND :end_date
";
$params = [':start_date' => $start_date, ':end_date' => $end_date];

if (!empty($status)) {
    $stats_sql .= " AND booking_status = :status";
    $params[':status'] = $status;
}

$stmt = $pdo->prepare($stats_sql);
$stmt->execute($params);
$report_stats = $stmt->fetch();

// 2. Room Booking Performance (Which rooms generated the most bookings and revenue)
$room_perf_stmt = $pdo->prepare("
    SELECT r.room_name, r.room_type,
           COUNT(b.id) AS booking_count,
           SUM(b.total_nights) AS total_room_nights,
           SUM(CASE WHEN b.booking_status = 'Confirmed' THEN b.total_amount ELSE 0 END) AS room_revenue
    FROM rooms r
    LEFT JOIN bookings b ON r.id = b.room_id AND DATE(b.booking_date) BETWEEN :start_date AND :end_date
    GROUP BY r.id
    ORDER BY room_revenue DESC
");
$room_perf_stmt->execute([':start_date' => $start_date, ':end_date' => $end_date]);
$room_performance = $room_perf_stmt->fetchAll();

// 3. Filtered Booking List
$list_sql = "
    SELECT b.*, r.room_name, u.full_name 
    FROM bookings b
    JOIN rooms r ON b.room_id = r.id
    JOIN users u ON b.user_id = u.id
    WHERE DATE(b.booking_date) BETWEEN :start_date AND :end_date
";
if (!empty($status)) {
    $list_sql .= " AND b.booking_status = :status";
}
$list_sql .= " ORDER BY b.id DESC";

$list_stmt = $pdo->prepare($list_sql);
$list_stmt->execute($params);
$report_bookings = $list_stmt->fetchAll();
?>

<div class="dashboard-wrapper">
    <!-- Admin Sidebar -->
    <?php require_once __DIR__ . '/../includes/admin-sidebar.php'; ?>

    <div class="dashboard-main">
        <!-- Topbar -->
        <div class="dashboard-topbar">
            <div class="dashboard-title">
                <h2><i class="fa-solid fa-file-invoice-dollar me-2 text-primary"></i> Financial & Booking Reports</h2>
                <small class="text-muted">Summary statistics, room performance, and revenue reports</small>
            </div>
            <div class="no-print">
                <button onclick="window.print()" class="btn btn-navy btn-sm">
                    <i class="fa-solid fa-print me-1"></i> Print / Save PDF
                </button>
            </div>
        </div>

        <div class="dashboard-content">
            <!-- Filter Bar (Hidden when printing) -->
            <div class="card border-0 rounded-4 shadow-sm mb-4 no-print">
                <div class="card-body p-3">
                    <form action="<?php echo url('admin/reports.php'); ?>" method="GET" class="row g-2 align-items-end">
                        <div class="col-md-3 col-sm-6">
                            <label for="start_date" class="form-label small text-navy fw-bold mb-1">Start Date</label>
                            <input type="date" class="form-control form-control-sm" id="start_date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <label for="end_date" class="form-label small text-navy fw-bold mb-1">End Date</label>
                            <input type="date" class="form-control form-control-sm" id="end_date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <label for="status" class="form-label small text-navy fw-bold mb-1">Status Filter</label>
                            <select name="status" id="status" class="form-select form-select-sm">
                                <option value="">All Statuses</option>
                                <option value="Confirmed" <?php echo ($status === 'Confirmed') ? 'selected' : ''; ?>>Confirmed Only</option>
                                <option value="Pending" <?php echo ($status === 'Pending') ? 'selected' : ''; ?>>Pending Only</option>
                                <option value="Cancelled" <?php echo ($status === 'Cancelled') ? 'selected' : ''; ?>>Cancelled Only</option>
                            </select>
                        </div>
                        <div class="col-md-3 col-sm-6 d-flex gap-2">
                            <button type="submit" class="btn btn-ocean btn-sm flex-grow-1">
                                <i class="fa-solid fa-filter me-1"></i> Generate Report
                            </button>
                            <a href="<?php echo url('admin/reports.php'); ?>" class="btn btn-outline-secondary btn-sm" title="Reset">
                                <i class="fa-solid fa-rotate-left"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Report Header for Printout -->
            <div class="mb-4">
                <h4 class="text-navy fw-bold mb-1">Coastal Villa Performance Report</h4>
                <p class="text-muted small mb-0">Period: <?php echo format_date($start_date); ?> to <?php echo format_date($end_date); ?> | Generated on: <?php echo date('M d, Y h:i A'); ?></p>
            </div>

            <!-- Summary KPI Metric Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-3 col-6">
                    <div class="stat-card stat-blue mb-0">
                        <div>
                            <div class="stat-label">Total Bookings</div>
                            <div class="stat-number"><?php echo (int)($report_stats['total_bookings'] ?? 0); ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-card stat-green mb-0">
                        <div>
                            <div class="stat-label">Confirmed Revenue</div>
                            <div class="stat-number fs-5 text-success"><?php echo format_currency($report_stats['total_revenue'] ?? 0); ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-card stat-orange mb-0">
                        <div>
                            <div class="stat-label">Pending Requests</div>
                            <div class="stat-number text-warning"><?php echo (int)($report_stats['pending_count'] ?? 0); ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-card stat-red mb-0">
                        <div>
                            <div class="stat-label">Cancelled</div>
                            <div class="stat-number text-danger"><?php echo (int)($report_stats['cancelled_count'] ?? 0); ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Room Performance Breakdown Table -->
            <div class="card border-0 rounded-4 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold text-navy mb-0"><i class="fa-solid fa-bed me-2 text-primary"></i> Room Performance Breakdown</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Room Name</th>
                                    <th>Category</th>
                                    <th class="text-center">Total Bookings</th>
                                    <th class="text-center">Occupied Nights</th>
                                    <th class="text-end">Confirmed Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($room_performance as $rp): ?>
                                    <tr>
                                        <td class="fw-bold text-navy"><?php echo htmlspecialchars($rp['room_name']); ?></td>
                                        <td><span class="badge bg-light text-navy border"><?php echo htmlspecialchars($rp['room_type']); ?></span></td>
                                        <td class="text-center"><?php echo (int)$rp['booking_count']; ?></td>
                                        <td class="text-center"><?php echo (int)($rp['total_room_nights'] ?? 0); ?> nights</td>
                                        <td class="text-end fw-bold text-ocean"><?php echo format_currency($rp['room_revenue'] ?? 0); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Complete List of Transactions in Period -->
            <div class="card border-0 rounded-4 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold text-navy mb-0"><i class="fa-solid fa-list me-2 text-primary"></i> Reservation Ledger</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="table-light">
                                <tr>
                                    <th>Ref ID</th>
                                    <th>Date</th>
                                    <th>Guest</th>
                                    <th>Room</th>
                                    <th>Stay Dates</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($report_bookings)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">No reservations found for selected report filters.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($report_bookings as $rb): ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($rb['booking_reference']); ?></strong></td>
                                            <td><?php echo format_date($rb['booking_date']); ?></td>
                                            <td><?php echo htmlspecialchars($rb['full_name']); ?></td>
                                            <td><?php echo htmlspecialchars($rb['room_name']); ?></td>
                                            <td><?php echo format_date($rb['check_in']); ?> &rarr; <?php echo format_date($rb['check_out']); ?></td>
                                            <td class="fw-bold text-navy"><?php echo format_currency($rb['total_amount']); ?></td>
                                            <td><?php echo get_status_badge($rb['booking_status']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
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
