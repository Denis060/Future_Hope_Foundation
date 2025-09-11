<?php
// Set page title
$page_title = "Page Not Found";
$page_description = "The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.";
include 'includes/header.php';
?>

<!-- 404 Section -->
<section class="error-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <div class="error-content fade-in">
                    <h1 class="display-1 text-primary">404</h1>
                    <h2 class="section-title">Page Not Found</h2>
                    <p class="lead mb-4">The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.</p>
                    <div class="error-actions">
                        <a href="index.php" class="btn btn-primary me-3"><i class="fas fa-home me-2"></i>Go to Home</a>
                        <a href="contact.php" class="btn btn-outline-primary"><i class="fas fa-envelope me-2"></i>Contact Support</a>
                    </div>
                    <div class="mt-5">
                        <h4>Here are some helpful links:</h4>
                        <div class="row mt-3">
                            <div class="col-md-4 mb-3">
                                <a href="about.php" class="card h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-info-circle fa-3x text-primary mb-3"></i>
                                        <h5>About Us</h5>
                                        <p class="mb-0">Learn more about our mission</p>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-4 mb-3">
                                <a href="projects.php" class="card h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-project-diagram fa-3x text-primary mb-3"></i>
                                        <h5>Our Projects</h5>
                                        <p class="mb-0">See our ongoing initiatives</p>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-4 mb-3">
                                <a href="donate.php" class="card h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-hand-holding-heart fa-3x text-primary mb-3"></i>
                                        <h5>Donate Now</h5>
                                        <p class="mb-0">Help make a difference</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
