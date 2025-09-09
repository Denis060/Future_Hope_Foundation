<?php
// Add the category column to the gallery table if it doesn't exist

// Include database connection
require_once 'includes/config.php';

// Check if the category column exists in the gallery table
$check_column = "SHOW COLUMNS FROM `gallery` LIKE 'category'";
$result = $conn->query($check_column);

if ($result->num_rows == 0) {
    // Column doesn't exist, add it
    $add_column = "ALTER TABLE `gallery` ADD COLUMN `category` VARCHAR(100) AFTER `thumbnail_path`";
    if ($conn->query($add_column) === TRUE) {
        echo "Category column added successfully to gallery table.<br>";
    } else {
        echo "Error adding category column: " . $conn->error . "<br>";
    }
} else {
    echo "Category column already exists in gallery table.<br>";
}

echo "Gallery table update complete.<br>";
echo "<a href='gallery.php'>Return to Gallery</a>";
?>
