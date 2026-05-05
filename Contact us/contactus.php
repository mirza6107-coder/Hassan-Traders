<?php session_start(); ?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Contact Us - Hassan Traders</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" />
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=DM+Sans:wght@300;400;500;600&display=swap"
    rel="stylesheet" />

  <link rel="stylesheet" href="../NavBar/navbar.css" />
  <link rel="stylesheet" href="contactus.css" />
</head>

<body>
  <!-- ══ NAVBAR ══ -->
  <?php include('../NavBar/navbar.php'); ?>


  <!-- ══ HERO ══ -->
  <section class="ht-hero">
    <div class="ht-hero-inner">
      <div class="ht-hero-eyebrow">
        <span></span> Hassan Traders — Sargodha
      </div>
      <h1>Let's <em>Connect</em> &amp;<br />Build Together</h1>
      <p>
        Premium plumbing solutions — reach out for quotes, support, or
        enquiries
      </p>
    </div>
  </section>

  <!-- ══ MAIN CONTENT ══ -->
  <div class="container ht-main-container">
    <!-- Stat Strip -->
    <div class="ht-stats">
      <div class="ht-stats-inner">
        <div class="ht-stat">
          <div class="ht-stat-number">15+</div>
          <div class="ht-stat-label">Years in Business</div>
        </div>
        <div class="ht-stat">
          <div class="ht-stat-number">5000+</div>
          <div class="ht-stat-label">Happy Customers</div>
        </div>
        <div class="ht-stat">
          <div class="ht-stat-number">24h</div>
          <div class="ht-stat-label">Response Time</div>
        </div>
      </div>
    </div>

    <!-- Contact Card -->
    <div class="ht-contact-wrap">
      <!-- Left: Info Pane -->
      <div class="ht-info-pane">
        <div class="ht-info-badge">Contact Information</div>
        <h2 class="ht-info-title">We're always<br />happy to help</h2>
        <p class="ht-info-desc">
          Visit our shop, drop us an email, or call — our team is ready to
          assist with any plumbing and sanitary enquiry.
        </p>

        <div class="ht-contact-item">
          <div class="ht-contact-icon">
            <i class="bi bi-geo-alt-fill"></i>
          </div>
          <div>
            <div class="ht-contact-label">Our Shop</div>
            <div class="ht-contact-value">
              Banas Bazar, Fatima Jinnah Road<br />Sargodha, Pakistan
            </div>
          </div>
        </div>

        <div class="ht-contact-item">
          <div class="ht-contact-icon">
            <i class="bi bi-telephone-fill"></i>
          </div>
          <div>
            <div class="ht-contact-label">Phone</div>
            <div class="ht-contact-value">+92 300 0687080</div>
          </div>
        </div>

        <div class="ht-contact-item">
          <div class="ht-contact-icon">
            <i class="bi bi-envelope-fill"></i>
          </div>
          <div>
            <div class="ht-contact-label">Email</div>
            <div class="ht-contact-value">swsaaretheweathers@gmail.com</div>
          </div>
        </div>

        <div class="ht-divider"></div>

        <div class="ht-contact-label" style="margin-bottom: 14px">
          Business Hours
        </div>
        <div class="ht-hours-row">
          <span class="ht-hours-day">Monday – Thursday</span>
          <span class="ht-hours-time">9:00 AM – 8:00 PM</span>
        </div>
        <div class="ht-hours-row">
          <span class="ht-hours-day">Friday</span>
          <span class="ht-hours-closed">Closed</span>
        </div>
        <div class="ht-hours-row">
          <span class="ht-hours-day">Saturday – Sunday</span>
          <span class="ht-hours-time">9:00 AM – 8:00 PM</span>
        </div>
      </div>

      <!-- Right: Form Pane -->
      <div class="ht-form-pane">
        <h3 class="ht-form-heading">Send a Message</h3>
        <p class="ht-form-sub">
          Fill in the form below and we'll get back to you within 24 hours.
        </p>

        <form
          id="contactForm"
          action="contact_process.php"
          method="POST"
          novalidate>
          <div class="ht-row">
            <div class="ht-field">
              <label class="ht-label" for="fname">Full Name</label>
              <input
                class="ht-input"
                type="text"
                id="fname"
                name="fname"
                placeholder="Your name"
                required />
            </div>
            <div class="ht-field">
              <label class="ht-label" for="company">Company</label>
              <input
                class="ht-input"
                type="text"
                id="company"
                name="company"
                placeholder="Optional" />
            </div>
          </div>

          <div class="ht-row">
            <div class="ht-field">
              <label class="ht-label" for="phone">Phone</label>
              <input
                class="ht-input"
                type="tel"
                id="phone"
                name="phone"
                placeholder="+92 xxx xxxxxxx" />
            </div>
            <div class="ht-field">
              <label class="ht-label" for="email">Email</label>
              <input
                class="ht-input"
                type="email"
                id="email"
                name="email"
                placeholder="you@example.com"
                required />
            </div>
          </div>

          <div class="ht-field">
            <label class="ht-label" for="subject">Subject</label>
            <input
              class="ht-input"
              type="text"
              id="subject"
              name="subject"
              placeholder="How can we help?" />
          </div>

          <div class="ht-field">
            <label class="ht-label" for="message">Message</label>
            <textarea
              class="ht-textarea"
              id="message"
              name="message"
              placeholder="Tell us about your requirement…"
              required></textarea>
          </div>

          <button type="submit" id="submitBtn" class="ht-submit">
            <i class="bi bi-send-fill me-2"></i> Send Message
          </button>

          <div class="ht-toast" id="successToast">
            <i class="bi bi-check-circle-fill fs-5"></i>
            <span id="toastText">Message sent! We'll get back to you shortly.</span>
          </div>
        </form>
      </div>
    </div>

    <!-- Map -->
    <div class="ht-map-section mt-5">
      <div class="ht-map-label">
        <i class="bi bi-pin-map-fill"></i>
        Hassan Traders — Banas Bazar, Sargodha
      </div>
      <iframe
        src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d211.27760454133667!2d72.66931152114927!3d32.08434670932148!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sen!2s!4v1773478236342!5m2!1sen!2s"
        allowfullscreen=""
        loading="lazy"></iframe>
    </div>

    <div class="ht-scroll-hint mt-5">Scroll up to explore</div>
  </div>

  <!-- ══ FOOTER ══ -->
  <?php include('../Footer/footer.php'); ?>

  <!-- WHATSAPP BUTTON -->
  <a href="https://wa.me/923000687080" class="whatsapp-btn" target="_blank">
    <i class="bi bi-whatsapp"></i>
  </a>

  <script src="../NavBar/navbar.js"></script>
  <script src="contactus.js"></script>
</body>

</html>