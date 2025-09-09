<?php
// Include database configuration
require_once 'includes/config.php';

// Update site name and logo
$update_query = "UPDATE settings SET site_name = 'Future Hope Foundation', site_logo = 'assets/images/logo.png' WHERE id = 1";

if ($conn->query($update_query) === TRUE) {
    echo "Logo updated successfully!";
} else {
    echo "Error updating logo: " . $conn->error;
}

$conn->close();
?>
