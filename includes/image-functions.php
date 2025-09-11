<?php
/**
 * Enhanced Image Functions for Future Hope Foundation
 * 
 * This file contains functions to optimize image loading and rendering
 */

/**
 * Generate a responsive image with srcset and lazy loading
 *
 * @param string $image_path Path to the image
 * @param string $alt Alt text for the image
 * @param string $class Additional CSS classes
 * @param array $sizes Breakpoints and sizes (optional)
 * @return string HTML for responsive image
 */
function responsiveImage($image_path, $alt = '', $class = '', $sizes = array()) {
    // Default sizes if not provided
    if (empty($sizes)) {
        $sizes = array(
            '(max-width: 576px)' => '100vw',
            '(max-width: 991px)' => '50vw',
            'default' => '33vw'
        );
    }
    
    // Get file extension
    $ext = pathinfo($image_path, PATHINFO_EXTENSION);
    
    // Base path without extension
    $base_path = substr($image_path, 0, -(strlen($ext) + 1));
    
    // Define standard sizes for srcset
    $widths = array(400, 800, 1200);
    
    // Build srcset
    $srcset = array();
    foreach ($widths as $width) {
        $srcset[] = getImageUrl($image_path) . ' ' . $width . 'w';
    }
    
    // Build sizes attribute
    $sizes_attr = array();
    foreach ($sizes as $breakpoint => $size) {
        if ($breakpoint === 'default') {
            $sizes_attr[] = $size;
        } else {
            $sizes_attr[] = $breakpoint . ' ' . $size;
        }
    }
    
    // Build the HTML
    $html = '<img src="' . getImageUrl($image_path) . '" ';
    $html .= 'alt="' . htmlspecialchars($alt) . '" ';
    $html .= 'class="lazy-image ' . $class . '" ';
    $html .= 'data-src="' . getImageUrl($image_path) . '" ';
    $html .= 'data-srcset="' . implode(', ', $srcset) . '" ';
    $html .= 'data-sizes="' . implode(', ', $sizes_attr) . '" ';
    $html .= 'loading="lazy">';
    
    return $html;
}

/**
 * Generate a placeholder based on image dimensions
 * 
 * @param string $image_path Path to the image
 * @param string $ratio Aspect ratio (optional)
 * @return string HTML for placeholder
 */
function imagePlaceholder($image_path = null, $ratio = '16:9') {
    // If image path is provided, try to get actual dimensions
    if ($image_path && file_exists($image_path)) {
        $size = getimagesize($image_path);
        if ($size) {
            $width = $size[0];
            $height = $size[1];
            $ratio = $width . ':' . $height;
        }
    }
    
    // Parse ratio
    list($width, $height) = explode(':', $ratio);
    $padding_bottom = ($height / $width) * 100;
    
    return '<div class="image-placeholder" style="padding-bottom: ' . $padding_bottom . '%"></div>';
}

/**
 * Enhanced getImageUrl function with better error handling
 * 
 * @param string $image_name Image name
 * @param string $default Default image to return if the requested one doesn't exist
 * @return string URL to the image
 */
function enhancedGetImageUrl($image_name, $default = 'assets/images/placeholder.jpg') {
    if (empty($image_name)) {
        return $default;
    }
    
    // Check if image already includes uploads/ directory
    if (strpos($image_name, 'uploads/') === 0) {
        $image_path = $image_name;
    } else {
        $image_path = 'uploads/' . $image_name;
    }
    
    // Check if file exists
    if (file_exists($image_path)) {
        return $image_path;
    }
    
    // Check if it's an absolute URL
    if (filter_var($image_name, FILTER_VALIDATE_URL)) {
        return $image_name;
    }
    
    // Return default image
    return $default;
}

/**
 * Generate an image gallery with Isotope filtering
 * 
 * @param array $images Array of image data
 * @param array $categories Array of category data for filtering
 * @return string HTML for gallery
 */
function generateGallery($images, $categories = array()) {
    if (empty($images)) {
        return '<div class="alert alert-info">No images available.</div>';
    }
    
    // Start HTML output
    $html = '';
    
    // Add filters if categories are provided
    if (!empty($categories)) {
        $html .= '<div class="gallery-filters text-center mb-4" role="tablist">';
        $html .= '<button class="btn btn-sm btn-outline-primary active me-2 mb-2" data-filter="*" role="tab" aria-selected="true">All</button>';
        
        foreach ($categories as $category) {
            $slug = preg_replace('/[^a-z0-9]+/', '-', strtolower($category['slug']));
            $html .= '<button class="btn btn-sm btn-outline-primary me-2 mb-2" data-filter=".' . $slug . '" role="tab" aria-selected="false">' . htmlspecialchars($category['name']) . '</button>';
        }
        
        $html .= '</div>';
    }
    
    // Gallery container
    $html .= '<div class="gallery-container row" aria-label="Image Gallery">';
    
    // Gallery items
    foreach ($images as $image) {
        $category_classes = '';
        if (!empty($image['categories'])) {
            foreach ($image['categories'] as $cat) {
                $category_classes .= ' ' . preg_replace('/[^a-z0-9]+/', '-', strtolower($cat));
            }
        }
        
        $html .= '<div class="gallery-item col-md-4 col-sm-6 mb-4' . $category_classes . '">';
        $html .= '<div class="gallery-card">';
        $html .= '<a href="' . enhancedGetImageUrl($image['src']) . '" class="gallery-lightbox" title="' . htmlspecialchars($image['title']) . '">';
        $html .= responsiveImage($image['src'], $image['title'], 'img-fluid');
        $html .= '<div class="gallery-overlay">';
        $html .= '<div class="gallery-info">';
        $html .= '<h5>' . htmlspecialchars($image['title']) . '</h5>';
        if (!empty($image['description'])) {
            $html .= '<p>' . htmlspecialchars($image['description']) . '</p>';
        }
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</a>';
        $html .= '</div>';
        $html .= '</div>';
    }
    
    $html .= '</div>';
    
    return $html;
}

/**
 * Process uploaded images with validation and optimization
 * 
 * @param string $input_name Form field name
 * @param array $allowed_types Allowed file types
 * @param int $max_size Maximum file size in bytes
 * @return array Result with status and data
 */
function processImageUpload($input_name, $allowed_types = array('jpg', 'jpeg', 'png', 'gif'), $max_size = 5242880) {
    $result = array(
        'success' => false,
        'message' => '',
        'file_name' => ''
    );
    
    // Check if file was uploaded
    if (!isset($_FILES[$input_name]) || $_FILES[$input_name]['error'] == UPLOAD_ERR_NO_FILE) {
        $result['message'] = 'No file uploaded.';
        return $result;
    }
    
    // Check for upload errors
    if ($_FILES[$input_name]['error'] != UPLOAD_ERR_OK) {
        $errors = array(
            UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
            UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form.',
            UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload.'
        );
        
        $error_code = $_FILES[$input_name]['error'];
        $result['message'] = isset($errors[$error_code]) ? $errors[$error_code] : 'Unknown upload error.';
        return $result;
    }
    
    // Get file information
    $file_tmp = $_FILES[$input_name]['tmp_name'];
    $file_name = $_FILES[$input_name]['name'];
    $file_size = $_FILES[$input_name]['size'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    
    // Validate file extension
    if (!in_array($file_ext, $allowed_types)) {
        $result['message'] = 'Invalid file type. Allowed types: ' . implode(', ', $allowed_types);
        return $result;
    }
    
    // Validate file size
    if ($file_size > $max_size) {
        $result['message'] = 'File is too large. Maximum size: ' . formatBytes($max_size);
        return $result;
    }
    
    // Generate unique file name
    $new_file_name = uniqid() . '.' . $file_ext;
    $upload_path = 'uploads/';
    
    // Create uploads directory if it doesn't exist
    if (!is_dir($upload_path)) {
        if (!mkdir($upload_path, 0755, true)) {
            $result['message'] = 'Failed to create uploads directory.';
            return $result;
        }
    }
    
    // Move uploaded file
    if (move_uploaded_file($file_tmp, $upload_path . $new_file_name)) {
        $result['success'] = true;
        $result['message'] = 'File uploaded successfully.';
        $result['file_name'] = $new_file_name;
    } else {
        $result['message'] = 'Failed to move uploaded file.';
    }
    
    return $result;
}

/**
 * Format bytes to human-readable format
 * 
 * @param int $bytes Number of bytes
 * @param int $precision Decimal precision
 * @return string Formatted size
 */
function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    
    $bytes /= pow(1024, $pow);
    
    return round($bytes, $precision) . ' ' . $units[$pow];
}
