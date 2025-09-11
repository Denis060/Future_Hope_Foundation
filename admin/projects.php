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

// Get all projects
$sql = "SELECT * FROM projects ORDER BY status, start_date DESC";
$result = $conn->query($sql);
$projects = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $projects[] = $row;
    }
}

// Message to display (if any)
$message = $message ?? '';

// Handle project status toggle
if (isset($_GET['action']) && $_GET['action'] === 'toggle' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    
    if (toggleActiveStatus('projects', $id)) {
        $_SESSION['message'] = "Project status updated successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error updating project status.";
        $_SESSION['message_type'] = "danger";
    }
    
    // Redirect to remove the action from URL
    header("Location: projects.php");
    exit;
}

// Handle project deletion
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    
    // Check if image exists and delete it
    $image_result = $conn->query("SELECT image FROM projects WHERE id = $id");
    if ($image_result->num_rows > 0) {
        $image = $image_result->fetch_assoc()['image'];
        if ($image && file_exists("../uploads/$image")) {
            unlink("../uploads/$image");
        }
    }
    
    $delete_sql = "DELETE FROM projects WHERE id = $id";
    
    if ($conn->query($delete_sql) === TRUE) {
        $_SESSION['message'] = "Project deleted successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error deleting project: " . $conn->error;
        $_SESSION['message_type'] = "danger";
    }
    
    // Redirect to remove the action from URL
    header("Location: projects.php");
    exit;
}

// Handle project creation/update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $start_date = $conn->real_escape_string($_POST['start_date']);
    $end_date = isset($_POST['end_date']) && !empty($_POST['end_date']) ? "'" . $conn->real_escape_string($_POST['end_date']) . "'" : "NULL";
    $status = $conn->real_escape_string($_POST['status']);
    $goal_amount = !empty($_POST['goal_amount']) ? (float) $_POST['goal_amount'] : "NULL";
    $raised_amount = !empty($_POST['raised_amount']) ? (float) $_POST['raised_amount'] : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $image_name = "";
    
    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $file_ext = pathinfo($filename, PATHINFO_EXTENSION);
        
        if (in_array(strtolower($file_ext), $allowed)) {
            $new_name = 'project_' . time() . '.' . $file_ext;
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
                    $old_image_result = $conn->query("SELECT image FROM projects WHERE id = $id");
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
        // Update existing project
        $sql = "UPDATE projects SET 
                title = '$title', 
                description = '$description', 
                start_date = '$start_date', 
                end_date = $end_date, 
                status = '$status', 
                goal_amount = $goal_amount, 
                raised_amount = $raised_amount, 
                is_active = $is_active";
                
        if ($image_name) {
            $sql .= ", image = '$image_name'";
        }
        
        $sql .= " WHERE id = $id";
        
        if ($conn->query($sql) === TRUE) {
            $_SESSION['message'] = "Project updated successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error updating project: " . $conn->error;
            $_SESSION['message_type'] = "danger";
        }
        
        // Redirect to refresh page and prevent form resubmission
        header("Location: projects.php");
        exit;
    } else {
        // Create new project
        $sql = "INSERT INTO projects (title, description, start_date, end_date, status, image, goal_amount, raised_amount, is_active) 
                VALUES ('$title', '$description', '$start_date', $end_date, '$status', '$image_name', $goal_amount, $raised_amount, $is_active)";
        
        if ($conn->query($sql) === TRUE) {
            $_SESSION['message'] = "Project created successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error creating project: " . $conn->error;
            $_SESSION['message_type'] = "danger";
        }
        
        // Redirect to refresh page and prevent form resubmission
        header("Location: projects.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Projects - <?php echo $settings['site_name'] ?? 'Future Hope Foundation'; ?> Admin</title>
    
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
                <a class="nav-link active" href="projects.php">
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
            <div class="row">
                <div class="col-12">
                    <!-- Page Title -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h1 class="mb-0">Manage Projects</h1>
                            <p class="text-muted">View, add, edit or delete foundation projects</p>
                        </div>
                        <div>
                            <button type="button" class="btn btn-primary btn-add-new">
                                <i class="fas fa-plus-circle"></i> Add New Project
                            </button>
                        </div>
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
            
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="projectsTable" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th width="250">Title</th>
                                            <th width="120">Status</th>
                                            <th width="120">Start Date</th>
                                            <th width="120">End Date</th>
                                            <th>Funding</th>
                                            <th width="150">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($projects as $project): ?>
                                        <tr>
                                            <td>
                                                <?php if ($project['image']): ?>
                                                <img src="../uploads/<?php echo $project['image']; ?>" class="img-thumbnail me-2" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                                <?php endif; ?>
                                                <?php echo htmlspecialchars($project['title']); ?>
                                            </td>
                                            <td>
                                                <?php 
                                                switch($project['status']) {
                                                    case 'upcoming':
                                                        echo '<span class="badge bg-info">Upcoming</span>';
                                                        break;
                                                    case 'ongoing':
                                                        echo '<span class="badge bg-primary">Ongoing</span>';
                                                        break;
                                                    case 'completed':
                                                        echo '<span class="badge bg-success">Completed</span>';
                                                        break;
                                                    default:
                                                        echo '<span class="badge bg-secondary">Unknown</span>';
                                                }
                                                
                                                if (!$project['is_active']) {
                                                    echo ' <span class="badge bg-danger">Inactive</span>';
                                                }
                                                ?>
                                            </td>
                                            <td><?php echo formatDate($project['start_date']); ?></td>
                                            <td><?php echo $project['end_date'] ? formatDate($project['end_date']) : 'N/A'; ?></td>
                                            <td>
                                                <?php if ($project['goal_amount']): ?>
                                                    $<?php echo number_format($project['goal_amount'], 2); ?>
                                                    <div class="progress mt-1" style="height: 5px;">
                                                        <?php 
                                                        $percentage = 0;
                                                        if ($project['goal_amount'] > 0) {
                                                            $percentage = min(100, ($project['raised_amount'] / $project['goal_amount']) * 100);
                                                        }
                                                        ?>
                                                        <div class="progress-bar" role="progressbar" style="width: <?php echo $percentage; ?>%" aria-valuenow="<?php echo $percentage; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                    <small class="text-muted">Raised: $<?php echo number_format($project['raised_amount'], 2); ?></small>
                                                <?php else: ?>
                                                    N/A
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-primary edit-project" 
                                                    data-id="<?php echo $project['id']; ?>"
                                                    data-title="<?php echo htmlspecialchars($project['title']); ?>"
                                                    data-description="<?php echo htmlspecialchars($project['description']); ?>"
                                                    data-start-date="<?php echo $project['start_date']; ?>"
                                                    data-end-date="<?php echo $project['end_date']; ?>"
                                                    data-status="<?php echo $project['status']; ?>"
                                                    data-goal="<?php echo $project['goal_amount']; ?>"
                                                    data-raised="<?php echo $project['raised_amount']; ?>"
                                                    data-active="<?php echo $project['is_active']; ?>"
                                                    data-image="<?php echo $project['image']; ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <a href="projects.php?action=toggle&id=<?php echo $project['id']; ?>" class="btn btn-sm btn-warning ms-1">
                                                    <i class="fas fa-power-off"></i>
                                                </a>
                                                <a href="javascript:void(0);" onclick="confirmDelete(<?php echo $project['id']; ?>)" class="btn btn-sm btn-danger ms-1">
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
            
            <!-- Add/Edit Project Modal -->
            <div class="modal fade" id="projectModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="formTitle">Add New Project</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="projectForm" action="projects.php" method="post" enctype="multipart/form-data">
                                <input type="hidden" id="project_id" name="id" value="">
                                
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label for="title" class="form-label">Project Title</label>
                                            <input type="text" class="form-control" id="title" name="title" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Status</label>
                                            <select class="form-select" id="status" name="status" required>
                                                <option value="upcoming">Upcoming</option>
                                                <option value="ongoing">Ongoing</option>
                                                <option value="completed">Completed</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="start_date" class="form-label">Start Date</label>
                                            <input type="date" class="form-control" id="start_date" name="start_date" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="end_date" class="form-label">End Date (optional)</label>
                                            <input type="date" class="form-control" id="end_date" name="end_date">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="goal_amount" class="form-label">Goal Amount (optional)</label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" class="form-control" id="goal_amount" name="goal_amount" step="0.01" min="0">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="raised_amount" class="form-label">Raised Amount</label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" class="form-control" id="raised_amount" name="raised_amount" step="0.01" min="0" value="0">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="5"></textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label for="image" class="form-label">Project Image</label>
                                            <input type="file" class="form-control" id="image" name="image">
                                        </div>
                                        <div id="imagePreview" class="mt-2 d-none">
                                            <p>Current image:</p>
                                            <img src="" id="currentImage" class="img-thumbnail" style="max-height: 150px;">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3 form-check mt-4">
                                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" checked>
                                            <label class="form-check-label" for="is_active">Active</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="modal-footer px-0 pb-0">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Save Project</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Delete Confirmation Modal -->
            <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Confirm Delete</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to delete this project? This action cannot be undone.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Delete Project</a>
                        </div>
                    </div>
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
    
    <!-- Summernote JS -->
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#projectsTable').DataTable({
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, 'All']
                ],
                language: {
                    search: "Search:",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    }
                }
            });
            
            // Initialize Summernote
            $('#description').summernote({
                height: 150,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });
            
            // Handle Add New Project Button Click
            $('.btn-add-new').click(function() {
                resetProjectForm();
                $('#projectModal').modal('show');
            });
            
            // Handle Edit Project Button Click
            $('.edit-project').click(function() {
                var id = $(this).data('id');
                var title = $(this).data('title');
                var description = $(this).data('description');
                var startDate = $(this).data('start-date');
                var endDate = $(this).data('end-date');
                var status = $(this).data('status');
                var goal = $(this).data('goal');
                var raised = $(this).data('raised');
                var active = $(this).data('active');
                var image = $(this).data('image');
                
                $('#project_id').val(id);
                $('#title').val(title);
                $('#description').summernote('code', description);
                $('#start_date').val(startDate);
                $('#end_date').val(endDate);
                $('#status').val(status);
                $('#goal_amount').val(goal);
                $('#raised_amount').val(raised);
                $('#is_active').prop('checked', active == 1);
                
                if (image) {
                    $('#imagePreview').removeClass('d-none');
                    $('#currentImage').attr('src', '../uploads/' + image);
                } else {
                    $('#imagePreview').addClass('d-none');
                }
                
                $('#formTitle').text('Edit Project');
                $('#projectModal').modal('show');
            });
            
            // Reset form function
            function resetProjectForm() {
                $('#projectForm')[0].reset();
                $('#project_id').val('');
                $('#description').summernote('code', '');
                $('#imagePreview').addClass('d-none');
                $('#formTitle').text('Add New Project');
            }
            
            // Handle delete confirmation
            window.confirmDelete = function(id) {
                $('#confirmDeleteBtn').attr('href', 'projects.php?action=delete&id=' + id);
                $('#deleteModal').modal('show');
            };
            
            // Check if we need to open the add modal (from quick actions)
            if (localStorage.getItem('openAddModal') === 'true') {
                resetProjectForm();
                $('#projectModal').modal('show');
                localStorage.removeItem('openAddModal');
            }
        });
    </script>
</body>
</html>
