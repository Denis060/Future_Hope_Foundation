/**
 * Timeline animation for About page
 */
document.addEventListener('DOMContentLoaded', function() {
    // Check if we're on the about page with timeline
    const timelineItems = document.querySelectorAll('.timeline-item');
    if (timelineItems.length === 0) return;
    
    // Initialize animation on scroll
    function animateOnScroll() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = 1;
                    entry.target.style.transform = 'translateY(0)';
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.2
        });
        
        // Observe timeline items
        timelineItems.forEach(item => {
            // Reset styles that might have been set by CSS animations
            item.style.opacity = 0;
            item.style.transform = 'translateY(20px)';
            item.style.transition = 'opacity 0.8s ease, transform 0.8s ease';
            observer.observe(item);
        });
        
        // Also observe other fade-in elements
        document.querySelectorAll('.fade-in').forEach(item => {
            if (!item.classList.contains('timeline-item')) {
                item.style.opacity = 0;
                item.style.transform = 'translateY(20px)';
                item.style.transition = 'opacity 0.8s ease, transform 0.8s ease';
                observer.observe(item);
            }
        });
    }
    
    // Start animation
    animateOnScroll();
    
    // Add some hover effects for impact items
    const impactItems = document.querySelectorAll('.impact-item');
    impactItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px)';
            this.style.boxShadow = '0 10px 25px rgba(0,0,0,0.1)';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = 'none';
        });
    });
});
