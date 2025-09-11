/**
 * Future Hope Foundation - Enhanced JavaScript Functions
 * Version: 2.0
 */

(function($) {
    "use strict";
    
    // Preloader
    $(window).on('load', function() {
        if($('#preloader').length) {
            $('#preloader').delay(100).fadeOut('slow', function() {
                $(this).remove();
            });
        }
    });
    
    // Back to top button with smooth scrolling
    $(window).scroll(function() {
        if ($(this).scrollTop() > 100) {
            $('#back-to-top').addClass('active');
        } else {
            $('#back-to-top').removeClass('active');
        }
    });
    
    $('#back-to-top').on('click', function(e) {
        e.preventDefault();
        $('html, body').animate({scrollTop: 0}, 800);
        return false;
    });
    
    // Activate scrollspy for the navigation
    $('body').scrollspy({ target: '#navbarNav', offset: 80 });
    
    // Smooth scroll for the navigation and links with .scrollto classes
    $(document).on('click', '.nav-link, .scrollto', function(e) {
        if(location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
            var target = $(this.hash);
            if (target.length) {
                e.preventDefault();
                
                var scrollto = target.offset().top - 70;
                
                $('html, body').animate({
                    scrollTop: scrollto
                }, 800);
                
                if ($(this).parents('.nav-menu, .navbar').length) {
                    $('.nav-menu .active, .navbar .active').removeClass('active');
                    $(this).closest('li').addClass('active');
                }
                
                return false;
            }
        }
    });
    
    // Initialize WOW.js for animations with enhanced options
    if (typeof WOW === 'function') {
        new WOW({
            boxClass: 'wow',
            animateClass: 'animated',
            offset: 50,
            mobile: true,
            live: true
        }).init();
    }
    
    // Add fade-in animation to elements as they come into view
    $(window).on('scroll', function() {
        $('.fade-in').each(function() {
            var elementTop = $(this).offset().top;
            var elementHeight = $(this).outerHeight();
            var windowHeight = $(window).height();
            var scrollY = $(window).scrollTop();
            
            if (scrollY + windowHeight > elementTop + elementHeight * 0.25) {
                $(this).addClass('visible');
            }
        });
    });
    
    // Enhanced Hero Slider
    if ($('.hero-slider').length) {
        $('.hero-slider').owlCarousel({
            items: 1,
            loop: true,
            margin: 0,
            nav: true,
            dots: true,
            autoplay: true,
            autoplayTimeout: 5000,
            smartSpeed: 1000,
            navText: ['<i class="fas fa-chevron-left"></i>', '<i class="fas fa-chevron-right"></i>'],
            responsive: {
                0: {
                    nav: false
                },
                768: {
                    nav: true
                }
            },
            onInitialized: function(event) {
                var totalItems = event.item.count;
                var currentItem = event.item.index - event.relatedTarget._clones.length / 2;
                var owlDots = $('.hero-slider .owl-dots');
                
                if (currentItem > totalItems) {
                    currentItem = currentItem % totalItems;
                }
                if (currentItem <= 0) {
                    currentItem = totalItems + currentItem;
                }
                
                // Add ARIA labels for accessibility
                owlDots.attr('aria-label', 'Carousel Pagination');
                $('.hero-slider .owl-nav button').attr('aria-label', 'Carousel Navigation');
            }
        });
    }
    
    // Enhanced Testimonial Slider
    if ($('.testimonial-slider').length) {
        $('.testimonial-slider').owlCarousel({
            loop: true,
            margin: 30,
            nav: true,
            dots: true,
            autoplay: true,
            smartSpeed: 1000,
            autoplayTimeout: 5000,
            navText: ['<i class="fas fa-chevron-left"></i>', '<i class="fas fa-chevron-right"></i>'],
            responsive: {
                0: {
                    items: 1,
                    nav: false
                },
                576: {
                    items: 1
                },
                768: {
                    items: 2
                },
                992: {
                    items: 3
                }
            }
        });
    }
    
    // Enhanced Partners Slider
    if ($('.partners-slider').length) {
        $('.partners-slider').owlCarousel({
            loop: true,
            margin: 30,
            nav: false,
            dots: false,
            autoplay: true,
            smartSpeed: 1000,
            autoplayTimeout: 5000,
            responsive: {
                0: {
                    items: 2
                },
                576: {
                    items: 3
                },
                768: {
                    items: 4
                },
                992: {
                    items: 5
                }
            }
        });
    }
    
    // Enhanced Gallery with Isotope and improved filtering
    if ($('.gallery-container').length) {
        $(window).on('load', function() {
            var galleryIsotope = $('.gallery-container').isotope({
                itemSelector: '.gallery-item',
                layoutMode: 'fitRows',
                transitionDuration: '0.6s'
            });
            
            // Add ARIA roles for accessibility
            $('.gallery-filters button').attr('role', 'tab');
            $('.gallery-filters').attr('role', 'tablist');
            
            $('.gallery-filters button').on('click', function() {
                $('.gallery-filters button').removeClass('active').attr('aria-selected', 'false');
                $(this).addClass('active').attr('aria-selected', 'true');
                
                galleryIsotope.isotope({
                    filter: $(this).data('filter')
                });
                
                // Update ARIA label
                $('.gallery-container').attr('aria-label', 'Filtered gallery showing ' + 
                                           ($(this).text() === 'All' ? 'all items' : $(this).text() + ' items'));
                
                return false;
            });
        });
    }
    
    // Enhanced Magnific Popup with accessibility improvements
    if ($.fn.magnificPopup) {
        $('.gallery-lightbox').magnificPopup({
            type: 'image',
            gallery: {
                enabled: true,
                navigateByImgClick: true,
                preload: [0,1]
            },
            image: {
                titleSrc: function(item) {
                    return item.el.attr('title') || 'Image';
                }
            },
            callbacks: {
                open: function() {
                    // Add keyboard navigation
                    $(document).on('keydown', function(e) {
                        if (e.keyCode === 37) { // left arrow
                            $.magnificPopup.instance.prev();
                        } else if (e.keyCode === 39) { // right arrow
                            $.magnificPopup.instance.next();
                        } else if (e.keyCode === 27) { // esc
                            $.magnificPopup.close();
                        }
                    });
                    
                    // Add ARIA attributes for accessibility
                    $('.mfp-container').attr('role', 'dialog');
                    $('.mfp-content').attr('role', 'document');
                    $('.mfp-figure').attr('aria-labelledby', 'mfp-title');
                    
                    // Add focus trap
                    this.focusTimer = setTimeout(function() {
                        $('.mfp-container').focus();
                    }, 100);
                },
                close: function() {
                    $(document).off('keydown');
                    clearTimeout(this.focusTimer);
                }
            }
        });
        
        $('.video-lightbox').magnificPopup({
            type: 'iframe',
            iframe: {
                patterns: {
                    youtube: {
                        index: 'youtube.com/',
                        id: 'v=',
                        src: '//www.youtube.com/embed/%id%?autoplay=1&rel=0&showinfo=0'
                    },
                    vimeo: {
                        index: 'vimeo.com/',
                        id: '/',
                        src: '//player.vimeo.com/video/%id%?autoplay=1'
                    }
                }
            }
        });
    }
    
    // Enhanced Counter with IntersectionObserver for better performance
    if ($('.counter-number').length) {
        var counted = false;
        
        var counterObserver = new IntersectionObserver(function(entries) {
            if(entries[0].isIntersecting && !counted) {
                counted = true;
                
                $('.counter-number').each(function() {
                    var $this = $(this);
                    var countTo = $this.attr('data-count');
                    
                    $({ countNum: 0 }).animate({
                        countNum: countTo
                    }, {
                        duration: 2000,
                        easing: 'swing',
                        step: function() {
                            $this.text(Math.floor(this.countNum));
                        },
                        complete: function() {
                            $this.text(this.countNum);
                        }
                    });
                });
            }
        }, { threshold: [0.5] });
        
        counterObserver.observe(document.querySelector('.counter-number').parentNode);
    }
    
    // Lazy loading for images
    if ('IntersectionObserver' in window) {
        const lazyImages = document.querySelectorAll('img.lazy-image');
        const imageObserver = new IntersectionObserver(function(entries, observer) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    const src = img.getAttribute('data-src');
                    
                    if (src) {
                        img.setAttribute('src', src);
                        img.onload = function() {
                            img.classList.add('loaded');
                            img.classList.remove('image-placeholder');
                        };
                        observer.unobserve(img);
                    }
                }
            });
        });
        
        lazyImages.forEach(function(image) {
            imageObserver.observe(image);
            image.classList.add('image-placeholder');
        });
    }
    
    // Form validation with enhanced user feedback
    $('.contact-form, .donation-form').on('submit', function(e) {
        var isValid = true;
        
        $(this).find('.form-control[required]').each(function() {
            if (!$(this).val()) {
                isValid = false;
                $(this).addClass('is-invalid');
                
                if (!$(this).next('.invalid-feedback').length) {
                    $(this).after('<div class="invalid-feedback">This field is required.</div>');
                }
            } else {
                $(this).removeClass('is-invalid').addClass('is-valid');
            }
        });
        
        // Email validation
        var emailInput = $(this).find('input[type="email"]');
        if (emailInput.length && emailInput.val()) {
            var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(emailInput.val())) {
                isValid = false;
                emailInput.addClass('is-invalid');
                
                if (!emailInput.next('.invalid-feedback').length) {
                    emailInput.after('<div class="invalid-feedback">Please enter a valid email address.</div>');
                }
            }
        }
        
        if (!isValid) {
            e.preventDefault();
            
            // Focus the first invalid input
            $(this).find('.is-invalid').first().focus();
            
            // Show error message
            var formError = $(this).find('.form-error');
            if (formError.length) {
                formError.html('<div class="alert alert-danger">Please correct the errors above.</div>');
            }
        }
    });
    
    // Clear validation state when input changes
    $('.contact-form, .donation-form').on('input', '.form-control', function() {
        $(this).removeClass('is-invalid is-valid');
        $(this).next('.invalid-feedback').remove();
    });
    
    // Progress bar animation with IntersectionObserver
    if ($('.progress').length) {
        var progressObserver = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    var progressBar = entry.target.querySelector('.progress-bar');
                    var percent = progressBar.getAttribute('aria-valuenow') + '%';
                    progressBar.style.width = percent;
                    progressObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });
        
        document.querySelectorAll('.progress').forEach(function(progress) {
            progressObserver.observe(progress);
        });
    }
    
    // Mobile optimization - prevent zoom on form input focus on iOS
    var viewportMeta = $('meta[name="viewport"]');
    if (viewportMeta.length) {
        var viewportContent = viewportMeta.attr('content');
        var originalContent = viewportContent;
        
        $('input, select, textarea').on({
            'touchstart': function() {
                viewportMeta.attr('content', originalContent + ', maximum-scale=1.0');
            },
            'blur': function() {
                viewportMeta.attr('content', originalContent);
            }
        });
    }
    
    // Add heading anchors for easy linking
    $('h2, h3').each(function() {
        var id = $(this).attr('id');
        if (!id) {
            id = $(this).text().toLowerCase().replace(/[^a-z0-9]/g, '-');
            $(this).attr('id', id);
        }
        
        $(this).append('<a class="heading-anchor" href="#' + id + '"><i class="fas fa-link"></i></a>');
    });
    
    // Remove the mobile date widget
    function removeUnwantedElements() {
        // Look for elements matching the date widget pattern at the top of the page
        $('body > div:first-child').each(function() {
            var $this = $(this);
            // Check if this is our main components
            if (!$this.hasClass('top-bar') && !$this.hasClass('header') && 
                !$this.hasClass('footer') && !$this.hasClass('preloader')) {
                
                // If it contains date-like format with numbers and colons
                if ($this.text().match(/\d{1,2}.*\d{4}.*\d{1,2}:\d{2}/) || 
                    $this.find('i.fa-calendar, i.fa-calendar-alt').length) {
                    $this.remove();
                }
            }
        });
    }

    // Run when DOM is ready
    $(document).ready(function() {
        removeUnwantedElements();
    });

    // Also run after window loads (for dynamic elements)
    $(window).on('load', function() {
        setTimeout(removeUnwantedElements, 100);
    });
    
})(jQuery);
