<?php session_start(); ?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Hassan Traders - Premium Plumbing & Sanitary Solutions</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  <!-- Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" />
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800;900&family=DM+Sans:wght@300;400;500;600&display=swap"
    rel="stylesheet" />

  <link rel="stylesheet" href="../Hassan Traders Sanitary Store/NavBar/navbar.css" />
  <link rel="stylesheet" href="style.css" />

</head>

<body>
  <!-- ══ NAVBAR ══ -->
  <?php include('NavBar/navbar.php'); ?>


  <!-- ══ HERO CAROUSEL ══ -->
  <div
    id="heroCarousel"
    class="carousel slide carousel-fade hm-carousel"
    data-bs-ride="carousel">
    <div class="carousel-indicators hm-indicators">
      <button
        type="button"
        data-bs-target="#heroCarousel"
        data-bs-slide-to="0"
        class="active"></button>
      <button
        type="button"
        data-bs-target="#heroCarousel"
        data-bs-slide-to="1"></button>
      <button
        type="button"
        data-bs-target="#heroCarousel"
        data-bs-slide-to="2"></button>
      <button
        type="button"
        data-bs-target="#heroCarousel"
        data-bs-slide-to="3"></button>
    </div>

    <div class="carousel-inner">
      <!-- Slide 1 -->
      <div class="carousel-item active">
        <div class="hm-slide-wrap">
          <div class="hm-slide-img-col">
            <div class="hm-img-frame">
              <img
                src="../Hassan Traders Sanitary Store/Images/PPR-C.jpg"
                alt="PPR-C Pipes"
                class="hm-hero-img" />
              <div class="hm-img-overlay"></div>
            </div>
          </div>
          <div class="hm-slide-content-col">
            <div class="hm-slide-eyebrow">
              <span></span> Premium Collection
            </div>
            <h1 class="hm-slide-title">Premium <em>PPR-C Pipes</em></h1>
            <p class="hm-slide-desc">
              High-quality hot &amp; cold water plumbing solutions engineered
              for durability, safety, and long-lasting performance.
            </p>
            <div class="hm-slide-actions">
              <a href="../../Hassan Traders Sanitary Store/Products/Products.php" class="hm-btn-primary">Explore PPR-C Range <i class="bi bi-arrow-right ms-1"></i></a>
              <a href="../../Hassan Traders Sanitary Store/Contact us/contactus.php" class="hm-btn-ghost">Get a Quote</a>
            </div>
            <div class="hm-slide-tags">
              <span><i class="bi bi-check-circle-fill"></i> Hot &amp; Cold
                Water</span>
              <span><i class="bi bi-check-circle-fill"></i> High Durability</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Slide 2 -->
      <div class="carousel-item">
        <div class="hm-slide-wrap">
          <div class="hm-slide-img-col">
            <div class="hm-img-frame">
              <img
                src="../Hassan Traders Sanitary Store/Images/U-pvc.jpg"
                alt="U-PVC Pipes"
                class="hm-hero-img" />
              <div class="hm-img-overlay"></div>
            </div>
          </div>
          <div class="hm-slide-content-col">
            <div class="hm-slide-eyebrow"><span></span> Heavy Duty</div>
            <h1 class="hm-slide-title">Heavy Duty <em>U-PVC Pipes</em></h1>
            <p class="hm-slide-desc">
              Strong, leak-proof drainage and sewerage systems trusted by
              builders and contractors across Sargodha.
            </p>
            <div class="hm-slide-actions">
              <a href="../../Hassan Traders Sanitary Store/Products/Products.php" class="hm-btn-primary">View U-PVC Collection <i class="bi bi-arrow-right ms-1"></i></a>
              <a href="../../Hassan Traders Sanitary Store/Contact us/contactus.php" class="hm-btn-ghost">Get a Quote</a>
            </div>
            <div class="hm-slide-tags">
              <span><i class="bi bi-check-circle-fill"></i> Leak-Proof</span>
              <span><i class="bi bi-check-circle-fill"></i> Contractor
                Trusted</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Slide 3 -->
      <div class="carousel-item">
        <div class="hm-slide-wrap">
          <div class="hm-slide-img-col">
            <div class="hm-img-frame">
              <img
                src="../Hassan Traders Sanitary Store/Images/WaterTank.jpg"
                alt="Water Tanks"
                class="hm-hero-img" />
              <div class="hm-img-overlay"></div>
            </div>
          </div>
          <div class="hm-slide-content-col">
            <div class="hm-slide-eyebrow"><span></span> Large Capacity</div>
            <h1 class="hm-slide-title">
              Large Capacity <em>Water Tanks</em>
            </h1>
            <p class="hm-slide-desc">
              Food-grade, multi-layer storage tanks built for homes,
              apartments, and industrial use.
            </p>
            <div class="hm-slide-actions">
              <a href="../../Hassan Traders Sanitary Store/Products/Products.php" class="hm-btn-primary">Browse Water Tanks <i class="bi bi-arrow-right ms-1"></i></a>
              <a href="../../Hassan Traders Sanitary Store/Contact us/contactus.php" class="hm-btn-ghost">Get a Quote</a>
            </div>
            <div class="hm-slide-tags">
              <span><i class="bi bi-check-circle-fill"></i> Food Grade</span>
              <span><i class="bi bi-check-circle-fill"></i> Multi-Layer</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Slide 4 -->
      <div class="carousel-item">
        <div class="hm-slide-wrap">
          <div class="hm-slide-img-col">
            <div class="hm-img-frame">
              <img
                src="../Hassan Traders Sanitary Store/Images/ElectricUpvc.jpg"
                alt="Conduit Pipes"
                class="hm-hero-img" />
              <div class="hm-img-overlay"></div>
            </div>
          </div>
          <div class="hm-slide-content-col">
            <div class="hm-slide-eyebrow"><span></span> Electrical Grade</div>
            <h1 class="hm-slide-title">Electrical <em>Conduit Pipes</em></h1>
            <p class="hm-slide-desc">
              High-quality UPVC conduit pipes for safe, organized, and
              protected electrical wiring in modern buildings.
            </p>
            <div class="hm-slide-actions">
              <a href="../../Hassan Traders Sanitary Store/Products/Products.php" class="hm-btn-primary">See Electrical Range <i class="bi bi-arrow-right ms-1"></i></a>
              <a href="../../Hassan Traders Sanitary Store/Contact us/contactus.php" class="hm-btn-ghost">Get a Quote</a>
            </div>
            <div class="hm-slide-tags">
              <span><i class="bi bi-check-circle-fill"></i> Fire Resistant</span>
              <span><i class="bi bi-check-circle-fill"></i> Safe Wiring</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Arrow controls -->
    <button
      class="hm-carousel-btn hm-prev"
      type="button"
      data-bs-target="#heroCarousel"
      data-bs-slide="prev">
      <i class="bi bi-chevron-left"></i>
    </button>
    <button
      class="hm-carousel-btn hm-next"
      type="button"
      data-bs-target="#heroCarousel"
      data-bs-slide="next">
      <i class="bi bi-chevron-right"></i>
    </button>
  </div>

  <!-- ══ QUICK STATS STRIP ══ -->
  <div class="hm-stats-strip">
    <div class="container">
      <div class="hm-stats-grid">
        <div class="hm-stat" data-animate>
          <i class="bi bi-people-fill hm-stat-icon"></i>
          <div class="hm-stat-num" data-target="5000">0<span>+</span></div>
          <div class="hm-stat-label">Happy Customers</div>
        </div>
        <div class="hm-stat-divider"></div>
        <div class="hm-stat" data-animate>
          <i class="bi bi-box-seam-fill hm-stat-icon"></i>
          <div class="hm-stat-num" data-target="200">0<span>+</span></div>
          <div class="hm-stat-label">Products Available</div>
        </div>
        <div class="hm-stat-divider"></div>
        <div class="hm-stat" data-animate>
          <i class="bi bi-award-fill hm-stat-icon"></i>
          <div class="hm-stat-num" data-target="10">0<span>+</span></div>
          <div class="hm-stat-label">Years Experience</div>
        </div>
        <div class="hm-stat-divider"></div>
        <div class="hm-stat" data-animate>
          <i class="bi bi-patch-check-fill hm-stat-icon"></i>
          <div class="hm-stat-num" data-target="15">0<span>+</span></div>
          <div class="hm-stat-label">Trusted Brands</div>
        </div>
      </div>
    </div>
  </div>

  <!-- ══ PRODUCT CATEGORIES ══ -->
  <section class="hm-categories-section">
    <div class="container">
      <div class="hm-section-header">
        <div class="hm-eyebrow">What We Offer</div>
        <h2 class="hm-section-title"><em>Our</em> Categories</h2>
        <p class="hm-section-sub">
          Browse our wide range of premium sanitary and plumbing products
        </p>
      </div>

      <div class="hm-cat-grid">
        <a href="../../Hassan Traders Sanitary Store/Products/Products.php" class="hm-cat-card" data-animate>
          <div class="hm-cat-img-wrap">
            <img src="../Hassan Traders Sanitary Store/Images/PPR-C.jpg" alt="PPR-C Pipes" />
            <div class="hm-cat-overlay">
              <i class="bi bi-arrow-right-circle-fill"></i>
            </div>
          </div>
          <div class="hm-cat-body">
            <h5>PPR-C Pipes</h5>
            <p>Hot &amp; cold water systems</p>
          </div>
        </a>

        <a href="../../Hassan Traders Sanitary Store/Products/Products.php" class="hm-cat-card" data-animate>
          <div class="hm-cat-img-wrap">
            <img src="../Hassan Traders Sanitary Store/Images/U-pvc.jpg" alt="UPVC Pipes" />
            <div class="hm-cat-overlay">
              <i class="bi bi-arrow-right-circle-fill"></i>
            </div>
          </div>
          <div class="hm-cat-body">
            <h5>UPVC Pipes</h5>
            <p>Drainage &amp; sewerage</p>
          </div>
        </a>

        <a href="../../Hassan Traders Sanitary Store/Products/Products.php" class="hm-cat-card" data-animate>
          <div class="hm-cat-img-wrap">
            <img src="../Hassan Traders Sanitary Store/Images/WaterTank.jpg" alt="Water Tanks" />
            <div class="hm-cat-overlay">
              <i class="bi bi-arrow-right-circle-fill"></i>
            </div>
          </div>
          <div class="hm-cat-body">
            <h5>Water Tanks</h5>
            <p>Food-grade storage</p>
          </div>
        </a>

        <a href="../../Hassan Traders Sanitary Store/Products/Products.php" class="hm-cat-card" data-animate>
          <div class="hm-cat-img-wrap">
            <img src="../Hassan Traders Sanitary Store/Images/ElectricUpvc.jpg" alt="Electric Pipes" />
            <div class="hm-cat-overlay">
              <i class="bi bi-arrow-right-circle-fill"></i>
            </div>
          </div>
          <div class="hm-cat-body">
            <h5>Electric Pipes</h5>
            <p>Conduit &amp; wiring solutions</p>
          </div>
        </a>
      </div>
    </div>
  </section>

  <!-- ══ BEST SELLING PRODUCTS ══ -->
  <section class="hm-bestsellers-section">
    <div class="container">
      <div class="hm-section-header">
        <div class="hm-eyebrow">Top Picks</div>
        <h2 class="hm-section-title">Best <em>Selling Products</em></h2>
        <p class="hm-section-sub">
          Our most popular products chosen by builders and homeowners across
          Sargodha
        </p>
      </div>

      <div class="hm-products-grid">
        <!-- Card 1 -->
        <div class="hm-product-card" data-animate>
          <div class="hm-product-badge">Best Seller</div>
          <div class="hm-product-img-wrap">
            <img src="../Hassan Traders Sanitary Store/Images/PPR-C.jpg" alt="PPR-C Pipes" />
            <div class="hm-product-img-overlay">
              <a href="../../Hassan Traders Sanitary Store/Products/Products.php" class="hm-quick-view">Quick View <i class="bi bi-eye ms-1"></i></a>
            </div>
          </div>
          <div class="hm-product-body">
            <h5>PPR-C (Pipes &amp; Fitting)</h5>
            <p>
              High-durability plumbing solutions for hot &amp; cold water.
            </p>
            <a href="../../Hassan Traders Sanitary Store/Products/Products.php" class="hm-product-btn">View Details <i class="bi bi-arrow-right ms-1"></i></a>
          </div>
        </div>

        <!-- Card 2 -->
        <div class="hm-product-card" data-animate>
          <div class="hm-product-img-wrap">
            <img src="../Hassan Traders Sanitary Store/Images/U-pvc.jpg" alt="U-PVC Pipes" />
            <div class="hm-product-img-overlay">
              <a href="../../Hassan Traders Sanitary Store/Products/Products.php" class="hm-quick-view">Quick View <i class="bi bi-eye ms-1"></i></a>
            </div>
          </div>
          <div class="hm-product-body">
            <h5>U-PVC (Pipes &amp; Fitting)</h5>
            <p>Leak-proof drainage and sewerage systems.</p>
            <a href="../../Hassan Traders Sanitary Store/Products/Products.php" class="hm-product-btn">View Details <i class="bi bi-arrow-right ms-1"></i></a>
          </div>
        </div>

        <!-- Card 3 -->
        <div class="hm-product-card" data-animate>
          <div class="hm-product-img-wrap">
            <img src="../Hassan Traders Sanitary Store/Images/WaterTank.jpg" alt="Water Tank" />
            <div class="hm-product-img-overlay">
              <a href="../../Hassan Traders Sanitary Store/Products/Products.php" class="hm-quick-view">Quick View <i class="bi bi-eye ms-1"></i></a>
            </div>
          </div>
          <div class="hm-product-body">
            <h5>Water Tank</h5>
            <p>Food-grade, multi-layer storage tanks.</p>
            <a href="../../Hassan Traders Sanitary Store/Products/Products.php" class="hm-product-btn">View Details <i class="bi bi-arrow-right ms-1"></i></a>
          </div>
        </div>
      </div>

      <div class="text-center mt-5">
        <a href="../../Hassan Traders Sanitary Store/Products/Products.php" class="hm-view-all">View All Products <i class="bi bi-grid-3x3-gap-fill ms-2"></i></a>
      </div>
    </div>
  </section>

  <!-- ══ WHY CHOOSE US ══ -->
  <section class="hm-why-section" id="choose">
    <div class="container">
      <div class="hm-section-header">
        <div class="hm-eyebrow">Our Promise</div>
        <h2 class="hm-section-title">Why Choose <em>Hassan Traders</em></h2>
        <p class="hm-section-sub">
          We are committed to delivering quality, value, and trust with every
          product we sell.
        </p>
      </div>

      <div class="hm-why-grid">
        <div class="hm-why-card" data-animate>
          <div class="hm-why-icon"><i class="fa-solid fa-award"></i></div>
          <h4>Premium Quality</h4>
          <p>
            We supply only high-quality, certified sanitary and plumbing
            products from trusted manufacturers.
          </p>
        </div>
        <div class="hm-why-card" data-animate>
          <div class="hm-why-icon"><i class="fa-solid fa-tag"></i></div>
          <h4>Best Prices</h4>
          <p>
            Competitive market prices for contractors, builders, and
            homeowners — maximum value guaranteed.
          </p>
        </div>
        <div class="hm-why-card" data-animate>
          <div class="hm-why-icon">
            <i class="fa-solid fa-shield-halved"></i>
          </div>
          <h4>Trusted Shop</h4>
          <p>
            Serving Sargodha since 2016 with reliable, honest service and
            thousands of satisfied customers.
          </p>
        </div>
        <div class="hm-why-card" data-animate>
          <div class="hm-why-icon"><i class="fa-solid fa-truck"></i></div>
          <h4>Fast Delivery</h4>
          <p>
            Quick and efficient delivery service tailored to builders and
            contractors across the region.
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- ══ BRANDS ══ -->
  <section class="hm-brands-section">
    <div class="container">
      <div class="hm-section-header">
        <div class="hm-eyebrow">Authorized Partners</div>
        <h2 class="hm-section-title"><em>Our</em> Brands</h2>
        <p class="hm-section-sub">
          We carry products from Pakistan's most trusted plumbing and sanitary
          brands
        </p>
      </div>

      <div class="hm-brands-grid">
        <div class="hm-brand-card" data-animate>
          <div class="hm-brand-icon"><i class="bi bi-hexagon-fill"></i></div>
          <h4>DURA <span>MAX</span></h4>
          <p>Pipes &amp; Fittings</p>
        </div>
        <div class="hm-brand-card" data-animate>
          <div class="hm-brand-icon"><i class="bi bi-hexagon-fill"></i></div>
          <h4><span>Adam Jee</span></h4>
          <p>Sanitary Solutions</p>
        </div>
        <div class="hm-brand-card" data-animate>
          <div class="hm-brand-icon"><i class="bi bi-hexagon-fill"></i></div>
          <h4>Best <span>Asia</span></h4>
          <p>Water Tanks</p>
        </div>
        <div class="hm-brand-card" data-animate>
          <div class="hm-brand-icon"><i class="bi bi-hexagon-fill"></i></div>
          <h4><span>Oreo</span></h4>
          <p>Premium Fittings</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ══ TESTIMONIALS ══ -->
  <section class="hm-reviews-section">
    <div class="container">
      <div class="hm-section-header">
        <div class="hm-eyebrow">What They Say</div>
        <h2 class="hm-section-title">Customer <em>Reviews</em></h2>
        <p class="hm-section-sub">
          Real feedback from builders and homeowners who trust Hassan Traders
        </p>
      </div>

      <div class="hm-reviews-grid">
        <div class="hm-review-card" data-animate>
          <div class="hm-review-stars">
            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
          </div>
          <p class="hm-review-text">
            "Best sanitary products in Sargodha. Great service and quality.
            We've been sourcing all our plumbing materials here for years."
          </p>
          <div class="hm-reviewer">
            <div class="hm-reviewer-avatar">AB</div>
            <div>
              <div class="hm-reviewer-name">Ali Builders</div>
              <div class="hm-reviewer-role">Construction Contractor</div>
            </div>
          </div>
        </div>

        <div class="hm-review-card" data-animate>
          <div class="hm-review-stars">
            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
          </div>
          <p class="hm-review-text">
            "Very reliable shop for all plumbing materials. Competitive prices
            and the staff is always knowledgeable and helpful."
          </p>
          <div class="hm-reviewer">
            <div class="hm-reviewer-avatar">AC</div>
            <div>
              <div class="hm-reviewer-name">Ahmad Contractor</div>
              <div class="hm-reviewer-role">Civil Engineer</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ══ CTA BANNER ══ -->
  <section class="hm-cta-section" id="contactusforplumbing">
    <div class="hm-cta-grid-bg"></div>
    <div class="container hm-cta-inner">
      <div class="hm-cta-content" data-animate>
        <div class="hm-eyebrow light">Get In Touch</div>
        <h2 class="hm-cta-title">Need Plumbing or<br />Sanitary Products?</h2>
        <p class="hm-cta-desc">
          Contact Hassan Traders today for the best deals, bulk orders, and
          expert guidance on every product we carry.
        </p>
        <div class="hm-cta-actions">
          <a href="../../Hassan Traders Sanitary Store/Contact us/contactus.php" class="hm-cta-btn-primary"><i class="bi bi-telephone-fill me-2"></i> Contact Us Now</a>
          <a href="../../Hassan Traders Sanitary Store/Products/Products.php" class="hm-cta-btn-ghost">Browse Products</a>
        </div>
      </div>
      <div class="hm-cta-info-card" data-animate>
        <div class="hm-cta-info-item">
          <div class="hm-cta-info-icon">
            <i class="bi bi-geo-alt-fill"></i>
          </div>
          <div>
            <div class="hm-cta-info-label">Visit Us</div>
            <div class="hm-cta-info-value">
              Banas Bazar, Fatima Jinnah Road, Sargodha
            </div>
          </div>
        </div>
        <div class="hm-cta-info-item">
          <div class="hm-cta-info-icon">
            <i class="bi bi-telephone-fill"></i>
          </div>
          <div>
            <div class="hm-cta-info-label">Call Us</div>
            <div class="hm-cta-info-value">+92 300 0687080</div>
          </div>
        </div>
        <div class="hm-cta-info-item">
          <div class="hm-cta-info-icon"><i class="bi bi-clock-fill"></i></div>
          <div>
            <div class="hm-cta-info-label">Hours</div>
            <div class="hm-cta-info-value">Mon–Sun 9AM–8PM · Fri Closed</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
     <?php include('Footer/footer.php'); ?>

  

  <!-- WHATSAPP BUTTON -->
  <a href="https://wa.me/923000687080" class="whatsapp-btn" target="_blank">
    <i class="bi bi-whatsapp"></i>
  </a>


  <script src="../Hassan Traders Sanitary Store/NavBar/navbar.js"></script>
  <script src="script.js"></script>
</body>

</html>