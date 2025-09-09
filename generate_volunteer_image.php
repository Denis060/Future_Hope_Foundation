<?php
// PHP script to create placeholder volunteer image

// Set content type
header('Content-Type: image/png');

// Generate a default volunteer image
$width = 800;
$height = 500;
$image = imagecreatetruecolor($width, $height);

// Define colors
$background = imagecolorallocate($image, 240, 240, 240); // Light gray background
$accent1 = imagecolorallocate($image, 65, 105, 225); // Royal blue accent
$accent2 = imagecolorallocate($image, 46, 139, 87); // Sea green
$text_color = imagecolorallocate($image, 50, 50, 50); // Dark text

// Fill background
imagefill($image, 0, 0, $background);

// Draw some volunteer silhouettes
// First volunteer
imagefilledellipse($image, 200, 150, 80, 80, $accent1); // Head
$points1 = array(
    160, 190,
    240, 190,
    260, 350,
    140, 350
);
imagefilledpolygon($image, $points1, 4, $accent1); // Body

// Second volunteer
imagefilledellipse($image, 350, 170, 70, 70, $accent2); // Head
$points2 = array(
    315, 205,
    385, 205,
    400, 350,
    300, 350
);
imagefilledpolygon($image, $points2, 4, $accent2); // Body

// Third volunteer
imagefilledellipse($image, 500, 140, 90, 90, $accent1); // Head
$points3 = array(
    455, 185,
    545, 185,
    570, 350,
    430, 350
);
imagefilledpolygon($image, $points3, 4, $accent1); // Body

// Add text
$text = "Volunteer with Future Hope Foundation";
$font_size = 5;
$text_width = imagefontwidth($font_size) * strlen($text);
imagestring($image, $font_size, ($width - $text_width)/2, $height - 100, $text, $text_color);

// Add second line
$text2 = "Make a difference in children's lives";
$text2_width = imagefontwidth($font_size) * strlen($text2);
imagestring($image, $font_size, ($width - $text2_width)/2, $height - 70, $text2, $text_color);

// Output the image
imagepng($image);
imagedestroy($image);
?>
