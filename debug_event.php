<?php
// Debug file to check a specific event

// Include necessary files
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Function to check file path
function checkFilePath($path) {
    echo "<p>Checking path: $path</p>";
    echo "<p>File exists: " . (file_exists($path) ? 'Yes' : 'No') . "</p>";
    echo "<p>Is readable: " . (is_readable($path) ? 'Yes' : 'No') . "</p>";
    
    // Check parent directory
    $dir = dirname($path);
    echo "<p>Parent directory ($dir) exists: " . (is_dir($dir) ? 'Yes' : 'No') . "</p>";
    echo "<p>Parent directory is writable: " . (is_writable($dir) ? 'Yes' : 'No') . "</p>";
}

// Get event
$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 1; // Default to ID 1 if not specified
$event = getRecordById('events', $event_id);

echo "<h1>Event Debug Information</h1>";

if ($event) {
    echo "<h2>Event Details</h2>";
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>Field</th><th>Value</th></tr>";
    
    foreach ($event as $field => $value) {
        echo "<tr>";
        echo "<td>$field</td>";
        echo "<td>" . (is_string($value) ? htmlspecialchars($value) : $value) . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    echo "<h2>Image Processing</h2>";
    
    if (!empty($event['image'])) {
        echo "<p>Raw image path: {$event['image']}</p>";
        $processed_image_path = getImageUrl($event['image']);
        echo "<p>Processed image path: $processed_image_path</p>";
        
        // Check paths
        echo "<h3>File Path Checking</h3>";
        
        // Check with ../uploads/ prefix (from admin perspective)
        echo "<h4>Admin Perspective</h4>";
        checkFilePath("../uploads/{$event['image']}");
        
        // Check with uploads/ prefix (from frontend perspective)
        echo "<h4>Frontend Perspective</h4>";
        checkFilePath("uploads/{$event['image']}");
        
        // Display the image
        echo "<h3>Image Preview</h3>";
        echo "<img src='$processed_image_path' style='max-width: 300px;' />";
    } else {
        echo "<p>No image associated with this event.</p>";
    }
    
} else {
    echo "<p>No event found with ID: $event_id</p>";
    echo "<p>Available events:</p>";
    
    $all_events = getEvents();
    if (!empty($all_events)) {
        echo "<ul>";
        foreach ($all_events as $e) {
            echo "<li>ID: {$e['id']} - Title: {$e['title']}</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No events found in the database.</p>";
    }
}
?>
