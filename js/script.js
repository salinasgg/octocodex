$(document).ready(function() {
    
    // Resetear transformaciones al cargar la página
    $('.hero-section').css('transform', 'translateY(0)');
    
    // Función para resetear completamente el hero section
    function resetHeroSection() {
        $('.hero-section').css({
            'transform': 'translateY(0)',
            'position': 'relative',
            'top': '0'
        });
    }
    
    // Resetear al cargar la página
    resetHeroSection();
    
    // Loading Screen
    setTimeout(function() {
        $('.loading').addClass('hidden');
        setTimeout(function() {
            $('.loading').remove();
        }, 500);
    }, 1000);
    
    // Smooth scrolling for navigation links
    $('a[href^="#"]').on('click', function(e) {
        e.preventDefault();
        
        var target = $(this.getAttribute('href'));
        if (target.length) {
            $('html, body').animate({
                scrollTop: target.offset().top - 70
            }, 800);
        }
    });
    
    // Active navigation link highlighting
    $(window).on('scroll', function() {
        var scrollDistance = $(window).scrollTop();
        
        $('section').each(function(i) {
            if ($(this).position().top - 100 <= scrollDistance) {
                $('.navbar-nav .nav-link.active').removeClass('active');
                $('.navbar-nav .nav-link').eq(i).addClass('active');
            }
        });
    });
    
    // Fade in animation for sections
    function fadeInOnScroll() {
        $('.fade-in').each(function() {
            var elementTop = $(this).offset().top;
            var elementBottom = elementTop + $(this).outerHeight();
            var viewportTop = $(window).scrollTop();
            var viewportBottom = viewportTop + $(window).height();
            
            if (elementBottom > viewportTop && elementTop < viewportBottom) {
                $(this).addClass('visible');
            }
        });
    }
    
    // Add fade-in class to sections
    $('section').addClass('fade-in');
    
    // Trigger fade in on scroll
    $(window).on('scroll', fadeInOnScroll);
    fadeInOnScroll(); // Initial check
    
    // Form input focus effects (mantenido para compatibilidad)
    $('.form-control').on('focus', function() {
        $(this).parent().addClass('focused');
    }).on('blur', function() {
        if (!$(this).val()) {
            $(this).parent().removeClass('focused');
        }
    });
    
    // Navbar background on scroll
    $(window).on('scroll', function() {
        if ($(window).scrollTop() > 50) {
            $('.navbar').addClass('scrolled');
        } else {
            $('.navbar').removeClass('scrolled');
        }
    });
    
    // Mobile menu close on link click
    $('.navbar-nav .nav-link').on('click', function() {
        $('.navbar-collapse').collapse('hide');
    });
    
    // Service cards hover effect
    $('.card').hover(
        function() {
            $(this).find('.service-icon i').addClass('fa-bounce');
        },
        function() {
            $(this).find('.service-icon i').removeClass('fa-bounce');
        }
    );
    
    // Technology cards click effect
    $('.tech-card').on('click', function() {
        $(this).addClass('clicked');
        setTimeout(function() {
            $('.tech-card').removeClass('clicked');
        }, 300);
    });
    
    // Hero section scroll effect (sin parallax problemático)
    $(window).on('scroll', function() {
        var scrolled = $(window).scrollTop();
        var heroSection = $('.hero-section');
        
        // Resetear completamente cuando estamos en el top
        if (scrolled <= 0) {
            resetHeroSection();
        } else if (scrolled > heroSection.outerHeight()) {
            // Cuando estamos fuera de la sección hero, mantener sin transformación
            heroSection.css('transform', 'translateY(0)');
        }
    });
    
    // Reset adicional cuando el usuario llega al top
    $(window).on('scroll', function() {
        if ($(window).scrollTop() === 0) {
            setTimeout(function() {
                resetHeroSection();
            }, 50);
        }
    });
    
    // Counter animation for statistics
    function animateCounters() {
        $('.counter').each(function() {
            var $this = $(this);
            var countTo = $this.attr('data-count');
            
            $({ countNum: $this.text() }).animate({
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
    
    // Trigger counter animation when in view
    var counterSection = $('#quienes-somos');
    if (counterSection.length) {
        $(window).on('scroll', function() {
            var counterTop = counterSection.offset().top;
            var scrollTop = $(window).scrollTop();
            var windowHeight = $(window).height();
            
            if (scrollTop + windowHeight > counterTop) {
                animateCounters();
                $(window).off('scroll');
            }
        });
    }
    
    // Back to top button
    var backToTop = $('<button class="btn btn-primary back-to-top" style="position: fixed; bottom: 20px; right: 20px; z-index: 1000; display: none;"><i class="fas fa-arrow-up"></i></button>');
    $('body').append(backToTop);
    
    $(window).on('scroll', function() {
        if ($(window).scrollTop() > 300) {
            backToTop.fadeIn();
        } else {
            backToTop.fadeOut();
        }
    });
    
    backToTop.on('click', function() {
        $('html, body').animate({
            scrollTop: 0
        }, 800);
        
        // Resetear hero section después de la animación
        setTimeout(function() {
            resetHeroSection();
        }, 850);
    });
    
    // Typing effect for hero title
    function typeWriter(element, text, speed = 100) {
        var i = 0;
        element.html('');
        
        function type() {
            if (i < text.length) {
                element.html(element.html() + text.charAt(i));
                i++;
                setTimeout(type, speed);
            }
        }
        type();
    }
    
    // Initialize typing effect
    var heroTitle = $('.hero-section h1');
    if (heroTitle.length) {
        var originalText = heroTitle.text();
        setTimeout(function() {
            typeWriter(heroTitle, originalText, 50);
        }, 1000);
    }
    
    // Form input focus effects
    $('.form-control').on('focus', function() {
        $(this).parent().addClass('focused');
    }).on('blur', function() {
        if (!$(this).val()) {
            $(this).parent().removeClass('focused');
        }
    });
    
    // Social links hover effect
    $('.social-links a').hover(
        function() {
            $(this).addClass('fa-bounce');
        },
        function() {
            $(this).removeClass('fa-bounce');
        }
    );
    
    // Preloader
    $(window).on('load', function() {
        $('.loading').fadeOut();
        // Resetear hero section después de que todo se cargue
        setTimeout(function() {
            resetHeroSection();
        }, 100);
    });
    
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Add some CSS for the back to top button
    $('<style>')
        .prop('type', 'text/css')
        .html(`
            .back-to-top {
                border-radius: 50%;
                width: 50px;
                height: 50px;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.3s ease;
            }
            .back-to-top:hover {
                transform: translateY(-3px);
            }
            .focused .form-control {
                border-color: #007bff;
                box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
            }
            .clicked {
                transform: scale(0.95);
            }
        `)
        .appendTo('head');
}); 