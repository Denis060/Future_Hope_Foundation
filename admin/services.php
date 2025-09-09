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

// Get all services
$sql = "SELECT * FROM services ORDER BY order_number ASC";
$result = $conn->query($sql);
$services = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $services[] = $row;
    }
}

// Handle service status toggle
if (isset($_GET['action']) && $_GET['action'] === 'toggle' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    
    if (toggleActiveStatus('services', $id)) {
        $message = displaySuccess("Service status updated successfully!");
    } else {
        $message = displayError("Error updating service status.");
    }
    
    // Refresh services list
    $result = $conn->query($sql);
    $services = [];
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $services[] = $row;
        }
    }
}

// Handle service deletion
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    
    // Get service details to delete image
    $service = getRecordById('services', $id);
    
    if ($service) {
        // Delete the service image if it exists
        if (!empty($service['image'])) {
            deleteFile($service['image']);
        }
        
        // Delete the service
        if (deleteRecord('services', $id)) {
            $message = displaySuccess("Service deleted successfully!");
        } else {
            $message = displayError("Error deleting service.");
        }
        
        // Refresh services list
        $result = $conn->query($sql);
        $services = [];
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $services[] = $row;
            }
        }
    } else {
        $message = displayError("Service not found.");
    }
}

// Message to display (if any)
$message = $message ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Services - <?php echo $settings['site_name'] ?? 'Future Hope'; ?> Admin</title>
    
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
        
        .service-image {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">
                <?php echo $settings['site_name'] ?? 'Future Hope'; ?> Admin
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
                <a class="nav-link active" href="services.php">
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
                    <?php $unread_messages = countUnreadMessages(); if ($unread_messages > 0): ?>
                    <span class="badge bg-danger rounded-pill ms-1"><?php echo $unread_messages; ?></span>
                    <?php endif; ?>
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
                            <h1 class="mb-0">Manage Services</h1>
                            <p class="text-muted">View, add, edit or delete services</p>
                        </div>
                        <div>
                            <a href="add-service.php" class="btn btn-primary">
                                <i class="fas fa-plus-circle"></i> Add New Service
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php echo $message; ?>
            
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="services-table" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th width="70">Image</th>
                                            <th>Title</th>
                                            <th>Description</th>
                                            <th width="80">Order</th>
                                            <th width="100">Status</th>
                                            <th width="150">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($services)): foreach ($services as $service): ?>
                                        <tr>
                                            <td>
                                                <?php if (!empty($service['image'])): ?>
                                                <img src="../<?php echo $service['image']; ?>" alt="<?php echo $service['title']; ?>" class="service-image">
                                                <?php else: ?>
                                                <div class="text-center">
                                                    <i class="<?php echo $service['icon'] ?: 'fas fa-hands-helping'; ?> fa-2x text-muted"></i>
                                                </div>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo $service['title']; ?></td>
                                            <td><?php echo truncateText($service['description'], 100); ?></td>
                                            <td><?php echo $service['order_number']; ?></td>
                                            <td>
                                                <?php if ($service['is_active']): ?>
                                                <span class="badge bg-success">Active</span>
                                                <?php else: ?>
                                                <span class="badge bg-danger">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="edit-service.php?id=<?php echo $service['id']; ?>" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="services.php?action=toggle&id=<?php echo $service['id']; ?>" class="btn btn-sm btn-warning ms-1">
                                                    <i class="fas fa-power-off"></i>
                                                </a>
                                                <a href="javascript:void(0);" onclick="confirmDelete(<?php echo $service['id']; ?>)" class="btn btn-sm btn-danger ms-1">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No services found.</td>
                                        </tr>
                                        <?php endif; ?>
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
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this service? This action cannot be undone.
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
            $('#services-table').DataTable({
                ordering: true,
                paging: true,
                lengthChange: false,
                pageLength: 10,
                info: true,
                searching: true
            });
        });
        
        // Delete confirmation
        function confirmDelete(id) {
            var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            document.getElementById('confirmDeleteBtn').href = 'services.php?action=delete&id=' + id;
            modal.show();
        }
    </script>
</body>
</html>
