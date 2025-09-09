<?php
// Include database configuration and functions
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get event ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get event details
$event = getRecordById('events', $id);

// If event not found, redirect to events page
if (!$event || !$event['is_active']) {
    redirect('events.php');
}

// Set page title
$page_title = $event['title'];

// Include header
include 'includes/header.php';

// Get related events
$related_sql = "SELECT * FROM events WHERE id != {$id} AND is_active = 1 ORDER BY RAND() LIMIT 3";
$related_result = $conn->query($related_sql);
$related_events = [];

if ($related_result->num_rows > 0) {
    while ($row = $related_result->fetch_assoc()) {
        $related_events[] = $row;
    }
}

// Check if event is upcoming or past
$is_upcoming = strtotime($event['event_date']) >= strtotime(date('Y-m-d'));
?>

<!-- Page Title -->
<section class="page-title-section bg-primary">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="text-white"><?php echo $event['title']; ?></h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent mb-0 p-0">
                        <li class="breadcrumb-item"><a href="index.php" class="text-white">Home</a></li>
                        <li class="breadcrumb-item"><a href="events.php" class="text-white">Events</a></li>
                        <li class="breadcrumb-item active text-white-50" aria-current="page"><?php echo $event['title']; ?></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Event Detail Section -->
<section class="event-detail-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="event-content">
                    <?php if (!empty($event['image'])): ?>
                    <img src="<?php echo getImageUrl($event['image']); ?>" alt="<?php echo $event['title']; ?>" class="img-fluid rounded mb-4">
                    <?php endif; ?>
                    
                    <div class="event-meta mb-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <div class="icon-box bg-primary text-white rounded-circle p-3 me-3">
                                        <i class="far fa-calendar-alt"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Date</h6>
                                        <p class="mb-0"><?php echo date('F d, Y', strtotime($event['event_date'])); ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <?php if (!empty($event['event_time'])): ?>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <div class="icon-box bg-primary text-white rounded-circle p-3 me-3">
                                        <i class="far fa-clock"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Time</h6>
                                        <p class="mb-0"><?php echo $event['event_time']; ?></p>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($event['location'])): ?>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <div class="icon-box bg-primary text-white rounded-circle p-3 me-3">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Location</h6>
                                        <p class="mb-0"><?php echo $event['location']; ?></p>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($event['organizer'])): ?>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <div class="icon-box bg-primary text-white rounded-circle p-3 me-3">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Organizer</h6>
                                        <p class="mb-0"><?php echo $event['organizer']; ?></p>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="event-description">
                        <h3>About This Event</h3>
                        <?php echo $event['description']; ?>
                    </div>
                    
                    <?php if (!empty($event['agenda'])): ?>
                    <div class="event-agenda mt-4">
                        <h3>Event Agenda</h3>
                        <?php echo $event['agenda']; ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!$is_upcoming && !empty($event['recap'])): ?>
                    <div class="event-recap mt-4">
                        <h3>Event Recap</h3>
                        <?php echo $event['recap']; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="col-lg-4 mt-4 mt-lg-0">
                <div class="event-sidebar">
                    <?php if ($is_upcoming): ?>
                    <!-- Registration Card -->
                    <div class="card mb-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">Registration</h5>
                        </div>
                        <div class="card-body">
                            <p>Interested in attending this event? Please register to secure your spot.</p>
                            <a href="contact.php?event=<?php echo $event['title']; ?>" class="btn btn-success w-100 mb-2">Register Now</a>
                            <small class="text-muted">For any questions about registration, please contact us.</small>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Share Event -->
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Share This Event</h5>
                        </div>
                        <div class="card-body">
                            <div class="social-share">
                                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" class="btn btn-outline-primary mb-2 me-2" target="_blank"><i class="fab fa-facebook-f"></i> Facebook</a>
                                <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>&text=<?php echo urlencode($event['title']); ?>" class="btn btn-outline-primary mb-2 me-2" target="_blank"><i class="fab fa-twitter"></i> Twitter</a>
                                <a href="mailto:?subject=<?php echo urlencode($event['title']); ?>&body=<?php echo urlencode('Check out this event: ' . 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" class="btn btn-outline-primary mb-2"><i class="fas fa-envelope"></i> Email</a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Contact Info -->
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">Need More Information?</h5>
                        </div>
                        <div class="card-body">
                            <p>For more information about this event, please contact us:</p>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2"><i class="fas fa-envelope me-2 text-primary"></i> <?php echo $settings['site_email'] ?? 'info@futurehopefoundation.org'; ?></li>
                                <li><i class="fas fa-phone me-2 text-primary"></i> <?php echo $settings['site_phone'] ?? '+1234567890'; ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Events -->
<?php if (!empty($related_events)): ?>
<section class="related-events-section py-5 bg-light">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="section-title text-center">Other Events You Might Be Interested In</h2>
            </div>
        </div>
        
        <div class="row g-4">
            <?php foreach ($related_events as $related): ?>
            <div class="col-md-6 col-lg-4">
                <div class="event-card h-100 border rounded shadow-sm">
                    <?php if (!empty($related['image'])): ?>
                    <img src="<?php echo $related['image']; ?>" alt="<?php echo $related['title']; ?>" class="img-fluid rounded-top">
                    <?php else: ?>
                    <div class="event-placeholder bg-light rounded-top d-flex align-items-center justify-content-center" style="height: 160px;">
                        <i class="fas fa-calendar-alt fa-4x text-primary opacity-50"></i>
                    </div>
                    <?php endif; ?>
                    
                    <div class="card-body">
                        <div class="event-date mb-2">
                            <span class="badge <?php echo strtotime($related['event_date']) >= strtotime(date('Y-m-d')) ? 'bg-primary' : 'bg-secondary'; ?>">
                                <i class="far <?php echo strtotime($related['event_date']) >= strtotime(date('Y-m-d')) ? 'fa-calendar-alt' : 'fa-calendar-check'; ?> me-1"></i>
                                <?php echo date('M d, Y', strtotime($related['event_date'])); ?>
                            </span>
                        </div>
                        <h3 class="h4"><?php echo $related['title']; ?></h3>
                        <?php if (!empty($related['location'])): ?>
                        <p class="event-location"><i class="fas fa-map-marker-alt text-muted me-2"></i><?php echo $related['location']; ?></p>
                        <?php endif; ?>
                        <p><?php echo truncateText(strip_tags($related['description']), 100); ?></p>
                        <a href="event-detail.php?id=<?php echo $related['id']; ?>" class="btn btn-primary">Event Details</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
