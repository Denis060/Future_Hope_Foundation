<?php
// Include database connection
require_once 'config.php';

// Include enhanced image functions
require_once 'image-functions.php';

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

/**
 * Legacy sanitize function (alias for sanitizeInput)
 * Used in older parts of the codebase
 */
function sanitize($conn, $input) {
    if (is_array($input)) {
        foreach ($input as $key => $value) {
            $input[$key] = sanitize($conn, $value);
        }
        return $input;
    }
    
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
    
    $sql = "SELECT * FROM team";
    
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
    $result = $conn->query("SELECT COUNT(*) as count FROM contact_messages WHERE status = 'unread'");
    return $result->fetch_assoc()['count'];
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

// Function to truncate text to a specific length with ellipsis
function truncateText($text, $length = 100, $ending = '...', $exact = false, $considerHtml = true) {
    if ($considerHtml) {
        // If the plain text is shorter than the maximum length, return the whole text
        if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
            return $text;
        }
        
        // Splits all html-tags to stored them in $tags array
        preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $matches, PREG_SET_ORDER);
        
        $total_length = 0;
        $open_tags = array();
        $truncate = '';
        
        foreach ($matches as $match) {
            // If there is any tag
            if (!empty($match[1])) {
                // If it's an "open" tag
                if (preg_match('/^<(\s*\/\s*)?([a-z]+).*?>$/i', $match[1], $tag)) {
                    // If it's a closing tag
                    if (preg_match('/^<\s*\//', $match[1])) {
                        // Delete the closing tag from $open_tags list
                        $pos = array_search($tag[2], $open_tags);
                        if ($pos !== false) {
                            array_splice($open_tags, $pos, 1);
                        }
                    }
                    // If it's an opening tag
                    else {
                        // Add tag to the beginning of $open_tags list
                        array_unshift($open_tags, strtolower($tag[2]));
                    }
                }
                // Add html-tag to $truncate
                $truncate .= $match[1];
            }
            
            // Calculate the length of the plain text part of the match
            $content_length = strlen($match[2]);
            
            // If the plain text is longer than the maximum length
            if ($total_length + $content_length > $length) {
                // The number of characters to add to the truncated text
                $left = $length - $total_length;
                $entities_length = 0;
                
                // If the plain text is shorter than the maximum length, add the whole match
                if ($left > 0) {
                    $truncate .= substr($match[2], 0, $left);
                }
                
                // Close all opened tags
                foreach ($open_tags as $tag) {
                    $truncate .= '</' . $tag . '>';
                }
                
                // Add the defined ending to the text
                $truncate .= $ending;
                break;
            } else {
                $truncate .= $match[2];
                $total_length += $content_length;
            }
            
            // If the maximum length is reached, break
            if ($total_length >= $length) {
                break;
            }
        }
    } else {
        if (strlen($text) <= $length) {
            return $text;
        } else {
            $truncate = substr($text, 0, $length);
            if (!$exact) {
                // Find the last space within the truncated string
                $spacepos = strrpos($truncate, ' ');
                if ($spacepos !== false) {
                    $truncate = substr($truncate, 0, $spacepos);
                }
            }
            $truncate .= $ending;
        }
    }
    
    return $truncate;
}

// Function to format date
function formatDate($date, $format = 'd M, Y') {
    return date($format, strtotime($date));
}

// Function to get image url with correct path
function getImageUrl($image) {
    if (empty($image)) {
        return 'assets/images/placeholder.jpg'; // Return a default placeholder image
    }
    
    // Check if the image already has a path
    if (strpos($image, 'http://') === 0 || 
        strpos($image, 'https://') === 0) {
        // External URL, return as is
        return $image;
    }
    
    // Check if image starts with uploads/ or assets/
    if (strpos($image, 'uploads/') === 0 || strpos($image, 'assets/') === 0) {
        // Path already includes uploads/ or assets/ prefix, return as is
        return $image;
    }
    
    // If path starts with a slash, remove it
    if (strpos($image, '/') === 0) {
        $image = substr($image, 1);
    }
    
    // For testimonials and other uploaded images that might only have the filename stored
    // Simply prepend 'uploads/' without checking file existence (which can fail due to relative path issues)
    return 'uploads/' . $image;
}

/**
 * Get website settings from the database
 * 
 * @param mysqli $conn Database connection
 * @return array Settings array
 */
function getSettings($conn = null) {
    $settings = [];
    
    // Check if connection exists
    if (!$conn) {
        // Set default values if no connection
        return [
            'site_name' => 'Future Hope Foundation',
            'site_logo' => 'assets/images/logo.png',
            'site_email' => 'info@futurehope.org',
            'site_phone' => '+1 (123) 456-7890',
            'site_address' => '123 Charity Street, City, Country',
            'facebook_url' => '',
            'twitter_url' => '',
            'instagram_url' => '',
            'youtube_url' => '',
            'mission_statement' => 'Our mission is to help those in need and create a better future for all.',
            'vision_statement' => 'A world where everyone has equal opportunities and access to resources.',
            'about_content' => 'Future Hope Foundation was founded during the Ebola epidemic in Sierra Leone in 2014.'
        ];
    }
    
    // Get settings from database
    $sql = "SELECT * FROM settings LIMIT 1";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $settings = $result->fetch_assoc();
    } else {
        // Set default values if not found in database
        $settings = [
            'site_name' => 'Future Hope Foundation',
            'site_logo' => 'assets/images/logo.png',
            'site_email' => 'info@futurehope.org',
            'site_phone' => '+1 (123) 456-7890',
            'site_address' => '123 Charity Street, City, Country',
            'facebook_url' => '',
            'twitter_url' => '',
            'instagram_url' => '',
            'youtube_url' => '',
            'mission_statement' => 'Our mission is to help those in need and create a better future for all.',
            'vision_statement' => 'A world where everyone has equal opportunities and access to resources.',
            'about_content' => 'Future Hope Foundation was founded during the Ebola epidemic in Sierra Leone in 2014.'
        ];
    }
    
    return $settings;
}
