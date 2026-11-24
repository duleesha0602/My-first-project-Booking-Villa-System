<?php
/**
 * ====================================================================
 *    Room Management
 * Coastal Villa & Room Booking Management System
 * 
 * ====================================================================
 */

require_once __DIR__ . '/../includes/header.php';
require_admin_login();

$search = sanitize_input($_GET['search'] ?? '');
$type_filter = sanitize_input($_GET['type'] ?? '');
$status_filter = sanitize_input($_GET['status'] ?? '');

$sql = "SELECT * FROM rooms WHERE 1=1";
$params = [];

if (!empty($search)) {
    $sql .= " AND (room_name LIKE :search OR room_type LIKE :search)";
    $params[':search'] = "%{$search}%";
}

if (!empty($type_filter)) {
    $sql .= " AND room_type = :type";
    $params[':type'] = $type_filter;
}

if (!empty($status_filter)) {
    $sql .= " AND status = :status";
    $params[':status'] = $status_filter;
}

$sql .= " ORDER BY id ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rooms = $stmt->fetchAll();

// Get unique types
$types_stmt = $pdo->query("SELECT DISTINCT room_type FROM rooms");
$all_types = $types_stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<div class="dashboard-wrapper">
    <!-- Admin Sidebar -->
    <?php require_once __DIR__ . '/../includes/admin-sidebar.php'; ?>

    <div class="dashboard-main">
        <!-- Topbar -->
        <div class="dashboard-topbar">
            <div class="dashboard-title">
                <h2><i class="fa-solid fa-bed me-2 text-primary"></i> Room Management</h2>
                <small class="text-muted">Manage villa accommodation types, nightly pricing, and availability status</small>
            </div>
            <div>
                <a href="<?php echo url('admin/add-room.php'); ?>" class="btn btn-navy btn-sm">
                    <i class="fa-solid fa-square-plus me-1"></i> Add New Room
                </a>
            </div>
        </div>

        <div class="dashboard-content">
            <!-- Flash Message -->
            <?php display_flash(); ?>

            <!-- Filter & Search Toolbar -->
            <div class="card border-0 rounded-4 shadow-sm mb-4">
                <div class="card-body p-3">
                    <form action="<?php echo url('admin/rooms.php'); ?>" method="GET" class="row g-2 align-items-center">
                        <div class="col-md-4 col-sm-6">
                            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search room by name or type..." value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <select name="type" class="form-select form-select-sm">
                                <option value="">All Categories</option>
                                <?php foreach ($all_types as $t): ?>
                                    <option value="<?php echo htmlspecialchars($t); ?>" <?php echo ($type_filter === $t) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($t); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <select name="status" class="form-select form-select-sm">
                                <option value="">All Statuses</option>
                                <option value="active" <?php echo ($status_filter === 'active') ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo ($status_filter === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-2 col-sm-6 d-flex gap-2">
                            <button type="submit" class="btn btn-ocean btn-sm flex-grow-1">
                                <i class="fa-solid fa-filter me-1"></i> Filter
                            </button>
                            <a href="<?php echo url('admin/rooms.php'); ?>" class="btn btn-outline-secondary btn-sm" title="Reset Filters">
                                <i class="fa-solid fa-rotate-left"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Room List Table -->
            <div class="card border-0 rounded-4 shadow-sm overflow-hidden">
                <div class="card-body p-0">
                    <?php if (empty($rooms)): ?>
                        <div class="p-5 text-center text-muted">
                            <i class="fa-solid fa-door-closed fa-3x mb-3 text-muted"></i>
                            <h5>No Rooms Found</h5>
                            <p class="small">No room entries match your search criteria.</p>
                            <a href="<?php echo url('admin/add-room.php'); ?>" class="btn btn-navy btn-sm">Add New Room</a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Photo</th>
                                        <th>Room Name</th>
                                        <th>Type</th>
                                        <th>Nightly Rate</th>
                                        <th>Capacity</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rooms as $r): ?>
                                        <tr>
                                            <td><strong>#<?php echo $r['id']; ?></strong></td>
                                            <td>
                                                <img src="<?php echo url($r['room_image']); ?>" alt="Room" class="rounded-3 shadow-sm" style="width: 55px; height: 42px; object-fit: cover;">
                                            </td>
                                            <td>
                                                <div class="fw-bold text-navy"><?php echo htmlspecialchars($r['room_name']); ?></div>
                                                <small class="text-muted"><?php echo htmlspecialchars(substr($r['description'], 0, 50)); ?>...</small>
                                            </td>
                                            <td><span class="badge bg-light text-navy border"><?php echo htmlspecialchars($r['room_type']); ?></span></td>
                                            <td class="fw-bold text-ocean"><?php echo format_currency($r['price_per_night']); ?></td>
                                            <td><i class="fa-solid fa-user-group text-primary me-1"></i> <?php echo $r['max_guests']; ?></td>
                                            <td>
                                                <?php if ($r['status'] === 'active'): ?>
                                                    <span class="badge bg-success"><i class="fa-solid fa-circle-check me-1"></i> Active</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary"><i class="fa-solid fa-circle-pause me-1"></i> Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end">
                                                <div class="d-inline-flex gap-1">
                                                    <a href="<?php echo url('room-details.php?id=' . $r['id']); ?>" target="_blank" class="btn btn-light btn-sm text-primary" title="Preview Public Page">
                                                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                                    </a>
                                                    <a href="<?php echo url('admin/edit-room.php?id=' . $r['id']); ?>" class="btn btn-light btn-sm text-success" title="Edit Room Details">
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                    </a>
                                                    <a href="<?php echo url('actions/room-action.php?action=toggle_status&id=' . $r['id']); ?>" class="btn btn-light btn-sm text-warning" title="Toggle Active / Inactive">
                                                        <i class="fa-solid fa-power-off"></i>
                                                    </a>
                                                    <a href="<?php echo url('actions/room-action.php?action=delete&id=' . $r['id']); ?>" class="btn btn-light btn-sm text-danger" title="Delete Room" onclick="return confirm('Are you sure you want to delete this room? If historical bookings exist, it will be deactivated instead to preserve records.');">
                                                        <i class="fa-solid fa-trash-can"></i>
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
