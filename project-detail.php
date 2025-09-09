<?php
// Include database configuration and functions
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get project ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get project details
$project = getRecordById('projects', $id);

// If project not found, redirect to projects page
if (!$project || !$project['is_active']) {
    redirect('projects.php');
}

// Set page title
$page_title = $project['title'];

// Include header
include 'includes/header.php';

// Get related projects
$related_sql = "SELECT * FROM projects WHERE id != {$id} AND is_active = 1 ORDER BY RAND() LIMIT 3";
$related_result = $conn->query($related_sql);
$related_projects = [];

if ($related_result->num_rows > 0) {
    while ($row = $related_result->fetch_assoc()) {
        $related_projects[] = $row;
    }
}

// Get project status badge
$status_badge = '';
switch ($project['status']) {
    case 'ongoing':
        $status_badge = '<span class="badge bg-success">Ongoing</span>';
        break;
    case 'completed':
        $status_badge = '<span class="badge bg-secondary">Completed</span>';
        break;
    case 'upcoming':
        $status_badge = '<span class="badge bg-info">Upcoming</span>';
        break;
}
?>

<!-- Page Title -->
<section class="page-title-section bg-primary">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="text-white"><?php echo $project['title']; ?></h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent mb-0 p-0">
                        <li class="breadcrumb-item"><a href="index.php" class="text-white">Home</a></li>
                        <li class="breadcrumb-item"><a href="projects.php" class="text-white">Projects</a></li>
                        <li class="breadcrumb-item active text-white-50" aria-current="page"><?php echo $project['title']; ?></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Project Detail Section -->
<section class="project-detail-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="project-content">
                    <?php if (!empty($project['image'])): ?>
                    <img src="<?php echo getImageUrl($project['image']); ?>" alt="<?php echo $project['title']; ?>" class="img-fluid rounded mb-4">
                    <?php endif; ?>
                    
                    <div class="mb-4">
                        <?php echo $status_badge; ?>
                        
                        <?php if (!empty($project['start_date'])): ?>
                        <span class="ms-3 text-muted"><i class="far fa-calendar-alt"></i> Started: <?php echo date('M d, Y', strtotime($project['start_date'])); ?></span>
                        <?php endif; ?>
                        
                        <?php if (!empty($project['end_date']) && $project['status'] === 'completed'): ?>
                        <span class="ms-3 text-muted"><i class="far fa-calendar-check"></i> Completed: <?php echo date('M d, Y', strtotime($project['end_date'])); ?></span>
                        <?php elseif (!empty($project['end_date']) && $project['status'] === 'upcoming'): ?>
                        <span class="ms-3 text-muted"><i class="far fa-calendar"></i> Starting: <?php echo date('M d, Y', strtotime($project['start_date'])); ?></span>
                        <?php endif; ?>
                        
                        <?php if (!empty($project['location'])): ?>
                        <span class="ms-3 text-muted"><i class="fas fa-map-marker-alt"></i> <?php echo $project['location']; ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="project-description">
                        <?php echo $project['description']; ?>
                    </div>
                    
                    <?php if (!empty($project['goals'])): ?>
                    <div class="project-goals mt-4">
                        <h3>Project Goals</h3>
                        <?php echo $project['goals']; ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($project['impact'])): ?>
                    <div class="project-impact mt-4">
                        <h3>Project Impact</h3>
                        <?php echo $project['impact']; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="col-lg-4 mt-4 mt-lg-0">
                <div class="project-sidebar">
                    <!-- Project Info -->
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Project Information</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Status
                                    <span><?php echo ucfirst($project['status']); ?></span>
                                </li>
                                
                                <?php if (!empty($project['budget'])): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Budget
                                    <span>$<?php echo number_format($project['budget'], 2); ?></span>
                                </li>
                                <?php endif; ?>
                                
                                <?php if (!empty($project['beneficiaries'])): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Beneficiaries
                                    <span><?php echo $project['beneficiaries']; ?></span>
                                </li>
                                <?php endif; ?>
                                
                                <?php if (!empty($project['partners'])): ?>
                                <li class="list-group-item">
                                    <div><strong>Partners</strong></div>
                                    <div><?php echo $project['partners']; ?></div>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                    
                    <!-- Get Involved -->
                    <div class="card mb-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">Get Involved</h5>
                        </div>
                        <div class="card-body">
                            <p>Want to support this project? There are several ways you can get involved:</p>
                            <ul class="mb-4">
                                <li>Make a donation</li>
                                <li>Volunteer your time</li>
                                <li>Spread awareness</li>
                                <li>Become a partner</li>
                            </ul>
                            <a href="contact.php" class="btn btn-success w-100">Contact Us</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Projects -->
<?php if (!empty($related_projects)): ?>
<section class="related-projects-section py-5 bg-light">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="section-title text-center">Related Projects</h2>
            </div>
        </div>
        
        <div class="row g-4">
            <?php foreach ($related_projects as $related): ?>
            <div class="col-md-6 col-lg-4">
                <div class="project-card h-100 border rounded shadow-sm">
                    <?php if (!empty($related['image'])): ?>
                    <img src="<?php echo $related['image']; ?>" alt="<?php echo $related['title']; ?>" class="img-fluid rounded-top">
                    <?php else: ?>
                    <div class="project-placeholder bg-light rounded-top d-flex align-items-center justify-content-center" style="height: 200px;">
                        <i class="fas fa-project-diagram fa-4x text-primary opacity-50"></i>
                    </div>
                    <?php endif; ?>
                    
                    <div class="card-body">
                        <h3 class="h4"><?php echo $related['title']; ?></h3>
                        <div class="mb-3">
                            <?php 
                            switch ($related['status']) {
                                case 'ongoing':
                                    echo '<span class="badge bg-success">Ongoing</span>';
                                    break;
                                case 'completed':
                                    echo '<span class="badge bg-secondary">Completed</span>';
                                    break;
                                case 'upcoming':
                                    echo '<span class="badge bg-info">Upcoming</span>';
                                    break;
                            }
                            ?>
                            <?php if (!empty($related['location'])): ?>
                            <span class="text-muted ms-2"><i class="fas fa-map-marker-alt"></i> <?php echo $related['location']; ?></span>
                            <?php endif; ?>
                        </div>
                        <p><?php echo truncateText(strip_tags($related['description']), 120); ?></p>
                        <a href="project-detail.php?id=<?php echo $related['id']; ?>" class="btn btn-primary">Learn More</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
