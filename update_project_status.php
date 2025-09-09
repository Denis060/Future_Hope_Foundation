<?php
// Include database configuration and functions
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Update project status to ongoing
$query = "UPDATE projects SET status = 'ongoing' WHERE id = 1";
$result = $conn->query($query);
if ($result) {
    echo "✅ Project status updated to 'ongoing'";
} else {
    echo "❌ Failed to update project status: " . $conn->error;
}
?>
