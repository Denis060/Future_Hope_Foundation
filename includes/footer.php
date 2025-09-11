    <!-- Main Content Ends -->
    
    <!-- Footer -->
    <footer class="footer bg-dark text-white pt-5 pb-3">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="footer-widget">
                        <h4 class="widget-title">About Us</h4>
                        <p>
                            <?php
                            echo truncateText($settings['about_content'] ?? 'Future Hope is a nonprofit charitable organization dedicated to making a positive impact in our communities through various humanitarian services.', 200);
                            ?>
                        </p>
                        <div class="social-links mt-3">
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
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="footer-widget">
                        <h4 class="widget-title">Quick Links</h4>
                        <ul class="footer-links">
                            <li><a href="index.php">Home</a></li>
                            <li><a href="about.php">About Us</a></li>
                            <li><a href="services.php">Services</a></li>
                            <li><a href="projects.php">Projects</a></li>
                            <li><a href="events.php">Events</a></li>
                            <li><a href="gallery.php">Gallery</a></li>
                            <li><a href="contact.php">Contact Us</a></li>
                            <li><a href="donate.php">Donate</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="footer-widget">
                        <h4 class="widget-title">Recent Events</h4>
                        <?php
                        $recent_events = getEvents(2);
                        if ($recent_events) {
                            foreach ($recent_events as $event) {
                                ?>
                                <div class="recent-event mb-3">
                                    <h6><a href="event-details.php?id=<?php echo $event['id']; ?>"><?php echo $event['title']; ?></a></h6>
                                    <p class="mb-0"><i class="far fa-calendar-alt me-1"></i> <?php echo formatDate($event['event_date']); ?></p>
                                    <p class="mb-0"><i class="far fa-clock me-1"></i> <?php echo date('h:i A', strtotime($event['event_time'])); ?></p>
                                </div>
                                <?php
                            }
                        } else {
                            echo '<p>No upcoming events.</p>';
                        }
                        ?>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="footer-widget">
                        <h4 class="widget-title">Contact Info</h4>
                        <div class="contact-info">
                            <?php if (!empty($settings['site_address'])): ?>
                            <div class="d-flex mb-3">
                                <i class="fas fa-map-marker-alt me-3 mt-1"></i>
                                <p class="mb-0"><?php echo $settings['site_address']; ?></p>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($settings['site_email'])): ?>
                            <div class="d-flex mb-3">
                                <i class="fas fa-envelope me-3 mt-1"></i>
                                <p class="mb-0"><?php echo $settings['site_email']; ?></p>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($settings['site_phone'])): ?>
                            <div class="d-flex mb-3">
                                <i class="fas fa-phone me-3 mt-1"></i>
                                <p class="mb-0"><?php echo $settings['site_phone']; ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="copyright text-center">
                        <p>&copy; <?php echo date('Y'); ?> <?php echo $settings['site_name']; ?>. All Rights Reserved.</p>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Back to Top -->
    <a href="#" class="back-to-top" id="back-to-top"><i class="fas fa-arrow-up"></i></a>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Owl Carousel -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    
    <!-- Isotope (for filtering gallery) -->
    <script src="https://unpkg.com/isotope-layout@3/dist/isotope.pkgd.min.js"></script>
    
    <!-- Magnific Popup (for lightbox gallery) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js"></script>
    
    <!-- WOW JS (for scroll animations) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>
    
    <!-- Custom JS -->
    <script src="assets/js/main.js"></script>
    <script src="assets/js/enhanced.js"></script>
    
    <!-- Fix for mobile date widget -->
    <script src="assets/js/fix-mobile-date.js"></script>
    
    <!-- Page specific JS -->
    <?php if (isset($additional_js)): echo $additional_js; endif; ?>
    
    <!-- Schema.org structured data for SEO -->
    <?php include 'includes/schema.php'; ?>
</body>
</html>
