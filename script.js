/**
 * HASSAN TRADERS - Premium UI/UX Scripts
 * Features: Scroll Animations, Counter Stats, Hero Parallax, Smooth Interactions
 */

document.addEventListener('DOMContentLoaded', () => {
    "use strict";

    // 1. SCROLL REVEAL ANIMATION
    // Uses Intersection Observer for a "fade-in-up" effect as user scrolls
    const observerOptions = {
        threshold: 0.15,
        rootMargin: "0px 0px -50px 0px"
    };

    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('hm-visible');
                // Once visible, we can stop observing this specific element
                revealObserver.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Attach observer to all elements with [data-animate]
    document.querySelectorAll('[data-animate]').forEach(el => {
        revealObserver.observe(el);
    });


    // 2. COUNTER ANIMATION FOR STATS
    // Animates numbers from 0 to their target value when they enter the viewport
    const countStats = () => {
        const stats = document.querySelectorAll('.hm-stat-num');
        
        const statsObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const target = parseInt(entry.target.getAttribute('data-target'));
                    animateValue(entry.target, 0, target, 2000);
                    statsObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        stats.forEach(stat => statsObserver.observe(stat));
    };

    function animateValue(obj, start, end, duration) {
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            const currentCount = Math.floor(progress * (end - start) + start);
            
            // Add a "+" if it's the final value
            obj.innerHTML = currentCount + (progress === 1 ? '<span>+</span>' : '<span></span>');
            
            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        };
        window.requestAnimationFrame(step);
    }

    countStats();


    // 3. CAROUSEL CUSTOM INTERACTION
    // Adds a slight parallax movement to hero images during slide change
    const heroCarousel = document.getElementById('heroCarousel');
    if (heroCarousel) {
        heroCarousel.addEventListener('slide.bs.carousel', (event) => {
            const activeImg = event.from.querySelector('.hm-hero-img');
            const nextImg = event.to.querySelector('.hm-hero-img');
            
            if (activeImg) activeImg.style.transform = 'scale(1.1) translateX(10px)';
            if (nextImg) nextImg.style.transform = 'scale(1)';
        });
    }


    // 4. SMOOTH SCROLL FOR ANCHOR LINKS
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                e.preventDefault();
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });


    // 5. BUTTON HOVER MAGNETIC EFFECT (Subtle)
    // Makes primary buttons feel "weighty" and premium
    const magneticBtns = document.querySelectorAll('.hm-btn-primary, .hm-cta-btn-primary');
    
    magneticBtns.forEach(btn => {
        btn.addEventListener('mousemove', (e) => {
            const rect = btn.getBoundingClientRect();
            const x = e.clientX - rect.left - rect.width / 2;
            const y = e.clientY - rect.top - rect.height / 2;
            
            btn.style.transform = `translate(${x * 0.15}px, ${y * 0.3}px)`;
        });
        
        btn.addEventListener('mouseleave', () => {
            btn.style.transform = `translate(0, 0)`;
        });
    });

    // 6. DYNAMIC HEADER NESTING
    // Shrinks the navbar slightly when scrolling for a more "focused" feel
    const navbar = document.querySelector('.navbar'); // Ensure your navbar has this class
    if (navbar) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.classList.add('navbar-scrolled');
            } else {
                navbar.classList.remove('navbar-scrolled');
            }
        });
    }
});