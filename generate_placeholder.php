<?php
// PHP script to create placeholder images for the website

// Set content type
header('Content-Type: image/png');

// Generate a default team member placeholder image
$width = 600;
$height = 600;
$image = imagecreatetruecolor($width, $height);

// Define colors
$background = imagecolorallocate($image, 230, 240, 250); // Light blue background
$text_color = imagecolorallocate($image, 50, 100, 150); // Dark blue text
$accent = imagecolorallocate($image, 65, 105, 225); // Royal blue accent

// Fill background
imagefill($image, 0, 0, $background);

// Create a circle for the profile
imagefilledellipse($image, $width/2, $height/2 - 50, 300, 300, $accent);
imagefilledellipse($image, $width/2, $height/2 - 50, 280, 280, $background);

// Draw silhouette
imagefilledellipse($image, $width/2, $height/2 - 100, 100, 100, $accent); // Head
$points = array(
    $width/2 - 75, $height/2 - 50,
    $width/2 + 75, $height/2 - 50,
    $width/2 + 100, $height/2 + 150,
    $width/2 - 100, $height/2 + 150
);
imagefilledpolygon($image, $points, 4, $accent); // Body

// Add text
$text = "Team Member";
$font_size = 5;
$text_width = imagefontwidth($font_size) * strlen($text);
$text_height = imagefontheight($font_size);
imagestring($image, $font_size, ($width - $text_width)/2, $height - 100, $text, $text_color);

// Add foundation name
$org_text = "Future Hope Foundation";
$org_width = imagefontwidth($font_size) * strlen($org_text);
imagestring($image, $font_size, ($width - $org_width)/2, $height - 70, $org_text, $text_color);

// Output the image
imagepng($image);
imagedestroy($image);
?>
