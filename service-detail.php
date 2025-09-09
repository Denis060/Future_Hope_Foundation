<?php
// Include database configuration and functions
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get service ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get service details
$service = getRecordById('services', $id);

// If service not found, redirect to services page
if (!$service || !$service['is_active']) {
    redirect('services.php');
}

// Set page title
$page_title = $service['title'];

// Include header
include 'includes/header.php';

// Get related services
$related_sql = "SELECT * FROM services WHERE id != {$id} AND is_active = 1 ORDER BY RAND() LIMIT 3";
$related_result = $conn->query($related_sql);
$related_services = [];

if ($related_result->num_rows > 0) {
    while ($row = $related_result->fetch_assoc()) {
        $related_services[] = $row;
    }
}
?>

<!-- Page Title -->
<section class="page-title-section bg-primary">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="text-white"><?php echo $service['title']; ?></h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent mb-0 p-0">
                        <li class="breadcrumb-item"><a href="index.php" class="text-white">Home</a></li>
                        <li class="breadcrumb-item"><a href="services.php" class="text-white">Services</a></li>
                        <li class="breadcrumb-item active text-white-50" aria-current="page"><?php echo $service['title']; ?></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Service Detail Section -->
<section class="service-detail-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="service-detail-content">
                    <?php if (!empty($service['image'])): ?>
                    <img src="<?php echo $service['image']; ?>" alt="<?php echo $service['title']; ?>" class="img-fluid rounded mb-4">
                    <?php endif; ?>
                    
                    <div class="service-content">
                        <?php echo $service['description']; ?>
                    </div>
                    
                    <div class="mt-4">
                        <a href="contact.php" class="btn btn-primary">Get in Touch</a>
                        <a href="services.php" class="btn btn-outline-secondary ms-2">View All Services</a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 mt-4 mt-lg-0">
                <div class="sidebar">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Our Services</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled service-list">
                                <?php 
                                $all_services = getServices(0, true);
                                foreach ($all_services as $s): 
                                ?>
                                <li class="<?php echo $s['id'] == $id ? 'active' : ''; ?>">
                                    <a href="service-detail.php?id=<?php echo $s['id']; ?>">
                                        <?php if (!empty($s['icon'])): ?>
                                        <i class="<?php echo $s['icon']; ?>"></i>
                                        <?php endif; ?>
                                        <?php echo $s['title']; ?>
                                    </a>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Need Help?</h5>
                        </div>
                        <div class="card-body">
                            <p>Have questions about our services or want to get involved? Reach out to us today!</p>
                            
                            <div class="mb-3">
                                <i class="fas fa-phone-alt text-primary me-2"></i> <?php echo $settings['site_phone'] ?? '+123456789'; ?>
                            </div>
                            
                            <div class="mb-3">
                                <i class="fas fa-envelope text-primary me-2"></i> <?php echo $settings['site_email'] ?? 'info@futurehope.org'; ?>
                            </div>
                            
                            <a href="contact.php" class="btn btn-primary btn-sm">Contact Us</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if (!empty($related_services)): ?>
        <div class="related-services mt-5">
            <h3 class="mb-4">Related Services</h3>
            <div class="row g-4">
                <?php foreach ($related_services as $related): ?>
                <div class="col-md-4">
                    <div class="service-card h-100 border rounded shadow-sm p-3">
                        <?php if (!empty($related['image'])): ?>
                        <img src="<?php echo $related['image']; ?>" alt="<?php echo $related['title']; ?>" class="img-fluid rounded mb-3">
                        <?php else: ?>
                        <div class="service-icon mb-3">
                            <i class="<?php echo $related['icon'] ?: 'fas fa-hands-helping'; ?> fa-3x text-primary"></i>
                        </div>
                        <?php endif; ?>
                        
                        <h4 class="h5"><?php echo $related['title']; ?></h4>
                        <p><?php echo truncateText(strip_tags($related['description']), 100); ?></p>
                        <a href="service-detail.php?id=<?php echo $related['id']; ?>" class="btn btn-sm btn-outline-primary">Learn More</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Call to Action -->
<section class="cta-section py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h3>Support Our Cause</h3>
                <p class="mb-4">Your contribution can make a significant difference in the lives of those we serve.</p>
                <a href="donate.php" class="btn btn-primary">Make a Donation</a>
            </div>
        </div>
    </div>
</section>

<style>
    .service-list li {
        margin-bottom: 10px;
        padding-bottom: 10px;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .service-list li:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }
    
    .service-list li a {
        color: #333;
        text-decoration: none;
        transition: all 0.3s;
        display: block;
        padding: 5px 0;
    }
    
    .service-list li a:hover,
    .service-list li.active a {
        color: #3498db;
    }
    
    .service-list li i {
        margin-right: 10px;
    }
    
    .service-content img {
        max-width: 100%;
        height: auto;
        margin: 15px 0;
    }
</style>

<?php
// Include footer
include 'includes/footer.php';
?>
