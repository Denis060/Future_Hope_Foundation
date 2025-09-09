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

// Process form submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $site_name = sanitize($conn, $_POST['site_name']);
    $site_email = sanitize($conn, $_POST['site_email']);
    $site_phone = sanitize($conn, $_POST['site_phone']);
    $site_address = sanitize($conn, $_POST['site_address']);
    $facebook_url = sanitize($conn, $_POST['facebook_url']);
    $twitter_url = sanitize($conn, $_POST['twitter_url']);
    $instagram_url = sanitize($conn, $_POST['instagram_url']);
    $youtube_url = sanitize($conn, $_POST['youtube_url']);
    $mission_statement = sanitize($conn, $_POST['mission_statement']);
    $vision_statement = sanitize($conn, $_POST['vision_statement']);
    $about_content = sanitize($conn, $_POST['about_content']);
    
    // Check if a new logo was uploaded
    $site_logo = $settings['site_logo'];
    if (isset($_FILES['site_logo']) && $_FILES['site_logo']['error'] === 0) {
        // Upload new logo
        $upload = uploadFile($_FILES['site_logo'], 'images', ['jpg', 'jpeg', 'png', 'gif']);
        if ($upload) {
            // Delete old logo if it exists
            if (!empty($settings['site_logo'])) {
                deleteFile($settings['site_logo']);
            }
            $site_logo = $upload;
        }
    }
    
    // Update settings in database
    $sql = "UPDATE settings SET 
            site_name = '$site_name',
            site_logo = '$site_logo',
            site_email = '$site_email',
            site_phone = '$site_phone',
            site_address = '$site_address',
            facebook_url = '$facebook_url',
            twitter_url = '$twitter_url',
            instagram_url = '$instagram_url',
            youtube_url = '$youtube_url',
            mission_statement = '$mission_statement',
            vision_statement = '$vision_statement',
            about_content = '$about_content'
            WHERE id = " . $settings['id'];
    
    if ($conn->query($sql)) {
        $message = displaySuccess("Settings updated successfully!");
        // Refresh settings
        $settings = getSettings($conn);
    } else {
        $message = displayError("Error updating settings: " . $conn->error);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Settings - <?php echo $settings['site_name'] ?? 'Future Hope'; ?> Admin</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <!-- Summernote CSS -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    
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
        
        .logo-preview {
            max-width: 200px;
            max-height: 100px;
            margin-top: 10px;
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
                <a class="nav-link active" href="settings.php">
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
                    <div class="content-title">
                        <h1>Site Settings</h1>
                        <p class="text-muted">Manage your website general settings</p>
                    </div>
                </div>
            </div>
            
            <?php echo $message; ?>
            
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <form action="" method="POST" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-4">
                                            <label for="site_name" class="form-label">Site Name *</label>
                                            <input type="text" name="site_name" id="site_name" class="form-control" value="<?php echo $settings['site_name'] ?? 'Future Hope'; ?>" required>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-6">
                                        <div class="mb-4">
                                            <label for="site_logo" class="form-label">Site Logo</label>
                                            <input type="file" name="site_logo" id="site_logo" class="form-control" accept="image/*">
                                            <small class="text-muted">Recommended size: 200x80 pixels</small>
                                            <?php if (!empty($settings['site_logo'])): ?>
                                            <div class="mt-2">
                                                <img src="../<?php echo $settings['site_logo']; ?>" alt="Current Logo" class="logo-preview">
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-4">
                                            <label for="site_email" class="form-label">Email Address *</label>
                                            <input type="email" name="site_email" id="site_email" class="form-control" value="<?php echo $settings['site_email'] ?? ''; ?>" required>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-6">
                                        <div class="mb-4">
                                            <label for="site_phone" class="form-label">Phone Number</label>
                                            <input type="text" name="site_phone" id="site_phone" class="form-control" value="<?php echo $settings['site_phone'] ?? ''; ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="site_address" class="form-label">Address</label>
                                    <textarea name="site_address" id="site_address" class="form-control" rows="3"><?php echo $settings['site_address'] ?? ''; ?></textarea>
                                </div>
                                
                                <h4 class="mt-4 mb-3">Social Media Links</h4>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-4">
                                            <label for="facebook_url" class="form-label">Facebook URL</label>
                                            <input type="url" name="facebook_url" id="facebook_url" class="form-control" value="<?php echo $settings['facebook_url'] ?? ''; ?>">
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-6">
                                        <div class="mb-4">
                                            <label for="twitter_url" class="form-label">Twitter URL</label>
                                            <input type="url" name="twitter_url" id="twitter_url" class="form-control" value="<?php echo $settings['twitter_url'] ?? ''; ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-4">
                                            <label for="instagram_url" class="form-label">Instagram URL</label>
                                            <input type="url" name="instagram_url" id="instagram_url" class="form-control" value="<?php echo $settings['instagram_url'] ?? ''; ?>">
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-6">
                                        <div class="mb-4">
                                            <label for="youtube_url" class="form-label">YouTube URL</label>
                                            <input type="url" name="youtube_url" id="youtube_url" class="form-control" value="<?php echo $settings['youtube_url'] ?? ''; ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <h4 class="mt-4 mb-3">Organization Information</h4>
                                <div class="mb-4">
                                    <label for="mission_statement" class="form-label">Mission Statement</label>
                                    <textarea name="mission_statement" id="mission_statement" class="form-control" rows="4"><?php echo $settings['mission_statement'] ?? ''; ?></textarea>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="vision_statement" class="form-label">Vision Statement</label>
                                    <textarea name="vision_statement" id="vision_statement" class="form-control" rows="4"><?php echo $settings['vision_statement'] ?? ''; ?></textarea>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="about_content" class="form-label">About Content</label>
                                    <textarea name="about_content" id="about_content" class="form-control summernote"><?php echo $settings['about_content'] ?? ''; ?></textarea>
                                    <small class="text-muted">This content will be displayed on the About page.</small>
                                </div>
                                
                                <div class="text-end mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Save Settings
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Summernote JS -->
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize Summernote
            $('.summernote').summernote({
                placeholder: 'Enter detailed content here...',
                tabsize: 2,
                height: 300,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });
        });
    </script>
</body>
</html>
