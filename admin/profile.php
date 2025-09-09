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

// Get user information
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = $user_id LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
} else {
    // Handle case where user isn't found (shouldn't happen normally)
    redirect('logout.php');
}

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $full_name = isset($_POST['full_name']) ? $conn->real_escape_string($_POST['full_name']) : '';
    
    // Check if username or email already exists for other users
    $check_sql = "SELECT * FROM users WHERE (username = '$username' OR email = '$email') AND id != $user_id";
    $check_result = $conn->query($check_sql);
    
    if ($check_result->num_rows > 0) {
        $_SESSION['message'] = "Username or email already exists";
        $_SESSION['message_type'] = "danger";
    } else {
        // Update user info
        $update_sql = "UPDATE users SET username = '$username', email = '$email'";
        
        // Add full_name to query if the column exists
        if (isset($_POST['full_name'])) {
            $update_sql .= ", full_name = '$full_name'";
        }
        
        $update_sql .= " WHERE id = $user_id";
        
        if ($conn->query($update_sql) === TRUE) {
            $_SESSION['message'] = "Profile updated successfully!";
            $_SESSION['message_type'] = "success";
            
            // Update session username if changed
            $_SESSION['username'] = $username;
            
            // Refresh user data
            $result = $conn->query("SELECT * FROM users WHERE id = $user_id LIMIT 1");
            $user = $result->fetch_assoc();
        } else {
            $_SESSION['message'] = "Error updating profile: " . $conn->error;
            $_SESSION['message_type'] = "danger";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - <?php echo $settings['site_name'] ?? 'Future Hope Foundation'; ?> Admin</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    
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
                        <a class="nav-link dropdown-toggle active" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle"></i> <?php echo $_SESSION['username']; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item active" href="profile.php"><i class="fas fa-user me-2"></i> Profile</a></li>
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
                        <h1 class="page-title">My Profile</h1>
                        <p class="text-muted">Update your account information</p>
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
            
            <!-- Profile Form -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0"><i class="fas fa-user-edit me-2"></i>Edit Profile</h5>
                        </div>
                        <div class="card-body">
                            <form action="profile.php" method="post">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                </div>
                                
                                <?php if (isset($user['full_name'])): ?>
                                <div class="mb-3">
                                    <label for="full_name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>">
                                </div>
                                <?php endif; ?>
                                
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">Update Profile</button>
                                    <a href="change-password.php" class="btn btn-outline-secondary">Change Password</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0"><i class="fas fa-info-circle me-2"></i>Account Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <div class="avatar avatar-xl">
                                    <span class="avatar-text rounded-circle bg-primary text-white">
                                        <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                                    </span>
                                </div>
                                <h5 class="mt-3 mb-0"><?php echo htmlspecialchars($user['username']); ?></h5>
                                <p class="text-muted"><?php echo htmlspecialchars($user['email']); ?></p>
                            </div>
                            
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>User ID:</span>
                                    <span class="badge bg-light text-dark">#<?php echo $user['id']; ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Account Created:</span>
                                    <span class="badge bg-light text-dark"><?php echo formatDate($user['created_at']); ?></span>
                                </li>
                                <?php if (isset($user['last_login'])): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Last Login:</span>
                                    <span class="badge bg-light text-dark"><?php echo formatDate($user['last_login']); ?></span>
                                </li>
                                <?php endif; ?>
                            </ul>
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
    
    <style>
        .avatar {
            display: inline-block;
            position: relative;
        }
        .avatar-xl {
            width: 80px;
            height: 80px;
            font-size: 2rem;
            display: inline-flex;
            margin: 0 auto;
        }
        .avatar-text {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</body>
</html>
