<?php
// Include database connection
require_once 'config.php';

// Function to upload files
function uploadFile($file, $directory = 'images', $allowed_types = ['jpg', 'jpeg', 'png', 'gif']) {
    global $conn;
    
    // Check if the file was uploaded without errors
    if ($file['error'] === 0) {
        $file_name = $file['name'];
        $file_size = $file['size'];
        $file_tmp = $file['tmp_name'];
        $file_type = $file['type'];
        
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Check if file type is allowed
        if (in_array($file_ext, $allowed_types)) {
            // Generate unique file name to prevent overwriting
            $new_file_name = uniqid() . '.' . $file_ext;
            $upload_path = '../uploads/' . $directory . '/' . $new_file_name;
            
            // Create directory if it doesn't exist
            $dir_path = '../uploads/' . $directory;
            if (!file_exists($dir_path)) {
                mkdir($dir_path, 0777, true);
            }
            
            // Move the file to the uploads directory
            if (move_uploaded_file($file_tmp, $upload_path)) {
                return 'uploads/' . $directory . '/' . $new_file_name;
            } else {
                return false;
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
}

// Function to upload image with additional checks and error messages
function uploadImage($file, $upload_dir) {
    // Check if the directory exists, if not create it
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // Check if file was uploaded
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        return ['error' => 'No file was uploaded.'];
    }
    
    // Get file information
    $file_name = basename($file['name']);
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $file_size = $file['size'];
    $file_tmp = $file['tmp_name'];
    
    // Validate file extension
    $allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($file_ext, $allowed_exts)) {
        return ['error' => 'Only JPG, JPEG, PNG & GIF files are allowed.'];
    }
    
    // Validate file size (max 5MB)
    if ($file_size > 5000000) {
        return ['error' => 'File size must be less than 5MB.'];
    }
    
    // Generate a unique file name
    $new_file_name = uniqid() . '.' . $file_ext;
    $upload_path = $upload_dir . $new_file_name;
    
    // Move the file to the uploads directory
    if (move_uploaded_file($file_tmp, $upload_path)) {
        return ['success' => true, 'path' => $upload_path];
    } else {
        return ['error' => 'Failed to upload file.'];
    }
}

// Function to sanitize input
function sanitizeInput($input) {
    global $conn;
    
    if (is_array($input)) {
        foreach ($input as $key => $value) {
            $input[$key] = sanitizeInput($value);
        }
        return $input;
    }
    
    // Note: get_magic_quotes_gpc() is deprecated in PHP 7.4 and removed in PHP 8.0
    
    $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    
    if ($conn) {
        $input = $conn->real_escape_string($input);
    }
    
    return $input;
}

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Function to redirect to a URL
function redirect($url) {
    header("Location: $url");
    exit();
}

// Function to display success message
function displaySuccess($message) {
    return '<div class="alert alert-success alert-dismissible fade show" role="alert">
                ' . $message . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
}

// Function to display error message
function displayError($message) {
    return '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                ' . $message . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
}

// Function to get services
function getServices($limit = 0, $active_only = false) {
    global $conn;
    
    $sql = "SELECT * FROM services";
    
    if ($active_only) {
        $sql .= " WHERE is_active = 1";
    }
    
    $sql .= " ORDER BY order_number ASC";
    
    if ($limit > 0) {
        $sql .= " LIMIT $limit";
    }
    
    $result = $conn->query($sql);
    $services = [];
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $services[] = $row;
        }
    }
    
    return $services;
}

// Function to get projects
function getProjects($limit = 0, $status = '', $active_only = false) {
    global $conn;
    
    $sql = "SELECT * FROM projects";
    $where = [];
    
    if ($active_only) {
        $where[] = "is_active = 1";
    }
    
    if ($status && in_array($status, ['ongoing', 'completed', 'upcoming'])) {
        $where[] = "status = '$status'";
    }
    
    if (!empty($where)) {
        $sql .= " WHERE " . implode(' AND ', $where);
    }
    
    $sql .= " ORDER BY created_at DESC";
    
    if ($limit > 0) {
        $sql .= " LIMIT $limit";
    }
    
    $result = $conn->query($sql);
    $projects = [];
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $projects[] = $row;
        }
    }
    
    return $projects;
}

// Function to get events
function getEvents($limit = 0, $upcoming_only = false, $active_only = false) {
    global $conn;
    
    $sql = "SELECT * FROM events";
    $where = [];
    
    if ($active_only) {
        $where[] = "is_active = 1";
    }
    
    if ($upcoming_only) {
        $where[] = "event_date >= CURDATE()";
    }
    
    if (!empty($where)) {
        $sql .= " WHERE " . implode(' AND ', $where);
    }
    
    $sql .= " ORDER BY event_date ASC";
    
    if ($limit > 0) {
        $sql .= " LIMIT $limit";
    }
    
    $result = $conn->query($sql);
    $events = [];
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $events[] = $row;
        }
    }
    
    return $events;
}

// Function to get testimonials
function getTestimonials($limit = 0, $active_only = true) {
    global $conn;
    
    $sql = "SELECT * FROM testimonials";
    
    if ($active_only) {
        $sql .= " WHERE is_active = 1";
    }
    
    $sql .= " ORDER BY RAND()";
    
    if ($limit > 0) {
        $sql .= " LIMIT $limit";
    }
    
    $result = $conn->query($sql);
    $testimonials = [];
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $testimonials[] = $row;
        }
    }
    
    return $testimonials;
}

// Function to get team members
function getTeamMembers($limit = 0, $active_only = true) {
    global $conn;
    
    $sql = "SELECT * FROM team_members";
    
    if ($active_only) {
        $sql .= " WHERE is_active = 1";
    }
    
    $sql .= " ORDER BY order_number ASC";
    
    if ($limit > 0) {
        $sql .= " LIMIT $limit";
    }
    
    $result = $conn->query($sql);
    $team_members = [];
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $team_members[] = $row;
        }
    }
    
    return $team_members;
}

// Function to get gallery images
function getGalleryImages($limit = 0, $category = '', $active_only = true) {
    global $conn;
    
    $sql = "SELECT * FROM gallery";
    $where = [];
    
    if ($active_only) {
        $where[] = "is_active = 1";
    }
    
    if ($category) {
        $where[] = "category = '$category'";
    }
    
    if (!empty($where)) {
        $sql .= " WHERE " . implode(' AND ', $where);
    }
    
    $sql .= " ORDER BY created_at DESC";
    
    if ($limit > 0) {
        $sql .= " LIMIT $limit";
    }
    
    $result = $conn->query($sql);
    $images = [];
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $images[] = $row;
        }
    }
    
    return $images;
}

// Function to get sliders
function getSliders($active_only = true) {
    global $conn;
    
    $sql = "SELECT * FROM sliders";
    
    if ($active_only) {
        $sql .= " WHERE is_active = 1";
    }
    
    $sql .= " ORDER BY order_number ASC";
    
    $result = $conn->query($sql);
    $sliders = [];
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $sliders[] = $row;
        }
    }
    
    return $sliders;
}

// Function to get contact messages
function getContactMessages($limit = 0, $status = '') {
    global $conn;
    
    $sql = "SELECT * FROM contact_messages";
    
    if ($status && in_array($status, ['unread', 'read', 'replied'])) {
        $sql .= " WHERE status = '$status'";
    }
    
    $sql .= " ORDER BY created_at DESC";
    
    if ($limit > 0) {
        $sql .= " LIMIT $limit";
    }
    
    $result = $conn->query($sql);
    $messages = [];
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $messages[] = $row;
        }
    }
    
    return $messages;
}

// Function to get donations
function getDonations($limit = 0) {
    global $conn;
    
    $sql = "SELECT * FROM donations ORDER BY created_at DESC";
    
    if ($limit > 0) {
        $sql .= " LIMIT $limit";
    }
    
    $result = $conn->query($sql);
    $donations = [];
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $donations[] = $row;
        }
    }
    
    return $donations;
}

// Function to count unread messages
function countUnreadMessages() {
    global $conn;
    
    $sql = "SELECT COUNT(*) as count FROM contact_messages WHERE status = 'unread'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['count'];
    }
    
    return 0;
}

// Function to calculate total donations
function getTotalDonations() {
    global $conn;
    
    $sql = "SELECT SUM(amount) as total FROM donations WHERE status = 'completed'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['total'] ? $row['total'] : 0;
    }
    
    return 0;
}

// Function to get a single record by ID from any table
function getRecordById($table, $id) {
    global $conn;
    
    $id = (int) $id;
    $sql = "SELECT * FROM `$table` WHERE id = $id LIMIT 1";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return false;
}

// Function to delete a record by ID from any table
function deleteRecord($table, $id) {
    global $conn;
    
    $id = (int) $id;
    $sql = "DELETE FROM `$table` WHERE id = $id";
    
    return $conn->query($sql);
}

// Function to toggle active status of a record
function toggleActiveStatus($table, $id) {
    global $conn;
    
    $id = (int) $id;
    $sql = "UPDATE `$table` SET is_active = NOT is_active WHERE id = $id";
    
    return $conn->query($sql);
}
