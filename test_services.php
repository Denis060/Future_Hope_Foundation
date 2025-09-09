<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'Denis55522');
define('DB_NAME', 'futurehope_db');

// Create database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check services table
$result = $conn->query("SELECT * FROM services");
echo "<h2>Services Data:</h2>";

if ($result && $result->num_rows > 0) {
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Title</th><th>Icon</th><th>Is Active</th><th>Created At</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['title'] . "</td>";
        echo "<td>" . $row['icon'] . "</td>";
        echo "<td>" . ($row['is_active'] ? 'Yes' : 'No') . "</td>";
        echo "<td>" . $row['created_at'] . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "No services found. Running initialize_services.php to populate data...<br>";
    
    // Include the initialize script
    include 'initialize_services.php';
    
    // Check again after initialization
    $result = $conn->query("SELECT * FROM services");
    
    if ($result && $result->num_rows > 0) {
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Title</th><th>Icon</th><th>Is Active</th><th>Created At</th></tr>";
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['title'] . "</td>";
            echo "<td>" . $row['icon'] . "</td>";
            echo "<td>" . ($row['is_active'] ? 'Yes' : 'No') . "</td>";
            echo "<td>" . $row['created_at'] . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
    } else {
        echo "Still no services found after initialization.";
    }
}

// Close the connection
$conn->close();
?>
