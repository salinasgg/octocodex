$(document).ready(function() {
    // Portfolio data
    const portfolioData = [
        {
            id: 1,
            title: "E-commerce Moderno",
            image: "anow.png",
            description: "Diseño y desarrollo de una plataforma de comercio electrónico con interfaz intuitiva y experiencia de usuario optimizada.",
            category: "web",
            tags: ["HTML", "CSS3", "Bootstrap5", "Jquery","PHP","MySQL"],
            details: "Un proyecto completo de e-commerce que incluye gestión de productos, registro de ventas y panel de administración."
        }
        // ,
        // {
        //     id: 2,
        //     title: "Identidad Corporativa StartupTech",
        //     image: "anow.png",
        //     description: "Creación de identidad visual completa para startup tecnológica, incluyendo logo, colores y guía de marca.",
        //     category: "branding",
        //     tags: ["Illustrator", "Photoshop", "Brand Design", "Logo"],
        //     details: "Desarrollo completo de la identidad visual para una startup de tecnología financiera. El proyecto incluyó investigación de mercado, conceptualización, diseño de logo, paleta de colores, tipografía y manual de marca."
        // },
        // {
        //     id: 3,
        //     title: "App Móvil FitTracker",
        //     image: "anow.png",
        //     description: "Aplicación móvil para seguimiento de ejercicios y nutrición con interfaz moderna y funcionalidades avanzadas.",
        //     category: "mobile",
        //     tags: ["React Native", "Firebase", "UI Design", "Health"],
        //     details: "Aplicación móvil completa para el seguimiento de actividad física y nutrición. Incluye tracking de ejercicios, contador de calorías, estadísticas personalizadas y gamificación para motivar a los usuarios."
        // },
        // {
        //     id: 4,
        //     title: "Portfolio Arquitectura",
        //     image: "anow.png",
        //     description: "Sitio web minimalista para estudio de arquitectura, enfocado en mostrar proyectos de manera elegante.",
        //     category: "web",
        //     tags: ["HTML5", "CSS3", "JavaScript", "Photography"],
        //     details: "Website elegante y minimalista para un estudio de arquitectura. El diseño se centra en la fotografía de alta calidad de los proyectos, con navegación fluida y animaciones sutiles que complementan el contenido visual."
        // },
        // {
        //     id: 5,
        //     title: "Catálogo Productos 2025",
        //     image: "anow.png",
        //     description: "Diseño editorial para catálogo de productos con layout moderno y fotografía profesional.",
        //     category: "print",
        //     tags: ["InDesign", "Photography", "Print Design", "Layout"],
        //     details: "Catálogo impreso de 120 páginas para empresa manufacturera. El proyecto incluyó dirección de arte, sesión fotográfica de productos, diseño editorial y preparación para impresión offset."
        // },
        // {
        //     id: 6,
        //     title: "Dashboard Analytics",
        //     image: "anow.png",
        //     description: "Interface de usuario para dashboard de analíticas con visualización de datos interactiva.",
        //     category: "web",
        //     tags: ["Vue.js", "D3.js", "Data Visualization", "UX"],
        //     details: "Dashboard completo para visualización de datos de marketing digital. Incluye gráficos interactivos, filtros avanzados, exportación de reportes y diseño responsive optimizado para diferentes dispositivos."
        // },
        // {
        //     id: 7,
        //     title: "App Delivery Food",
        //     image: "anow.png",
        //     description: "Aplicación de delivery de comida con geolocalización y sistema de pagos integrado.",
        //     category: "mobile",
        //     tags: ["Flutter", "GPS", "Payment", "Real-time"],
        //     details: "Aplicación completa de delivery de comida que conecta restaurantes con usuarios. Incluye geolocalización en tiempo real, sistema de pagos múltiples, chat con repartidores y sistema de calificaciones."
        // },
        // {
        //     id: 8,
        //     title: "Rebranding Restaurant Chain",
        //     image: "anow.png",
        //     description: "Renovación completa de imagen para cadena de restaurantes, incluyendo señalética y packaging.",
        //     category: "branding",
        //     tags: ["Rebranding", "Packaging", "Signage", "Food"],
        //     details: "Proyecto de rebranding completo para cadena de restaurantes familiares. Incluyó nuevo logo, colores corporativos, diseño de menús, packaging para delivery, señalética de locales y uniformes para staff."
        // }
    ];

    // Generate portfolio items
    function generatePortfolioItems(data) {
        const grid = $('#portfolioGrid');
        grid.empty();
        
        data.forEach(item => {
            // Debug: verificar si item.image existe
            console.log(`Item ${item.id}: image = ${item.image}`);
            
            // Validar que item.image existe, si no, usar una imagen por defecto
            const imageSrc = item.image ? `img/${item.image}` : 'img/anow.png';
            
            // Debug: mostrar la URL completa que se está generando
            console.log(`Item ${item.id}: URL completa = ${imageSrc}`);
            
            const portfolioItem = $(`
                <div class="portfolio-item reveal" data-category="${item.category}" data-id="${item.id}">
                    <div class="portfolio-image">
                        <img src="${imageSrc}" alt="${item.title}" onload="console.log('Imagen cargada exitosamente:', '${imageSrc}')" onerror="console.log('Error cargando imagen:', '${imageSrc}'); this.style.display='none'; this.parentElement.innerHTML='<div style=\\'height: 200px; background: linear-gradient(45deg, #667eea, #764ba2); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white;\\'>Imagen no disponible</div>'">
                    </div>
                    <div class="portfolio-content">
                        <h3 class="portfolio-title">${item.title}</h3>
                        <p class="portfolio-description">${item.description}</p>
                        <div class="portfolio-tags">
                            ${item.tags.map(tag => `<span class="tag">${tag}</span>`).join('')}
                        </div>
                    </div>
                </div>
            `);
            grid.append(portfolioItem);
        });
    }

    // Initialize portfolio
    generatePortfolioItems(portfolioData);

    // Filter functionality
    $('.filter-btn').click(function() {
        const filter = $(this).data('filter');
        
        $('.filter-btn').removeClass('active');
        $(this).addClass('active');
        
        if (filter === 'all') {
            $('.portfolio-item').show().addClass('reveal');
        } else {
            $('.portfolio-item').hide();
            $(`.portfolio-item[data-category="${filter}"]`).show().addClass('reveal');
        }
    });

    // Modal functionality
    $('.portfolio-grid').on('click', '.portfolio-item', function() {
        const itemId = $(this).data('id');
        const item = portfolioData.find(p => p.id === itemId);
        
        if (item) {
            const modalContent = `
                <h2>${item.title}</h2>
                <div style="height: 200px; background: linear-gradient(45deg, #667eea, #764ba2); border-radius: 10px; margin: 1rem 0;"></div>
                <p><strong>Categoría:</strong> ${item.category}</p>
                <p><strong>Descripción:</strong> ${item.description}</p>
                <p><strong>Detalles:</strong> ${item.details}</p>
                <div style="margin-top: 1rem;">
                    <strong>Tecnologías:</strong>
                    <div style="margin-top: 0.5rem;">
                        ${item.tags.map(tag => `<span class="tag" style="margin-right: 0.5rem;">${tag}</span>`).join('')}
                    </div>
                </div>
            `;
            
            $('#modalContent').html(modalContent);
            $('#projectModal').fadeIn(300);
        }
    });

    // Close modal
    $('.modal-close, .modal').click(function(e) {
        if (e.target === this) {
            $('#projectModal').fadeOut(300);
        }
    });

    // Smooth scrolling
    $('a[href^="#"]').click(function(e) {
        e.preventDefault();
        const target = $($(this).attr('href'));
        if (target.length) {
            $('html, body').animate({
                scrollTop: target.offset().top - 80
            }, 800);
        }
    });

    // Scroll reveal animation
    function reveal() {
        const reveals = $('.reveal');
        
        reveals.each(function() {
            const windowHeight = $(window).height();
            const elementTop = $(this).offset().top;
            const elementVisible = 150;
            
            if (elementTop < $(window).scrollTop() + windowHeight - elementVisible) {
                $(this).addClass('active');
            }
        });
    }

    $(window).scroll(reveal);
    reveal(); // Initial check

    // Header scroll effect
    $(window).scroll(function() {
        if ($(this).scrollTop() > 100) {
            $('.header').css('background', 'rgba(255, 255, 255, 0.98)');
        } else {
            $('.header').css('background', 'rgba(255, 255, 255, 0.95)');
        }
    });

    // Add hover effects and micro-interactions
    $('.portfolio-item').hover(
        function() {
            $(this).find('.portfolio-image').css('transform', 'scale(1.05)');
        },
        function() {
            $(this).find('.portfolio-image').css('transform', 'scale(1)');
        }
    );

    // Parallax effect for hero section
    $(window).scroll(function() {
        const scrolled = $(this).scrollTop();
        const parallax = $('.hero');
        const speed = scrolled * 0.5;
        
        parallax.css('transform', 'translateY(' + speed + 'px)');
    });
});