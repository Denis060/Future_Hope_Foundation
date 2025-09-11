<?php
// Include header
$page_title = "About Us";
include 'includes/header.php';

// Get settings
$settings = getSettings($conn);
?>

<!-- Page Title -->
<section class="page-title-section bg-primary">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="text-white">About Us</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent mb-0 p-0">
                        <li class="breadcrumb-item"><a href="index.php" class="text-white">Home</a></li>
                        <li class="breadcrumb-item active text-white-50" aria-current="page">About Us</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- About Foundation -->
<section class="about-section py-5">
    <div class="container">
        <div class="row align-items-center mb-5">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="rounded overflow-hidden shadow-sm">
                    <img src="<?php echo !empty($settings['site_logo']) ? $settings['site_logo'] : 'assets/images/logo.png'; ?>" alt="Future Hope Foundation" class="img-fluid rounded mx-auto d-block" style="max-width: 300px;">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="section-title mb-4">
                    <h2>Future Hope Foundation (FHF)</h2>
                    <div class="separator-line"></div>
                </div>
                <div class="mb-4">
                    <h4>Our Motto</h4>
                    <p class="lead font-weight-bold text-primary">"Build a Child, You Build a Nation"</p>
                </div>
                <div class="mb-4">
                    <h4>Mission Statement</h4>
                    <p>To reinstating hope in orphans and less privileged by providing some of their basic needs, medical attention and ensuring they attain the education they desire, to discourage the mentality of dependence, create a social, moral and productive life for them and render assistance to the needy.</p>
                </div>
                <div class="mb-4">
                    <h4>Our Aim</h4>
                    <p>To advocate for the rights of vulnerable children and the needy, with special reference to the girl child so that they can live a comfortable life even after some sad experience in their lives.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Background Section -->
<section class="background-section py-5 bg-light">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-12 text-center">
                <h2 class="section-title fade-in">Our Journey</h2>
                <p class="lead text-muted mb-0 fade-in">A Brief Background About Future Hope Foundation</p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-12">
                <div class="timeline">
                    <!-- 2014: Ebola Epidemic -->
                    <div class="timeline-item fade-in">
                        <div class="timeline-year">
                            <span>2014 Ebola Crisis</span>
                        </div>
                        <div class="timeline-content">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body p-4">
                                    <h4 class="timeline-title">Ebola Epidemic Crisis</h4>
                                    <div class="timeline-marker"></div>
                                    <p>Future Hope Foundation was conceptualized during the Ebola epidemic in Sierra Leone. Abu Bakarr Conteh, now Director General, worked as a volunteer with the All Political Party Association (APPA), monitoring Ebola Quarantine Homes in the Western Area Rural District.</p>
                                    
                                    <div class="timeline-gallery row mt-3 g-3">
                                        <div class="col-md-6">
                                            <div class="crisis-point">
                                                <i class="fas fa-utensils text-danger"></i>
                                                <h5 class="h6">Food Shortages</h5>
                                                <p class="small">Serious food shortages and hunger in Quarantine Homes made life for victims extremely difficult.</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="crisis-point">
                                                <i class="fas fa-user-shield text-danger"></i>
                                                <h5 class="h6">Security Issues</h5>
                                                <p class="small">Lack of proper security forced victims to leave quarantine areas to find food, causing virus spread.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Late 2014: Inspiration -->
                    <div class="timeline-item fade-in">
                        <div class="timeline-year">
                            <span>Late 2014<br>Inspiration</span>
                        </div>
                        <div class="timeline-content">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body p-4">
                                    <h4 class="timeline-title">The Spark of Inspiration</h4>
                                    <div class="timeline-marker"></div>
                                    <p>During his service with APPA, Abu Bakarr Conteh was deeply moved by the suffering of children. Witnessing their struggles firsthand, he felt compelled to take action beyond his official duties.</p>
                                    <p>Motivated by compassion, he used his own resources, including personal allowances, to provide for children in need. Their faces lit up with hope, inspiring him to create a permanent solution to their plight.</p>
                                    <div class="text-center mt-3">
                                        <span class="badge bg-primary p-2"><i class="fas fa-lightbulb me-1"></i> Birth of a Vision</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- January 2015: Official Registration -->
                    <div class="timeline-item fade-in">
                        <div class="timeline-year">
                            <span>January 26, 2015</span>
                        </div>
                        <div class="timeline-content">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body p-4">
                                    <h4 class="timeline-title">Official Foundation Establishment</h4>
                                    <div class="timeline-marker"></div>
                                    <p>With financial support from Osman Jalloh who believed in the vision, Future Hope Foundation was officially registered under the Corporate Affairs Commission.</p>
                                    <p>This marked the beginning of a structured effort to support vulnerable children and restore hope in their lives through sustainable programs.</p>
                                    <div class="d-flex align-items-center justify-content-center mt-3">
                                        <i class="fas fa-certificate text-success me-2"></i>
                                        <span class="fw-bold">Official Registration Document #FHF-2015-01</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 2015-Present: Growth and Impact -->
                    <div class="timeline-item fade-in">
                        <div class="timeline-year">
                            <span>2015-Present<br>Growth</span>
                        </div>
                        <div class="timeline-content">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body p-4">
                                    <h4 class="timeline-title">A Decade of Growth and Impact</h4>
                                    <div class="timeline-marker"></div>
                                    <p>Since its establishment, Future Hope Foundation has been dedicated to bringing smiles to children's faces and hope to their hearts. Our programs have expanded across communities, focusing on education, healthcare, advocacy, and support for vulnerable children.</p>
                                    
                                    <div class="row mt-4 impact-stats text-center g-3">
                                        <div class="col-md-3 col-sm-6">
                                            <div class="impact-item">
                                                <i class="fas fa-users text-primary"></i>
                                                <h5>1,000+</h5>
                                                <p>Children Supported</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="impact-item">
                                                <i class="fas fa-graduation-cap text-primary"></i>
                                                <h5>500+</h5>
                                                <p>Educational Scholarships</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="impact-item">
                                                <i class="fas fa-hands-helping text-primary"></i>
                                                <h5>20+</h5>
                                                <p>Community Programs</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="impact-item">
                                                <i class="fas fa-map-marker-alt text-primary"></i>
                                                <h5>10+</h5>
                                                <p>Communities Served</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Future -->
                    <div class="timeline-item fade-in">
                        <div class="timeline-year">
                            <span>Future<br>Vision</span>
                        </div>
                        <div class="timeline-content">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body p-4">
                                    <h4 class="timeline-title">Our Vision for the Future</h4>
                                    <div class="timeline-marker"></div>
                                    <p>As we look ahead, Future Hope Foundation remains committed to expanding our reach and deepening our impact. We envision a world where every child has access to education, healthcare, and opportunities to thrive regardless of their circumstances.</p>
                                    
                                    <div class="text-center mt-3">
                                        <a href="donate.php" class="btn btn-primary">Join Our Mission</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Objectives -->
<section class="objectives-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center mb-5">
                <h2 class="section-title fade-in">Our Objectives</h2>
                <p class="lead text-muted mb-0 fade-in">Working together to create lasting impact for children in need</p>
            </div>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="row g-4">
                    <!-- Objective 1 -->
                    <div class="col-md-6 col-lg-4">
                        <div class="objective-card h-100 fade-in">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body p-4">
                                    <div class="objective-icon mb-3">
                                        <i class="fas fa-graduation-cap text-primary"></i>
                                    </div>
                                    <h4 class="card-title h5 mb-3">Educational Support</h4>
                                    <p class="card-text">To promote educational programs for orphans and the vulnerable children in our communities.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Objective 2 -->
                    <div class="col-md-6 col-lg-4">
                        <div class="objective-card h-100 fade-in">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body p-4">
                                    <div class="objective-icon mb-3">
                                        <i class="fas fa-bullhorn text-primary"></i>
                                    </div>
                                    <h4 class="card-title h5 mb-3">Child Rights Advocacy</h4>
                                    <p class="card-text">To seek for the right of children through sensitisation and advocacy with community leaders by conducting seminars, workshops and radio programmes.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Objective 3 -->
                    <div class="col-md-6 col-lg-4">
                        <div class="objective-card h-100 fade-in">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body p-4">
                                    <div class="objective-icon mb-3">
                                        <i class="fas fa-hands-helping text-primary"></i>
                                    </div>
                                    <h4 class="card-title h5 mb-3">Relief & Development</h4>
                                    <p class="card-text">To initiate and promote programs concerned with provision of relief aid, rehabilitation and development of children.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Objective 4 -->
                    <div class="col-md-6 col-lg-4">
                        <div class="objective-card h-100 fade-in">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body p-4">
                                    <div class="objective-icon mb-3">
                                        <i class="fas fa-building text-primary"></i>
                                    </div>
                                    <h4 class="card-title h5 mb-3">Community Building</h4>
                                    <p class="card-text">To build an academically sound, moral upright and self-contained society.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Objective 5 -->
                    <div class="col-md-6 col-lg-4">
                        <div class="objective-card h-100 fade-in">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body p-4">
                                    <div class="objective-icon mb-3">
                                        <i class="fas fa-award text-primary"></i>
                                    </div>
                                    <h4 class="card-title h5 mb-3">Education Quality</h4>
                                    <p class="card-text">To encourage good and qualitative education through sponsorship.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Objective 6 -->
                    <div class="col-md-6 col-lg-4">
                        <div class="objective-card h-100 fade-in">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body p-4">
                                    <div class="objective-icon mb-3">
                                        <i class="fas fa-globe-africa text-primary"></i>
                                    </div>
                                    <h4 class="card-title h5 mb-3">Global Partnerships</h4>
                                    <p class="card-text">To coordinate activities with potential donors, charitable organisations and other NGOs nationally and internationally.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Objective 7 -->
                    <div class="col-md-6 col-lg-4">
                        <div class="objective-card h-100 fade-in">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body p-4">
                                    <div class="objective-icon mb-3">
                                        <i class="fas fa-chart-line text-primary"></i>
                                    </div>
                                    <h4 class="card-title h5 mb-3">Resource Management</h4>
                                    <p class="card-text">To put in place effect management of funds and all donations geared towards benefiting orphans and the less privileged.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Objective 8 -->
                    <div class="col-md-6 col-lg-4">
                        <div class="objective-card h-100 fade-in">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body p-4">
                                    <div class="objective-icon mb-3">
                                        <i class="fas fa-female text-primary"></i>
                                    </div>
                                    <h4 class="card-title h5 mb-3">Girl Child Support</h4>
                                    <p class="card-text">To support girl child education by linking them to organisations that gives scholarships in any part of the world.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Objective 9 -->
                    <div class="col-md-6 col-lg-4">
                        <div class="objective-card h-100 fade-in">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body p-4">
                                    <div class="objective-icon mb-3">
                                        <i class="fas fa-balance-scale text-primary"></i>
                                    </div>
                                    <h4 class="card-title h5 mb-3">Gender Equality</h4>
                                    <p class="card-text">To undertake programs on children's rights and gender awareness through rural community sensitisations to promote gender equality.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="cta-section py-5 bg-primary text-white">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h3>Join Us in Making a Difference</h3>
                <p class="mb-4">Support our mission to help vulnerable children and create lasting change in their lives.</p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="donate.php" class="btn btn-light text-primary">Make a Donation</a>
                    <a href="contact.php" class="btn btn-outline-light">Contact Us</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Add custom CSS for this page -->
<style>
    .separator-line {
        height: 3px;
        width: 60px;
        background-color: #3498db;
        margin-bottom: 20px;
    }
    
    .objectives-list {
        padding-left: 20px;
    }
    
    .objectives-list li {
        margin-bottom: 15px;
        position: relative;
        padding-left: 15px;
    }
    
    .objectives-list li::before {
        content: "";
        position: absolute;
        left: -5px;
        top: 8px;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #3498db;
    }
</style>

<?php
// Include footer
include 'includes/footer.php';
?>
