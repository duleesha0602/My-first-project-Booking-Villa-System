<?php
/**
 * ====================================================================
 *  Guest Management
 * Coastal Villa & Room Booking Management System
 * 
 * ====================================================================
 */

require_once __DIR__ . '/../includes/header.php';
require_admin_login();

// Handle Account Activation / Deactivation toggle
if (isset($_GET['toggle_status']) && !empty($_GET['id'])) {
    $guest_id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT status, role FROM users WHERE id = ?");
    $stmt->execute([$guest_id]);
    $u = $stmt->fetch();

    if ($u && $u['role'] === 'guest') {
        $new_st = ($u['status'] === 'active') ? 'inactive' : 'active';
        $up = $pdo->prepare("UPDATE users SET status = ? WHERE id = ?");
        $up->execute([$new_st, $guest_id]);
        set_flash('success', "Guest account status updated to '{$new_st}'.");
    }
    header('Location: ' . url('admin/guests.php'));
    exit();
}

$search = sanitize_input($_GET['search'] ?? '');

// Fetch all registered guests with booking statistics
$sql = "
    SELECT u.*, 
           COUNT(b.id) AS total_bookings,
           SUM(CASE WHEN b.booking_status = 'Confirmed' THEN b.total_amount ELSE 0 END) AS total_spent
    FROM users u
    LEFT JOIN bookings b ON u.id = b.user_id
    WHERE u.role = 'guest'
";
$params = [];

if (!empty($search)) {
    $sql .= " AND (u.full_name LIKE :search OR u.email LIKE :search OR u.contact_number LIKE :search)";
    $params[':search'] = "%{$search}%";
}

$sql .= " GROUP BY u.id ORDER BY u.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$guests = $stmt->fetchAll();
?>

<div class="dashboard-wrapper">
    <!-- Admin Sidebar -->
    <?php require_once __DIR__ . '/../includes/admin-sidebar.php'; ?>

    <div class="dashboard-main">
        <!-- Topbar -->
        <div class="dashboard-topbar">
            <div class="dashboard-title">
                <h2><i class="fa-solid fa-users me-2 text-primary"></i> Guest Management</h2>
                <small class="text-muted">Registered customer profiles, account activity, and booking totals</small>
            </div>
        </div>

        <div class="dashboard-content">
            <!-- Flash Message -->
            <?php display_flash(); ?>

            <!-- Search Toolbar -->
            <div class="card border-0 rounded-4 shadow-sm mb-4">
                <div class="card-body p-3">
                    <form action="<?php echo url('admin/guests.php'); ?>" method="GET" class="row g-2 align-items-center">
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by name, email, or phone number..." value="<?php echo htmlspecialchars($search); ?>">
                                <button type="submit" class="btn btn-ocean btn-sm">
                                    <i class="fa-solid fa-magnifying-glass me-1"></i> Search
                                </button>
                                <?php if (!empty($search)): ?>
                                    <a href="<?php echo url('admin/guests.php'); ?>" class="btn btn-outline-secondary btn-sm">
                                        <i class="fa-solid fa-rotate-left"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6 text-md-end text-muted small">
                            Total registered guests: <strong><?php echo count($guests); ?></strong>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Guests Table -->
            <div class="card border-0 rounded-4 shadow-sm overflow-hidden">
                <div class="card-body p-0">
                    <?php if (empty($guests)): ?>
                        <div class="p-5 text-center text-muted">
                            <i class="fa-solid fa-user-xmark fa-3x mb-3 text-muted"></i>
                            <h5>No Guests Found</h5>
                            <p class="small">No registered guest accounts matched your search.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Guest Name</th>
                                        <th>Email Address</th>
                                        <th>Contact Phone</th>
                                        <th>Total Bookings</th>
                                        <th>Total Spent</th>
                                        <th>Registered Date</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($guests as $g): ?>
                                        <tr>
                                            <td><strong>#<?php echo $g['id']; ?></strong></td>
                                            <td>
                                                <div class="fw-bold text-navy"><?php echo htmlspecialchars($g['full_name']); ?></div>
                                            </td>
                                            <td class="small"><?php echo htmlspecialchars($g['email']); ?></td>
                                            <td class="small"><?php echo htmlspecialchars($g['contact_number']); ?></td>
                                            <td class="text-center">
                                                <span class="badge bg-light text-navy border"><?php echo (int)$g['total_bookings']; ?> booking(s)</span>
                                            </td>
                                            <td class="small fw-bold text-ocean"><?php echo format_currency($g['total_spent'] ?? 0); ?></td>
                                            <td class="small text-muted"><?php echo format_date($g['created_at']); ?></td>
                                            <td>
                                                <?php if ($g['status'] === 'active'): ?>
                                                    <span class="badge bg-success"><i class="fa-solid fa-check me-1"></i> Active</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary"><i class="fa-solid fa-ban me-1"></i> Deactivated</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end">
                                                <div class="d-inline-flex gap-1">
                                                    <!-- Filter bookings for this guest -->
                                                    <a href="<?php echo url('admin/bookings.php?search=' . urlencode($g['email'])); ?>" class="btn btn-light btn-sm text-primary" title="View Booking History">
                                                        <i class="fa-solid fa-list-check"></i>
                                                    </a>
                                                    <!-- Toggle Account Status -->
                                                    <a href="<?php echo url('admin/guests.php?toggle_status=1&id=' . $g['id']); ?>" class="btn btn-light btn-sm <?php echo ($g['status'] === 'active') ? 'text-warning' : 'text-success'; ?>" title="Toggle Status" onclick="return confirm('Change active status for this guest account?');">
                                                        <i class="fa-solid fa-power-off"></i>
                                                    </a>
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
