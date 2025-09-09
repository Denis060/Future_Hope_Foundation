<?php
// Include header
$page_title = "Our Team";
include 'includes/header.php';

// Get all active team members
$team_members = getTeamMembers(0, true);
?>

<style>
    /* Team Card Styling */
    .team-card {
        transition: transform 0.3s ease;
    }
    
    .team-card:hover {
        transform: translateY(-5px);
    }
    
    .team-image {
        position: relative;
        overflow: hidden;
    }
    
    .team-social-links {
        position: absolute;
        bottom: -50px;
        left: 0;
        width: 100%;
        background-color: rgba(0,0,0,0.7);
        padding: 10px;
        transition: bottom 0.3s ease;
    }
    
    .team-image:hover .team-social-links {
        bottom: 0;
    }
    
    .social-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        background-color: #fff;
        border-radius: 50%;
        color: #0d6efd;
        margin: 0 5px;
        transition: all 0.3s ease;
    }
    
    .social-link:hover {
        background-color: #0d6efd;
        color: #fff;
    }
    
    .bio-container {
        position: relative;
    }
</style>

<!-- Page Title -->
<section class="page-title-section bg-primary">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="text-white">Our Team</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent mb-0 p-0">
                        <li class="breadcrumb-item"><a href="index.php" class="text-white">Home</a></li>
                        <li class="breadcrumb-item active text-white-50" aria-current="page">Our Team</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Team Section -->
<section class="team-section py-5">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="section-title">Meet Our Team</h2>
                <p class="lead">Dedicated professionals working together to make a difference in the lives of orphans and the less privileged.</p>
            </div>
        </div>
        
        <div class="row g-4">
            <?php if (!empty($team_members)): foreach ($team_members as $member): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="team-card h-100 border rounded shadow-sm overflow-hidden">
                    <div class="team-image position-relative">
                        <?php if (!empty($member['image'])): ?>
                        <img src="<?php echo getImageUrl($member['image']); ?>" alt="<?php echo $member['name']; ?>" class="img-fluid w-100" style="height: 300px; object-fit: cover;">
                        <?php else: ?>
                        <img src="assets/images/team-placeholder.jpg" alt="<?php echo $member['name']; ?>" class="img-fluid w-100" style="height: 300px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="team-social-links position-absolute w-100 d-flex justify-content-center">
                            <?php if (!empty($member['facebook'])): ?>
                            <a href="<?php echo $member['facebook']; ?>" class="social-link" target="_blank"><i class="fab fa-facebook-f"></i></a>
                            <?php endif; ?>
                            
                            <?php if (!empty($member['twitter'])): ?>
                            <a href="<?php echo $member['twitter']; ?>" class="social-link" target="_blank"><i class="fab fa-twitter"></i></a>
                            <?php endif; ?>
                            
                            <?php if (!empty($member['instagram'])): ?>
                            <a href="<?php echo $member['instagram']; ?>" class="social-link" target="_blank"><i class="fab fa-instagram"></i></a>
                            <?php endif; ?>
                            
                            <?php if (!empty($member['linkedin'])): ?>
                            <a href="<?php echo $member['linkedin']; ?>" class="social-link" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="team-info p-3 text-center">
                        <h3 class="h4 mb-1"><?php echo $member['name']; ?></h3>
                        <p class="text-muted mb-2"><?php echo $member['position']; ?></p>
                        <?php if (!empty($member['bio'])): ?>
                        <div class="bio-container" style="height: 80px; overflow: hidden; margin-bottom: 10px;">
                            <p class="small mb-2"><?php echo nl2br(truncateText($member['bio'], 150)); ?></p>
                        </div>
                        <button class="btn btn-sm btn-outline-primary read-more-btn" data-member-id="<?php echo $member['id']; ?>" data-full-bio="<?php echo htmlspecialchars($member['bio']); ?>">Read More</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; else: ?>
            <!-- Display placeholders if no team members exist -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="team-card h-100 border rounded shadow-sm overflow-hidden">
                    <div class="team-image position-relative">
                        <img src="assets/images/team-placeholder.jpg" alt="Abu Bakarr Conteh" class="img-fluid w-100" style="height: 300px; object-fit: cover;">
                    </div>
                    <div class="team-info p-3 text-center">
                        <h3 class="h4 mb-1">Abu Bakarr Conteh</h3>
                        <p class="text-muted mb-2">Director General</p>
                        <p class="small mb-2">Founder of Future Hope Foundation and dedicated humanitarian worker with over 10 years of experience.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="team-card h-100 border rounded shadow-sm overflow-hidden">
                    <div class="team-image position-relative">
                        <img src="assets/images/team-placeholder.jpg" alt="Sarah Johnson" class="img-fluid w-100" style="height: 300px; object-fit: cover;">
                    </div>
                    <div class="team-info p-3 text-center">
                        <h3 class="h4 mb-1">Sarah Johnson</h3>
                        <p class="text-muted mb-2">Program Coordinator</p>
                        <p class="small mb-2">Specializing in child welfare and educational development programs.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="team-card h-100 border rounded shadow-sm overflow-hidden">
                    <div class="team-image position-relative">
                        <img src="assets/images/team-placeholder.jpg" alt="Daniel Kamara" class="img-fluid w-100" style="height: 300px; object-fit: cover;">
                    </div>
                    <div class="team-info p-3 text-center">
                        <h3 class="h4 mb-1">Daniel Kamara</h3>
                        <p class="text-muted mb-2">Health Program Director</p>
                        <p class="small mb-2">Leads our health initiatives and medical outreach programs.</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Team Member Bio Modal -->
<div class="modal fade" id="teamBioModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bioModalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="bioModalContent" class="mb-0"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Join Our Team Section -->
<section class="join-team-section py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7 mb-4 mb-lg-0">
                <h2>Join Our Team</h2>
                <p>We are always looking for passionate individuals to join our team. Whether you want to volunteer or work with us full-time, we welcome your contribution to making a difference in the lives of orphans and less privileged children.</p>
                <ul class="list-unstyled mb-4">
                    <li class="mb-2"><i class="fas fa-check-circle text-primary me-2"></i> Make a meaningful impact on children's lives</li>
                    <li class="mb-2"><i class="fas fa-check-circle text-primary me-2"></i> Work with a dedicated team of professionals</li>
                    <li class="mb-2"><i class="fas fa-check-circle text-primary me-2"></i> Develop valuable skills while contributing to a worthy cause</li>
                </ul>
                <a href="contact.php" class="btn btn-primary">Contact Us</a>
            </div>
            <div class="col-lg-5">
                <img src="assets/images/volunteer.jpg" alt="Volunteer with us" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</section>

<?php
// Include footer
include 'includes/footer.php';
?>

<script>
$(document).ready(function() {
    // Handle Read More button clicks
    $('.read-more-btn').click(function() {
        var memberName = $(this).closest('.team-info').find('h3').text();
        var fullBio = $(this).data('full-bio'); // Get the full biography from data attribute
        
        // Set modal content
        $('#bioModalTitle').text(memberName);
        $('#bioModalContent').html(nl2br(fullBio)); // Convert newlines to <br> tags
        
        // Show modal
        var bioModal = new bootstrap.Modal(document.getElementById('teamBioModal'));
        bioModal.show();
    });
    
    // Helper function to convert newlines to <br> tags
    function nl2br(str) {
        return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1<br>$2');
    }
});
</script>
