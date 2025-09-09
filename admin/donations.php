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

// Handle donation deletion
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    $delete_sql = "DELETE FROM donations WHERE id = $delete_id";
    
    if ($conn->query($delete_sql)) {
        $_SESSION['message'] = displaySuccess("Donation record deleted successfully.");
    } else {
        $_SESSION['message'] = displayError("Error deleting donation: " . $conn->error);
    }
    
    // Redirect to remove the action from URL
    header("Location: donations.php");
    exit;
}

// Get all donations
$donations_sql = "SELECT * FROM donations ORDER BY created_at DESC";
$donations_result = $conn->query($donations_sql);
$donations = [];

if ($donations_result && $donations_result->num_rows > 0) {
    while ($row = $donations_result->fetch_assoc()) {
        $donations[] = $row;
    }
}

// Calculate total donations
$total_amount_sql = "SELECT SUM(amount) as total FROM donations";
$total_result = $conn->query($total_amount_sql);
$total_donations = $total_result->fetch_assoc()['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Donations - <?php echo $settings['site_name'] ?? 'Future Hope'; ?> Admin</title>
    
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
                <a class="nav-link active" href="donations.php">
                    <i class="fas fa-donate"></i>
                    <span>Donations</span>
                    <span class="badge bg-success rounded-pill ms-1">New</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="messages.php">
                    <i class="fas fa-envelope"></i>
                    <span>Messages</span>
                    <?php if (isset($unread_messages) && $unread_messages > 0): ?>
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
            <!-- Page Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <h1 class="fs-3 mb-1">Manage Donations</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Donations</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <!-- Success/Error Messages -->
            <?php if (isset($success_message)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $success_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $error_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Donations Stats -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon bg-success text-white me-3">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                                <div>
                                    <h6 class="card-subtitle mb-2 text-muted">Total Donations</h6>
                                    <h2 class="card-title mb-0">$<?php echo number_format($total_donations, 2); ?></h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon bg-info text-white me-3">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div>
                                    <h6 class="card-subtitle mb-2 text-muted">Total Donors</h6>
                                    <h2 class="card-title mb-0"><?php echo count($donations); ?></h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Donations Table -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">All Donations</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="donations-table" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Payment Method</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($donations)): ?>
                                <tr>
                                    <td colspan="8" class="text-center">No donations found.</td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($donations as $donation): ?>
                                <tr>
                                    <td><?php echo $donation['id']; ?></td>
                                    <td><?php echo $donation['is_anonymous'] ? 'Anonymous' : htmlspecialchars($donation['donor_name']); ?></td>
                                    <td><?php echo $donation['is_anonymous'] ? 'Anonymous' : htmlspecialchars($donation['donor_email']); ?></td>
                                    <td>$<?php echo number_format($donation['amount'], 2); ?></td>
                                    <td><?php echo formatDate($donation['created_at']); ?></td>
                                    <td><?php echo htmlspecialchars($donation['payment_method']); ?></td>
                                    <td>
                                        <?php if ($donation['status'] == 'completed'): ?>
                                            <span class="badge bg-success">Completed</span>
                                        <?php elseif ($donation['status'] == 'pending'): ?>
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Failed</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#viewDonation<?php echo $donation['id']; ?>">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="donations.php?delete=<?php echo $donation['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this donation record?');">
                                            <i class="fas fa-trash"></i>
                                        </a>

                                        <!-- View Donation Modal -->
                                        <div class="modal fade" id="viewDonation<?php echo $donation['id']; ?>" tabindex="-1" aria-labelledby="viewDonationLabel<?php echo $donation['id']; ?>" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="viewDonationLabel<?php echo $donation['id']; ?>">Donation Details</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <h6>Donor Information</h6>
                                                            <?php if ($donation['is_anonymous']): ?>
                                                            <p><strong>Donor:</strong> Anonymous</p>
                                                            <?php else: ?>
                                                            <p><strong>Name:</strong> <?php echo htmlspecialchars($donation['donor_name']); ?></p>
                                                            <p><strong>Email:</strong> <?php echo htmlspecialchars($donation['donor_email']); ?></p>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="mb-3">
                                                            <h6>Donation Information</h6>
                                                            <p><strong>Amount:</strong> $<?php echo number_format($donation['amount'], 2); ?></p>
                                                            <p><strong>Date:</strong> <?php echo formatDate($donation['created_at']); ?></p>
                                                            <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($donation['payment_method']); ?></p>
                                                            <p><strong>Transaction ID:</strong> <?php echo htmlspecialchars($donation['transaction_id']); ?></p>
                                                            <p><strong>Campaign:</strong> <?php echo htmlspecialchars($donation['campaign'] ?? 'General'); ?></p>
                                                            <p><strong>Status:</strong> 
                                                                <?php if ($donation['status'] == 'completed'): ?>
                                                                    <span class="badge bg-success">Completed</span>
                                                                <?php elseif ($donation['status'] == 'pending'): ?>
                                                                    <span class="badge bg-warning text-dark">Pending</span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-danger">Failed</span>
                                                                <?php endif; ?>
                                                            </p>
                                                        </div>
                                                        <?php if (!empty($donation['notes'])): ?>
                                                        <div class="mb-3">
                                                            <h6>Notes</h6>
                                                            <p><?php echo nl2br(htmlspecialchars($donation['notes'])); ?></p>
                                                        </div>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#donations-table').DataTable({
                "order": [[4, "desc"]] // Sort by date column descending
            });
        });
    </script>
</body>
</html>
