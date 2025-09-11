<?php
// This file checks for any BOM (Byte Order Mark) or whitespace at the beginning of key files
// BOM can cause "headers already sent" issues and unexpected output

// Files to check
$files_to_check = [
    'events.php',
    'includes/header.php',
    'includes/config.php',
    'includes/functions.php'
];

function checkForBOM($filePath) {
    $handle = fopen($filePath, 'rb');
    $contents = fread($handle, 10); // Read first few bytes
    fclose($handle);
    
    $bom = pack('H*','EFBBBF'); // UTF-8 BOM as hexadecimal
    
    echo "<strong>File: $filePath</strong><br>";
    
    // Convert first bytes to hex for display
    $hex = '';
    for ($i = 0; $i < strlen($contents); $i++) {
        $hex .= sprintf("%02X", ord($contents[$i])) . ' ';
    }
    
    echo "First bytes (hex): $hex<br>";
    
    if (0 === strncmp($contents, $bom, 3)) {
        echo "BOM DETECTED - This file has a UTF-8 BOM at the beginning<br>";
    } else {
        echo "No BOM detected<br>";
    }
    
    // Check for whitespace before <?php
    if (preg_match('/^\s+<\?php/', file_get_contents($filePath))) {
        echo "WHITESPACE DETECTED before <?php tag<br>";
    } else {
        echo "No whitespace before <?php tag<br>";
    }
    
    echo "<hr>";
}

echo "<h1>Checking Files for BOM and Whitespace Issues</h1>";

foreach ($files_to_check as $file) {
    checkForBOM($file);
}

// Create a test version of events.php with strict error reporting
echo "<h2>Creating fixed copy of events.php with strict error reporting</h2>";

$events_content = file_get_contents('events.php');

// Remove any BOM if present
$bom = pack('H*','EFBBBF');
if (0 === strncmp($events_content, $bom, 3)) {
    $events_content = substr($events_content, 3);
    echo "BOM removed from events.php<br>";
}

// Create a new version with error reporting
$fixed_content = "<?php
// Enable all error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start output buffering
ob_start();

" . substr($events_content, strpos($events_content, "// Set page title"));

// Save to events_fixed.php
file_put_contents('events_fixed.php', $fixed_content);

echo "<p>Created events_fixed.php with error reporting enabled and output buffering.<br>
Try accessing <a href='events_fixed.php'>events_fixed.php</a> to see if the issue is resolved.</p>";
