<?php
// Debug file to check image paths

// Include necessary files
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Get testimonials
$testimonials = getTestimonials();

echo "<h1>Image Path Debug</h1>";
echo "<h2>Testimonials</h2>";

if (!empty($testimonials)) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Name</th><th>Raw Image Path</th><th>Processed Image Path</th><th>Image Preview</th></tr>";
    
    foreach ($testimonials as $testimonial) {
        echo "<tr>";
        echo "<td>{$testimonial['id']}</td>";
        echo "<td>{$testimonial['name']}</td>";
        echo "<td>{$testimonial['image']}</td>";
        echo "<td>" . getImageUrl($testimonial['image']) . "</td>";
        echo "<td><img src='" . getImageUrl($testimonial['image']) . "' style='max-width: 100px;'></td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p>No testimonials found.</p>";
}
?>
