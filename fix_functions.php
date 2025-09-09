<?php
// Read the original file
$content = file_get_contents('includes/functions.php');

// Remove the closing PHP tag and everything after it
$closingTagPos = strpos($content, '?>');
if ($closingTagPos !== false) {
    $content = substr($content, 0, $closingTagPos);
    echo "PHP closing tag removed successfully!\n";
}

// Clean up the file content
$lines = explode("\n", $content);
$cleanedLines = [];
$seenFunctions = [];
$inFunction = false;
$currentFunction = '';
$skipFunction = false;
$braceCount = 0;

foreach ($lines as $line) {
    // Check for function declaration
    if (preg_match('/function\s+(\w+)\s*\(/', $line, $matches)) {
        $functionName = $matches[1];
        
        if (isset($seenFunctions[$functionName])) {
            // We've already seen this function before, skip it
            echo "Skipping duplicate function: $functionName\n";
            $skipFunction = true;
            $inFunction = true;
            $currentFunction = $functionName;
            $braceCount = 0;
        } else {
            // First time seeing this function
            $seenFunctions[$functionName] = true;
            $skipFunction = false;
            $inFunction = true;
            $currentFunction = $functionName;
            $braceCount = 0;
            $cleanedLines[] = $line;
        }
        
        // Count opening braces
        $braceCount += substr_count($line, '{');
    } else if ($inFunction) {
        // We're inside a function declaration
        if (!$skipFunction) {
            $cleanedLines[] = $line;
        }
        
        // Count braces
        $braceCount += substr_count($line, '{');
        $braceCount -= substr_count($line, '}');
        
        // If braces are balanced, we're out of the function
        if ($braceCount <= 0) {
            $inFunction = false;
            $skipFunction = false;
        }
    } else {
        // Regular line outside function
        $cleanedLines[] = $line;
    }
}

// Write the cleaned content back to the file
file_put_contents('includes/functions.php', implode("\n", $cleanedLines));

echo "\nFile cleaned and fixed successfully!";
?>
