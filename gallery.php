<?php
/**
 * ====================================================================
 *  Gallery Management
 * Coastal Villa & Room Booking Management System
 * 
 * ====================================================================
 */

require_once __DIR__ . '/../includes/header.php';
require_admin_login();

// Handle New Image Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_gallery'])) {
    $title    = sanitize_input($_POST['title'] ?? '');
    $category = sanitize_input($_POST['category'] ?? 'Villa Exterior');

    if (empty($title) || empty($_FILES['gallery_image']['name'])) {
        set_flash('danger', 'Please provide an image title and select a photo to upload.');
    } else {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $filename = $_FILES['gallery_image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            set_flash('danger', 'Invalid file type. Only JPG, PNG, and WEBP formats are allowed.');
        } else {
            $new_name = 'gallery_' . time() . '_' . rand(100, 999) . '.' . $ext;
            $upload_dir = __DIR__ . '/../assets/uploads/gallery/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            if (move_uploaded_file($_FILES['gallery_image']['tmp_name'], $upload_dir . $new_name)) {
                $image_path = 'assets/uploads/gallery/' . $new_name;
                $stmt = $pdo->prepare("INSERT INTO gallery (title, category, image_path) VALUES (?, ?, ?)");
                $stmt->execute([$title, $category, $image_path]);
                set_flash('success', 'New gallery photograph uploaded successfully!');
            } else {
                set_flash('danger', 'Failed to upload photo to directory.');
            }
        }
    }
    header('Location: ' . url('admin/gallery.php'));
    exit();
}

// Handle Image Delete
if (isset($_GET['delete_id'])) {
    $del_id = (int)$_GET['delete_id'];
    $stmt = $pdo->prepare("SELECT image_path FROM gallery WHERE id = ?");
    $stmt->execute([$del_id]);
    $img = $stmt->fetch();

    if ($img) {
        // Delete record from database
        $pdo->prepare("DELETE FROM gallery WHERE id = ?")->execute([$del_id]);
        set_flash('success', 'Gallery photograph deleted.');
    }
    header('Location: ' . url('admin/gallery.php'));
    exit();
}

// Fetch all gallery items
$items = $pdo->query("SELECT * FROM gallery ORDER BY id DESC")->fetchAll();
?>

<div class="dashboard-wrapper">
    <!-- Admin Sidebar -->
    <?php require_once __DIR__ . '/../includes/admin-sidebar.php'; ?>

    <div class="dashboard-main">
        <!-- Topbar -->
        <div class="dashboard-topbar">
            <div class="dashboard-title">
                <h2><i class="fa-solid fa-images me-2 text-primary"></i> Gallery Management</h2>
                <small class="text-muted">Upload and manage promotional villa imagery for the public gallery</small>
            </div>
            <div>
                <a href="<?php echo url('gallery.php'); ?>" target="_blank" class="btn btn-outline-ocean btn-sm">
                    <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> View Public Gallery
                </a>
            </div>
        </div>

        <div class="dashboard-content">
            <!-- Flash Message -->
            <?php display_flash(); ?>

            <div class="row g-4">
                <!-- Left: Upload Form -->
                <div class="col-lg-4">
                    <div class="card border-0 rounded-4 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h5 class="fw-bold text-navy mb-0"><i class="fa-solid fa-cloud-arrow-up me-2 text-primary"></i> Upload New Photo</h5>
                        </div>
                        <div class="card-body p-4">
                            <form action="<?php echo url('admin/gallery.php'); ?>" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="upload_gallery" value="1">

                                <div class="mb-3">
                                    <label for="title" class="form-label text-navy fw-bold small">Photo Caption / Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="title" name="title" required placeholder="e.g. Sunset View from Balcony">
                                </div>

                                <div class="mb-3">
                                    <label for="category" class="form-label text-navy fw-bold small">Category <span class="text-danger">*</span></label>
                                    <select class="form-select" id="category" name="category" required>
                                        <option value="Villa Exterior">Villa Exterior</option>
                                        <option value="Rooms">Rooms</option>
                                        <option value="Swimming Pool">Swimming Pool</option>
                                        <option value="Dining Area">Dining Area</option>
                                        <option value="Beach and Surroundings">Beach and Surroundings</option>
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label for="gallery_image" class="form-label text-navy fw-bold small">Choose Image File <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control" id="gallery_image" name="gallery_image" accept="image/png, image/jpeg, image/webp" required>
                                    <small class="text-muted">High resolution JPG or PNG recommended</small>
                                </div>

                                <button type="submit" class="btn btn-navy w-100 py-2">
                                    <i class="fa-solid fa-upload me-1"></i> Upload to Gallery
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Right: Existing Gallery Grid -->
                <div class="col-lg-8">
                    <div class="card border-0 rounded-4 shadow-sm">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold text-navy mb-0"><i class="fa-solid fa-photo-film me-2 text-primary"></i> Existing Photos (<?php echo count($items); ?>)</h5>
                        </div>
                        <div class="card-body p-4">
                            <?php if (empty($items)): ?>
                                <p class="text-muted text-center my-4">No gallery images uploaded yet.</p>
                            <?php else: ?>
                                <div class="row g-3">
                                    <?php foreach ($items as $img): ?>
                                        <div class="col-md-6 col-sm-6">
                                            <div class="border rounded-3 p-2 h-100 d-flex flex-column bg-light shadow-sm">
                                                <div class="rounded overflow-hidden mb-2" style="height: 160px;">
                                                    <img src="<?php echo url($img['image_path']); ?>" alt="Gallery" class="w-100 h-100" style="object-fit: cover;">
                                                </div>
                                                <div class="mb-2">
                                                    <span class="badge bg-info text-dark small mb-1"><?php echo htmlspecialchars($img['category']); ?></span>
                                                    <div class="fw-bold text-navy small text-truncate"><?php echo htmlspecialchars($img['title']); ?></div>
                                                </div>
                                                <div class="mt-auto text-end">
                                                    <a href="<?php echo url('admin/gallery.php?delete_id=' . $img['id']); ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Delete this gallery photo?');">
                                                        <i class="fa-solid fa-trash me-1"></i> Delete
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
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
