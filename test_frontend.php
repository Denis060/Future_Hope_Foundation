<?php
// Include database configuration and functions
require_once 'includes/config.php';
require_once 'includes/functions.php';

echo "<h1>Future Hope Foundation - Frontend Data Display Test</h1>";

// Test sliders
echo "<h2>Sliders Test</h2>";
$sliders = getSliders();
if (!empty($sliders)) {
    echo "<p>✅ Successfully fetched " . count($sliders) . " sliders</p>";
    echo "<ul>";
    foreach ($sliders as $slider) {
        echo "<li>" . $slider['title'] . " - Image path: " . $slider['image'] . " - Full URL: " . getImageUrl($slider['image']) . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>❌ No sliders found</p>";
}

// Test projects
echo "<h2>Projects Test</h2>";
$ongoing_projects = getProjects(3, 'ongoing');
if (!empty($ongoing_projects)) {
    echo "<p>✅ Successfully fetched " . count($ongoing_projects) . " ongoing projects</p>";
    echo "<ul>";
    foreach ($ongoing_projects as $project) {
        echo "<li>" . $project['title'] . " - Image path: " . $project['image'] . " - Full URL: " . getImageUrl($project['image']) . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>❌ No ongoing projects found</p>";
}

// Test events
echo "<h2>Events Test</h2>";
$upcoming_events = getEvents(3, true);
if (!empty($upcoming_events)) {
    echo "<p>✅ Successfully fetched " . count($upcoming_events) . " upcoming events</p>";
    echo "<ul>";
    foreach ($upcoming_events as $event) {
        echo "<li>" . $event['title'] . " - Image path: " . $event['image'] . " - Full URL: " . getImageUrl($event['image']) . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>❌ No upcoming events found</p>";
}

// Test team members
echo "<h2>Team Members Test</h2>";
$team_members = getTeamMembers();
if (!empty($team_members)) {
    echo "<p>✅ Successfully fetched " . count($team_members) . " team members</p>";
    echo "<ul>";
    foreach ($team_members as $member) {
        echo "<li>" . $member['name'] . " - Position: " . $member['position'] . " - Image path: " . $member['image'] . " - Full URL: " . getImageUrl($member['image']) . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>❌ No team members found</p>";
}

// Test gallery
echo "<h2>Gallery Test</h2>";
$gallery_items = getGalleryImages(5);
if (!empty($gallery_items)) {
    echo "<p>✅ Successfully fetched " . count($gallery_items) . " gallery items</p>";
    echo "<ul>";
    foreach ($gallery_items as $item) {
        echo "<li>Category: " . $item['category'] . " - Image path: " . $item['file_path'] . " - Full URL: " . getImageUrl($item['file_path']) . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>❌ No gallery items found</p>";
}

// Test settings
echo "<h2>Settings Test</h2>";
$settings_query = "SELECT * FROM settings LIMIT 1";
$settings_result = $conn->query($settings_query);
if ($settings_result && $settings_result->num_rows > 0) {
    $settings = $settings_result->fetch_assoc();
    echo "<p>✅ Successfully fetched site settings</p>";
    echo "<ul>";
    echo "<li>Site Name: " . $settings['site_name'] . "</li>";
    echo "<li>Logo path: " . $settings['site_logo'] . " - Full URL: " . getImageUrl($settings['site_logo']) . "</li>";
    echo "<li>Email: " . $settings['site_email'] . "</li>";
    echo "</ul>";
} else {
    echo "<p>❌ No settings found</p>";
}

echo "<p>Test completed. You may need to modify the `getImageUrl()` function to properly display images in the frontend.</p>";
