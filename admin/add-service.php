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

// Initialize variables
$title = '';
$description = '';
$icon = '';
$order_number = 0;
$is_active = 1;
$message = '';

// Get the latest order number
$result = $conn->query("SELECT MAX(order_number) as max_order FROM services");
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $order_number = $row['max_order'] + 1;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $title = sanitizeInput($_POST['title']);
    $description = $_POST['description'];
    $icon = sanitizeInput($_POST['icon']);
    $order_number = (int) $_POST['order_number'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Validate required fields
    if (empty($title)) {
        $message = displayError("Title is required.");
    } elseif (empty($description)) {
        $message = displayError("Description is required.");
    } else {
        // Upload image if provided
        $image_path = '';
        if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
            $upload_result = uploadImage($_FILES['image'], '../uploads/services/');
            
            if ($upload_result['success']) {
                $image_path = $upload_result['path'];
            } else {
                $message = displayError($upload_result['message']);
            }
        }
        
        // If no image upload errors, insert the service
        if (empty($message)) {
            $sql = "INSERT INTO services (title, description, image, icon, order_number, is_active, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssii", $title, $description, $image_path, $icon, $order_number, $is_active);
            
            if ($stmt->execute()) {
                // Redirect to services page with success message
                $_SESSION['success_message'] = "Service added successfully!";
                redirect('services.php');
            } else {
                $message = displayError("Error adding service: " . $conn->error);
            }
            
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Service - <?php echo $settings['site_name'] ?? 'Future Hope'; ?> Admin</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    
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
        
        .icon-picker-container {
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            padding: 10px;
            margin-top: 10px;
        }
        
        .icon-item {
            display: inline-block;
            padding: 10px;
            cursor: pointer;
            font-size: 18px;
            text-align: center;
            width: 50px;
            height: 50px;
            margin: 5px;
            border-radius: 4px;
            transition: all 0.2s;
        }
        
        .icon-item:hover {
            background-color: #e9ecef;
        }
        
        .icon-item.selected {
            background-color: #3498db;
            color: white;
        }
        
        .image-preview {
            max-width: 200px;
            max-height: 150px;
            display: none;
            margin-top: 10px;
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
            <div class="row mb-4">
                <div class="col-12">
                    <!-- Page Title -->
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="mb-0">Add New Service</h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="services.php">Services</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Add New Service</li>
                                </ol>
                            </nav>
                        </div>
                        <div>
                            <a href="services.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Services
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
                            <form action="add-service.php" method="post" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label for="title" class="form-label">Service Title <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="title" name="title" value="<?php echo $title; ?>" required>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                                            <textarea class="form-control summernote" id="description" name="description" rows="5" required><?php echo $description; ?></textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="image" class="form-label">Service Image (Optional)</label>
                                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                            <div class="form-text">Recommended size: 800x600 pixels</div>
                                            <img id="imagePreview" src="#" alt="Image Preview" class="image-preview">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="icon" class="form-label">Icon (Optional)</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i id="selectedIcon" class="fas fa-hands-helping"></i></span>
                                                <input type="text" class="form-control" id="icon" name="icon" value="<?php echo $icon; ?>" placeholder="fas fa-hands-helping">
                                                <button type="button" class="btn btn-outline-secondary" id="iconPickerBtn">
                                                    <i class="fas fa-icons"></i> Select Icon
                                                </button>
                                            </div>
                                            <div class="form-text">Use Font Awesome icon classes (e.g., fas fa-hands-helping)</div>
                                        </div>
                                        
                                        <div id="iconPicker" class="icon-picker-container" style="display: none;">
                                            <div class="row">
                                                <div class="col-12 mb-2">
                                                    <input type="text" class="form-control" id="iconSearch" placeholder="Search icons...">
                                                </div>
                                            </div>
                                            <div id="iconContainer">
                                                <!-- Common Font Awesome icons will be loaded here -->
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="order_number" class="form-label">Order Number</label>
                                            <input type="number" class="form-control" id="order_number" name="order_number" value="<?php echo $order_number; ?>" min="0">
                                        </div>
                                        
                                        <div class="mb-3 form-check">
                                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" <?php echo $is_active ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="is_active">Active</label>
                                            <div class="form-text">Uncheck this to hide the service on the website</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="border-top pt-3 mt-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save Service
                                    </button>
                                    <a href="services.php" class="btn btn-secondary ms-2">Cancel</a>
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
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Check if we need to redirect from quick actions
            if (localStorage.getItem('openAddModal') === 'true') {
                localStorage.removeItem('openAddModal');
                // We're already on the add service page, so no need to redirect or open a modal
            }
            
            // Initialize Summernote
            $('.summernote').summernote({
                height: 200,
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
            
            // Image Preview
            $("#image").change(function() {
                readURL(this);
            });
            
            // Icon preview update
            $("#icon").on('input', function() {
                $("#selectedIcon").attr('class', $(this).val() || 'fas fa-hands-helping');
            });
            
            // Toggle icon picker
            $("#iconPickerBtn").click(function() {
                if ($("#iconPicker").is(":visible")) {
                    $("#iconPicker").hide();
                } else {
                    if ($("#iconContainer").children().length === 0) {
                        loadIcons();
                    }
                    $("#iconPicker").show();
                }
            });
            
            // Icon search
            $("#iconSearch").on('input', function() {
                let searchTerm = $(this).val().toLowerCase();
                $(".icon-item").each(function() {
                    if ($(this).data('icon').toLowerCase().includes(searchTerm)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });
        });
        
        // Function to preview image
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                
                reader.onload = function(e) {
                    $('#imagePreview').attr('src', e.target.result);
                    $('#imagePreview').css('display', 'block');
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        // Function to load common Font Awesome icons
        function loadIcons() {
            const commonIcons = [
                'fas fa-hands-helping', 'fas fa-heart', 'fas fa-house', 'fas fa-users', 
                'fas fa-graduation-cap', 'fas fa-seedling', 'fas fa-book', 'fas fa-hand-holding-heart', 
                'fas fa-hand-holding-usd', 'fas fa-medkit', 'fas fa-baby', 'fas fa-apple-alt', 
                'fas fa-bread-slice', 'fas fa-hamburger', 'fas fa-utensils', 'fas fa-water', 
                'fas fa-tshirt', 'fas fa-home', 'fas fa-book-open', 'fas fa-bible', 
                'fas fa-school', 'fas fa-hospital', 'fas fa-clinic-medical', 'fas fa-briefcase-medical', 
                'fas fa-donate', 'fas fa-praying-hands', 'fas fa-church', 'fas fa-cross', 
                'fas fa-stethoscope', 'fas fa-heartbeat', 'fas fa-brain', 'fas fa-wheelchair', 
                'fas fa-deaf', 'fas fa-blind', 'fas fa-child', 'fas fa-female', 
                'fas fa-male', 'fas fa-hands', 'fas fa-handshake', 'fas fa-globe', 
                'fas fa-globe-americas', 'fas fa-globe-asia', 'fas fa-globe-europe', 'fas fa-globe-africa', 
                'fas fa-tree', 'fas fa-leaf', 'fas fa-recycle', 'fas fa-solar-panel',
                'fas fa-lightbulb', 'fas fa-shower', 'fas fa-faucet', 'fas fa-toilet', 
                'fas fa-pump-soap', 'fas fa-hands-wash', 'fas fa-microscope', 'fas fa-atom'
            ];
            
            let container = $("#iconContainer");
            commonIcons.forEach(icon => {
                let iconItem = $('<div class="icon-item" data-icon="' + icon + '"><i class="' + icon + '"></i></div>');
                iconItem.click(function() {
                    $(".icon-item").removeClass("selected");
                    $(this).addClass("selected");
                    $("#icon").val($(this).data('icon'));
                    $("#selectedIcon").attr('class', $(this).data('icon'));
                    $("#iconPicker").hide();
                });
                container.append(iconItem);
            });
        }
    </script>
</body>
</html>
