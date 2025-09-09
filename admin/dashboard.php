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

// Get current user
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = $user_id LIMIT 1";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

// Get settings
$settings = getSettings($conn);

// Get stats
$total_services = $conn->query("SELECT COUNT(*) as count FROM services")->fetch_assoc()['count'];
$total_projects = $conn->query("SELECT COUNT(*) as count FROM projects")->fetch_assoc()['count'];
$total_events = $conn->query("SELECT COUNT(*) as count FROM events")->fetch_assoc()['count'];
$total_team = $conn->query("SELECT COUNT(*) as count FROM team_members")->fetch_assoc()['count'];
$total_testimonials = $conn->query("SELECT COUNT(*) as count FROM testimonials")->fetch_assoc()['count'];
$total_gallery = $conn->query("SELECT COUNT(*) as count FROM gallery")->fetch_assoc()['count'];
$unread_messages = countUnreadMessages();
$total_donations = getTotalDonations();

// Get recent messages
$recent_messages = getContactMessages(5, 'unread');

// Get recent donations
$recent_donations = getDonations(5);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo $settings['site_name'] ?? 'Future Hope'; ?> Admin</title>
    
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
        
        .card-stats {
            transition: all 0.3s;
        }
        
        .card-stats:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
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
                            <i class="fas fa-user-circle"></i> <?php echo $user['username']; ?>
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
                <a class="nav-link active" href="dashboard.php">
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
                    <span class="badge bg-success rounded-pill ms-1">New</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="messages.php">
                    <i class="fas fa-envelope"></i>
                    <span>Messages</span>
                    <?php if ($unread_messages > 0): ?>
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
            <!-- Welcome Alert -->
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <h4 class="alert-heading"><i class="fas fa-smile me-2"></i> Welcome back, <?php echo $user['username']; ?>!</h4>
                <p>You can manage all aspects of your website from this dashboard.</p>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            
            <!-- Stats Cards -->
            <div class="row">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card card-stats border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon bg-primary text-white me-3">
                                    <i class="fas fa-hands-helping"></i>
                                </div>
                                <div>
                                    <h5 class="card-title mb-0">Services</h5>
                                    <h2 class="mb-0"><?php echo $total_services; ?></h2>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="services.php" class="btn btn-sm btn-primary">Manage Services</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card card-stats border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon bg-success text-white me-3">
                                    <i class="fas fa-project-diagram"></i>
                                </div>
                                <div>
                                    <h5 class="card-title mb-0">Projects</h5>
                                    <h2 class="mb-0"><?php echo $total_projects; ?></h2>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="projects.php" class="btn btn-sm btn-success">Manage Projects</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card card-stats border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon bg-info text-white me-3">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <div>
                                    <h5 class="card-title mb-0">Events</h5>
                                    <h2 class="mb-0"><?php echo $total_events; ?></h2>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="events.php" class="btn btn-sm btn-info">Manage Events</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card card-stats border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon bg-warning text-white me-3">
                                    <i class="fas fa-donate"></i>
                                </div>
                                <div>
                                    <h5 class="card-title mb-0">Donations</h5>
                                    <h2 class="mb-0">$<?php echo number_format($total_donations, 0); ?></h2>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="donations.php" class="btn btn-sm btn-warning">View Donations</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card card-stats border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon bg-danger text-white me-3">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div>
                                    <h5 class="card-title mb-0">Unread Messages</h5>
                                    <h2 class="mb-0"><?php echo $unread_messages; ?></h2>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="messages.php" class="btn btn-sm btn-danger">View Messages</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card card-stats border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon bg-secondary text-white me-3">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div>
                                    <h5 class="card-title mb-0">Team Members</h5>
                                    <h2 class="mb-0"><?php echo $total_team; ?></h2>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="team.php" class="btn btn-sm btn-secondary">Manage Team</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card card-stats border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon bg-dark text-white me-3">
                                    <i class="fas fa-quote-left"></i>
                                </div>
                                <div>
                                    <h5 class="card-title mb-0">Testimonials</h5>
                                    <h2 class="mb-0"><?php echo $total_testimonials; ?></h2>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="testimonials.php" class="btn btn-sm btn-dark">Manage Testimonials</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card card-stats border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon bg-purple text-white me-3" style="background-color: #8e44ad;">
                                    <i class="fas fa-photo-video"></i>
                                </div>
                                <div>
                                    <h5 class="card-title mb-0">Gallery Items</h5>
                                    <h2 class="mb-0"><?php echo $total_gallery; ?></h2>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="gallery.php" class="btn btn-sm text-white" style="background-color: #8e44ad;">Manage Gallery</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <!-- Recent Messages -->
                <div class="col-lg-6 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="fas fa-envelope me-2"></i> Recent Messages</h5>
                                <a href="messages.php" class="btn btn-sm btn-primary">View All</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Subject</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($recent_messages): foreach ($recent_messages as $message): ?>
                                        <tr>
                                            <td><?php echo $message['name']; ?></td>
                                            <td><?php echo $message['email']; ?></td>
                                            <td><?php echo truncateText($message['subject'] ?: 'No Subject', 30); ?></td>
                                            <td><?php echo formatDate($message['created_at']); ?></td>
                                        </tr>
                                        <?php endforeach; else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center">No unread messages</td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Donations -->
                <div class="col-lg-6 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="fas fa-donate me-2"></i> Recent Donations</h5>
                                <a href="donations.php" class="btn btn-sm btn-primary">View All</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Donor</th>
                                            <th>Amount</th>
                                            <th>Campaign</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($recent_donations): foreach ($recent_donations as $donation): ?>
                                        <tr>
                                            <td><?php echo $donation['is_anonymous'] ? 'Anonymous' : $donation['donor_name']; ?></td>
                                            <td>$<?php echo number_format($donation['amount'], 2); ?></td>
                                            <td><?php echo truncateText($donation['campaign'] ?: 'General', 30); ?></td>
                                            <td><?php echo formatDate($donation['created_at']); ?></td>
                                        </tr>
                                        <?php endforeach; else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center">No recent donations</td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Links -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="fas fa-link me-2"></i> Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-2 col-sm-4 mb-3">
                                    <a href="services.php" class="btn btn-light w-100 py-4 h-100" onclick="localStorage.setItem('openAddModal', 'true');">
                                        <i class="fas fa-plus-circle fa-2x d-block mb-2"></i>
                                        Add Service
                                    </a>
                                </div>
                                <div class="col-md-2 col-sm-4 mb-3">
                                    <a href="projects.php" class="btn btn-light w-100 py-4 h-100" onclick="localStorage.setItem('openAddModal', 'true');">
                                        <i class="fas fa-plus-circle fa-2x d-block mb-2"></i>
                                        Add Project
                                    </a>
                                </div>
                                <div class="col-md-2 col-sm-4 mb-3">
                                    <a href="events.php" class="btn btn-light w-100 py-4 h-100" onclick="localStorage.setItem('openAddModal', 'true');">
                                        <i class="fas fa-plus-circle fa-2x d-block mb-2"></i>
                                        Add Event
                                    </a>
                                </div>
                                <div class="col-md-2 col-sm-4 mb-3">
                                    <a href="team.php" class="btn btn-light w-100 py-4 h-100" onclick="localStorage.setItem('openAddModal', 'true');">
                                        <i class="fas fa-plus-circle fa-2x d-block mb-2"></i>
                                        Add Team Member
                                    </a>
                                </div>
                                <div class="col-md-2 col-sm-4 mb-3">
                                    <a href="testimonials.php" class="btn btn-light w-100 py-4 h-100" onclick="localStorage.setItem('openAddModal', 'true');">
                                        <i class="fas fa-plus-circle fa-2x d-block mb-2"></i>
                                        Add Testimonial
                                    </a>
                                </div>
                                <div class="col-md-2 col-sm-4 mb-3">
                                    <a href="gallery.php" class="btn btn-light w-100 py-4 h-100" onclick="localStorage.setItem('openAddModal', 'true');">
                                        <i class="fas fa-plus-circle fa-2x d-block mb-2"></i>
                                        Add Gallery Item
                                    </a>
                                </div>
                            </div>
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
    
    <!-- Datatables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // Initialize DataTables
        $(document).ready(function() {
            // Any custom JS can be added here
        });
    </script>
</body>
</html>
