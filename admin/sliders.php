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

// Get all sliders
$sql = "SELECT * FROM sliders ORDER BY order_number ASC";
$result = $conn->query($sql);
$sliders = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $sliders[] = $row;
    }
}

// This section previously handled old message format
// Now using session messages with message_type

// Handle slider status toggle
if (isset($_GET['action']) && $_GET['action'] === 'toggle' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    
    if (toggleActiveStatus('sliders', $id)) {
        $_SESSION['message'] = "Slider status updated successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error updating slider status: " . $conn->error;
        $_SESSION['message_type'] = "danger";
    }
    
    // Redirect to remove the action from URL
    header("Location: sliders.php");
    exit;
}

// Handle slider deletion
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    
    // Check if image exists and delete it
    $image_result = $conn->query("SELECT image FROM sliders WHERE id = $id");
    if ($image_result->num_rows > 0) {
        $image = $image_result->fetch_assoc()['image'];
        if ($image && file_exists("../uploads/$image")) {
            unlink("../uploads/$image");
        }
    }
    
    $delete_sql = "DELETE FROM sliders WHERE id = $id";
    
    if ($conn->query($delete_sql) === TRUE) {
        $_SESSION['message'] = "Slider deleted successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error deleting slider: " . $conn->error;
        $_SESSION['message_type'] = "danger";
    }
    
    // Redirect to remove the action from URL
    header("Location: sliders.php");
    exit;
}

// Handle slider creation/update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $title = $conn->real_escape_string($_POST['title']);
    $subtitle = $conn->real_escape_string($_POST['subtitle']);
    $button_text = $conn->real_escape_string($_POST['button_text']);
    $button_url = $conn->real_escape_string($_POST['button_url']);
    $order_number = (int) $_POST['order_number'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $image_name = "";
    
    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $file_ext = pathinfo($filename, PATHINFO_EXTENSION);
        
        if (in_array(strtolower($file_ext), $allowed)) {
            $new_name = 'slider_' . time() . '.' . $file_ext;
            $upload_dir = '../uploads/';
            
            // Create uploads directory if it doesn't exist
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $destination = $upload_dir . $new_name;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                $image_name = $new_name;
                
                // If updating, delete old image
                if ($id > 0) {
                    $old_image_result = $conn->query("SELECT image FROM sliders WHERE id = $id");
                    if ($old_image_result->num_rows > 0) {
                        $old_image = $old_image_result->fetch_assoc()['image'];
                        if ($old_image && file_exists("../uploads/$old_image")) {
                            unlink("../uploads/$old_image");
                        }
                    }
                }
            }
        }
    }
    
    if ($id > 0) {
        // Update existing slider
        $sql = "UPDATE sliders SET title = '$title', subtitle = '$subtitle', button_text = '$button_text', 
                button_url = '$button_url', order_number = $order_number, is_active = $is_active";
                
        if ($image_name) {
            $sql .= ", image = '$image_name'";
        }
        
        $sql .= " WHERE id = $id";
        
        if ($conn->query($sql) === TRUE) {
            $_SESSION['message'] = "Slider updated successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error updating slider: " . $conn->error;
            $_SESSION['message_type'] = "danger";
        }
    } else {
        // Check if image is uploaded for new slider
        if (empty($image_name)) {
            $_SESSION['message'] = "Image is required for new sliders.";
            $_SESSION['message_type'] = "danger";
        } else {
            // Create new slider
            $sql = "INSERT INTO sliders (title, subtitle, button_text, button_url, image, order_number, is_active) 
                    VALUES ('$title', '$subtitle', '$button_text', '$button_url', '$image_name', $order_number, $is_active)";
            
            if ($conn->query($sql) === TRUE) {
                $_SESSION['message'] = "Slider created successfully!";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "Error creating slider: " . $conn->error;
                $_SESSION['message_type'] = "danger";
            }
        }
    }
    
    // Redirect to the page to avoid form resubmission
    header("Location: sliders.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Sliders - <?php echo $settings['site_name'] ?? 'Future Hope Foundation'; ?> Admin</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <!-- Datatables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    
    <!-- Custom Admin CSS -->
    <link rel="stylesheet" href="css/admin-style.css?v=<?php echo time(); ?>">
    
    <!-- Summernote CSS -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
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
                <a class="nav-link active" href="sliders.php">
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
                <a class="nav-link" href="gallery.php">
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
                        <h1 class="page-title">Manage Sliders</h1>
                        <p class="text-muted">Manage hero sliders displayed on the homepage</p>
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#sliderModal">
                            <i class="fas fa-plus-circle me-2"></i>Add New Slider
                        </button>
                    </div>
                </div>
            </div>
            
            <?php if(isset($_SESSION['message'])): ?>
                <div class="alert alert-<?= $_SESSION['message_type']; ?> alert-dismissible fade show" role="alert">
                    <?= $_SESSION['message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
            <?php endif; ?>
            
            <!-- Slider Modal -->
            <div class="modal fade" id="sliderModal" tabindex="-1" aria-labelledby="sliderModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="sliderModalLabel">Add New Slider</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="sliderForm" action="sliders.php" method="post" enctype="multipart/form-data">
                            <div class="modal-body">
                                <input type="hidden" id="slider_id" name="id" value="">
                                
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label for="title" class="form-label">Title</label>
                                            <input type="text" class="form-control" id="title" name="title" required>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="order_number" class="form-label">Display Order</label>
                                            <input type="number" class="form-control" id="order_number" name="order_number" value="0" min="0">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="subtitle" class="form-label">Subtitle/Description</label>
                                    <textarea class="form-control" id="subtitle" name="subtitle" rows="3"></textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="button_text" class="form-label">Button Text</label>
                                            <input type="text" class="form-control" id="button_text" name="button_text" placeholder="Learn More">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="button_url" class="form-label">Button URL</label>
                                            <input type="text" class="form-control" id="button_url" name="button_url" placeholder="/about.php">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="image" class="form-label">Slider Image</label>
                                    <input type="file" class="form-control" id="image" name="image">
                                    <small class="form-text text-muted">Recommended size: 1920x800 pixels</small>
                                    <div id="imagePreview" class="mt-2 d-none">
                                        <p>Current image:</p>
                                        <img src="" id="currentImage" class="img-thumbnail" style="max-height: 150px;">
                                    </div>
                                </div>
                                
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" checked>
                                    <label class="form-check-label" for="is_active">Active</label>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Save Slider</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Sliders Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">All Sliders</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="slidersTable" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th width="100">Image</th>
                                            <th>Title</th>
                                            <th>Order</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($sliders as $slider): ?>
                                        <tr>
                                            <td>
                                                <?php if ($slider['image']): ?>
                                                <img src="../uploads/<?php echo $slider['image']; ?>" class="img-thumbnail" style="max-height: 50px;">
                                                <?php else: ?>
                                                <span class="text-muted">No image</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($slider['title']); ?></td>
                                            <td><?php echo $slider['order_number']; ?></td>
                                            <td>
                                                <?php if ($slider['is_active']): ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="sliders.php?action=toggle&id=<?php echo $slider['id']; ?>" class="btn btn-sm <?php echo $slider['is_active'] ? 'btn-outline-success' : 'btn-outline-danger'; ?>" title="<?php echo $slider['is_active'] ? 'Deactivate' : 'Activate'; ?>">
                                                        <i class="fas fa-<?php echo $slider['is_active'] ? 'check' : 'times'; ?>"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-outline-primary edit-slider" 
                                                        data-id="<?php echo $slider['id']; ?>"
                                                        data-title="<?php echo htmlspecialchars($slider['title']); ?>"
                                                        data-subtitle="<?php echo htmlspecialchars($slider['subtitle']); ?>"
                                                        data-button-text="<?php echo htmlspecialchars($slider['button_text']); ?>"
                                                        data-button-url="<?php echo htmlspecialchars($slider['button_url']); ?>"
                                                        data-order="<?php echo $slider['order_number']; ?>"
                                                        data-active="<?php echo $slider['is_active']; ?>"
                                                        data-image="<?php echo $slider['image']; ?>"
                                                        title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-danger delete-slider"
                                                        data-id="<?php echo $slider['id']; ?>"
                                                        data-title="<?php echo htmlspecialchars($slider['title']); ?>"
                                                        title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
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
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this slider? This action cannot be undone.</p>
                    <p class="mb-0"><strong>Slider: </strong><span id="deleteSliderTitle"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Delete</a>
                </div>
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
            // Initialize DataTable
            $('#slidersTable').DataTable();
            
            // Handle Edit Slider Button Click
            $('.edit-slider').click(function() {
                var id = $(this).data('id');
                var title = $(this).data('title');
                var subtitle = $(this).data('subtitle');
                var buttonText = $(this).data('button-text');
                var buttonUrl = $(this).data('button-url');
                var order = $(this).data('order');
                var active = $(this).data('active');
                var image = $(this).data('image');
                
                $('#slider_id').val(id);
                $('#title').val(title);
                $('#subtitle').val(subtitle);
                $('#button_text').val(buttonText);
                $('#button_url').val(buttonUrl);
                $('#order_number').val(order);
                $('#is_active').prop('checked', active == 1);
                
                if (image) {
                    $('#imagePreview').removeClass('d-none');
                    $('#currentImage').attr('src', '../uploads/' + image);
                } else {
                    $('#imagePreview').addClass('d-none');
                }
                
                // Change modal title and show modal
                $('#sliderModalLabel').text('Edit Slider');
                var sliderModal = new bootstrap.Modal(document.getElementById('sliderModal'));
                sliderModal.show();
            });
            
            // Reset form when opening the modal for a new slider
            $('#sliderModal').on('hidden.bs.modal', function() {
                $('#sliderForm')[0].reset();
                $('#slider_id').val('');
                $('#imagePreview').addClass('d-none');
            });
            
            // Set modal title when adding a new slider
            $('[data-bs-target="#sliderModal"]').click(function() {
                $('#sliderModalLabel').text('Add New Slider');
            });
            
            // Handle delete confirmation
            $('.delete-slider').click(function() {
                var id = $(this).data('id');
                var title = $(this).data('title');
                
                $('#deleteSliderTitle').text(title);
                $('#confirmDeleteBtn').attr('href', 'sliders.php?action=delete&id=' + id);
                
                var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
                deleteModal.show();
            });
        });
    </script>
</body>
</html>
