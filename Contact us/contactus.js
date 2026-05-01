document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('contactForm');
    const toast = document.getElementById('successToast');
    const toastText = document.getElementById('toastText');
    const submitBtn = document.getElementById('submitBtn');

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        // Loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = "Sending...";

        const formData = new FormData(form);

        fetch('contact_process.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(result => {
            if (result.trim() === "Success") {
                toast.style.background = "#f0fdf4"; // Success Green
                toastText.innerText = "Message send successfully! We'll get back to you shortly";
                toast.classList.add('show');
                form.reset();
            } else {
                throw new Error(result);
            }
        })
        .catch(error => {
            toast.style.background = "#fef2f2"; // Error Red
            toast.style.color = "#991b1b";
            toastText.innerText = "Error: " + error.message;
            toast.classList.add('show');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-send-fill me-2"></i> Send Message';
            setTimeout(() => toast.classList.remove('show'), 5000);
        });
    });

  /* ──────────────────────────────────────────
     SCROLL ANIMATION — staggered fade-up
     for contact items and stat cards
  ────────────────────────────────────────── */
  const animatedItems = document.querySelectorAll(".ht-contact-item, .ht-stat");

  if ("IntersectionObserver" in window) {
    const observer = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry, index) {
          if (entry.isIntersecting) {
            entry.target.style.animation =
              "fade-up 0.5s " + index * 0.08 + "s ease both";
            observer.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.1 },
    );

    animatedItems.forEach(function (el) {
      observer.observe(el);
    });
  }

  /* ──────────────────────────────────────────
     ACTIVE NAV LINK — highlight Contact Us
  ────────────────────────────────────────── */
  const navLinks = document.querySelectorAll(".nav-link");
  navLinks.forEach(function (link) {
    if (link.href && link.href.includes("contactus")) {
      link.classList.add("active");
    }
  });
});
