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

// Check settings table
$result = $conn->query("SELECT * FROM settings");
echo "<h2>Settings Data:</h2>";

if ($result && $result->num_rows > 0) {
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Site Name</th><th>Site Logo</th><th>Site Email</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['site_name'] . "</td>";
        echo "<td>" . $row['site_logo'] . "</td>";
        echo "<td>" . $row['site_email'] . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "No settings found. Adding initial settings...<br>";
    
    // Insert initial settings
    $sql = "INSERT INTO settings (site_name, site_logo, site_email) 
            VALUES ('Future Hope Foundation', 'assets/images/logo.png', 'info@futurehopefoundation.org')";
    
    if ($conn->query($sql) === TRUE) {
        echo "Initial settings added.<br>";
        
        // Check again
        $result = $conn->query("SELECT * FROM settings");
        
        if ($result && $result->num_rows > 0) {
            echo "<table border='1'>";
            echo "<tr><th>ID</th><th>Site Name</th><th>Site Logo</th><th>Site Email</th></tr>";
            
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['site_name'] . "</td>";
                echo "<td>" . $row['site_logo'] . "</td>";
                echo "<td>" . $row['site_email'] . "</td>";
                echo "</tr>";
            }
            
            echo "</table>";
        }
    } else {
        echo "Error adding settings: " . $conn->error;
    }
}

// Close the connection
$conn->close();
?>
