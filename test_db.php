<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'Denis55522');
define('DB_NAME', 'futurehope_db');

// Create database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Connected to MySQL server successfully.<br>";
    
    // Create database if it doesn't exist
    $sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
    if ($conn->query($sql) === TRUE) {
        echo "Database created or already exists.<br>";
    } else {
        die("Error creating database: " . $conn->error);
    }
    
    // Select the database
    $conn->select_db(DB_NAME);
    echo "Selected database: " . DB_NAME . "<br>";
    
    // Check if tables exist
    $result = $conn->query("SHOW TABLES");
    echo "Tables in database:<br>";
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_row()) {
            echo "- " . $row[0] . "<br>";
        }
    } else {
        echo "No tables found.<br>";
    }
}

// Close the connection
$conn->close();
?>
