<?php
// Debug file to check image paths for events

// Include necessary files
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Get events
$upcoming_events = getEvents(0, true, true);

echo "<h1>Event Image Path Debug</h1>";
echo "<h2>Upcoming Events</h2>";

if (!empty($upcoming_events)) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Title</th><th>Date</th><th>Raw Image Path</th><th>Processed Image Path</th><th>Image Preview</th></tr>";
    
    foreach ($upcoming_events as $event) {
        echo "<tr>";
        echo "<td>{$event['id']}</td>";
        echo "<td>{$event['title']}</td>";
        echo "<td>{$event['event_date']}</td>";
        echo "<td>{$event['image']}</td>";
        echo "<td>" . getImageUrl($event['image']) . "</td>";
        echo "<td><img src='" . getImageUrl($event['image']) . "' style='max-width: 100px;'></td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p>No upcoming events found.</p>";
}

// Check past events
$past_events_query = "SELECT * FROM events WHERE event_date < CURDATE() AND is_active = 1 ORDER BY event_date DESC";
$past_events_result = $conn->query($past_events_query);
$past_events = [];

if ($past_events_result && $past_events_result->num_rows > 0) {
    while ($row = $past_events_result->fetch_assoc()) {
        $past_events[] = $row;
    }
}

echo "<h2>Past Events</h2>";

if (!empty($past_events)) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Title</th><th>Date</th><th>Raw Image Path</th><th>Processed Image Path</th><th>Image Preview</th></tr>";
    
    foreach ($past_events as $event) {
        echo "<tr>";
        echo "<td>{$event['id']}</td>";
        echo "<td>{$event['title']}</td>";
        echo "<td>{$event['event_date']}</td>";
        echo "<td>{$event['image']}</td>";
        echo "<td>" . getImageUrl($event['image']) . "</td>";
        echo "<td><img src='" . getImageUrl($event['image']) . "' style='max-width: 100px;'></td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p>No past events found.</p>";
}

// Check file existence in uploads directory
echo "<h2>File System Check</h2>";
echo "<p>Checking if uploads directory exists: " . (is_dir('uploads') ? 'Yes' : 'No') . "</p>";

if (is_dir('uploads')) {
    echo "<p>Files in uploads directory:</p>";
    $files = scandir('uploads');
    echo "<ul>";
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            echo "<li>$file - " . (file_exists('uploads/' . $file) ? 'Exists' : 'Missing') . "</li>";
        }
    }
    echo "</ul>";
}
?>
