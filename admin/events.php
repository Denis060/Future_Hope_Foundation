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

// Get all events
$sql = "SELECT * FROM events ORDER BY event_date DESC";
$result = $conn->query($sql);
$events = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
}

// This section previously handled old message format
// Now using session messages with message_type

// Handle event status toggle
if (isset($_GET['action']) && $_GET['action'] === 'toggle' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    
    if (toggleActiveStatus('events', $id)) {
        $_SESSION['message'] = "Event status updated successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error updating event status: " . $conn->error;
        $_SESSION['message_type'] = "danger";
    }
    
    // Redirect to remove the action from URL
    header("Location: events.php");
    exit;
}

// Handle event deletion
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    
    // Check if image exists and delete it
    $image_result = $conn->query("SELECT image FROM events WHERE id = $id");
    if ($image_result->num_rows > 0) {
        $image = $image_result->fetch_assoc()['image'];
        if ($image && file_exists("../uploads/$image")) {
            unlink("../uploads/$image");
        }
    }
    
    $delete_sql = "DELETE FROM events WHERE id = $id";
    
    if ($conn->query($delete_sql) === TRUE) {
        $_SESSION['message'] = "Event deleted successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error deleting event: " . $conn->error;
        $_SESSION['message_type'] = "danger";
    }
    
    // Redirect to remove the action from URL
    header("Location: events.php");
    exit;
}

// Handle event creation/update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $location = $conn->real_escape_string($_POST['location']);
    $event_date = $conn->real_escape_string($_POST['event_date']);
    $event_time = $conn->real_escape_string($_POST['event_time']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $image_name = "";
    
    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $file_ext = pathinfo($filename, PATHINFO_EXTENSION);
        
        if (in_array(strtolower($file_ext), $allowed)) {
            $new_name = 'event_' . time() . '.' . $file_ext;
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
                    $old_image_result = $conn->query("SELECT image FROM events WHERE id = $id");
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
        // Update existing event
        $sql = "UPDATE events SET title = '$title', description = '$description', location = '$location', 
                event_date = '$event_date', event_time = '$event_time', is_active = $is_active";
                
        if ($image_name) {
            $sql .= ", image = '$image_name'";
        }
        
        $sql .= " WHERE id = $id";
        
        if ($conn->query($sql) === TRUE) {
            $_SESSION['message'] = "Event updated successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error updating event: " . $conn->error;
            $_SESSION['message_type'] = "danger";
        }
        
        // Redirect to refresh page and prevent form resubmission
        header("Location: events.php");
        exit;
    } else {
        // Create new event
        $sql = "INSERT INTO events (title, description, location, event_date, event_time, image, is_active) 
                VALUES ('$title', '$description', '$location', '$event_date', '$event_time', '$image_name', $is_active)";
        
        if ($conn->query($sql) === TRUE) {
            $_SESSION['message'] = "Event created successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error creating event: " . $conn->error;
            $_SESSION['message_type'] = "danger";
        }
        
        // Redirect to refresh page and prevent form resubmission
        header("Location: events.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Events - <?php echo $settings['site_name'] ?? 'Future Hope Foundation'; ?> Admin</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <!-- Datatables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    
    <!-- Custom Admin CSS -->
    <link rel="stylesheet" href="css/admin-style.css?v=<?php echo time(); ?>">
    
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
                <a class="nav-link" href="projects.php">
                    <i class="fas fa-project-diagram"></i>
                    <span>Projects</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="events.php">
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
            
            <!-- Main Content -->
            <div class="content p-4">
                <div class="row">
                    <div class="col-12">
                        <!-- Page Title -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h1 class="mb-0">Manage Events</h1>
                                <p class="text-muted">View, add, edit or delete foundation events</p>
                            </div>
                            <div>
                                <button type="button" class="btn btn-primary btn-add-new">
                                    <i class="fas fa-plus-circle"></i> Add New Event
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
            
                <!-- Events Table -->
                <div class="row">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="eventsTable" class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th width="250">Title</th>
                                                <th width="150">Date</th>
                                                <th width="180">Location</th>
                                                <th width="100">Status</th>
                                                <th width="150">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($events as $event): ?>
                                            <tr>
                                                <td>
                                                    <?php if ($event['image']): ?>
                                                    <img src="../uploads/<?php echo $event['image']; ?>" class="img-thumbnail me-2" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                                    <?php endif; ?>
                                                    <?php echo htmlspecialchars($event['title']); ?>
                                                </td>
                                                <td>
                                                    <?php 
                                                        echo formatDate($event['event_date']);
                                                        if (!empty($event['event_time'])) {
                                                            echo '<br><small class="text-muted">' . date('h:i A', strtotime($event['event_time'])) . '</small>';
                                                        }
                                                    ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($event['location']); ?></td>
                                                <td>
                                                    <?php if ($event['is_active']): ?>
                                                        <span class="badge bg-success">Active</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Inactive</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-primary edit-btn" 
                                                        data-id="<?php echo $event['id']; ?>"
                                                        data-title="<?php echo htmlspecialchars($event['title']); ?>"
                                                        data-description="<?php echo htmlspecialchars($event['description']); ?>"
                                                        data-location="<?php echo htmlspecialchars($event['location']); ?>"
                                                        data-date="<?php echo $event['event_date']; ?>"
                                                        data-time="<?php echo $event['event_time']; ?>"
                                                        data-active="<?php echo $event['is_active']; ?>"
                                                        data-image="<?php echo $event['image']; ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <a href="events.php?action=toggle&id=<?php echo $event['id']; ?>" class="btn btn-sm btn-warning ms-1">
                                                        <i class="fas fa-power-off"></i>
                                                    </a>
                                                    <a href="javascript:void(0);" onclick="confirmDelete(<?php echo $event['id']; ?>)" class="btn btn-sm btn-danger ms-1">
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
                
                <!-- Add/Edit Event Modal -->
                <div class="modal fade" id="eventModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="formTitle">Add New Event</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="eventForm" action="events.php" method="post" enctype="multipart/form-data">
                                    <input type="hidden" id="event_id" name="id" value="">
                                    
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="mb-3">
                                                <label for="title" class="form-label">Event Title</label>
                                                <input type="text" class="form-control" id="title" name="title" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3 form-check mt-4">
                                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" checked>
                                                <label class="form-check-label" for="is_active">Active</label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="event_date" class="form-label">Event Date</label>
                                                <input type="date" class="form-control" id="event_date" name="event_date" required>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="event_time" class="form-label">Event Time</label>
                                                <input type="time" class="form-control" id="event_time" name="event_time">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="location" class="form-label">Location</label>
                                        <input type="text" class="form-control" id="location" name="location" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="5"></textarea>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="mb-3">
                                                <label for="image" class="form-label">Event Image</label>
                                                <input type="file" class="form-control" id="image" name="image">
                                                <small class="form-text text-muted">Recommended size: 800x600 pixels</small>
                                            </div>
                                            <div id="imagePreview" class="mt-2 d-none">
                                                <p>Current image:</p>
                                                <img src="" id="currentImage" class="img-thumbnail" style="max-height: 150px;">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="modal-footer px-0 pb-0">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Save Event</button>
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
                                <p>Are you sure you want to delete this event? This action cannot be undone.</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Delete Event</a>
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
            $('#eventsTable').DataTable({
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
                },
                "order": [[1, "desc"]]
            });
            
            // Initialize Summernote
            $('#description').summernote({
                height: 200,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });
            
            // Handle Add New Event Button Click
            $('.btn-add-new').click(function() {
                resetEventForm();
                $('#eventModal').modal('show');
            });
            
            // Handle Edit Event Button Click
            $('.edit-btn').click(function() {
                var id = $(this).data('id');
                var title = $(this).data('title');
                var description = $(this).data('description');
                var location = $(this).data('location');
                var date = $(this).data('date');
                var time = $(this).data('time');
                var active = $(this).data('active');
                var image = $(this).data('image');
                
                $('#event_id').val(id);
                $('#title').val(title);
                $('#description').summernote('code', description);
                $('#location').val(location);
                $('#event_date').val(date);
                $('#event_time').val(time);
                $('#is_active').prop('checked', active == 1);
                
                if (image) {
                    $('#imagePreview').removeClass('d-none');
                    $('#currentImage').attr('src', '../uploads/' + image);
                } else {
                    $('#imagePreview').addClass('d-none');
                }
                
                $('#formTitle').text('Edit Event');
                $('#eventModal').modal('show');
            });
            
            // Reset form function
            function resetEventForm() {
                $('#eventForm')[0].reset();
                $('#event_id').val('');
                $('#description').summernote('code', '');
                $('#imagePreview').addClass('d-none');
                $('#formTitle').text('Add New Event');
            }
            
            // Handle delete confirmation
            window.confirmDelete = function(id) {
                $('#confirmDeleteBtn').attr('href', 'events.php?action=delete&id=' + id);
                $('#deleteModal').modal('show');
            };
            
            // Check if we need to open the add modal (from quick actions)
            if (localStorage.getItem('openAddModal') === 'true') {
                resetEventForm();
                $('#eventModal').modal('show');
                localStorage.removeItem('openAddModal');
            }
        });
    </script>
</body>
</html>
