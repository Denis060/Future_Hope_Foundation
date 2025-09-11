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

// Get all testimonials
$sql = "SELECT * FROM testimonials ORDER BY order_number ASC";
$result = $conn->query($sql);
$testimonials = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $testimonials[] = $row;
    }
}

// Message to display (if any)
$message = $message ?? '';

// Handle testimonial status toggle
if (isset($_GET['action']) && $_GET['action'] === 'toggle' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    
    if (toggleActiveStatus('testimonials', $id)) {
        $_SESSION['message'] = displaySuccess("Testimonial status updated successfully!");
    } else {
        $_SESSION['message'] = displayError("Error updating testimonial status: " . $conn->error);
    }
    
    // Redirect to remove the action from URL
    header("Location: testimonials.php");
    exit;
}

// Handle testimonial deletion
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    
    // Check if image exists and delete it
    $image_result = $conn->query("SELECT image FROM testimonials WHERE id = $id");
    if ($image_result->num_rows > 0) {
        $image = $image_result->fetch_assoc()['image'];
        if ($image && file_exists("../uploads/$image")) {
            unlink("../uploads/$image");
        }
    }
    
    $delete_sql = "DELETE FROM testimonials WHERE id = $id";
    
    if ($conn->query($delete_sql) === TRUE) {
        $_SESSION['message'] = displaySuccess("Testimonial deleted successfully!");
    } else {
        $_SESSION['message'] = displayError("Error deleting testimonial: " . $conn->error);
    }
    
    // Redirect to remove the action from URL
    header("Location: testimonials.php");
    exit;
}

// Handle testimonial creation/update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $name = $conn->real_escape_string($_POST['name']);
    $position = $conn->real_escape_string($_POST['position']);
    $testimonial = $conn->real_escape_string($_POST['testimonial']);
    $order_number = isset($_POST['order_number']) ? (int) $_POST['order_number'] : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $image_name = "";
    
    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $file_ext = pathinfo($filename, PATHINFO_EXTENSION);
        
        if (in_array(strtolower($file_ext), $allowed)) {
            $new_name = 'testimonial_' . time() . '.' . $file_ext;
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
                    $old_image_result = $conn->query("SELECT image FROM testimonials WHERE id = $id");
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
        // Update existing testimonial
        $sql = "UPDATE testimonials SET 
                name = '$name', 
                position = '$position', 
                testimonial = '$testimonial', 
                order_number = $order_number, 
                is_active = $is_active";
                
        if ($image_name) {
            $sql .= ", image = '$image_name'";
        }
        
        $sql .= " WHERE id = $id";
        
        if ($conn->query($sql) === TRUE) {
            $status_message = "Testimonial updated successfully.";
        } else {
            $error_message = "Error updating testimonial: " . $conn->error;
        }
    } else {
        // Create new testimonial
        $sql = "INSERT INTO testimonials (name, position, testimonial, image, order_number, is_active) 
                VALUES ('$name', '$position', '$testimonial', '$image_name', $order_number, $is_active)";
        
        if ($conn->query($sql) === TRUE) {
            $status_message = "Testimonial added successfully.";
        } else {
            $error_message = "Error adding testimonial: " . $conn->error;
        }
    }
    
    // Refresh testimonials list
    $result = $conn->query("SELECT * FROM testimonials ORDER BY order_number ASC");
    $testimonials = [];
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $testimonials[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Testimonials - <?php echo $settings['site_name'] ?? 'Future Hope'; ?> Admin</title>
    
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
        body {
            background-color: #f8f9fa;
            font-family: 'Roboto', sans-serif;
            padding-top: 56px;
        }
        
        .sidebar {
            position: fixed;
            top: 56px;
            left: 0;
            width: 250px;
            height: calc(100vh - 56px);
            background-color: #343a40;
            color: #fff;
            overflow-y: auto;
            transition: all 0.3s;
            z-index: 1000;
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.75);
            padding: 12px 20px;
            transition: all 0.3s;
        }
        
        .sidebar .nav-link:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .sidebar .nav-link.active {
            color: #fff;
            background-color: #3498db;
        }
        
        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .content-wrapper {
            margin-left: 250px;
            padding: 20px;
            transition: all 0.3s;
        }
        
        @media (max-width: 991px) {
            .sidebar {
                width: 60px;
            }
            
            .sidebar .nav-link span {
                display: none;
            }
            
            .sidebar .nav-link i {
                margin-right: 0;
                font-size: 18px;
            }
            
            .content-wrapper {
                margin-left: 60px;
            }
        }
        
        .testimonial-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
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
                            <i class="fas fa-user-circle"></i> <?php echo $_SESSION['username']; ?>
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
                <a class="nav-link active" href="testimonials.php">
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
            <div class="row">
                <div class="col-12">
                    <!-- Page Title -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h1 class="mb-0">Manage Testimonials</h1>
                            <p class="text-muted">View, add, edit or delete testimonials</p>
                        </div>
                        <div>
                            <button type="button" class="btn btn-primary add-new-testimonial">
                                <i class="fas fa-plus-circle"></i> Add New Testimonial
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php 
            // Initialize message variable
            $message = '';
            
            // Set success message if exists
            if (isset($status_message)) {
                $message = displaySuccess($status_message);
            }
            
            // Set error message if exists
            if (isset($error_message)) {
                $message = displayError($error_message);
            }
            
            // Display message if any
            echo $message; 
            ?>
            
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="testimonials-table" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th width="120">Name</th>
                                            <th>Position</th>
                                            <th>Testimonial</th>
                                            <th width="80">Order</th>
                                            <th width="100">Status</th>
                                            <th width="150">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($testimonials as $item): ?>
                                        <tr>
                                            <td>
                                                <?php if ($item['image']): ?>
                                                <img src="../uploads/<?php echo $item['image']; ?>" class="testimonial-image me-2">
                                                <?php endif; ?>
                                                <?php echo htmlspecialchars($item['name']); ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($item['position']); ?></td>
                                            <td><?php echo substr(strip_tags($item['testimonial']), 0, 100) . '...'; ?></td>
                                            <td><?php echo $item['order_number']; ?></td>
                                            <td>
                                                <?php if ($item['is_active']): ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-primary edit-testimonial" 
                                                    data-id="<?php echo $item['id']; ?>"
                                                    data-name="<?php echo htmlspecialchars($item['name']); ?>"
                                                    data-position="<?php echo htmlspecialchars($item['position']); ?>"
                                                    data-testimonial="<?php echo htmlspecialchars($item['testimonial']); ?>"
                                                    data-order="<?php echo $item['order_number']; ?>"
                                                    data-active="<?php echo $item['is_active']; ?>"
                                                    data-image="<?php echo $item['image']; ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <a href="testimonials.php?action=toggle&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-warning ms-1">
                                                    <i class="fas fa-power-off"></i>
                                                </a>
                                                <a href="javascript:void(0);" onclick="confirmDelete(<?php echo $item['id']; ?>)" class="btn btn-sm btn-danger ms-1">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
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
    
    <!-- Testimonial Form Modal -->
    <div class="modal fade" id="testimonialModal" tabindex="-1" aria-labelledby="testimonialModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="formTitle">Add New Testimonial</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="testimonialForm" action="testimonials.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" id="testimonial_id" name="id" value="">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="position" class="form-label">Position/Organization</label>
                                <input type="text" class="form-control" id="position" name="position" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="testimonial" class="form-label">Testimonial</label>
                            <textarea class="form-control" id="testimonial" name="testimonial" rows="5" required></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="order_number" class="form-label">Display Order</label>
                                <input type="number" class="form-control" id="order_number" name="order_number" value="0" min="0">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="image" class="form-label">Photo</label>
                                <input type="file" class="form-control" id="image" name="image">
                                <div id="imagePreview" class="mt-2 d-none">
                                    <p class="mb-1">Current image:</p>
                                    <img src="" id="currentImage" class="img-thumbnail" style="max-height: 100px; max-width: 100px;">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" checked>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                        
                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Save Testimonial</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this testimonial? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Delete</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Datatables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#testimonials-table').DataTable({
                ordering: true,
                paging: true,
                lengthChange: false,
                pageLength: 10,
                info: true,
                searching: true
            });
            
            // Add New Testimonial Button Click
            $('.add-new-testimonial').click(function() {
                $('#testimonialForm')[0].reset();
                $('#testimonial_id').val('');
                $('#imagePreview').addClass('d-none');
                $('#formTitle').text('Add New Testimonial');
                
                var testimonialModal = new bootstrap.Modal(document.getElementById('testimonialModal'));
                testimonialModal.show();
            });
            
            // Handle Edit Testimonial Button Click
            $('.edit-testimonial').click(function() {
                var id = $(this).data('id');
                var name = $(this).data('name');
                var position = $(this).data('position');
                var testimonial = $(this).data('testimonial');
                var order = $(this).data('order');
                var active = $(this).data('active');
                var image = $(this).data('image');
                
                $('#testimonial_id').val(id);
                $('#name').val(name);
                $('#position').val(position);
                $('#testimonial').val(testimonial);
                $('#order_number').val(order);
                $('#is_active').prop('checked', active == 1);
                
                if (image) {
                    $('#imagePreview').removeClass('d-none');
                    $('#currentImage').attr('src', '../uploads/' + image);
                } else {
                    $('#imagePreview').addClass('d-none');
                }
                
                $('#formTitle').text('Edit Testimonial');
                
                var testimonialModal = new bootstrap.Modal(document.getElementById('testimonialModal'));
                testimonialModal.show();
            });
        });
        
        // Delete confirmation
        function confirmDelete(id) {
            var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            document.getElementById('confirmDeleteBtn').href = 'testimonials.php?action=delete&id=' + id;
            modal.show();
        }
        
        // Check if we need to open the add modal (from quick actions)
        document.addEventListener('DOMContentLoaded', function() {
            if (localStorage.getItem('openAddModal') === 'true') {
                // Reset form
                document.getElementById('testimonialForm').reset();
                document.getElementById('testimonial_id').value = '';
                document.getElementById('formTitle').textContent = 'Add New Testimonial';
                document.getElementById('imagePreview').classList.add('d-none');
                
                // Show modal
                var testimonialModal = new bootstrap.Modal(document.getElementById('testimonialModal'));
                testimonialModal.show();
                
                // Clear localStorage
                localStorage.removeItem('openAddModal');
            }
        });
    </script>
</body>
</html>
