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

// Get all messages
$sql = "SELECT * FROM contact_messages ORDER BY created_at DESC";
$result = $conn->query($sql);
$messages = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
}

// Handle message status update
if (isset($_GET['action']) && $_GET['action'] === 'mark' && isset($_GET['id']) && isset($_GET['status'])) {
    $id = (int) $_GET['id'];
    $status = $conn->real_escape_string($_GET['status']);
    
    if (in_array($status, ['read', 'unread', 'replied'])) {
        $update_sql = "UPDATE contact_messages SET status = '$status' WHERE id = $id";
        
        if ($conn->query($update_sql) === TRUE) {
            $_SESSION['message'] = displaySuccess("Message marked as $status successfully!");
        } else {
            $_SESSION['message'] = displayError("Error updating message status: " . $conn->error);
        }
    }
    
    // Redirect to refresh the page
    header("Location: messages.php" . (isset($_GET['id']) ? "?id=" . $_GET['id'] : ""));
    exit;
}

// Handle message deletion
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $delete_sql = "DELETE FROM contact_messages WHERE id = $id";
    
    if ($conn->query($delete_sql) === TRUE) {
        $_SESSION['message'] = displaySuccess("Message deleted successfully!");
    } else {
        $_SESSION['message'] = displayError("Error deleting message: " . $conn->error);
    }
    
    // Redirect to remove the action from URL
    header("Location: messages.php");
    exit;
}

// Get message by ID for viewing
$selected_message = null;
if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $message_sql = "SELECT * FROM contact_messages WHERE id = $id LIMIT 1";
    $message_result = $conn->query($message_sql);
    
    if ($message_result->num_rows > 0) {
        $selected_message = $message_result->fetch_assoc();
        
        // Update message status to read if it was unread
        if ($selected_message['status'] === 'unread') {
            $conn->query("UPDATE contact_messages SET status = 'read' WHERE id = $id");
            $selected_message['status'] = 'read';
        }
    }
}

// Function to count unread messages
function countUnreadMessages() {
    global $conn;
    $result = $conn->query("SELECT COUNT(*) as count FROM contact_messages WHERE status = 'unread'");
    return $result->fetch_assoc()['count'];
}

// Get unread count
$unread_count = countUnreadMessages();

// Message to display (if any)
$message = $message ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - <?php echo $settings['site_name'] ?? 'Future Hope Foundation'; ?> Admin</title>
    
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
        .message-list {
            max-height: 600px;
            overflow-y: auto;
        }
        
        .message-item {
            border-left: 4px solid transparent;
            transition: all 0.2s;
        }
        
        .message-item:hover {
            background-color: rgba(0, 123, 255, 0.05);
        }
        
        .message-item.active {
            background-color: rgba(0, 123, 255, 0.1);
            border-left-color: #0d6efd;
        }
        
        .message-item.unread {
            border-left-color: #0dcaf0;
            background-color: rgba(13, 202, 240, 0.05);
        }
        
        .message-item.unread .name {
            font-weight: bold;
        }
        
        .message-content {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
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
                <a class="nav-link active" href="messages.php">
                    <i class="fas fa-envelope"></i>
                    <span>Messages</span>
                    <?php if ($unread_count > 0): ?>
                    <span class="badge bg-danger rounded-pill ms-1"><?php echo $unread_count; ?></span>
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
            <!-- Page Title -->
            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h1 class="mb-0">Manage Messages</h1>
                            <p class="text-muted">View and respond to contact messages</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php echo $message; ?>
            
            <div class="row">
                <!-- Message List -->
                <div class="col-lg-5">
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-envelope me-2"></i> Messages</h5>
                            <div>
                                <?php if ($unread_count > 0): ?>
                                <span class="badge bg-light text-dark me-2"><?php echo $unread_count; ?> unread</span>
                                <?php endif; ?>
                                <button class="btn btn-sm btn-light" id="refreshMessages"><i class="fas fa-sync-alt"></i></button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="message-list">
                                <?php if (count($messages) === 0): ?>
                                <div class="text-center p-4">
                                    <i class="fas fa-envelope-open fa-3x text-muted mb-3"></i>
                                    <p>No messages found</p>
                                </div>
                                <?php else: ?>
                                <div class="list-group list-group-flush">
                                    <?php foreach ($messages as $message): ?>
                                    <a href="messages.php?id=<?php echo $message['id']; ?>" class="list-group-item list-group-item-action message-item <?php echo $message['status'] === 'unread' ? 'unread' : ''; ?> <?php echo ($selected_message && $selected_message['id'] === $message['id']) ? 'active' : ''; ?>">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mb-1 name"><?php echo htmlspecialchars($message['name']); ?></h6>
                                            <small class="text-muted">
                                                <?php echo formatDate($message['created_at']); ?>
                                            </small>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <p class="mb-1 text-muted small">
                                                <?php echo htmlspecialchars($message['subject'] ? $message['subject'] : substr($message['message'], 0, 30) . '...'); ?>
                                            </p>
                                            <?php if ($message['status'] === 'unread'): ?>
                                            <span class="badge bg-info">New</span>
                                            <?php elseif ($message['status'] === 'replied'): ?>
                                            <span class="badge bg-success">Replied</span>
                                            <?php endif; ?>
                                        </div>
                                    </a>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Message Content -->
                <div class="col-lg-7">
                    <?php if ($selected_message): ?>
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><?php echo htmlspecialchars($selected_message['subject'] ? $selected_message['subject'] : 'Message from ' . $selected_message['name']); ?></h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h5 class="mb-1"><?php echo htmlspecialchars($selected_message['name']); ?></h5>
                                    <div class="text-muted small">
                                        <?php echo htmlspecialchars($selected_message['email']); ?>
                                        <?php if ($selected_message['phone']): ?>
                                        | <?php echo htmlspecialchars($selected_message['phone']); ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="text-muted small mb-1">Received: <?php echo formatDate($selected_message['created_at']); ?></div>
                                    <?php if ($selected_message['status'] === 'unread'): ?>
                                    <span class="badge bg-info">New</span>
                                    <?php elseif ($selected_message['status'] === 'replied'): ?>
                                    <span class="badge bg-success">Replied</span>
                                    <?php else: ?>
                                    <span class="badge bg-secondary">Read</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="message-content">
                                <p><?php echo nl2br(htmlspecialchars($selected_message['message'])); ?></p>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <div class="btn-group">
                                    <?php if ($selected_message['status'] === 'unread'): ?>
                                    <a href="messages.php?action=mark&id=<?php echo $selected_message['id']; ?>&status=read" class="btn btn-sm btn-primary">
                                        <i class="fas fa-check me-1"></i> Mark as Read
                                    </a>
                                    <?php elseif ($selected_message['status'] === 'read'): ?>
                                    <a href="messages.php?action=mark&id=<?php echo $selected_message['id']; ?>&status=unread" class="btn btn-sm btn-info">
                                        <i class="fas fa-envelope me-1"></i> Mark as Unread
                                    </a>
                                    <?php endif; ?>
                                    <?php if ($selected_message['status'] !== 'replied'): ?>
                                    <a href="messages.php?action=mark&id=<?php echo $selected_message['id']; ?>&status=replied" class="btn btn-sm btn-success">
                                        <i class="fas fa-reply me-1"></i> Mark as Replied
                                    </a>
                                    <?php endif; ?>
                                </div>
                                
                                <div>
                                    <a href="mailto:<?php echo $selected_message['email']; ?>?subject=Re: <?php echo htmlspecialchars($selected_message['subject'] ? $selected_message['subject'] : 'Your message to ' . $settings['site_name']); ?>" class="btn btn-primary">
                                        <i class="fas fa-reply me-1"></i> Reply
                                    </a>
                                    <a href="messages.php?action=delete&id=<?php echo $selected_message['id']; ?>" class="btn btn-danger ms-2" onclick="return confirm('Are you sure you want to delete this message?');">
                                        <i class="fas fa-trash me-1"></i> Delete
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-envelope-open-text fa-4x text-muted mb-3"></i>
                            <h4>Select a message to view</h4>
                            <p class="text-muted">Click on a message from the list to view its content.</p>
                        </div>
                    </div>
                    <?php endif; ?>
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
            // Refresh messages
            $('#refreshMessages').click(function() {
                window.location.reload();
            });
        });
    </script>
</body>
</html>
