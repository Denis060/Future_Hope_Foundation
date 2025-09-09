<?php
// Include database configuration and functions
require_once 'includes/config.php';
require_once 'includes/functions.php';

echo "<h1>Project Images Debug</h1>";

// Get all projects
$query = "SELECT id, title, image FROM projects";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Title</th><th>Image Path in DB</th><th>Full Image URL</th><th>Image Exists?</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        $id = $row['id'];
        $title = $row['title'];
        $image = $row['image'];
        $full_path = "uploads/" . $image;
        $image_exists = file_exists($full_path) ? "Yes" : "No";
        
        echo "<tr>";
        echo "<td>{$id}</td>";
        echo "<td>{$title}</td>";
        echo "<td>{$image}</td>";
        echo "<td>{$full_path}</td>";
        echo "<td>{$image_exists}</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p>No projects found in the database.</p>";
}

// Also check how our function is working
echo "<h2>Testing getImageUrl function</h2>";
$test_paths = [
    "project_1234567890.jpg",
    "uploads/project_1234567890.jpg",
    "assets/images/logo.png",
    "",
    null,
    "http://example.com/image.jpg"
];

echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Original Path</th><th>Result from getImageUrl()</th></tr>";

foreach ($test_paths as $path) {
    echo "<tr>";
    echo "<td>" . (is_null($path) ? "NULL" : $path) . "</td>";
    echo "<td>" . getImageUrl($path) . "</td>";
    echo "</tr>";
}

echo "</table>";

// Check if uploads directory exists
$upload_dir = "uploads/";
echo "<h2>Uploads Directory Check</h2>";
if (is_dir($upload_dir)) {
    echo "<p>✅ Uploads directory exists</p>";
    
    // List files in uploads directory
    $files = scandir($upload_dir);
    echo "<p>Files in uploads directory:</p>";
    echo "<ul>";
    foreach ($files as $file) {
        if ($file != "." && $file != "..") {
            echo "<li>{$file}</li>";
        }
    }
    echo "</ul>";
} else {
    echo "<p>❌ Uploads directory does not exist!</p>";
}
?>
