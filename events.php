<?php
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
                <div class="event-card h-100 border rounded shadow-sm">
                    <?php if (!empty($event['image'])): ?>
                    <img src="<?php echo getImageUrl($event['image']); ?>" alt="<?php echo $event['title']; ?>" class="img-fluid rounded-top">
                    <?php else: ?>
                    <div class="event-placeholder bg-light rounded-top d-flex align-items-center justify-content-center" style="height: 160px;">
                        <i class="fas fa-calendar-alt fa-4x text-primary opacity-50"></i>
                    </div>
                    <?php endif; ?>
                    
                    <div class="card-body">
                        <div class="event-date mb-2">
                            <span class="badge bg-primary">
                                <i class="far fa-calendar-alt me-1"></i>
                                <?php echo date('M d, Y', strtotime($event['event_date'])); ?>
                                <?php if (!empty($event['event_time'])): ?>
                                at <?php echo $event['event_time']; ?>
                                <?php endif; ?>
                            </span>
                        </div>
                        <h3 class="h4"><?php echo $event['title']; ?></h3>
                        <?php if (!empty($event['location'])): ?>
                        <p class="event-location"><i class="fas fa-map-marker-alt text-muted me-2"></i><?php echo $event['location']; ?></p>
                        <?php endif; ?>
                        <p><?php echo truncateText(strip_tags($event['description']), 120); ?></p>
                        <a href="event-detail.php?id=<?php echo $event['id']; ?>" class="btn btn-primary">Event Details</a>
                    </div>
                </div>
            </div>
            <?php endforeach; else: ?>
            <div class="col-12">
                <div class="alert alert-info">
                    <p class="mb-0">No upcoming events at this time. Please check back later.</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Past Events Section -->
<section class="past-events-section py-5 bg-light">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="section-title">Past Events</h2>
                <p class="lead">A look back at our previous events and activities.</p>
            </div>
        </div>
        
        <div class="row g-4">
            <?php if (!empty($past_events)): foreach ($past_events as $event): ?>
            <div class="col-md-6 col-lg-4">
                <div class="event-card h-100 border rounded shadow-sm">
                    <?php if (!empty($event['image'])): ?>
                    <img src="<?php echo $event['image']; ?>" alt="<?php echo $event['title']; ?>" class="img-fluid rounded-top">
                    <?php else: ?>
                    <div class="event-placeholder bg-light rounded-top d-flex align-items-center justify-content-center" style="height: 160px;">
                        <i class="fas fa-history fa-4x text-secondary opacity-50"></i>
                    </div>
                    <?php endif; ?>
                    
                    <div class="card-body">
                        <div class="event-date mb-2">
                            <span class="badge bg-secondary">
                                <i class="far fa-calendar-check me-1"></i>
                                <?php echo date('M d, Y', strtotime($event['event_date'])); ?>
                                <?php if (!empty($event['event_time'])): ?>
                                at <?php echo $event['event_time']; ?>
                                <?php endif; ?>
                            </span>
                        </div>
                        <h3 class="h4"><?php echo $event['title']; ?></h3>
                        <?php if (!empty($event['location'])): ?>
                        <p class="event-location"><i class="fas fa-map-marker-alt text-muted me-2"></i><?php echo $event['location']; ?></p>
                        <?php endif; ?>
                        <p><?php echo truncateText(strip_tags($event['description']), 120); ?></p>
                        <a href="event-detail.php?id=<?php echo $event['id']; ?>" class="btn btn-outline-secondary">View Recap</a>
                    </div>
                </div>
            </div>
            <?php endforeach; else: ?>
            <div class="col-12">
                <div class="alert alert-info">
                    <p class="mb-0">No past events to display.</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
