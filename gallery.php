<?php
// Set page title
$page_title = "Gallery";
include 'includes/header.php';

// Get categories
try {
    $categories_query = "SELECT DISTINCT category FROM gallery WHERE is_active = 1 AND category IS NOT NULL AND category != ''";
    $categories_result = $conn->query($categories_query);
    $categories = [];

    if ($categories_result && $categories_result->num_rows > 0) {
        while ($row = $categories_result->fetch_assoc()) {
            if (!empty($row['category'])) {
                $categories[] = $row['category'];
            }
        }
    }
} catch (Exception $e) {
    // If there's an error (like missing column), just continue with empty categories
    $categories = [];
}

// Get active gallery images
$gallery_images = getGalleryImages(0, '', true);
?>

<!-- Page Title -->
<section class="page-title-section bg-primary">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="text-white">Gallery</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent mb-0 p-0">
                        <li class="breadcrumb-item"><a href="index.php" class="text-white">Home</a></li>
                        <li class="breadcrumb-item active text-white-50" aria-current="page">Gallery</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Gallery Section -->
<section class="gallery-section py-5">
    <div class="container">
        <div class="row mb-4">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="section-title">Our Photo Gallery</h2>
                <p class="lead">Capturing moments of impact and inspiration from our work.</p>
            </div>
        </div>
        
        <?php if (!empty($categories)): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="gallery-filter">
                    <ul class="nav nav-pills justify-content-center" id="gallery-filter">
                        <li class="nav-item">
                            <button class="nav-link active" data-filter="*">All</button>
                        </li>
                        <?php foreach ($categories as $category): ?>
                        <li class="nav-item">
                            <button class="nav-link" data-filter=".<?php echo strtolower(str_replace(' ', '-', $category)); ?>"><?php echo $category; ?></button>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="row g-4" id="gallery-grid">
            <?php if (!empty($gallery_images)): foreach ($gallery_images as $image): ?>
            <div class="col-md-4 col-lg-3 gallery-item <?php echo !empty($image['category']) ? strtolower(str_replace(' ', '-', $image['category'])) : ''; ?>">
                <div class="gallery-card">
                    <a href="<?php echo getImageUrl($image['file_path']); ?>" class="gallery-popup">
                        <img src="<?php echo getImageUrl($image['file_path']); ?>" alt="<?php echo $image['title']; ?>" class="img-fluid rounded">
                        <div class="gallery-overlay">
                            <div class="gallery-info">
                                <h5><?php echo $image['title']; ?></h5>
                                <?php if (!empty($image['description'])): ?>
                                <p><?php echo truncateText($image['description'], 100); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <?php endforeach; else: ?>
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <p class="mb-0">No gallery images available yet. Please check back later.</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Additional CSS for Gallery -->
<style>
.gallery-card {
    position: relative;
    margin-bottom: 20px;
    overflow: hidden;
    border-radius: 4px;
    transition: all 0.3s ease;
}

.gallery-card img {
    width: 100%;
    height: 250px;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.gallery-card:hover img {
    transform: scale(1.05);
}

.gallery-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
    padding: 15px;
    text-align: center;
}

.gallery-card:hover .gallery-overlay {
    opacity: 1;
}

.gallery-info {
    color: #fff;
}

.gallery-info h5 {
    margin-bottom: 10px;
}

.gallery-filter {
    margin-bottom: 30px;
}

.gallery-filter .nav-link {
    color: #333;
    border-radius: 30px;
    padding: 8px 20px;
    margin: 0 5px;
    margin-bottom: 10px;
    background-color: #f8f9fa;
    transition: all 0.3s ease;
}

.gallery-filter .nav-link.active,
.gallery-filter .nav-link:hover {
    background-color: var(--bs-primary);
    color: #fff;
}
</style>

<!-- Additional JavaScript for Gallery -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Initialize Magnific Popup for gallery
    if (typeof $.fn.magnificPopup !== 'undefined') {
        $('.gallery-popup').magnificPopup({
            type: 'image',
            gallery: {
                enabled: true
            }
        });
    }
    
    // Simple filtering functionality
    const filterButtons = document.querySelectorAll('#gallery-filter .nav-link');
    const galleryItems = document.querySelectorAll('.gallery-item');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            filterButtons.forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Get filter value
            const filterValue = this.getAttribute('data-filter');
            
            // Filter gallery items
            galleryItems.forEach(item => {
                if (filterValue === '*') {
                    item.style.display = 'block';
                } else if (item.classList.contains(filterValue.substring(1))) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
});
</script>

<!-- Magnific Popup CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.min.css">
<!-- Magnific Popup JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js"></script>

<?php include 'includes/footer.php'; ?>
