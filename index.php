<?php
// Set page title
$page_title = "Home";

// Include header
include_once 'includes/header.php';

// Get sliders
$sliders = getSliders();

// Get services
$services = getServices(3);

// Get upcoming events
$upcoming_events = getEvents(3, true, true);

// Get projects
$ongoing_projects = getProjects(3, 'ongoing');

// Get testimonials
$testimonials = getTestimonials();

// Get team members
$team_members = getTeamMembers(4);
?>

<!-- Hero Slider -->
<section class="hero-slider owl-carousel">
    <?php if ($sliders): foreach ($sliders as $slider): ?>
    <div class="item" style="background-image: url('<?php echo getImageUrl($slider['image']); ?>')">
        <div class="overlay">
            <div class="container">
                <div class="hero-content">
                    <?php if (!empty($slider['title'])): ?>
                    <h1 class="animate__animated animate__fadeInDown"><?php echo $slider['title']; ?></h1>
                    <?php endif; ?>
                    
                    <?php if (!empty($slider['subtitle'])): ?>
                    <p class="animate__animated animate__fadeInUp animate__delay-1s"><?php echo $slider['subtitle']; ?></p>
                    <?php endif; ?>
                    
                    <?php if (!empty($slider['button_text']) && !empty($slider['button_url'])): ?>
                    <a href="<?php echo $slider['button_url']; ?>" class="btn btn-primary btn-lg animate__animated animate__fadeInUp animate__delay-2s"><?php echo $slider['button_text']; ?></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; else: ?>
    <div class="item" style="background-image: url('assets/images/slider-1.jpg')">
        <div class="overlay">
            <div class="container">
                <div class="hero-content">
                    <h1 class="animate__animated animate__fadeInDown">Welcome to Future Hope Foundation</h1>
                    <p class="animate__animated animate__fadeInUp animate__delay-1s">Build a Child, You Build a Nation</p>
                    <a href="donate.php" class="btn btn-primary btn-lg animate__animated animate__fadeInUp animate__delay-2s">Donate Now</a>
                </div>
            </div>
        </div>
    </div>
    <div class="item" style="background-image: url('assets/images/slider-2.jpg')">
        <div class="overlay">
            <div class="container">
                <div class="hero-content">
                    <h1 class="animate__animated animate__fadeInDown">Helping Orphans and the Less Privileged</h1>
                    <p class="animate__animated animate__fadeInUp animate__delay-1s">Join our mission to reinstate hope through education, care, and essential support.</p>
                    <a href="about.php" class="btn btn-primary btn-lg animate__animated animate__fadeInUp animate__delay-2s">Learn More</a>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</section>

<!-- About Section -->
<section class="about-section section-padding">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="about-image">
                    <img src="assets/images/about.jpg" alt="About Future Hope" class="img-fluid">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="about-content">
                    <h2>About Future Hope Foundation</h2>
                    <p><?php echo $settings['about_content'] ?? 'Future Hope Foundation was founded during the Ebola epidemic in Sierra Leone in 2014. The current Director General of the foundation, Abu Bakarr Conteh, was a volunteer for the All Political Party Association (APPA), the organization monitoring the Ebola Quarantine Homes in the Western Area Rural District. Officially, the organisation was registered under Corporate Affairs Commission on the 26th January, 2015. Since then, the strive to support and keep children happy started under Future Hope Foundation.'; ?></p>
                    <div class="row mission-vision">
                        <div class="col-md-6 mb-4">
                            <div class="mission-vision-box">
                                <h3>Our Mission</h3>
                                <p><?php echo $settings['mission_statement'] ?? 'To reinstating hope in orphans and less privileged by providing some of their basic needs, medical attention and ensuring they attain the education they desire, to discourage the mentality of dependence, create a social, moral and productive life for them and render assistance to the needy.'; ?></p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="mission-vision-box">
                                <h3>Our Vision</h3>
                                <p><?php echo $settings['vision_statement'] ?? 'To advocate for the rights of vulnerable children and the needy, with special reference to the girl child so that they can live a comfortable life even after some sad experience in their lives.'; ?></p>
                            </div>
                        </div>
                    </div>
                    <a href="about.php" class="btn btn-primary">Learn More</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="services-section section-padding">
    <div class="container">
        <div class="section-title text-center">
            <h2>Our Services</h2>
            <p>We provide various services to help our communities and make a positive impact.</p>
        </div>
        <div class="row">
            <?php if ($services): foreach ($services as $service): ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="service-box">
                    <div class="service-icon">
                        <i class="<?php echo !empty($service['icon']) ? $service['icon'] : 'fas fa-hands-helping'; ?>"></i>
                    </div>
                    <h3><?php echo $service['title']; ?></h3>
                    <p><?php echo truncateText($service['description'], 150); ?></p>
                    <a href="service-details.php?id=<?php echo $service['id']; ?>" class="btn btn-sm btn-outline-primary mt-3">Learn More</a>
                </div>
            </div>
            <?php endforeach; else: ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="service-box">
                    <div class="service-icon">
                        <i class="fas fa-hands-helping"></i>
                    </div>
                    <h3>Community Support</h3>
                    <p>We provide essential resources and support to communities in need, ensuring they have access to food, clothing, and basic necessities.</p>
                    <a href="services.php" class="btn btn-sm btn-outline-primary mt-3">Learn More</a>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="service-box">
                    <div class="service-icon">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <h3>Education Programs</h3>
                    <p>We believe education is the foundation of progress. Our programs provide educational opportunities for underprivileged children and adults.</p>
                    <a href="services.php" class="btn btn-sm btn-outline-primary mt-3">Learn More</a>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="service-box">
                    <div class="service-icon">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    <h3>Healthcare Access</h3>
                    <p>We work to improve access to quality healthcare services in underserved communities through medical camps and health education.</p>
                    <a href="services.php" class="btn btn-sm btn-outline-primary mt-3">Learn More</a>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <div class="text-center mt-4">
            <a href="services.php" class="btn btn-primary">View All Services</a>
        </div>
    </div>
</section>

<!-- Counter Section -->
<section class="counter-section py-5" style="background-image: url('assets/images/counter-bg.jpg'); background-attachment: fixed; background-size: cover;">
    <div class="overlay py-5" style="background-color: rgba(52, 152, 219, 0.8);">
        <div class="container">
            <div class="row text-center">
                <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                    <div class="counter-box text-white">
                        <i class="fas fa-users fa-3x mb-3"></i>
                        <h3 class="counter-number">1500</h3>
                        <p>People Helped</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                    <div class="counter-box text-white">
                        <i class="fas fa-project-diagram fa-3x mb-3"></i>
                        <h3 class="counter-number">48</h3>
                        <p>Projects Completed</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                    <div class="counter-box text-white">
                        <i class="fas fa-donate fa-3x mb-3"></i>
                        <h3 class="counter-number">$<?php echo number_format(getTotalDonations(), 0); ?></h3>
                        <p>Donations Received</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="counter-box text-white">
                        <i class="fas fa-hands-helping fa-3x mb-3"></i>
                        <h3 class="counter-number">200</h3>
                        <p>Volunteers</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Projects Section -->
<section class="projects-section section-padding">
    <div class="container">
        <div class="section-title text-center">
            <h2>Our Projects</h2>
            <p>See the impact we're making through our ongoing projects.</p>
        </div>
        <div class="row">
            <?php if ($ongoing_projects): foreach ($ongoing_projects as $project): 
                $progress_percentage = 0;
                if ($project['goal_amount'] > 0) {
                    $progress_percentage = ($project['raised_amount'] / $project['goal_amount']) * 100;
                    $progress_percentage = min($progress_percentage, 100);
                }
            ?>
            <div class="col-lg-4 col-md-6">
                <div class="project-box">
                    <div class="project-image">
                        <?php if (!empty($project['image'])): ?>
                        <img src="<?php echo getImageUrl($project['image']); ?>" alt="<?php echo $project['title']; ?>" class="img-fluid">
                        <?php else: ?>
                        <img src="assets/images/project-placeholder.jpg" alt="<?php echo $project['title']; ?>" class="img-fluid">
                        <?php endif; ?>
                    </div>
                    <div class="project-content">
                        <h4><?php echo $project['title']; ?></h4>
                        <div class="project-meta mb-3">
                            <span><i class="fas fa-calendar-alt me-1"></i> Started: <?php echo formatDate($project['start_date']); ?></span>
                        </div>
                        <p><?php echo truncateText($project['description'], 100); ?></p>
                        <div class="project-progress">
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: <?php echo $progress_percentage; ?>%" aria-valuenow="<?php echo $progress_percentage; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="project-stats mt-2">
                                <span>Raised: $<?php echo number_format($project['raised_amount'], 2); ?></span>
                                <span>Goal: $<?php echo number_format($project['goal_amount'], 2); ?></span>
                            </div>
                        </div>
                        <a href="project-details.php?id=<?php echo $project['id']; ?>" class="btn btn-sm btn-outline-primary mt-3">View Details</a>
                    </div>
                </div>
            </div>
            <?php endforeach; else: ?>
            <div class="col-lg-4 col-md-6">
                <div class="project-box">
                    <div class="project-image">
                        <img src="assets/images/project-1.jpg" alt="Clean Water Initiative" class="img-fluid">
                    </div>
                    <div class="project-content">
                        <h4>Clean Water Initiative</h4>
                        <div class="project-meta mb-3">
                            <span><i class="fas fa-calendar-alt me-1"></i> Started: Jan 15, 2025</span>
                        </div>
                        <p>Providing clean water access to rural communities facing water scarcity and contamination issues.</p>
                        <div class="project-progress">
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: 65%" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="project-stats mt-2">
                                <span>Raised: $32,500</span>
                                <span>Goal: $50,000</span>
                            </div>
                        </div>
                        <a href="projects.php" class="btn btn-sm btn-outline-primary mt-3">View Details</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="project-box">
                    <div class="project-image">
                        <img src="assets/images/project-2.jpg" alt="Education For All" class="img-fluid">
                    </div>
                    <div class="project-content">
                        <h4>Education For All</h4>
                        <div class="project-meta mb-3">
                            <span><i class="fas fa-calendar-alt me-1"></i> Started: Mar 5, 2025</span>
                        </div>
                        <p>Building schools and providing educational materials for children in underprivileged communities.</p>
                        <div class="project-progress">
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: 40%" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="project-stats mt-2">
                                <span>Raised: $20,000</span>
                                <span>Goal: $50,000</span>
                            </div>
                        </div>
                        <a href="projects.php" class="btn btn-sm btn-outline-primary mt-3">View Details</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="project-box">
                    <div class="project-image">
                        <img src="assets/images/project-3.jpg" alt="Healthcare Outreach" class="img-fluid">
                    </div>
                    <div class="project-content">
                        <h4>Healthcare Outreach</h4>
                        <div class="project-meta mb-3">
                            <span><i class="fas fa-calendar-alt me-1"></i> Started: Feb 20, 2025</span>
                        </div>
                        <p>Providing medical services and health education to remote areas with limited healthcare access.</p>
                        <div class="project-progress">
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="project-stats mt-2">
                                <span>Raised: $37,500</span>
                                <span>Goal: $50,000</span>
                            </div>
                        </div>
                        <a href="projects.php" class="btn btn-sm btn-outline-primary mt-3">View Details</a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <div class="text-center mt-4">
            <a href="projects.php" class="btn btn-primary">View All Projects</a>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section py-5" style="background-image: url('assets/images/cta-bg.jpg'); background-size: cover; background-position: center;">
    <div class="overlay py-5" style="background-color: rgba(44, 62, 80, 0.85);">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8 text-center text-lg-start mb-4 mb-lg-0">
                    <h2 class="text-white">Make a Difference Today</h2>
                    <p class="text-white mb-0">Your donation can change lives and help us create a better future for those in need.</p>
                </div>
                <div class="col-lg-4 text-center text-lg-end">
                    <a href="donate.php" class="btn btn-primary btn-lg">Donate Now</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Events Section -->
<section class="events-section section-padding">
    <div class="container">
        <div class="section-title text-center">
            <h2>Upcoming Events</h2>
            <p>Join us at our upcoming events and be a part of the positive change.</p>
        </div>
        <div class="row">
            <?php if ($upcoming_events): foreach ($upcoming_events as $event): ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="event-box">
                    <div class="event-image">
                        <?php if (!empty($event['image'])): ?>
                        <img src="<?php echo $event['image']; ?>" alt="<?php echo $event['title']; ?>" class="img-fluid">
                        <?php else: ?>
                        <img src="assets/images/event-placeholder.jpg" alt="<?php echo $event['title']; ?>" class="img-fluid">
                        <?php endif; ?>
                        <div class="event-date">
                            <span><?php echo date('d', strtotime($event['event_date'])); ?></span>
                            <small><?php echo date('M', strtotime($event['event_date'])); ?></small>
                        </div>
                    </div>
                    <div class="event-content">
                        <h4><?php echo $event['title']; ?></h4>
                        <div class="event-meta">
                            <p><i class="far fa-clock me-1"></i> <?php echo date('h:i A', strtotime($event['event_time'])); ?></p>
                            <p><i class="fas fa-map-marker-alt me-1"></i> <?php echo $event['location']; ?></p>
                        </div>
                        <p><?php echo truncateText($event['description'], 100); ?></p>
                        <a href="event-details.php?id=<?php echo $event['id']; ?>" class="btn btn-sm btn-outline-primary mt-3">View Details</a>
                    </div>
                </div>
            </div>
            <?php endforeach; else: ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="event-box">
                    <div class="event-image">
                        <img src="assets/images/event-1.jpg" alt="Community Fundraiser" class="img-fluid">
                        <div class="event-date">
                            <span>15</span>
                            <small>Oct</small>
                        </div>
                    </div>
                    <div class="event-content">
                        <h4>Community Fundraiser</h4>
                        <div class="event-meta">
                            <p><i class="far fa-clock me-1"></i> 6:00 PM</p>
                            <p><i class="fas fa-map-marker-alt me-1"></i> Community Center</p>
                        </div>
                        <p>Join us for a night of fun and fundraising to support our community projects.</p>
                        <a href="events.php" class="btn btn-sm btn-outline-primary mt-3">View Details</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="event-box">
                    <div class="event-image">
                        <img src="assets/images/event-2.jpg" alt="Charity Marathon" class="img-fluid">
                        <div class="event-date">
                            <span>22</span>
                            <small>Oct</small>
                        </div>
                    </div>
                    <div class="event-content">
                        <h4>Charity Marathon</h4>
                        <div class="event-meta">
                            <p><i class="far fa-clock me-1"></i> 7:00 AM</p>
                            <p><i class="fas fa-map-marker-alt me-1"></i> City Park</p>
                        </div>
                        <p>Run for a cause! Join our annual charity marathon to support healthcare initiatives.</p>
                        <a href="events.php" class="btn btn-sm btn-outline-primary mt-3">View Details</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="event-box">
                    <div class="event-image">
                        <img src="assets/images/event-3.jpg" alt="Volunteer Training" class="img-fluid">
                        <div class="event-date">
                            <span>29</span>
                            <small>Oct</small>
                        </div>
                    </div>
                    <div class="event-content">
                        <h4>Volunteer Training</h4>
                        <div class="event-meta">
                            <p><i class="far fa-clock me-1"></i> 10:00 AM</p>
                            <p><i class="fas fa-map-marker-alt me-1"></i> Main Office</p>
                        </div>
                        <p>Learn how you can make a difference by becoming a volunteer for our organization.</p>
                        <a href="events.php" class="btn btn-sm btn-outline-primary mt-3">View Details</a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <div class="text-center mt-4">
            <a href="events.php" class="btn btn-primary">View All Events</a>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="testimonials-section section-padding" style="background-color: #f9f9f9;">
    <div class="container">
        <div class="section-title text-center">
            <h2>Testimonials</h2>
            <p>What people say about our work and impact.</p>
        </div>
        <div class="testimonial-slider owl-carousel">
            <?php if ($testimonials): foreach ($testimonials as $testimonial): ?>
            <div class="testimonial-box">
                <?php if (!empty($testimonial['image'])): ?>
                <div class="testimonial-image">
                    <img src="<?php echo $testimonial['image']; ?>" alt="<?php echo $testimonial['name']; ?>" class="img-fluid">
                </div>
                <?php endif; ?>
                <div class="testimonial-text">
                    <p><?php echo $testimonial['testimonial']; ?></p>
                </div>
                <h4 class="testimonial-name"><?php echo $testimonial['name']; ?></h4>
                <?php if (!empty($testimonial['position'])): ?>
                <p class="testimonial-position"><?php echo $testimonial['position']; ?></p>
                <?php endif; ?>
            </div>
            <?php endforeach; else: ?>
            <div class="testimonial-box">
                <div class="testimonial-image">
                    <img src="assets/images/testimonial-1.jpg" alt="John Smith" class="img-fluid">
                </div>
                <div class="testimonial-text">
                    <p>"Future Hope's clean water project has transformed our community. Now our children have access to safe drinking water and waterborne diseases have significantly reduced."</p>
                </div>
                <h4 class="testimonial-name">John Smith</h4>
                <p class="testimonial-position">Community Leader</p>
            </div>
            <div class="testimonial-box">
                <div class="testimonial-image">
                    <img src="assets/images/testimonial-2.jpg" alt="Sarah Johnson" class="img-fluid">
                </div>
                <div class="testimonial-text">
                    <p>"The education program has given my daughter a chance for a better future. She's now excelling in school and has dreams of becoming a doctor."</p>
                </div>
                <h4 class="testimonial-name">Sarah Johnson</h4>
                <p class="testimonial-position">Parent</p>
            </div>
            <div class="testimonial-box">
                <div class="testimonial-image">
                    <img src="assets/images/testimonial-3.jpg" alt="Michael Brown" class="img-fluid">
                </div>
                <div class="testimonial-text">
                    <p>"As a volunteer with Future Hope, I've witnessed firsthand the incredible impact they're making. Their dedication to helping others is truly inspiring."</p>
                </div>
                <h4 class="testimonial-name">Michael Brown</h4>
                <p class="testimonial-position">Volunteer</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Team Section -->
<section class="team-section section-padding">
    <div class="container">
        <div class="section-title text-center">
            <h2>Our Team</h2>
            <p>Meet the dedicated individuals behind our mission.</p>
        </div>
        <div class="row">
            <?php if ($team_members): foreach ($team_members as $member): ?>
            <div class="col-lg-3 col-md-6">
                <div class="team-member">
                    <div class="team-image">
                        <?php if (!empty($member['image'])): ?>
                        <img src="<?php echo $member['image']; ?>" alt="<?php echo $member['name']; ?>" class="img-fluid">
                        <?php else: ?>
                        <img src="assets/images/team-placeholder.jpg" alt="<?php echo $member['name']; ?>" class="img-fluid">
                        <?php endif; ?>
                        <div class="team-social">
                            <?php if (!empty($member['facebook_url'])): ?>
                            <a href="<?php echo $member['facebook_url']; ?>" target="_blank"><i class="fab fa-facebook-f"></i></a>
                            <?php endif; ?>
                            
                            <?php if (!empty($member['twitter_url'])): ?>
                            <a href="<?php echo $member['twitter_url']; ?>" target="_blank"><i class="fab fa-twitter"></i></a>
                            <?php endif; ?>
                            
                            <?php if (!empty($member['linkedin_url'])): ?>
                            <a href="<?php echo $member['linkedin_url']; ?>" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="team-content">
                        <h4><?php echo $member['name']; ?></h4>
                        <p><?php echo $member['position']; ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; else: ?>
            <div class="col-lg-3 col-md-6">
                <div class="team-member">
                    <div class="team-image">
                        <img src="assets/images/team-1.jpg" alt="Michael Anderson" class="img-fluid">
                        <div class="team-social">
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                    <div class="team-content">
                        <h4>Michael Anderson</h4>
                        <p>Executive Director</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="team-member">
                    <div class="team-image">
                        <img src="assets/images/team-2.jpg" alt="Emily Rodriguez" class="img-fluid">
                        <div class="team-social">
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                    <div class="team-content">
                        <h4>Emily Rodriguez</h4>
                        <p>Program Director</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="team-member">
                    <div class="team-image">
                        <img src="assets/images/team-3.jpg" alt="David Wilson" class="img-fluid">
                        <div class="team-social">
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                    <div class="team-content">
                        <h4>David Wilson</h4>
                        <p>Finance Manager</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="team-member">
                    <div class="team-image">
                        <img src="assets/images/team-4.jpg" alt="Jennifer Lee" class="img-fluid">
                        <div class="team-social">
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                    <div class="team-content">
                        <h4>Jennifer Lee</h4>
                        <p>Volunteer Coordinator</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <div class="text-center mt-4">
            <a href="team.php" class="btn btn-primary">View All Members</a>
        </div>
    </div>
</section>

<!-- Partners Section -->
<section class="partners-section py-5">
    <div class="container">
        <div class="section-title text-center">
            <h2>Our Partners</h2>
            <p>We're proud to work with organizations that share our vision.</p>
        </div>
        <div class="partners-slider owl-carousel">
            <div class="partner-item">
                <img src="assets/images/partner-1.png" alt="Partner 1" class="img-fluid">
            </div>
            <div class="partner-item">
                <img src="assets/images/partner-2.png" alt="Partner 2" class="img-fluid">
            </div>
            <div class="partner-item">
                <img src="assets/images/partner-3.png" alt="Partner 3" class="img-fluid">
            </div>
            <div class="partner-item">
                <img src="assets/images/partner-4.png" alt="Partner 4" class="img-fluid">
            </div>
            <div class="partner-item">
                <img src="assets/images/partner-5.png" alt="Partner 5" class="img-fluid">
            </div>
        </div>
    </div>
</section>

<!-- Newsletter Section -->
<section class="newsletter-section py-5" style="background-color: #3498db;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h3 class="text-white">Subscribe to Our Newsletter</h3>
                <p class="text-white mb-0">Stay updated with our latest news, events, and initiatives.</p>
            </div>
            <div class="col-lg-6">
                <form id="newsletter-form" class="newsletter-form">
                    <div class="input-group">
                        <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                        <button type="submit" class="btn btn-light">Subscribe</button>
                    </div>
                    <div class="form-message mt-2 text-white"></div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php
// Include footer
include_once 'includes/footer.php';
?>
