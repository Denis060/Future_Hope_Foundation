<?php
// Include database configuration and functions
require_once 'includes/config.php';
require_once 'includes/functions.php';

echo "<h1>Projects Status Check</h1>";

// Get all projects
$query = "SELECT id, title, status, is_active FROM projects";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Title</th><th>Status</th><th>Active</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        $id = $row['id'];
        $title = $row['title'];
        $status = $row['status'];
        $is_active = $row['is_active'] ? 'Yes' : 'No';
        
        echo "<tr>";
        echo "<td>{$id}</td>";
        echo "<td>{$title}</td>";
        echo "<td>{$status}</td>";
        echo "<td>{$is_active}</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p>No projects found in the database.</p>";
}

// Test getProjects function
echo "<h2>Testing getProjects() function</h2>";
echo "<h3>All projects:</h3>";
$all_projects = getProjects();
echo "Count: " . count($all_projects) . "<br>";

echo "<h3>Only ongoing projects:</h3>";
$ongoing = getProjects(0, 'ongoing');
echo "Count: " . count($ongoing) . "<br>";
if (count($ongoing) > 0) {
    echo "<ul>";
    foreach ($ongoing as $project) {
        echo "<li>{$project['title']} (Status: {$project['status']}, Active: " . ($project['is_active'] ? 'Yes' : 'No') . ")</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No ongoing projects found</p>";
}

// Update project status if needed
if (isset($_GET['update']) && $_GET['update'] == 'yes') {
    $query = "UPDATE projects SET status = 'ongoing' WHERE id = 1";
    $result = $conn->query($query);
    if ($result) {
        echo "<p>✅ Project status updated to 'ongoing'</p>";
    } else {
        echo "<p>❌ Failed to update project status</p>";
    }
}
?>
