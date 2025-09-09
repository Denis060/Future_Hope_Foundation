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

// Check users table
$result = $conn->query("SELECT id, username, email, created_at FROM users");
echo "<h2>Admin Users:</h2>";

if ($result && $result->num_rows > 0) {
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Created At</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['username'] . "</td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "<td>" . $row['created_at'] . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "No admin users found. Adding default admin...<br>";
    
    // Insert default admin
    $username = 'admin';
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    $email = 'admin@futurehopefoundation.org';
    
    $sql = "INSERT INTO users (username, password, email) 
            VALUES ('$username', '$password', '$email')";
    
    if ($conn->query($sql) === TRUE) {
        echo "Default admin added. Username: admin, Password: admin123<br>";
        
        // Check again
        $result = $conn->query("SELECT id, username, email, created_at FROM users");
        
        if ($result && $result->num_rows > 0) {
            echo "<table border='1'>";
            echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Created At</th></tr>";
            
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['username'] . "</td>";
                echo "<td>" . $row['email'] . "</td>";
                echo "<td>" . $row['created_at'] . "</td>";
                echo "</tr>";
            }
            
            echo "</table>";
        }
    } else {
        echo "Error adding admin: " . $conn->error;
    }
}

// Close the connection
$conn->close();
?>
