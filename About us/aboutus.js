/* ══════════════════════════════════════════════
   HASSAN TRADERS — ABOUT US PAGE SCRIPTS
   aboutus.js
══════════════════════════════════════════════ */

document.addEventListener('DOMContentLoaded', function () {

  /* ──────────────────────────────────────────
     SCROLL ANIMATION
     Any element with [data-animate] fades up
     when it enters the viewport
  ────────────────────────────────────────── */
  const animatables = document.querySelectorAll('[data-animate]');

  if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry, i) {
        if (entry.isIntersecting) {
          // Stagger siblings within the same parent
          const siblings = Array.from(
            entry.target.parentElement.querySelectorAll('[data-animate]')
          );
          const idx = siblings.indexOf(entry.target);
          entry.target.style.transitionDelay = (idx * 0.1) + 's';
          entry.target.classList.add('au-visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12 });

    animatables.forEach(function (el) {
      observer.observe(el);
    });
  } else {
    // Fallback: show all immediately if IntersectionObserver unavailable
    animatables.forEach(function (el) {
      el.classList.add('au-visible');
    });
  }

  /* ──────────────────────────────────────────
     ACTIVE NAV LINK — highlight About Us
  ────────────────────────────────────────── */
  const navLinks = document.querySelectorAll('.nav-link');
  navLinks.forEach(function (link) {
    if (link.href && link.href.includes('aboutus')) {
      link.classList.add('active');
    }
  });

  /* ──────────────────────────────────────────
     STAT COUNTER ANIMATION
     Counts up numbers in .au-stat-num
     when the stats strip enters view
  ────────────────────────────────────────── */
  function animateCounter(el, target, duration) {
    let start = 0;
    const step = target / (duration / 16);
    const timer = setInterval(function () {
      start += step;
      if (start >= target) {
        start = target;
        clearInterval(timer);
      }
      // Preserve the <span>+</span> suffix
      const suffix = el.querySelector('span') ? '<span>+</span>' : '';
      el.innerHTML = Math.floor(start) + suffix;
    }, 16);
  }

  const statsGrid = document.querySelector('.au-stats-grid');
  if (statsGrid && 'IntersectionObserver' in window) {
    let counted = false;
    const statsObs = new IntersectionObserver(function (entries) {
      if (entries[0].isIntersecting && !counted) {
        counted = true;
        document.querySelectorAll('.au-stat-num').forEach(function (numEl) {
          const raw = numEl.textContent.replace('+', '').trim();
          const target = parseInt(raw, 10);
          if (!isNaN(target)) animateCounter(numEl, target, 1200);
        });
      }
    }, { threshold: 0.3 });
    statsObs.observe(statsGrid);
  }

});
