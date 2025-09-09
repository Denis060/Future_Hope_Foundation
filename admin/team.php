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

// Get all team members
$sql = "SELECT * FROM team ORDER BY order_number ASC";
$result = $conn->query($sql);
$team_members = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $team_members[] = $row;
    }
}

// Message to display (if any)
$message = $message ?? '';

// Handle team member status toggle
if (isset($_GET['action']) && $_GET['action'] === 'toggle' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    
    if (toggleActiveStatus('team', $id)) {
        $message = displaySuccess("Team member status updated successfully!");
    } else {
        $message = displayError("Error updating team member status.");
    }
    
    // Refresh team members list
    $result = $conn->query($sql);
    $team_members = [];
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $team_members[] = $row;
        }
    }
}

// Handle team member deletion
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    
    // Check if image exists and delete it
    $image_result = $conn->query("SELECT image FROM team WHERE id = $id");
    if ($image_result->num_rows > 0) {
        $image = $image_result->fetch_assoc()['image'];
        if ($image && file_exists("../uploads/$image")) {
            unlink("../uploads/$image");
        }
    }
    
    $delete_sql = "DELETE FROM team WHERE id = $id";
    
    if ($conn->query($delete_sql) === TRUE) {
        $message = displaySuccess("Team member deleted successfully!");
    } else {
        $message = displayError("Error deleting team member: " . $conn->error);
    }
    
    // Refresh team members list
    $result = $conn->query($sql);
    $team_members = [];
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $team_members[] = $row;
        }
    }
}

// Handle team member creation/update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $name = $conn->real_escape_string($_POST['name']);
    $position = $conn->real_escape_string($_POST['position']);
    $bio = $conn->real_escape_string($_POST['bio']);
    $order_number = isset($_POST['order_number']) ? (int) $_POST['order_number'] : 0;
    $facebook = $conn->real_escape_string($_POST['facebook'] ?? '');
    $twitter = $conn->real_escape_string($_POST['twitter'] ?? '');
    $instagram = $conn->real_escape_string($_POST['instagram'] ?? '');
    $linkedin = $conn->real_escape_string($_POST['linkedin'] ?? '');
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $image_name = "";
    
    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $file_ext = pathinfo($filename, PATHINFO_EXTENSION);
        
        if (in_array(strtolower($file_ext), $allowed)) {
            $new_name = 'team_' . time() . '.' . $file_ext;
            $destination = '../uploads/' . $new_name;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                $image_name = $new_name;
                
                // If updating, delete old image
                if ($id > 0) {
                    $old_image_result = $conn->query("SELECT image FROM team WHERE id = $id");
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
        // Update existing team member
        $sql = "UPDATE team SET 
                name = '$name', 
                position = '$position', 
                bio = '$bio', 
                order_number = $order_number, 
                facebook = '$facebook',
                twitter = '$twitter',
                instagram = '$instagram',
                linkedin = '$linkedin',
                is_active = $is_active";
                
        if ($image_name) {
            $sql .= ", image = '$image_name'";
        }
        
        $sql .= " WHERE id = $id";
        
        if ($conn->query($sql) === TRUE) {
            $status_message = "Team member updated successfully.";
        } else {
            $error_message = "Error updating team member: " . $conn->error;
        }
    } else {
        // Create new team member
        $sql = "INSERT INTO team (name, position, bio, image, order_number, facebook, twitter, instagram, linkedin, is_active) 
                VALUES ('$name', '$position', '$bio', '$image_name', $order_number, '$facebook', '$twitter', '$instagram', '$linkedin', $is_active)";
        
        if ($conn->query($sql) === TRUE) {
            $status_message = "Team member added successfully.";
        } else {
            $error_message = "Error adding team member: " . $conn->error;
        }
    }
    
    // Refresh team members list
    $result = $conn->query("SELECT * FROM team ORDER BY order_number ASC");
    $team_members = [];
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $team_members[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Team Members - <?php echo $settings['site_name'] ?? 'Future Hope'; ?> Admin</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <!-- Datatables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    
    <!-- Summernote CSS -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    
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
                <a class="nav-link active" href="team.php">
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
                            <h1 class="mb-0">Manage Team Members</h1>
                            <p class="text-muted">View, add, edit or delete team members</p>
                        </div>
                        <div>
                            <button type="button" class="btn btn-primary btn-add-new">
                                <i class="fas fa-plus-circle"></i> Add New Team Member
                            </button>
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
                                <table id="teamTable" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th width="200">Name</th>
                                            <th>Position</th>
                                            <th width="80">Order</th>
                                            <th width="100">Status</th>
                                            <th width="150">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($team_members as $member): ?>
                                        <tr>
                                            <td>
                                                <?php if ($member['image']): ?>
                                                <img src="../uploads/<?php echo $member['image']; ?>" class="img-thumbnail me-2" style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%;">
                                                <?php endif; ?>
                                                <?php echo htmlspecialchars($member['name']); ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($member['position']); ?></td>
                                            <td><?php echo $member['order_number']; ?></td>
                                            <td>
                                                <?php if ($member['is_active']): ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-primary edit-team-member" 
                                                    data-id="<?php echo $member['id']; ?>"
                                                    data-name="<?php echo htmlspecialchars($member['name']); ?>"
                                                    data-position="<?php echo htmlspecialchars($member['position']); ?>"
                                                    data-bio="<?php echo htmlspecialchars($member['bio']); ?>"
                                                    data-order="<?php echo $member['order_number']; ?>"
                                                    data-facebook="<?php echo htmlspecialchars($member['facebook'] ?? ''); ?>"
                                                    data-twitter="<?php echo htmlspecialchars($member['twitter'] ?? ''); ?>"
                                                    data-instagram="<?php echo htmlspecialchars($member['instagram'] ?? ''); ?>"
                                                    data-linkedin="<?php echo htmlspecialchars($member['linkedin'] ?? ''); ?>"
                                                    data-active="<?php echo $member['is_active']; ?>"
                                                    data-image="<?php echo $member['image']; ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <a href="team.php?action=toggle&id=<?php echo $member['id']; ?>" class="btn btn-sm btn-warning ms-1">
                                                    <i class="fas fa-power-off"></i>
                                                </a>
                                                <a href="javascript:void(0);" onclick="confirmDelete(<?php echo $member['id']; ?>)" class="btn btn-sm btn-danger ms-1">
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
            
            <!-- Add/Edit Team Member Form (Hidden initially) -->
            <div class="modal fade" id="teamMemberModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="formTitle">Add New Team Member</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="teamMemberForm" action="team.php" method="post" enctype="multipart/form-data">
                                <input type="hidden" id="team_id" name="id" value="">
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Name</label>
                                            <input type="text" class="form-control" id="name" name="name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="position" class="form-label">Position/Title</label>
                                            <input type="text" class="form-control" id="position" name="position" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="bio" class="form-label">Biography</label>
                                    <textarea class="form-control" id="bio" name="bio" rows="5"></textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="order_number" class="form-label">Display Order</label>
                                            <input type="number" class="form-control" id="order_number" name="order_number" value="0" min="0">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="is_active" class="form-label d-block">Status</label>
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" checked>
                                                <label class="form-check-label" for="is_active">Active</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="image" class="form-label">Photo</label>
                                    <input type="file" class="form-control" id="image" name="image">
                                    <div id="imagePreview" class="mt-2 d-none">
                                        <p>Current image:</p>
                                        <img src="" id="currentImage" class="img-thumbnail" style="max-height: 100px; max-width: 100px;">
                                    </div>
                                </div>
                                
                                <h5 class="border-top pt-3 mt-4">Social Media Links</h5>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="facebook" class="form-label">Facebook</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fab fa-facebook-f"></i></span>
                                                <input type="text" class="form-control" id="facebook" name="facebook" placeholder="Facebook URL">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="twitter" class="form-label">Twitter</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fab fa-twitter"></i></span>
                                                <input type="text" class="form-control" id="twitter" name="twitter" placeholder="Twitter URL">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="instagram" class="form-label">Instagram</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fab fa-instagram"></i></span>
                                                <input type="text" class="form-control" id="instagram" name="instagram" placeholder="Instagram URL">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="linkedin" class="form-label">LinkedIn</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fab fa-linkedin-in"></i></span>
                                                <input type="text" class="form-control" id="linkedin" name="linkedin" placeholder="LinkedIn URL">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="saveTeamMember"><i class="fas fa-save me-1"></i> Save Team Member</button>
                        </div>
                    </div>
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
                    Are you sure you want to delete this team member? This action cannot be undone.
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
    
    <!-- Summernote JS -->
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#teamTable').DataTable({
                "order": [[2, "asc"]], // Order by 'order_number' column
                "language": {
                    "search": "Search:",
                    "lengthMenu": "Show _MENU_ entries",
                    "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                    "infoEmpty": "Showing 0 to 0 of 0 entries",
                    "infoFiltered": "(filtered from _MAX_ total entries)",
                    "paginate": {
                        "first": "First",
                        "last": "Last",
                        "next": "Next",
                        "previous": "Previous"
                    }
                }
            });
            
            // Initialize Summernote editor
            $('#bio').summernote({
                height: 200,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });
            
            // Show modal when Add New Team Member button is clicked
            $('.btn-add-new').click(function() {
                $('#teamMemberForm')[0].reset();
                $('#team_id').val('');
                $('#bio').summernote('code', '');
                $('#imagePreview').addClass('d-none');
                $('#formTitle').text('Add New Team Member');
                $('#teamMemberModal').modal('show');
            });
            
            // Handle Edit Team Member Button Click
            $('.edit-team-member').click(function() {
                var id = $(this).data('id');
                var name = $(this).data('name');
                var position = $(this).data('position');
                var bio = $(this).data('bio');
                var order = $(this).data('order');
                var facebook = $(this).data('facebook');
                var twitter = $(this).data('twitter');
                var instagram = $(this).data('instagram');
                var linkedin = $(this).data('linkedin');
                var active = $(this).data('active');
                var image = $(this).data('image');
                
                $('#team_id').val(id);
                $('#name').val(name);
                $('#position').val(position);
                $('#bio').summernote('code', bio);
                $('#order_number').val(order);
                $('#facebook').val(facebook);
                $('#twitter').val(twitter);
                $('#instagram').val(instagram);
                $('#linkedin').val(linkedin);
                $('#is_active').prop('checked', active == 1);
                
                if (image) {
                    $('#imagePreview').removeClass('d-none');
                    $('#currentImage').attr('src', '../uploads/' + image);
                } else {
                    $('#imagePreview').addClass('d-none');
                }
                
                $('#formTitle').text('Edit Team Member');
                $('#teamMemberModal').modal('show');
            });
            
            // Handle Save Button Click in Modal
            $('#saveTeamMember').click(function() {
                if ($('#name').val() && $('#position').val()) {
                    $('#teamMemberForm').submit();
                } else {
                    alert('Please fill in all required fields.');
                }
            });
            
            // Delete confirmation
            function confirmDelete(id) {
                $('#confirmDeleteBtn').attr('href', 'team.php?action=delete&id=' + id);
                $('#deleteModal').modal('show');
            }
            
            // Make the confirmDelete function globally available
            window.confirmDelete = confirmDelete;
            
            // Check if we need to open the add modal (from quick actions)
            if (localStorage.getItem('openAddModal') === 'true') {
                $('#teamMemberForm')[0].reset();
                $('#team_id').val('');
                $('#bio').summernote('code', '');
                $('#imagePreview').addClass('d-none');
                $('#formTitle').text('Add New Team Member');
                $('#teamMemberModal').modal('show');
                localStorage.removeItem('openAddModal');
            }
        });
    </script>
</body>
</html>
