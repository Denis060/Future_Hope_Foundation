<?php
// Include database configuration
require_once 'includes/config.php';

echo "<h1>Admin Login Information</h1>";

// Check if users table exists
$tables_query = "SHOW TABLES LIKE 'users'";
$tables_result = $conn->query($tables_query);
if ($tables_result->num_rows == 0) {
    echo "<p>❌ The users table doesn't exist!</p>";
    exit;
} else {
    echo "<p>✅ The users table exists</p>";
}

// Get all users from the database (don't display passwords)
$users_query = "SELECT id, username, email, created_at FROM users";
$users_result = $conn->query($users_query);
if ($users_result) {
    if ($users_result->num_rows > 0) {
        echo "<p>✅ Found " . $users_result->num_rows . " user accounts</p>";
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Created At</th></tr>";
        while ($row = $users_result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>{$row['username']}</td>";
            echo "<td>{$row['email']}</td>";
            echo "<td>{$row['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>⚠️ No user accounts found in the database</p>";
    }
} else {
    echo "<p>❌ Error querying users table: " . $conn->error . "</p>";
}

// Check if there's a default admin account created during setup
// Note: We're only checking for existence, not displaying the password
$default_admin_query = "SELECT COUNT(*) as count FROM users WHERE username = 'admin'";
$default_admin_result = $conn->query($default_admin_query);
if ($default_admin_result) {
    $row = $default_admin_result->fetch_assoc();
    if ($row['count'] > 0) {
        echo "<p>✅ Default admin account (username: admin) exists</p>";
    } else {
        echo "<p>❌ No default admin account found</p>";
    }
} else {
    echo "<p>❌ Error checking for default admin: " . $conn->error . "</p>";
}

// Check the setup script for default credentials
echo "<h2>Default Admin Setup Code Check</h2>";
if (file_exists('includes/config.php')) {
    $config_content = file_get_contents('includes/config.php');
    if (preg_match('/INSERT INTO.*users.*VALUES/i', $config_content)) {
        echo "<p>Found potential default admin setup in config.php</p>";
    } else {
        echo "<p>No default admin setup found in config.php</p>";
    }
} else {
    echo "<p>Config file not found</p>";
}
?>
