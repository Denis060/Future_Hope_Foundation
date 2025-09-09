<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

echo "<h1>Future Hope Foundation Website Debug</h1>";
echo "<h2>1. Checking Includes</h2>";

// Check if include files exist
echo "config.php: " . (file_exists('includes/config.php') ? "Found ✅" : "Missing ❌") . "<br>";
echo "functions.php: " . (file_exists('includes/functions.php') ? "Found ✅" : "Missing ❌") . "<br>";
echo "header.php: " . (file_exists('includes/header.php') ? "Found ✅" : "Missing ❌") . "<br>";

echo "<h2>2. Database Connection</h2>";

// Try to include config
try {
    require_once 'includes/config.php';
    echo "Config included successfully ✅<br>";
    
    if (isset($conn) && $conn->ping()) {
        echo "Database connection established ✅<br>";
    } else {
        echo "Database connection failed ❌<br>";
    }
} catch (Exception $e) {
    echo "Error including config: " . $e->getMessage() . " ❌<br>";
}

echo "<h2>3. Functions Availability</h2>";

// Try to include functions
try {
    require_once 'includes/functions.php';
    echo "Functions included successfully ✅<br>";
    
    echo "getSettings(): " . (function_exists('getSettings') ? "Available ✅" : "Not available ❌") . "<br>";
    echo "getServices(): " . (function_exists('getServices') ? "Available ✅" : "Not available ❌") . "<br>";
    echo "getEvents(): " . (function_exists('getEvents') ? "Available ✅" : "Not available ❌") . "<br>";
    echo "getProjects(): " . (function_exists('getProjects') ? "Available ✅" : "Not available ❌") . "<br>";
} catch (Exception $e) {
    echo "Error including functions: " . $e->getMessage() . " ❌<br>";
}

echo "<h2>4. Data Availability</h2>";

// Check if we can get data
if (function_exists('getSettings') && isset($conn)) {
    $settings = getSettings($conn);
    echo "Site name from settings: " . ($settings && isset($settings['site_name']) ? $settings['site_name'] . " ✅" : "Not available ❌") . "<br>";
}

if (function_exists('getServices')) {
    $services = getServices(3);
    echo "Number of services: " . (is_array($services) ? count($services) . " ✅" : "Error getting services ❌") . "<br>";
}

echo "<h2>5. Template Components</h2>";

// Check assets
echo "CSS file: " . (file_exists('assets/css/style.css') ? "Found ✅" : "Missing ❌") . "<br>";
echo "Logo image: " . (file_exists('assets/images/logo.png') ? "Found ✅" : "Missing ❌") . "<br>";

echo "<h2>Links to Pages:</h2>";
echo "<ul>";
echo "<li><a href='index.php'>Home Page</a></li>";
echo "<li><a href='about.php'>About Page</a></li>";
echo "<li><a href='services.php'>Services Page</a></li>";
echo "<li><a href='contact.php'>Contact Page</a></li>";
echo "<li><a href='admin/'>Admin Login</a></li>";
echo "</ul>";
?>
