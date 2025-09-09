<?php
// Include header
$page_title = "Our Services";
include 'includes/header.php';

// Get all active services
$services = getServices(0, true);
?>

<!-- Page Title -->
<section class="page-title-section bg-primary">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="text-white">Our Services</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent mb-0 p-0">
                        <li class="breadcrumb-item"><a href="index.php" class="text-white">Home</a></li>
                        <li class="breadcrumb-item active text-white-50" aria-current="page">Services</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="services-section py-5">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="section-title">What We Do</h2>
                <p class="lead">Dedicated to making a difference in our community through these vital services.</p>
            </div>
        </div>
        
        <div class="row g-4">
            <?php if (!empty($services)): foreach ($services as $service): ?>
            <div class="col-md-6 col-lg-4">
                <div class="service-card h-100 border rounded shadow-sm p-4">
                    <?php if (!empty($service['image'])): ?>
                    <img src="<?php echo $service['image']; ?>" alt="<?php echo $service['title']; ?>" class="img-fluid rounded mb-3">
                    <?php else: ?>
                    <div class="service-icon mb-3">
                        <i class="<?php echo $service['icon'] ?: 'fas fa-hands-helping'; ?> fa-3x text-primary"></i>
                    </div>
                    <?php endif; ?>
                    
                    <h3 class="h4"><?php echo $service['title']; ?></h3>
                    <div class="service-description">
                        <?php echo $service['description']; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; else: ?>
            <div class="col-12">
                <div class="alert alert-info" role="alert">
                    <p class="text-center mb-0">No services available at the moment. Please check back later.</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="cta-section py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h3>Want to Help Our Cause?</h3>
                <p class="mb-4">Join our team of volunteers or make a donation to support our work in the community.</p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="donate.php" class="btn btn-primary">Make a Donation</a>
                    <a href="contact.php" class="btn btn-outline-secondary">Contact Us</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Include footer
include 'includes/footer.php';
?>
