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

// Set content type to JSON
header('Content-Type: application/json');
$response = ['success' => false];

// Check if this is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Log received data
    $response['received_data'] = $_POST;
    
    // Basic validation
    if (!empty($_POST['name']) && !empty($_POST['position'])) {
        $name = $conn->real_escape_string($_POST['name']);
        $position = $conn->real_escape_string($_POST['position']);
        $bio = $conn->real_escape_string($_POST['bio'] ?? '');
        $order_number = isset($_POST['order_number']) ? (int)$_POST['order_number'] : 0;
        
        // Try to insert test data
        $sql = "INSERT INTO team (name, position, bio, order_number, is_active) 
                VALUES ('$name', '$position', '$bio', $order_number, 1)";
        
        if ($conn->query($sql)) {
            $response['success'] = true;
            $response['message'] = 'Test team member created successfully. ID: ' . $conn->insert_id;
            $response['inserted_id'] = $conn->insert_id;
            
            // Clean up test data
            $conn->query("DELETE FROM team WHERE id = " . $conn->insert_id);
            $response['cleanup'] = 'Test data removed';
        } else {
            $response['error'] = $conn->error;
            $response['sql'] = $sql;
        }
    } else {
        $response['error'] = 'Missing required fields';
    }
}

echo json_encode($response, JSON_PRETTY_PRINT);
?>
