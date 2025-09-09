<?php
// Start session
session_start();

// Include database configuration and functions
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('index.php');
}

// Get settings
$settings = getSettings($conn);

// Get all gallery images
$sql = "SELECT * FROM gallery ORDER BY order_number ASC";
$result = $conn->query($sql);
$gallery_items = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $gallery_items[] = $row;
    }
}

// Handle image status toggle
if (isset($_GET['action']) && $_GET['action'] === 'toggle' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $gallery_item = $conn->query("SELECT is_active FROM gallery WHERE id = $id")->fetch_assoc();
    
    if ($gallery_item) {
        $new_status = $gallery_item['is_active'] ? 0 : 1;
        $update_sql = "UPDATE gallery SET is_active = $new_status WHERE id = $id";
        
        if ($conn->query($update_sql) === TRUE) {
            $_SESSION['message'] = "Gallery item status updated successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error updating gallery item status: " . $conn->error;
            $_SESSION['message_type'] = "danger";
        }
    }
    
    // Redirect to remove the action from URL
    header("Location: gallery.php");
    exit;
}

// Handle image deletion
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    
    // Get image info
    $image_result = $conn->query("SELECT file_path, thumbnail_path FROM gallery WHERE id = $id");
    if ($image_result->num_rows > 0) {
        $image_info = $image_result->fetch_assoc();
        
        // Delete files
        if ($image_info['file_path'] && file_exists("../uploads/" . $image_info['file_path'])) {
            unlink("../uploads/" . $image_info['file_path']);
        }
        
        if ($image_info['thumbnail_path'] && file_exists("../uploads/" . $image_info['thumbnail_path'])) {
            unlink("../uploads/" . $image_info['thumbnail_path']);
        }
    }
    
    $delete_sql = "DELETE FROM gallery WHERE id = $id";
    
    if ($conn->query($delete_sql) === TRUE) {
        $_SESSION['message'] = "Gallery item deleted successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error deleting gallery item: " . $conn->error;
        $_SESSION['message_type'] = "danger";
    }
    
    // Redirect to remove the action from URL
    header("Location: gallery.php");
    exit;
}

// Handle image upload/update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $category = $conn->real_escape_string($_POST['category']);
    $order_number = isset($_POST['order_number']) ? (int) $_POST['order_number'] : 0;
    $media_type = $conn->real_escape_string($_POST['media_type']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    $file_path = '';
    $thumbnail_path = '';
    
    // Handle file upload
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'mp4'];
        $file_name = $_FILES['file']['name'];
        $file_tmp = $_FILES['file']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        if (in_array($file_ext, $allowed_extensions)) {
            $new_file_name = 'gallery_' . time() . '.' . $file_ext;
            $destination = '../uploads/' . $new_file_name;
            
            if (move_uploaded_file($file_tmp, $destination)) {
                $file_path = $new_file_name;
                
                // Create thumbnail for images
                if ($media_type === 'image') {
                    $thumbnail_name = 'thumb_' . $new_file_name;
                    $thumbnail_path = $thumbnail_name;
                    
                    // Create thumbnail
                    list($width, $height) = getimagesize($destination);
                    $thumb_width = 300;
                    $thumb_height = ($height / $width) * $thumb_width;
                    
                    // Explicitly cast to integers to avoid deprecation warning
                    $thumb = imagecreatetruecolor((int)$thumb_width, (int)$thumb_height);
                    
                    switch ($file_ext) {
                        case 'jpg':
                        case 'jpeg':
                            $source = imagecreatefromjpeg($destination);
                            break;
                        case 'png':
                            $source = imagecreatefrompng($destination);
                            break;
                        case 'gif':
                            $source = imagecreatefromgif($destination);
                            break;
                    }
                    
                    imagecopyresized($thumb, $source, 0, 0, 0, 0, (int)$thumb_width, (int)$thumb_height, (int)$width, (int)$height);
                    
                    $thumb_destination = '../uploads/' . $thumbnail_name;
                    
                    switch ($file_ext) {
                        case 'jpg':
                        case 'jpeg':
                            imagejpeg($thumb, $thumb_destination, 90);
                            break;
                        case 'png':
                            imagepng($thumb, $thumb_destination);
                            break;
                        case 'gif':
                            imagegif($thumb, $thumb_destination);
                            break;
                    }
                    
                    imagedestroy($thumb);
                    imagedestroy($source);
                }
                
                // If updating, delete old files
                if ($id > 0) {
                    $old_files_result = $conn->query("SELECT file_path, thumbnail_path FROM gallery WHERE id = $id");
                    if ($old_files_result->num_rows > 0) {
                        $old_files = $old_files_result->fetch_assoc();
                        
                        if ($old_files['file_path'] && file_exists("../uploads/" . $old_files['file_path'])) {
                            unlink("../uploads/" . $old_files['file_path']);
                        }
                        
                        if ($old_files['thumbnail_path'] && file_exists("../uploads/" . $old_files['thumbnail_path'])) {
                            unlink("../uploads/" . $old_files['thumbnail_path']);
                        }
                    }
                }
            } else {
                $_SESSION['message'] = "Error uploading file.";
                $_SESSION['message_type'] = "danger";
                
                // Redirect to refresh page and prevent form resubmission
                header("Location: gallery.php");
                exit;
            }
        } else {
            $_SESSION['message'] = "Invalid file type. Allowed: jpg, jpeg, png, gif, mp4";
            $_SESSION['message_type'] = "danger";
            
            // Redirect to refresh page and prevent form resubmission
            header("Location: gallery.php");
            exit;
        }
    }
    
    if (!isset($error_message)) {
        if ($id > 0) {
            // Update existing gallery item
            $sql = "UPDATE gallery SET 
                    title = '$title', 
                    description = '$description', 
                    category = '$category',
                    order_number = $order_number, 
                    media_type = '$media_type', 
                    is_active = $is_active";
                    
            // Only update file paths if a new file was uploaded
            if ($file_path) {
                $sql .= ", file_path = '$file_path', thumbnail_path = '$thumbnail_path'";
            }
            
            $sql .= " WHERE id = $id";
            
            if ($conn->query($sql) === TRUE) {
                $_SESSION['message'] = "Gallery item updated successfully!";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "Error updating gallery item: " . $conn->error;
                $_SESSION['message_type'] = "danger";
            }
            
            // Redirect to refresh page and prevent form resubmission
            header("Location: gallery.php");
            exit;
        } else {
            // Create new gallery item - require file
            if (empty($file_path)) {
                $_SESSION['message'] = "File is required for new gallery items.";
                $_SESSION['message_type'] = "danger";
                
                // Redirect to refresh page and prevent form resubmission
                header("Location: gallery.php");
                exit;
            } else {
                $sql = "INSERT INTO gallery (title, description, category, media_type, file_path, thumbnail_path, order_number, is_active) 
                        VALUES ('$title', '$description', '$category', '$media_type', '$file_path', '$thumbnail_path', $order_number, $is_active)";
                
                if ($conn->query($sql) === TRUE) {
                    $_SESSION['message'] = "Gallery item added successfully!";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = "Error adding gallery item: " . $conn->error;
                    $_SESSION['message_type'] = "danger";
                }
                
                // Redirect to refresh page and prevent form resubmission
                header("Location: gallery.php");
                exit;
            }
        }
    }
}

// Get unique categories
$categories = [];
foreach ($gallery_items as $item) {
    if (!empty($item['category']) && !in_array($item['category'], $categories)) {
        $categories[] = $item['category'];
    }
}
sort($categories);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Gallery - <?php echo $settings['site_name'] ?? 'Future Hope Foundation'; ?> Admin</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <!-- Datatables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    
    <!-- Custom Admin CSS -->
    <link rel="stylesheet" href="css/admin-style.css">
    
    <style>
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            grid-gap: 20px;
        }
        
        .gallery-item {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
        }
        
        .gallery-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .gallery-item img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }
        
        .gallery-item .badge {
            position: absolute;
            top: 10px;
            right: 10px;
        }
        
        .gallery-item .gallery-actions {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.7);
            padding: 8px;
            display: flex;
            justify-content: space-around;
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .gallery-item:hover .gallery-actions {
            opacity: 1;
        }
        
        .gallery-item .title {
            padding: 10px;
            font-weight: 500;
            text-align: center;
            background: #f8f9fa;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .video-item::after {
            content: "\f144";
            font-family: "Font Awesome 5 Free";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 3rem;
            color: white;
            text-shadow: 0 0 10px rgba(0,0,0,0.5);
        }
    </style>
</head>
<body>
    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">
                <?php echo $settings['site_name'] ?? 'Future Hope Foundation'; ?> Admin
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php" target="_blank">
                            <i class="fas fa-external-link-alt"></i> View Website
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle"></i> Admin
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i> Profile</a></li>
                            <li><a class="dropdown-item" href="change-password.php"><i class="fas fa-key me-2"></i> Change Password</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Sidebar -->
    <div class="sidebar">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="dashboard.php">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="settings.php">
                    <i class="fas fa-cog"></i>
                    <span>Site Settings</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="sliders.php">
                    <i class="fas fa-images"></i>
                    <span>Manage Sliders</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="services.php">
                    <i class="fas fa-hands-helping"></i>
                    <span>Services</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="projects.php">
                    <i class="fas fa-project-diagram"></i>
                    <span>Projects</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="events.php">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Events</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="team.php">
                    <i class="fas fa-users"></i>
                    <span>Team Members</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="testimonials.php">
                    <i class="fas fa-quote-left"></i>
                    <span>Testimonials</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="gallery.php">
                    <i class="fas fa-photo-video"></i>
                    <span>Gallery</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="donations.php">
                    <i class="fas fa-donate"></i>
                    <span>Donations</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="messages.php">
                    <i class="fas fa-envelope"></i>
                    <span>Messages</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="users.php">
                    <i class="fas fa-user-shield"></i>
                    <span>Users</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </div>
    
    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <div class="container-fluid">
            <!-- Page Header -->
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h1 class="page-title">Manage Gallery</h1>
                        <p class="text-muted">Upload and manage images and videos for the gallery section</p>
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addGalleryModal">
                            <i class="fas fa-plus-circle me-2"></i>Add New Media
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Status Messages -->
            <?php if(isset($_SESSION['message'])): ?>
                <div class="alert alert-<?= $_SESSION['message_type']; ?> alert-dismissible fade show" role="alert">
                    <?= $_SESSION['message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
            <?php endif; ?>
            
            <!-- Add Gallery Modal -->
            <div class="modal fade" id="addGalleryModal" tabindex="-1" aria-labelledby="addGalleryModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addGalleryModalLabel">Add New Gallery Item</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="gallery.php" method="post" enctype="multipart/form-data">
                            <div class="modal-body">
                                <input type="hidden" name="id" value="">
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="title" class="form-label">Title</label>
                                            <input type="text" class="form-control" id="title" name="title" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="category" class="form-label">Category</label>
                                            <input type="text" class="form-control" id="category" name="category" list="categories">
                                            <datalist id="categories">
                                                <?php foreach ($categories as $category): ?>
                                                <option value="<?php echo htmlspecialchars($category); ?>">
                                                <?php endforeach; ?>
                                            </datalist>
                                            <small class="form-text text-muted">Type a new category or select from existing ones</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="media_type" class="form-label">Media Type</label>
                                            <select class="form-select" id="media_type" name="media_type" required>
                                                <option value="image">Image</option>
                                                <option value="video">Video</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="order_number" class="form-label">Display Order</label>
                                            <input type="number" class="form-control" id="order_number" name="order_number" value="0" min="0">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="file" class="form-label">File (Image or Video)</label>
                                    <input type="file" class="form-control" id="file" name="file" required>
                                    <small class="form-text text-muted">Allowed formats: jpg, jpeg, png, gif, mp4</small>
                                </div>
                                
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" checked>
                                    <label class="form-check-label" for="is_active">Active</label>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Add to Gallery</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-photo-video me-2"></i> Gallery Items</h5>
                        </div>
                        <div class="card-body">
                            <?php if (count($gallery_items) === 0): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-images fa-4x text-muted mb-3"></i>
                                <h4>No Gallery Items</h4>
                                <p class="text-muted">Add your first gallery item using the form above.</p>
                            </div>
                            <?php else: ?>
                            <div class="gallery-filter mb-4">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-outline-primary active filter-btn" data-filter="all">All</button>
                                    <?php foreach ($categories as $category): ?>
                                    <button type="button" class="btn btn-outline-primary filter-btn" data-filter="<?php echo htmlspecialchars($category); ?>">
                                        <?php echo htmlspecialchars($category); ?>
                                    </button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            
                            <div class="gallery-grid">
                                <?php foreach ($gallery_items as $item): ?>
                                <div class="gallery-item" data-category="<?php echo htmlspecialchars($item['category']); ?>">
                                    <?php if ($item['media_type'] === 'image'): ?>
                                    <img src="../uploads/<?php echo $item['file_path']; ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                                    <?php else: ?>
                                    <div class="video-item">
                                        <img src="../uploads/<?php echo $item['thumbnail_path'] ?: 'video_placeholder.jpg'; ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!$item['is_active']): ?>
                                    <span class="badge bg-danger">Inactive</span>
                                    <?php endif; ?>
                                    
                                    <div class="gallery-actions">
                                        <a href="#" class="btn btn-sm btn-light edit-gallery-item" 
                                           data-id="<?php echo $item['id']; ?>"
                                           data-title="<?php echo htmlspecialchars($item['title']); ?>"
                                           data-description="<?php echo htmlspecialchars($item['description']); ?>"
                                           data-category="<?php echo htmlspecialchars($item['category']); ?>"
                                           data-media-type="<?php echo $item['media_type']; ?>"
                                           data-order="<?php echo $item['order_number']; ?>"
                                           data-active="<?php echo $item['is_active']; ?>"
                                           data-bs-toggle="modal" data-bs-target="#editGalleryModal">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="gallery.php?action=toggle&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-warning">
                                            <i class="fas fa-toggle-<?php echo $item['is_active'] ? 'on' : 'off'; ?>"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger delete-gallery-item" 
                                           data-id="<?php echo $item['id']; ?>"
                                           data-title="<?php echo htmlspecialchars($item['title']); ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    
                                    <div class="title">
                                        <?php echo htmlspecialchars($item['title']); ?>
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
    
    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this gallery item? This action cannot be undone.</p>
                    <p class="mb-0"><strong>Item: </strong><span id="deleteItemTitle"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Delete</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Gallery Modal -->
    <div class="modal fade" id="editGalleryModal" tabindex="-1" aria-labelledby="editGalleryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="editGalleryModalLabel">Edit Gallery Item</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="gallery.php" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" id="edit_id" name="id" value="">
                        
                        <div class="mb-3">
                            <label for="edit_title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="edit_title" name="title" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_category" class="form-label">Category</label>
                            <input type="text" class="form-control" id="edit_category" name="category" list="edit_categories">
                            <datalist id="edit_categories">
                                <?php foreach ($categories as $category): ?>
                                <option value="<?php echo htmlspecialchars($category); ?>">
                                <?php endforeach; ?>
                            </datalist>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Description</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_media_type" class="form-label">Media Type</label>
                                    <select class="form-select" id="edit_media_type" name="media_type" required>
                                        <option value="image">Image</option>
                                        <option value="video">Video</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_order_number" class="form-label">Display Order</label>
                                    <input type="number" class="form-control" id="edit_order_number" name="order_number" value="0" min="0">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_file" class="form-label">Replace File (Optional)</label>
                            <input type="file" class="form-control" id="edit_file" name="file">
                            <small class="form-text text-muted">Leave empty to keep current file</small>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="edit_is_active" name="is_active">
                            <label class="form-check-label" for="edit_is_active">Active</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Gallery Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Edit gallery item
            $('.edit-gallery-item').click(function() {
                var id = $(this).data('id');
                var title = $(this).data('title');
                var description = $(this).data('description');
                var category = $(this).data('category');
                var mediaType = $(this).data('media-type');
                var order = $(this).data('order');
                var active = $(this).data('active');
                
                $('#edit_id').val(id);
                $('#edit_title').val(title);
                $('#edit_description').val(description);
                $('#edit_category').val(category);
                $('#edit_media_type').val(mediaType);
                $('#edit_order_number').val(order);
                $('#edit_is_active').prop('checked', active == 1);
            });
            
            // Delete gallery item
            $('.delete-gallery-item').click(function() {
                var id = $(this).data('id');
                var title = $(this).data('title');
                
                $('#deleteItemTitle').text(title);
                $('#confirmDeleteBtn').attr('href', 'gallery.php?action=delete&id=' + id);
                
                var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
                deleteModal.show();
            });
            
            // Gallery filter
            $('.filter-btn').click(function() {
                var filter = $(this).data('filter');
                
                // Update active button
                $('.filter-btn').removeClass('active');
                $(this).addClass('active');
                
                // Filter gallery items
                if (filter === 'all') {
                    $('.gallery-item').show();
                } else {
                    $('.gallery-item').hide();
                    $('.gallery-item[data-category="' + filter + '"]').show();
                }
            });
            
            // Check if we need to open the add modal (from quick actions)
            if (localStorage.getItem('openAddModal') === 'true') {
                $('#addGalleryModal').modal('show');
                localStorage.removeItem('openAddModal');
            }
        });
    </script>
</body>
</html>
