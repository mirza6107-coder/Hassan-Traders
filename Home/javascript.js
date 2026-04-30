/* ══════════════════════════════════════════════
   HASSAN TRADERS — HOME PAGE SCRIPTS
   javascript.js
══════════════════════════════════════════════ */

document.addEventListener('DOMContentLoaded', function () {

  
  /* ──────────────────────────────────────────
   HERO CAROUSEL
────────────────────────────────────────── */
const heroCarouselEl = document.querySelector('#heroCarousel');
if (heroCarouselEl) {
  // Use the Bootstrap constructor to ensure it's initialized with your specific settings
  const carousel = new bootstrap.Carousel(heroCarouselEl, {
    interval: 4500,
    touch: true, // Better for mobile
    ride: 'carousel'
  });
}

  /* ──────────────────────────────────────────
     SCROLL ANIMATION
     Fade-up any [data-animate] element
  ────────────────────────────────────────── */
  const animatables = document.querySelectorAll('[data-animate]');

  if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          // Stagger siblings
          const siblings = Array.from(
            entry.target.parentElement.querySelectorAll('[data-animate]')
          );
          const idx = siblings.indexOf(entry.target);
          entry.target.style.transitionDelay = (idx * 0.1) + 's';
          entry.target.classList.add('hm-visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12 });

    animatables.forEach(el => observer.observe(el));
  } else {
    animatables.forEach(el => el.classList.add('hm-visible'));
  }

  /* ──────────────────────────────────────────
     STAT COUNTER ANIMATION
  ────────────────────────────────────────── */
  function animateCounter(el, target, duration) {
    let start = 0;
    const step = target / (duration / 16);
    const suffix = el.querySelector('span') ? '<span>+</span>' : '';
    const timer = setInterval(function () {
      start += step;
      if (start >= target) {
        start = target;
        clearInterval(timer);
      }
      el.innerHTML = Math.floor(start) + suffix;
    }, 16);
  }

  const statsStrip = document.querySelector('.hm-stats-strip');
  if (statsStrip && 'IntersectionObserver' in window) {
    let counted = false;
    const statsObs = new IntersectionObserver(function (entries) {
      if (entries[0].isIntersecting && !counted) {
        counted = true;
        document.querySelectorAll('.hm-stat-num').forEach(function (numEl) {
          const target = parseInt(numEl.dataset.target || '0', 10);
          if (!isNaN(target)) animateCounter(numEl, target, 1400);
        });
      }
    }, { threshold: 0.3 });
    statsObs.observe(statsStrip);
  }

  /* ──────────────────────────────────────────
     NAVBAR SCROLL SHADOW
  ────────────────────────────────────────── */
  const navbar = document.querySelector('.navbar');
  if (navbar) {
    window.addEventListener('scroll', function () {
      navbar.classList.toggle('scrolled', window.scrollY > 50);
    }, { passive: true });
  }

  /* ──────────────────────────────────────────
     SMOOTH SCROLLING for anchor links
  ────────────────────────────────────────── */
  document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
    anchor.addEventListener('click', function (e) {
      const targetId = this.getAttribute('href').substring(1);
      const targetEl = document.getElementById(targetId);
      if (targetEl) {
        e.preventDefault();
        const offset = targetEl.getBoundingClientRect().top + window.scrollY - 80;
        window.scrollTo({ top: offset, behavior: 'smooth' });
      }
    });
  });

  /* ──────────────────────────────────────────
     BUTTON PRESS EFFECT
  ────────────────────────────────────────── */
  document.querySelectorAll('.hm-btn-primary, .hm-cta-btn-primary').forEach(function (btn) {
    btn.addEventListener('mousedown', () => { btn.style.transform = 'translateY(0) scale(0.96)'; });
    btn.addEventListener('mouseup',   () => { btn.style.transform = ''; });
    btn.addEventListener('mouseleave',() => { btn.style.transform = ''; });
  });

  /* ──────────────────────────────────────────
     FOOTER YEAR AUTO UPDATE
  ────────────────────────────────────────── */
  const yearEl = document.querySelector('.footer-section .small');
  if (yearEl) {
    yearEl.textContent = yearEl.textContent.replace(/\d{4}/, new Date().getFullYear());
  }

  /* ──────────────────────────────────────────
     ACTIVE NAV LINK
  ────────────────────────────────────────── */
  document.querySelectorAll('.nav-link').forEach(function (link) {
    if (link.href && link.href.includes('home')) {
      link.classList.add('active');
    }
  });

  /* ──────────────────────────────────────────
     CAROUSEL ARIA LABELS
  ────────────────────────────────────────── */
  document.querySelectorAll('.carousel-control-prev, .carousel-control-next')
    .forEach(function (btn, i) {
      if (!btn.getAttribute('aria-label')) {
        btn.setAttribute('aria-label', i === 0 ? 'Previous slide' : 'Next slide');
      }
    });

  console.log('%c✅ Hassan Traders — Home page loaded!', 'color:#dc3545;font-weight:bold;');
});
