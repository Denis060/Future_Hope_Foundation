<?php
session_start();
include 'includes/config.php';
include 'includes/functions.php';

// Set user session variables directly for testing
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'admin@futurehope.org';
$_SESSION['name'] = 'Admin';
$_SESSION['logged_in'] = true;

// Output success message
echo "<h1>Admin Login Test</h1>";
echo "<p>Admin session variables set successfully.</p>";
echo "<p>Now you can <a href='admin/dashboard.php'>access the dashboard</a>.</p>";
?>
