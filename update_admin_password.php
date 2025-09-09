<?php
// Include database connection
require_once 'includes/config.php';

// Update admin password
$new_password = password_hash('admin@futurehope.org', PASSWORD_DEFAULT);
$update_sql = "UPDATE users SET password = '$new_password' WHERE username = 'admin'";

if ($conn->query($update_sql)) {
    echo "Admin password updated successfully to 'admin@futurehope.org'.<br>";
} else {
    echo "Error updating admin password: " . $conn->error . "<br>";
}

echo "<p>You can now login to the admin panel with:</p>";
echo "<p>Username: <strong>admin</strong><br>";
echo "Password: <strong>admin@futurehope.org</strong></p>";
echo "<p><a href='admin/index.php'>Go to Admin Login</a></p>";
?>
