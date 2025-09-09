<?php
// Set page title
$page_title = "Our Projects";
include 'includes/header.php';

// Get all active projects
$ongoing_projects = getProjects(0, 'ongoing', true);
$completed_projects = getProjects(0, 'completed', true);
$upcoming_projects = getProjects(0, 'upcoming', true);
?>

<!-- Page Title -->
<section class="page-title-section bg-primary">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="text-white">Our Projects</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent mb-0 p-0">
                        <li class="breadcrumb-item"><a href="index.php" class="text-white">Home</a></li>
                        <li class="breadcrumb-item active text-white-50" aria-current="page">Projects</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Ongoing Projects Section -->
<section class="projects-section py-5">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="section-title">Ongoing Projects</h2>
                <p class="lead">Projects we are currently working on to make a difference.</p>
            </div>
        </div>
        
        <div class="row g-4">
            <?php if (!empty($ongoing_projects)): foreach ($ongoing_projects as $project): ?>
            <div class="col-md-6 col-lg-4">
                <div class="project-card h-100 border rounded shadow-sm">
                    <?php if (!empty($project['image'])): ?>
                    <img src="<?php echo getImageUrl($project['image']); ?>" alt="<?php echo $project['title']; ?>" class="img-fluid rounded-top">
                    <?php else: ?>
                    <div class="project-placeholder bg-light rounded-top d-flex align-items-center justify-content-center" style="height: 200px;">
                        <i class="fas fa-project-diagram fa-4x text-primary opacity-50"></i>
                    </div>
                    <?php endif; ?>
                    
                    <div class="card-body">
                        <h3 class="h4"><?php echo $project['title']; ?></h3>
                        <div class="mb-3">
                            <span class="badge bg-success">Ongoing</span>
                            <?php if (!empty($project['location'])): ?>
                            <span class="text-muted ms-2"><i class="fas fa-map-marker-alt"></i> <?php echo $project['location']; ?></span>
                            <?php endif; ?>
                        </div>
                        <p><?php echo truncateText(strip_tags($project['description']), 120); ?></p>
                        <a href="project-detail.php?id=<?php echo $project['id']; ?>" class="btn btn-primary">Learn More</a>
                    </div>
                </div>
            </div>
            <?php endforeach; else: ?>
            <div class="col-12">
                <div class="alert alert-info">
                    <p class="mb-0">No ongoing projects at this time. Please check back later.</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Completed Projects Section -->
<section class="projects-section py-5 bg-light">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="section-title">Completed Projects</h2>
                <p class="lead">Projects we have successfully completed.</p>
            </div>
        </div>
        
        <div class="row g-4">
            <?php if (!empty($completed_projects)): foreach ($completed_projects as $project): ?>
            <div class="col-md-6 col-lg-4">
                <div class="project-card h-100 border rounded shadow-sm">
                    <?php if (!empty($project['image'])): ?>
                    <img src="<?php echo $project['image']; ?>" alt="<?php echo $project['title']; ?>" class="img-fluid rounded-top">
                    <?php else: ?>
                    <div class="project-placeholder bg-light rounded-top d-flex align-items-center justify-content-center" style="height: 200px;">
                        <i class="fas fa-check-circle fa-4x text-success opacity-50"></i>
                    </div>
                    <?php endif; ?>
                    
                    <div class="card-body">
                        <h3 class="h4"><?php echo $project['title']; ?></h3>
                        <div class="mb-3">
                            <span class="badge bg-secondary">Completed</span>
                            <?php if (!empty($project['location'])): ?>
                            <span class="text-muted ms-2"><i class="fas fa-map-marker-alt"></i> <?php echo $project['location']; ?></span>
                            <?php endif; ?>
                        </div>
                        <p><?php echo truncateText(strip_tags($project['description']), 120); ?></p>
                        <a href="project-detail.php?id=<?php echo $project['id']; ?>" class="btn btn-primary">Learn More</a>
                    </div>
                </div>
            </div>
            <?php endforeach; else: ?>
            <div class="col-12">
                <div class="alert alert-info">
                    <p class="mb-0">No completed projects at this time. Please check back later.</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Upcoming Projects Section -->
<section class="projects-section py-5">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="section-title">Upcoming Projects</h2>
                <p class="lead">Projects we are planning to undertake soon.</p>
            </div>
        </div>
        
        <div class="row g-4">
            <?php if (!empty($upcoming_projects)): foreach ($upcoming_projects as $project): ?>
            <div class="col-md-6 col-lg-4">
                <div class="project-card h-100 border rounded shadow-sm">
                    <?php if (!empty($project['image'])): ?>
                    <img src="<?php echo $project['image']; ?>" alt="<?php echo $project['title']; ?>" class="img-fluid rounded-top">
                    <?php else: ?>
                    <div class="project-placeholder bg-light rounded-top d-flex align-items-center justify-content-center" style="height: 200px;">
                        <i class="fas fa-calendar-alt fa-4x text-info opacity-50"></i>
                    </div>
                    <?php endif; ?>
                    
                    <div class="card-body">
                        <h3 class="h4"><?php echo $project['title']; ?></h3>
                        <div class="mb-3">
                            <span class="badge bg-info">Upcoming</span>
                            <?php if (!empty($project['location'])): ?>
                            <span class="text-muted ms-2"><i class="fas fa-map-marker-alt"></i> <?php echo $project['location']; ?></span>
                            <?php endif; ?>
                        </div>
                        <p><?php echo truncateText(strip_tags($project['description']), 120); ?></p>
                        <a href="project-detail.php?id=<?php echo $project['id']; ?>" class="btn btn-primary">Learn More</a>
                    </div>
                </div>
            </div>
            <?php endforeach; else: ?>
            <div class="col-12">
                <div class="alert alert-info">
                    <p class="mb-0">No upcoming projects at this time. Please check back later.</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
