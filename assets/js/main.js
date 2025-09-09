/**
 * Future Hope - Nonprofit Organization JavaScript
 * Version: 1.0
 */

(function($) {
    "use strict";
    
    // Back to top button
    $(window).scroll(function() {
        if ($(this).scrollTop() > 100) {
            $('#back-to-top').addClass('active');
        } else {
            $('#back-to-top').removeClass('active');
        }
    });
    
    // Smooth scrolling for the back to top button
    $('#back-to-top').on('click', function(e) {
        e.preventDefault();
        $('html, body').animate({scrollTop: 0}, 800);
        return false;
    });
    
    // Initialize WOW.js for animations
    if (typeof WOW === 'function') {
        new WOW().init();
    }
    
    // Hero Slider
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
            }
        });
    }
    
    // Testimonial Slider
    if ($('.testimonial-slider').length) {
        $('.testimonial-slider').owlCarousel({
            loop: true,
            margin: 30,
            nav: false,
            dots: true,
            autoplay: true,
            smartSpeed: 1000,
            autoplayTimeout: 5000,
            responsive: {
                0: {
                    items: 1
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
    
    // Partners Slider
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
    
    // Gallery Isotope and filter
    if ($('.gallery-container').length) {
        $(window).on('load', function() {
            var galleryIsotope = $('.gallery-container').isotope({
                itemSelector: '.gallery-item',
                layoutMode: 'fitRows'
            });
            
            $('.gallery-filters button').on('click', function() {
                $('.gallery-filters button').removeClass('active');
                $(this).addClass('active');
                
                galleryIsotope.isotope({
                    filter: $(this).data('filter')
                });
            });
        });
    }
    
    // Initialize Magnific Popup
    if ($.fn.magnificPopup) {
        $('.gallery-lightbox').magnificPopup({
            type: 'image',
            gallery: {
                enabled: true
            }
        });
        
        $('.video-lightbox').magnificPopup({
            type: 'iframe'
        });
    }
    
    // Counter
    if ($('.counter-number').length) {
        $('.counter-number').each(function() {
            $(this).prop('Counter', 0).animate({
                Counter: $(this).text()
            }, {
                duration: 4000,
                easing: 'swing',
                step: function(now) {
                    $(this).text(Math.ceil(now));
                }
            });
        });
    }
    
    // Donation Amount Selection
    $('.amount-box').on('click', function() {
        $('.amount-box').removeClass('active');
        $(this).addClass('active');
        
        var amount = $(this).data('amount');
        $('#custom-amount').val(amount);
    });
    
    // Custom Donation Amount
    $('#custom-amount').on('focus', function() {
        $('.amount-box').removeClass('active');
    });
    
    // Contact Form Validation
    if ($('#contact-form').length) {
        $('#contact-form').on('submit', function(e) {
            e.preventDefault();
            
            var form = $(this);
            var formData = form.serialize();
            var submitBtn = form.find('button[type="submit"]');
            var originalBtnText = submitBtn.html();
            
            $.ajax({
                type: 'POST',
                url: 'process/contact-process.php',
                data: formData,
                beforeSend: function() {
                    submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Sending...');
                    submitBtn.attr('disabled', 'disabled');
                },
                success: function(response) {
                    var data = JSON.parse(response);
                    
                    if (data.success) {
                        form.find('.form-message').html('<div class="alert alert-success">' + data.message + '</div>');
                        form[0].reset();
                    } else {
                        form.find('.form-message').html('<div class="alert alert-danger">' + data.message + '</div>');
                    }
                    
                    submitBtn.html(originalBtnText);
                    submitBtn.removeAttr('disabled');
                },
                error: function() {
                    form.find('.form-message').html('<div class="alert alert-danger">An error occurred. Please try again later.</div>');
                    submitBtn.html(originalBtnText);
                    submitBtn.removeAttr('disabled');
                }
            });
        });
    }
    
    // Donation Form Validation
    if ($('#donation-form').length) {
        $('#donation-form').on('submit', function(e) {
            e.preventDefault();
            
            var form = $(this);
            var formData = form.serialize();
            var submitBtn = form.find('button[type="submit"]');
            var originalBtnText = submitBtn.html();
            
            $.ajax({
                type: 'POST',
                url: 'process/donation-process.php',
                data: formData,
                beforeSend: function() {
                    submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Processing...');
                    submitBtn.attr('disabled', 'disabled');
                },
                success: function(response) {
                    var data = JSON.parse(response);
                    
                    if (data.success) {
                        form.find('.form-message').html('<div class="alert alert-success">' + data.message + '</div>');
                        form[0].reset();
                    } else {
                        form.find('.form-message').html('<div class="alert alert-danger">' + data.message + '</div>');
                    }
                    
                    submitBtn.html(originalBtnText);
                    submitBtn.removeAttr('disabled');
                },
                error: function() {
                    form.find('.form-message').html('<div class="alert alert-danger">An error occurred. Please try again later.</div>');
                    submitBtn.html(originalBtnText);
                    submitBtn.removeAttr('disabled');
                }
            });
        });
    }
    
    // Newsletter Form
    if ($('#newsletter-form').length) {
        $('#newsletter-form').on('submit', function(e) {
            e.preventDefault();
            
            var form = $(this);
            var formData = form.serialize();
            var submitBtn = form.find('button[type="submit"]');
            var originalBtnText = submitBtn.html();
            
            $.ajax({
                type: 'POST',
                url: 'process/newsletter-process.php',
                data: formData,
                beforeSend: function() {
                    submitBtn.html('<i class="fas fa-spinner fa-spin"></i>');
                    submitBtn.attr('disabled', 'disabled');
                },
                success: function(response) {
                    var data = JSON.parse(response);
                    
                    if (data.success) {
                        form.find('.form-message').html('<div class="alert alert-success">' + data.message + '</div>');
                        form[0].reset();
                    } else {
                        form.find('.form-message').html('<div class="alert alert-danger">' + data.message + '</div>');
                    }
                    
                    submitBtn.html(originalBtnText);
                    submitBtn.removeAttr('disabled');
                },
                error: function() {
                    form.find('.form-message').html('<div class="alert alert-danger">An error occurred. Please try again later.</div>');
                    submitBtn.html(originalBtnText);
                    submitBtn.removeAttr('disabled');
                }
            });
        });
    }
    
})(jQuery);
