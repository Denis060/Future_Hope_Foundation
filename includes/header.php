<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database configuration and functions
$base_path = dirname(__FILE__) . '/';
require_once $base_path . 'config.php';
require_once $base_path . 'functions.php';

// Get site settings
$settings = getSettings($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="format-detection" content="telephone=no, date=no">
    <meta name="theme-color" content="#3498db">
    <meta name="description" content="<?php echo isset($page_description) ? htmlspecialchars($page_description) : 'Future Hope Foundation is dedicated to supporting children and vulnerable communities through education, healthcare and sustainable development.'; ?>">
    <meta name="keywords" content="nonprofit, charity, foundation, donation, volunteer, children, education, healthcare, community development">
    <meta name="author" content="Future Hope Foundation">
    <meta property="og:title" content="<?php echo isset($page_title) ? htmlspecialchars($page_title) . ' - ' . htmlspecialchars($settings['site_name']) : htmlspecialchars($settings['site_name']); ?>">
    <meta property="og:description" content="<?php echo isset($page_description) ? htmlspecialchars($page_description) : 'Future Hope Foundation is dedicated to supporting children and vulnerable communities through education, healthcare and sustainable development.'; ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>">
    <?php if (!empty($settings['site_logo'])): ?>
    <meta property="og:image" content="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/" . $settings['site_logo']; ?>">
    <?php endif; ?>
    
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' - ' . htmlspecialchars($settings['site_name']) : htmlspecialchars($settings['site_name']); ?></title>
    
    <!-- Favicon -->
    <?php if (!empty($settings['site_logo'])): ?>
    <link rel="shortcut icon" href="<?php echo $settings['site_logo']; ?>" type="image/x-icon">
    <link rel="apple-touch-icon" href="<?php echo $settings['site_logo']; ?>">
    <?php endif; ?>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Owl Carousel -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
    
    <!-- Animate CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/enhanced-style.css">
    
    <!-- Fix for mobile date widget -->
    <link rel="stylesheet" href="assets/css/fix-mobile-date.css">
    
    <!-- Page specific CSS -->
    <?php if (isset($additional_css)): echo $additional_css; endif; ?>
</head>
<body>
    <?php include 'includes/preloader.php'; ?>
    <!-- Top Bar -->
    <div class="top-bar bg-light py-2">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="top-bar-left">
                        <?php if (!empty($settings['site_email'])): ?>
                        <span class="me-3"><i class="fas fa-envelope me-1"></i> <?php echo $settings['site_email']; ?></span>
                        <?php endif; ?>
                        
                        <?php if (!empty($settings['site_phone'])): ?>
                        <span><i class="fas fa-phone me-1"></i> <?php echo $settings['site_phone']; ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 text-end">
                    <div class="top-bar-right">
                        <?php if (!empty($settings['facebook_url'])): ?>
                        <a href="<?php echo $settings['facebook_url']; ?>" class="me-2" target="_blank"><i class="fab fa-facebook-f"></i></a>
                        <?php endif; ?>
                        
                        <?php if (!empty($settings['twitter_url'])): ?>
                        <a href="<?php echo $settings['twitter_url']; ?>" class="me-2" target="_blank"><i class="fab fa-twitter"></i></a>
                        <?php endif; ?>
                        
                        <?php if (!empty($settings['instagram_url'])): ?>
                        <a href="<?php echo $settings['instagram_url']; ?>" class="me-2" target="_blank"><i class="fab fa-instagram"></i></a>
                        <?php endif; ?>
                        
                        <?php if (!empty($settings['youtube_url'])): ?>
                        <a href="<?php echo $settings['youtube_url']; ?>" target="_blank"><i class="fab fa-youtube"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Header -->
    <header class="header">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light">
                <div class="container-fluid px-0">
                    <a class="navbar-brand" href="index.php">
                        <?php if (!empty($settings['site_logo'])): ?>
                        <div class="logo-container">
                            <img src="<?php echo $settings['site_logo']; ?>" alt="<?php echo $settings['site_name']; ?>" class="logo">
                            <span class="site-name-mobile d-lg-none">Future Hope</span>
                        </div>
                        <?php else: ?>
                        <h2><?php echo $settings['site_name']; ?></h2>
                        <?php endif; ?>
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>" href="index.php">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'about.php') ? 'active' : ''; ?>" href="about.php">About Us</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'services.php') ? 'active' : ''; ?>" href="services.php">Services</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'projects.php') ? 'active' : ''; ?>" href="projects.php">Projects</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'events.php') ? 'active' : ''; ?>" href="events.php">Events</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'gallery.php') ? 'active' : ''; ?>" href="gallery.php">Gallery</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'team.php') ? 'active' : ''; ?>" href="team.php">Our Team</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'contact.php') ? 'active' : ''; ?>" href="contact.php">Contact</a>
                            </li>
                        </ul>
                        <div class="ms-lg-3 mt-3 mt-lg-0">
                            <a href="donate.php" class="btn btn-primary">Donate Now</a>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
    </header>
    <!-- Main Content Begins -->
