<?php
// Output buffering to catch any early output
ob_start();

// Set page title
$page_title = "Events Debug";

// Include header but capture any output before it
$header_output = ob_get_contents();
ob_clean(); // Clear buffer but continue buffering
include 'includes/header.php';

echo "<h1>Early Output Detection</h1>";

// If there was output before the header, display it
if (!empty($header_output)) {
    echo "<div style='background-color: #ffaaaa; padding: 15px; margin: 15px 0; border: 2px solid red;'>";
    echo "<h2>Output detected before header:</h2>";
    echo "<pre>" . htmlspecialchars($header_output) . "</pre>";
    echo "</div>";
}

// Get all included files
$included_files = get_included_files();
echo "<h2>Included Files:</h2>";
echo "<ul>";
foreach ($included_files as $file) {
    echo "<li>" . htmlspecialchars($file) . "</li>";
}
echo "</ul>";

// Include footer
include 'includes/footer.php';
ob_end_flush();
?>
