<?php
/**
 * ====================================================================
 *  Enquiry Management
 * Coastal Villa & Room Booking Management System
 * 
 * ====================================================================
 */

require_once __DIR__ . '/../includes/header.php';
require_admin_login();

// Handle Status Changes or Delete
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = sanitize_input($_GET['action']);
    $enquiry_id = (int)$_GET['id'];

    if ($action === 'mark_read') {
        $pdo->prepare("UPDATE enquiries SET status = 'read' WHERE id = ?")->execute([$enquiry_id]);
        set_flash('success', 'Enquiry marked as read.');
    } elseif ($action === 'mark_replied') {
        $pdo->prepare("UPDATE enquiries SET status = 'replied' WHERE id = ?")->execute([$enquiry_id]);
        set_flash('success', 'Enquiry marked as replied.');
    } elseif ($action === 'delete') {
        $pdo->prepare("DELETE FROM enquiries WHERE id = ?")->execute([$enquiry_id]);
        set_flash('success', 'Enquiry message deleted.');
    }
    header('Location: ' . url('admin/enquiries.php'));
    exit();
}

$status_filter = sanitize_input($_GET['status'] ?? '');
$sql = "SELECT * FROM enquiries WHERE 1=1";
$params = [];

if (!empty($status_filter)) {
    $sql .= " AND status = :status";
    $params[':status'] = $status_filter;
}
$sql .= " ORDER BY id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$enquiries = $stmt->fetchAll();
?>

<div class="dashboard-wrapper">
    <!-- Admin Sidebar -->
    <?php require_once __DIR__ . '/../includes/admin-sidebar.php'; ?>

    <div class="dashboard-main">
        <!-- Topbar -->
        <div class="dashboard-topbar">
            <div class="dashboard-title">
                <h2><i class="fa-solid fa-envelope-open-text me-2 text-primary"></i> Guest Inquiries</h2>
                <small class="text-muted">Manage messages submitted through the public Contact Us page</small>
            </div>
        </div>

        <div class="dashboard-content">
            <!-- Flash Message -->
            <?php display_flash(); ?>

            <!-- Filter Buttons -->
            <div class="mb-4 d-flex gap-2">
                <a href="<?php echo url('admin/enquiries.php'); ?>" class="btn btn-sm <?php echo empty($status_filter) ? 'btn-ocean' : 'btn-outline-ocean'; ?>">
                    All Messages
                </a>
                <a href="<?php echo url('admin/enquiries.php?status=unread'); ?>" class="btn btn-sm <?php echo ($status_filter === 'unread') ? 'btn-ocean' : 'btn-outline-ocean'; ?>">
                    Unread
                </a>
                <a href="<?php echo url('admin/enquiries.php?status=read'); ?>" class="btn btn-sm <?php echo ($status_filter === 'read') ? 'btn-ocean' : 'btn-outline-ocean'; ?>">
                    Read
                </a>
                <a href="<?php echo url('admin/enquiries.php?status=replied'); ?>" class="btn btn-sm <?php echo ($status_filter === 'replied') ? 'btn-ocean' : 'btn-outline-ocean'; ?>">
                    Replied
                </a>
            </div>

            <!-- Enquiries Table -->
            <div class="card border-0 rounded-4 shadow-sm overflow-hidden">
                <div class="card-body p-0">
                    <?php if (empty($enquiries)): ?>
                        <div class="p-5 text-center text-muted">
                            <i class="fa-solid fa-inbox fa-3x mb-3 text-muted"></i>
                            <h5>No Messages Found</h5>
                            <p class="small">No enquiries recorded under the selected filter.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Sender</th>
                                        <th>Subject</th>
                                        <th>Message</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($enquiries as $eq): ?>
                                        <tr class="<?php echo ($eq['status'] === 'unread') ? 'table-warning-subtle fw-semibold' : ''; ?>">
                                            <td class="small text-muted" style="white-space: nowrap;">
                                                <?php echo date('M d, Y', strtotime($eq['created_at'])); ?>
                                            </td>
                                            <td>
                                                <div class="text-navy"><?php echo htmlspecialchars($eq['sender_name']); ?></div>
                                                <small class="text-muted d-block"><?php echo htmlspecialchars($eq['sender_email']); ?></small>
                                                <?php if (!empty($eq['sender_phone'])): ?>
                                                    <small class="text-muted"><?php echo htmlspecialchars($eq['sender_phone']); ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td class="small"><?php echo htmlspecialchars($eq['subject']); ?></td>
                                            <td class="small text-muted" style="max-width: 300px;">
                                                <?php echo nl2br(htmlspecialchars($eq['message'])); ?>
                                            </td>
                                            <td>
                                                <?php if ($eq['status'] === 'unread'): ?>
                                                    <span class="badge bg-warning text-dark">Unread</span>
                                                <?php elseif ($eq['status'] === 'read'): ?>
                                                    <span class="badge bg-info text-dark">Read</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success">Replied</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end">
                                                <div class="d-inline-flex gap-1">
                                                    <?php if ($eq['status'] === 'unread'): ?>
                                                        <a href="<?php echo url('admin/enquiries.php?action=mark_read&id=' . $eq['id']); ?>" class="btn btn-light btn-sm text-info" title="Mark as Read">
                                                            <i class="fa-solid fa-envelope-open"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    <?php if ($eq['status'] !== 'replied'): ?>
                                                        <a href="<?php echo url('admin/enquiries.php?action=mark_replied&id=' . $eq['id']); ?>" class="btn btn-light btn-sm text-success" title="Mark as Replied">
                                                            <i class="fa-solid fa-reply"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    <a href="mailto:<?php echo htmlspecialchars($eq['sender_email']); ?>?subject=Re: <?php echo urlencode($eq['subject']); ?>" class="btn btn-light btn-sm text-primary" title="Reply via Email">
                                                        <i class="fa-solid fa-paper-plane"></i>
                                                    </a>
                                                    <a href="<?php echo url('admin/enquiries.php?action=delete&id=' . $eq['id']); ?>" class="btn btn-light btn-sm text-danger" title="Delete" onclick="return confirm('Delete this message?');">
                                                        <i class="fa-solid fa-trash"></i>
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
