<?php
// Start output buffering to catch any unexpected output
ob_start();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set page title
$page_title = "Events";
include 'includes/header.php';

// Get all upcoming events
$upcoming_events = getEvents(0, true, true);

// Get past events
$past_events_query = "SELECT * FROM events WHERE event_date < CURDATE() AND is_active = 1 ORDER BY event_date DESC";
$past_events_result = $conn->query($past_events_query);
$past_events = [];

if ($past_events_result->num_rows > 0) {
    while ($row = $past_events_result->fetch_assoc()) {
        $past_events[] = $row;
    }
}
?>

<!-- Page Title -->
<section class="page-title-section bg-primary">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="text-white">Events</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent mb-0 p-0">
                        <li class="breadcrumb-item"><a href="index.php" class="text-white">Home</a></li>
                        <li class="breadcrumb-item active text-white-50" aria-current="page">Events</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Upcoming Events Section -->
<section class="events-section py-5">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="section-title">Upcoming Events</h2>
                <p class="lead">Join us for these upcoming events and make a difference.</p>
            </div>
        </div>
        
        <div class="row g-4">
            <?php if (!empty($upcoming_events)): foreach ($upcoming_events as $event): ?>
            <div class="col-md-6 col-lg-4">
                <div class="event-card h-100">
                    <?php if (!empty($event['image'])): ?>
                    <div class="event-image">
                        <img src="<?php echo getImageUrl($event['image']); ?>" alt="<?php echo $event['title']; ?>" class="img-fluid">
                    </div>
                    <?php else: ?>
                    <div class="event-image event-no-image d-flex align-items-center justify-content-center bg-light">
                        <i class="fas fa-calendar-alt fa-4x text-primary opacity-50"></i>
                    </div>
                    <?php endif; ?>
                    <div class="event-content">
                        <div class="event-meta mb-2">
                            <span>
                                <i class="far fa-calendar-alt me-1"></i>
                                <?php echo formatDate($event['event_date']); ?>
                            </span>
                            <span class="ms-3">
                                <i class="far fa-clock me-1"></i>
                                <?php echo date('h:i A', strtotime($event['event_time'])); ?>
                            </span>
                        </div>
                        <h3 class="event-title"><?php echo $event['title']; ?></h3>
                        <p><?php echo truncateText(strip_tags($event['description']), 120); ?></p>
                        <div class="event-bottom">
                            <?php if (!empty($event['location'])): ?>
                            <div class="event-location">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                <?php echo $event['location']; ?>
                            </div>
                            <?php endif; ?>
                            <a href="event-detail.php?id=<?php echo $event['id']; ?>" class="btn btn-sm btn-outline-primary mt-3">Read More</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; else: ?>
            <div class="col-12">
                <div class="alert alert-info">No upcoming events scheduled at this time. Please check back later.</div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Past Events Section -->
<section class="events-section py-5 bg-light">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="section-title">Past Events</h2>
                <p class="lead">Take a look at our previously organized events.</p>
            </div>
        </div>
        
        <div class="row g-4">
            <?php if (!empty($past_events)): foreach ($past_events as $event): ?>
            <div class="col-md-6 col-lg-4">
                <div class="event-card h-100">
                    <?php if (!empty($event['image'])): ?>
                    <div class="event-image">
                        <img src="<?php echo getImageUrl($event['image']); ?>" alt="<?php echo $event['title']; ?>" class="img-fluid">
                        <div class="event-badge bg-secondary">Past Event</div>
                    </div>
                    <?php else: ?>
                    <div class="event-image event-no-image d-flex align-items-center justify-content-center bg-light">
                        <i class="fas fa-calendar-alt fa-4x text-secondary opacity-50"></i>
                        <div class="event-badge bg-secondary">Past Event</div>
                    </div>
                    <?php endif; ?>
                    <div class="event-content">
                        <div class="event-meta mb-2">
                            <span>
                                <i class="far fa-calendar-check me-1"></i>
                                <?php echo formatDate($event['event_date']); ?>
                            </span>
                            <span class="ms-3">
                                <i class="far fa-clock me-1"></i>
                                <?php echo date('h:i A', strtotime($event['event_time'])); ?>
                            </span>
                        </div>
                        <h3 class="event-title"><?php echo $event['title']; ?></h3>
                        <p><?php echo truncateText(strip_tags($event['description']), 120); ?></p>
                        <div class="event-bottom">
                            <?php if (!empty($event['location'])): ?>
                            <div class="event-location">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                <?php echo $event['location']; ?>
                            </div>
                            <?php endif; ?>
                            <a href="event-detail.php?id=<?php echo $event['id']; ?>" class="btn btn-sm btn-outline-secondary mt-3">Event Details</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; else: ?>
            <div class="col-12">
                <div class="alert alert-info">No past events to display.</div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; 

// End output buffering and send content to browser
ob_end_flush();
?>
