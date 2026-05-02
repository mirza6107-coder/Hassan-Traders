<?php session_start(); ?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>About Us - Hassan Traders</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet" />

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<link rel="stylesheet" href="../NavBar/navbar.css" />
<link rel="stylesheet" href="aboutus.css" />


<body>

  <!-- ══ NAVBAR ══ -->
  <?php include('../NavBar/navbar.php'); ?>


  <!-- ══ HERO ══ -->
  <section class="au-hero">
    <div class="au-hero-inner">
      <div class="au-hero-eyebrow"><span></span> Est. 2016 — Sargodha, Pakistan</div>
      <h1>Built on <em>Trust</em>,<br>Driven by <em>Quality</em></h1>
      <p>Your premier sanitary &amp; plumbing partner in Sargodha for over a decade</p>
      <div class="au-hero-cta-row">
        <a href="../Contact us/contactus.html" class="au-btn-primary">Get in Touch <i class="bi bi-arrow-right ms-1"></i></a>
        <a href="../Products/Products.php" class="au-btn-ghost">Browse Products</a>
      </div>
    </div>
    <!-- Floating badge -->
    <div class="au-hero-badge">
      <div class="au-badge-number">10<span>+</span></div>
      <div class="au-badge-label">Years of Excellence</div>
    </div>
  </section>

  <!-- ══ STATS STRIP ══ -->
  <div class="container au-stats-container">
    <div class="au-stats-grid">
      <div class="au-stat-item" data-animate>
        <div class="au-stat-icon"><i class="bi bi-people-fill"></i></div>
        <div class="au-stat-num">5000<span>+</span></div>
        <div class="au-stat-label">Happy Customers</div>
      </div>
      <div class="au-stat-item" data-animate>
        <div class="au-stat-icon"><i class="bi bi-box-seam-fill"></i></div>
        <div class="au-stat-num">200<span>+</span></div>
        <div class="au-stat-label">Products in Stock</div>
      </div>
      <div class="au-stat-item" data-animate>
        <div class="au-stat-icon"><i class="bi bi-award-fill"></i></div>
        <div class="au-stat-num">10<span>+</span></div>
        <div class="au-stat-label">Years in Business</div>
      </div>
      <div class="au-stat-item" data-animate>
        <div class="au-stat-icon"><i class="bi bi-patch-check-fill"></i></div>
        <div class="au-stat-num">15<span>+</span></div>
        <div class="au-stat-label">Trusted Brands</div>
      </div>
    </div>
  </div>

  <!-- ══ OUR STORY ══ -->
  <section class="au-story-section">
    <div class="container">
      <div class="au-story-grid">
        <div class="au-story-image-wrap" data-animate>
          <img src="../Images/dura1.jpg" alt="Hassan Traders Store" class="au-story-img" />
          <div class="au-story-img-badge">
            <i class="bi bi-shield-fill-check"></i>
            Trusted Since 2016
          </div>
        </div>
        <div class="au-story-content" data-animate>
          <div class="au-section-eyebrow">Our Story</div>
          <h2 class="au-section-title">A Legacy of <em>Plumbing Excellence</em> in Sargodha</h2>
          <p class="au-story-text">Hassan Traders is a trusted sanitary and plumbing store located in the heart of Sargodha, Pakistan. Since our establishment in 2016, we have been committed to providing high-quality plumbing materials and bathroom accessories to builders, contractors, and homeowners.</p>
          <p class="au-story-text">Our mission is simple: provide reliable products, honest pricing, and excellent customer service. We carefully select brands that ensure durability and long-lasting performance — because we believe every home deserves the best.</p>
          <div class="au-story-tags">
            <span class="au-tag"><i class="bi bi-check-circle-fill"></i> PPR-C Pipes</span>
            <span class="au-tag"><i class="bi bi-check-circle-fill"></i> U-PVC Fittings</span>
            <span class="au-tag"><i class="bi bi-check-circle-fill"></i> Bath Sets</span>
            <span class="au-tag"><i class="bi bi-check-circle-fill"></i> Water Tanks</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ══ WHY CHOOSE US ══ -->
  <section class="au-why-section">
    <div class="container">
      <div class="au-section-header text-center">
        <div class="au-section-eyebrow">Why Us</div>
        <h2 class="au-section-title">Why Choose <em>Hassan Traders</em></h2>
        <p class="au-section-sub">We don't just sell products — we deliver confidence, quality, and care with every order.</p>
      </div>

      <div class="au-why-grid">
        <div class="au-why-card" data-animate>
          <div class="au-why-icon-wrap">
            <i class="fa-solid fa-award"></i>
          </div>
          <h4>Premium Quality</h4>
          <p>We stock only trusted, certified brands that meet the highest durability standards for every application.</p>
        </div>

        <div class="au-why-card" data-animate>
          <div class="au-why-icon-wrap">
            <i class="fa-solid fa-truck"></i>
          </div>
          <h4>Fast Delivery</h4>
          <p>Quick and reliable delivery service tailored for builders and contractors across Sargodha and nearby areas.</p>
        </div>

        <div class="au-why-card" data-animate>
          <div class="au-why-icon-wrap">
            <i class="fa-solid fa-users"></i>
          </div>
          <h4>Trusted Service</h4>
          <p>Hundreds of satisfied customers recommend us. Our reputation is built on consistent, honest service.</p>
        </div>

        <div class="au-why-card" data-animate>
          <div class="au-why-icon-wrap">
            <i class="fa-solid fa-tag"></i>
          </div>
          <h4>Best Prices</h4>
          <p>Competitive market pricing on all products. We ensure you get maximum value without compromise.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ══ MISSION / VALUES STRIP ══ -->
  <section class="au-values-section">
    <div class="container">
      <div class="au-values-grid">
        <div class="au-value-block" data-animate>
          <div class="au-value-number">01</div>
          <h5>Our Mission</h5>
          <p>To provide Sargodha's builders and homeowners with premium-grade plumbing products at fair prices, backed by expert guidance.</p>
        </div>
        <div class="au-value-divider"></div>
        <div class="au-value-block" data-animate>
          <div class="au-value-number">02</div>
          <h5>Our Vision</h5>
          <p>To become the most trusted sanitary and plumbing destination in Punjab — known for quality, integrity, and innovation.</p>
        </div>
        <div class="au-value-divider"></div>
        <div class="au-value-block" data-animate>
          <div class="au-value-number">03</div>
          <h5>Our Values</h5>
          <p>Honesty, reliability, and customer-first thinking guide every decision we make — from product sourcing to after-sale support.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ══ CTA BANNER ══ -->
  <section class="au-cta-section">
    <div class="au-cta-grid-bg"></div>
    <div class="container au-cta-inner">
      <div class="au-cta-text" data-animate>
        <div class="au-section-eyebrow light">Work With Us</div>
        <h2 class="au-cta-title">Trusted by Builders &amp;<br>Homeowners Across Sargodha</h2>
        <p class="au-cta-desc">Whether you're constructing a home, renovating a bathroom, or need bulk supply — we're your go-to partner. Reach out today and let's build something great together.</p>
        <a href="../Contact us/contactus.html" class="au-btn-cta">
          <i class="bi bi-telephone-fill me-2"></i> Contact Us Now
        </a>
      </div>
      <div class="au-cta-card" data-animate>
        <div class="au-cta-card-item">
          <i class="bi bi-geo-alt-fill"></i>
          <span>Banas Bazar, Fatima Jinnah Road, Sargodha</span>
        </div>
        <div class="au-cta-card-item">
          <i class="bi bi-telephone-fill"></i>
          <span>+92 300 0687080</span>
        </div>
        <div class="au-cta-card-item">
          <i class="bi bi-clock-fill"></i>
          <span>Mon–Sun: 9:00 AM – 8:00 PM (Fri Closed)</span>
        </div>
        <div class="au-cta-card-item">
          <i class="bi bi-envelope-fill"></i>
          <span>swsaaretheweathers@gmail.com</span>
        </div>
      </div>
    </div>
  </section>

  <!-- ══ FOOTER ══ -->
  <footer class="footer-section text-white pt-5 mt-5">
    <div class="container">
      <div class="row gy-4">
        <div class="col-lg-4 col-md-6">
          <h4 class="fw-bold mb-4 text-danger">HASSAN TRADERS</h4>
          <p class="text-secondary">Premium plumbing and sanitary solutions in Sargodha. Quality durability you can trust for every build.</p>
          <div class="d-flex gap-3 mt-4">
            <a href="#" class="social-icon"><i class="bi bi-facebook"></i></a>
            <a href="#" class="social-icon"><i class="bi bi-instagram"></i></a>
            <a href="#" class="social-icon"><i class="bi bi-whatsapp"></i></a>
          </div>
        </div>
        <div class="col-lg-2 col-md-6">
          <h5 class="fw-bold mb-4">Explore</h5>
          <ul class="list-unstyled footer-links">
            <li><a href="../Home/home.html">Home</a></li>
            <li><a href="../About us/aboutus.html">About Us</a></li>
            <li><a href="../Products/Products.html">Our Products</a></li>
            <li><a href="../Contact us/contactus.html">Support</a></li>
          </ul>
        </div>
        <div class="col-lg-2 col-md-6">
          <h5 class="fw-bold mb-4">Products</h5>
          <ul class="list-unstyled footer-links">
            <li><a href="../Products/Products.html">PPR-C Pipes</a></li>
            <li><a href="../Products/Products.html">U-PVC Fittings</a></li>
            <li><a href="../Products/Products.html">Water Tanks</a></li>
            <li><a href="../Products/Products.html">Bath Sets</a></li>
          </ul>
        </div>
        <div class="col-lg-4 col-md-6">
          <h5 class="fw-bold mb-4">Contact Info</h5>
          <ul class="list-unstyled contact-list">
            <li><i class="bi bi-geo-alt-fill text-danger me-2"></i> Sargodha, Punjab, Pakistan</li>
            <li><i class="bi bi-telephone-fill text-danger me-2"></i> +92 300 0687080</li>
            <li><i class="bi bi-envelope-fill text-danger me-2"></i> swsaaretheweathers@gmail.com</li>
          </ul>
        </div>
      </div>
      <hr class="mt-5 mb-4 border-secondary" />
      <div class="row align-items-center pb-4">
        <div class="col-md-6 text-center text-md-start">
          <p class="mb-0 text-secondary small">© 2026 Hassan Traders. All Rights Reserved.</p>
        </div>
        <div class="col-md-6 text-center text-md-end mt-3 mt-md-0">
          <p class="mb-0 text-secondary small">Designed by <span class="text-white">Umar Jalal</span></p>
        </div>
      </div>
    </div>
  </footer>

  <!-- WHATSAPP BUTTON -->
  <a href="https://wa.me/923000687080" class="whatsapp-btn" target="_blank">
    <i class="bi bi-whatsapp"></i>
  </a>

  <script src="../NavBar/navbar.js"></script>
  <script src="aboutus.js"></script>
</body>

</html>