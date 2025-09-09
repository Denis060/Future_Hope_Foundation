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

// Get all users
$sql = "SELECT * FROM users ORDER BY id ASC";
$result = $conn->query($sql);
$users = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

// Handle session messages
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $message_type = $_SESSION['message_type'] ?? 'success';
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}

// Handle form submission for adding/editing user
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        // Add new user
        if ($_POST['action'] === 'add') {
            $username = $conn->real_escape_string($_POST['username']);
            $email = $conn->real_escape_string($_POST['email']);
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];
            
            // Validate inputs
            if (empty($username) || empty($email) || empty($password)) {
                $_SESSION['message'] = "All fields are required";
                $_SESSION['message_type'] = "danger";
            } else if ($password !== $confirm_password) {
                $_SESSION['message'] = "Passwords do not match";
                $_SESSION['message_type'] = "danger";
            } else {
                // Check if username or email already exists
                $check_sql = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
                $check_result = $conn->query($check_sql);
                
                if ($check_result->num_rows > 0) {
                    $_SESSION['message'] = "Username or email already exists";
                    $_SESSION['message_type'] = "danger";
                } else {
                    // Hash password
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    
                    // Insert new user
                    $insert_sql = "INSERT INTO users (username, password, email) VALUES ('$username', '$hashed_password', '$email')";
                    
                    if ($conn->query($insert_sql) === TRUE) {
                        $_SESSION['message'] = "User added successfully!";
                        $_SESSION['message_type'] = "success";
                    } else {
                        $_SESSION['message'] = "Error adding user: " . $conn->error;
                        $_SESSION['message_type'] = "danger";
                    }
                }
            }
            
            // Redirect to refresh page and prevent form resubmission
            header("Location: users.php");
            exit;
        }
        // Update user
        else if ($_POST['action'] === 'edit' && isset($_POST['user_id'])) {
            $user_id = (int) $_POST['user_id'];
            $username = $conn->real_escape_string($_POST['username']);
            $email = $conn->real_escape_string($_POST['email']);
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];
            
            // Check if username or email already exists for other users
            $check_sql = "SELECT * FROM users WHERE (username = '$username' OR email = '$email') AND id != $user_id";
            $check_result = $conn->query($check_sql);
            
            if ($check_result->num_rows > 0) {
                $_SESSION['message'] = "Username or email already exists";
                $_SESSION['message_type'] = "danger";
            } else {
                // Update user info
                if (!empty($password)) {
                    // Validate password
                    if ($password !== $confirm_password) {
                        $_SESSION['message'] = "Passwords do not match";
                        $_SESSION['message_type'] = "danger";
                    } else {
                        // Hash password and update
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        $update_sql = "UPDATE users SET username = '$username', email = '$email', password = '$hashed_password' WHERE id = $user_id";
                        
                        if ($conn->query($update_sql) === TRUE) {
                            $_SESSION['message'] = "User updated successfully!";
                            $_SESSION['message_type'] = "success";
                        } else {
                            $_SESSION['message'] = "Error updating user: " . $conn->error;
                            $_SESSION['message_type'] = "danger";
                        }
                    }
                } else {
                    // Update without changing password
                    $update_sql = "UPDATE users SET username = '$username', email = '$email' WHERE id = $user_id";
                    
                    if ($conn->query($update_sql) === TRUE) {
                        $_SESSION['message'] = "User updated successfully!";
                        $_SESSION['message_type'] = "success";
                    } else {
                        $_SESSION['message'] = "Error updating user: " . $conn->error;
                        $_SESSION['message_type'] = "danger";
                    }
                }
            }
            
            // Redirect to refresh page and prevent form resubmission
            header("Location: users.php");
            exit;
        }
    }
}

// Handle user deletion
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $user_id = (int) $_GET['id'];
    
    // Prevent deleting self
    if ($_SESSION['user_id'] === $user_id) {
        $_SESSION['message'] = "You cannot delete your own account";
        $_SESSION['message_type'] = "danger";
    } else {
        $delete_sql = "DELETE FROM users WHERE id = $user_id";
        
        if ($conn->query($delete_sql) === TRUE) {
            $_SESSION['message'] = "User deleted successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error deleting user: " . $conn->error;
            $_SESSION['message_type'] = "danger";
        }
    }
    
    // Redirect to remove the action from URL
    header("Location: users.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - <?php echo $settings['site_name'] ?? 'Future Hope Foundation'; ?> Admin</title>
    
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
                <a class="nav-link active" href="users.php">
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
                        <h1 class="page-title">Manage Users</h1>
                        <p class="text-muted">View, add, edit or delete system users</p>
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                            <i class="fas fa-plus-circle me-2"></i>Add New User
                        </button>
                    </div>
                </div>
            </div>
            
            <?php if(isset($message)): ?>
                <div class="alert alert-<?= $message_type; ?> alert-dismissible fade show" role="alert">
                    <?= $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0"><i class="fas fa-users me-2"></i> User Management</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="usersTable" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td><?php echo $user['id']; ?></td>
                                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                                            <td><?php echo formatDate($user['created_at']); ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-sm btn-primary edit-user" 
                                                        data-id="<?php echo $user['id']; ?>"
                                                        data-username="<?php echo htmlspecialchars($user['username']); ?>"
                                                        data-email="<?php echo htmlspecialchars($user['email']); ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    
                                                    <?php if ($_SESSION['user_id'] != $user['id']): ?>
                                                    <button type="button" class="btn btn-sm btn-danger delete-user" 
                                                        data-id="<?php echo $user['id']; ?>"
                                                        data-username="<?php echo htmlspecialchars($user['username']); ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                    <?php else: ?>
                                                    <button type="button" class="btn btn-sm btn-secondary" disabled title="Cannot delete your own account">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                    <?php endif; ?>
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
    
    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="users.php" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="users.php" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="user_id" id="edit_user_id">
                        
                        <div class="mb-3">
                            <label for="edit_username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="edit_username" name="username" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="edit_email" name="email" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_password" class="form-label">New Password (leave blank to keep current)</label>
                            <input type="password" class="form-control" id="edit_password" name="password">
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_confirm_password" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="edit_confirm_password" name="confirm_password">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Delete User Confirmation Modal -->
    <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteUserModalLabel">Confirm User Deletion</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this user? This action cannot be undone.</p>
                    <p class="mb-0"><strong>Username: </strong><span id="deleteUsername"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Delete User</a>
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
            $('#usersTable').DataTable();
            
            // Handle Edit User Button Click
            $('.edit-user').click(function() {
                var userId = $(this).data('id');
                var username = $(this).data('username');
                var email = $(this).data('email');
                
                $('#edit_user_id').val(userId);
                $('#edit_username').val(username);
                $('#edit_email').val(email);
                $('#edit_password').val('');
                $('#edit_confirm_password').val('');
                
                $('#editUserModal').modal('show');
            });
            
            // Handle Delete User Button Click
            $('.delete-user').click(function() {
                var userId = $(this).data('id');
                var username = $(this).data('username');
                
                $('#deleteUsername').text(username);
                $('#confirmDeleteBtn').attr('href', 'users.php?action=delete&id=' + userId);
                
                var deleteModal = new bootstrap.Modal(document.getElementById('deleteUserModal'));
                deleteModal.show();
            });
        });
    </script>
</body>
</html>
