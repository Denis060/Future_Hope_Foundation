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
        <div class="row">
            <div class="col-lg-12">
                <h2 class="text-center mb-4">A Brief Background About Future Hope Foundation</h2>
                <div class="card border-0 shadow-sm p-4">
                    <div class="card-body">
                        <p>Future Hope Foundation was founded during the Ebola epidemic in Sierra Leone in 2014. The current Director General of the foundation, Abu Bakarr Conteh, was a volunteer for the All Political Party Association (APPA), the organization monitoring the Ebola Quarantine Homes in the Western Area Rural District.</p>
                        
                        <p>A lots of unfortunate and traumatic incidences took place during that sad period of our country's history, ranging from displacement, improper monitoring mechanisms, food shortage and hunger in Quarantine Homes. This eventually made life for the victims very difficult.</p>
                        
                        <p>The lack of proper security made way for victims to go out and fend for themselves outside the quarantine homes just to survive the scurging hunger.</p>
                        
                        <p>Among the worst incidences that caused struggles and hardship for the victims in quarantine homes were ineffective security and proper manning of the homes where the victims were kept so they will not go out and spread the virus. In the quarantine home where they were kept, a lot was expected from those responsible to provide them their basic necessities such as food, but sadly they could not, so the victims quarantined had to move around, spreading the virus in and around the communities they went to in search of food and other basic needs.</p>
                        
                        <p>The greatest challenge was that the community people were restricted to go into the quarantine homes. This limited the support they could provide to the victims. This resulted to unforseen hardship, hunger and other trouble on the victims that surpassed the epidemic itself.</p>
                        
                        <p>I discovered all those unfortunate incidents during my service with APPA. Touched with the feeling to help those victims in hunger, and urged to provide services that will ease up their suffering, I was able to see how children suffer greatly, the help they desire but could not get and what intervention that can help alleviate their sufferings. Then the idea of establishing an organisation that could support government's efforts and restore the lost hope in these children came up in mind and it resulted to founding of Future Hope Foundation.</p>
                        
                        <p>Filled with the idea of helping the children, I spent all the money I was paid, including my transportation and feeding allowances on those children just to encourage and make them live happily. After that entire exercise, I spoke with a brother, Osman Jalloh, who supported the idea and vision by providing funds for the registration of the foundation.</p>
                        
                        <p>Officially, the organisation was registered under Corporate Affairs Commission on the 26th January, 2015. Since then, the strive to support and keep children happy started under Future Hope Foundation.</p>
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
            <div class="col-lg-12 text-center mb-4">
                <h2>Our Objectives</h2>
                <div class="separator-line mx-auto"></div>
            </div>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="objectives-list">
                                    <li>To promote educational programs for orphans and the vulnerable children in our communities.</li>
                                    <li>To seek for the right of children mainly through sensitisation and advocacy with community leaders by conducting seminars, workshops and radio programmes.</li>
                                    <li>To initiate and promote programs concerned with provision of relief aid, rehabilitation and development of children.</li>
                                    <li>To build an academically sound, moral upright and self-contained society.</li>
                                    <li>To encourage good and qualitative education through sponsorship.</li>
                                    <li>To coordinate activities of national and international interests with potential donors, charitable organisations and other NGOs.</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="objectives-list">
                                    <li>To put in place effect management of funds and all donations geared towards benefiting orphans and the less privileged.</li>
                                    <li>To support girl child education by linking them to organisations that gives scholarships in any part of the world.</li>
                                    <li>To undertake programs centred on children's right and gender awareness through rural community sensitisations and intimate training on gender issues so as to promote gender equality.</li>
                                    <li>To coordinate and work amicably with other organisations with similar intersections, aims and objectives.</li>
                                    <li>To undertake any other relevant matter that may arise from time to time.</li>
                                </ul>
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
