<?php session_start(); ?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Hassan Traders - Premium Plumbing & Sanitary Solutions</title>

    <link rel="stylesheet" href="Style.css" />
    <link rel="stylesheet" href="../NavBar/navbar.css" />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800;900&family=DM+Sans:wght@300;400;500;600&display=swap"
      rel="stylesheet"
    />
  </head>

  <body>
    <!-- ══ NAVBAR ══ -->
    <nav
      class="navbar navbar-expand-lg navbar-light bg-white fixed-top premium-navbar"
    >
      <div class="container">
        <a
          class="navbar-brand d-flex align-items-center"
          id="logo"
          href="../Home/home.php"
        >
          <img
            src="../Images/Hassan Traders logo 2.png"
            alt="Hassan Traders"
            class="logo-img"
          />
        </a>
        <button
          class="navbar-toggler border-0"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navbarSupportedContent"
          aria-controls="navbarSupportedContent"
          aria-expanded="false"
          aria-label="Toggle navigation"
        >
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav mx-auto align-items-center">
            <li class="nav-item">
              <a class="nav-link" href="../Home/home.php"
                ><i class="bi bi-house-door-fill me-2"></i>Home</a
              >
            </li>
            <li class="nav-item">
              <a class="nav-link" href="../Products/Products.php"
                ><i class="bi bi-grid-3x3-gap-fill me-2"></i>Products</a
              >
            </li>
            <li class="nav-item">
              <a class="nav-link" href="../About us/aboutus.php"
                ><i class="bi bi-info-circle-fill me-2"></i>About Us</a
              >
            </li>
            <li class="nav-item">
              <a class="nav-link" href="../Contact us/contactus.php"
                ><i class="bi bi-telephone-fill me-2"></i>Contact Us</a
              >
            </li>
          </ul>
          <div class="d-flex justify-content-center justify-content-lg-end mt-3 mt-lg-0">
          <?php if (isset($_SESSION['user_name'])): ?>
            <div class="dropdown">
              <button class="btn btn-premium px-4 rounded-pill dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle me-2"></i>
                <?php echo htmlspecialchars($_SESSION['user_name']); ?>
              </button>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="../login and signup/logout.php">Logout</a></li>
              </ul>
            </div>
          <?php else: ?>
            <a href="../login and signup/login.php" class="text-decoration-none">
              <button class="btn btn-premium px-5 rounded-pill d-flex align-items-center gap-2">
                <i class="bi bi-person-circle fs-5"></i>
                <span class="fw-semibold">LOGIN</span>
              </button>
            </a>
          <?php endif; ?>
        </div>
          
        </div>
      </div>
    </nav>

    <!-- ══ HERO CAROUSEL ══ -->
    <div
      id="heroCarousel"
      class="carousel slide carousel-fade hm-carousel"
      data-bs-ride="carousel"
    >
      <div class="carousel-indicators hm-indicators">
        <button
          type="button"
          data-bs-target="#heroCarousel"
          data-bs-slide-to="0"
          class="active"
        ></button>
        <button
          type="button"
          data-bs-target="#heroCarousel"
          data-bs-slide-to="1"
        ></button>
        <button
          type="button"
          data-bs-target="#heroCarousel"
          data-bs-slide-to="2"
        ></button>
        <button
          type="button"
          data-bs-target="#heroCarousel"
          data-bs-slide-to="3"
        ></button>
      </div>

      <div class="carousel-inner">
        <!-- Slide 1 -->
        <div class="carousel-item active">
          <div class="hm-slide-wrap">
            <div class="hm-slide-img-col">
              <div class="hm-img-frame">
                <img
                  src="../Images/PPR-C.jpg"
                  alt="PPR-C Pipes"
                  class="hm-hero-img"
                />
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
                <a href="../Products/Products.php" class="hm-btn-primary"
                  >Explore PPR-C Range <i class="bi bi-arrow-right ms-1"></i
                ></a>
                <a href="../Contact us/contactus.html" class="hm-btn-ghost"
                  >Get a Quote</a
                >
              </div>
              <div class="hm-slide-tags">
                <span
                  ><i class="bi bi-check-circle-fill"></i> Hot &amp; Cold
                  Water</span
                >
                <span
                  ><i class="bi bi-check-circle-fill"></i> High Durability</span
                >
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
                  src="../Images/U-pvc.jpg"
                  alt="U-PVC Pipes"
                  class="hm-hero-img"
                />
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
                <a href="../Products/Products.php" class="hm-btn-primary"
                  >View U-PVC Collection <i class="bi bi-arrow-right ms-1"></i
                ></a>
                <a href="../Contact us/contactus.html" class="hm-btn-ghost"
                  >Get a Quote</a
                >
              </div>
              <div class="hm-slide-tags">
                <span><i class="bi bi-check-circle-fill"></i> Leak-Proof</span>
                <span
                  ><i class="bi bi-check-circle-fill"></i> Contractor
                  Trusted</span
                >
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
                  src="../Images/WaterTank.jpg"
                  alt="Water Tanks"
                  class="hm-hero-img"
                />
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
                <a href="../Products/Products.php" class="hm-btn-primary"
                  >Browse Water Tanks <i class="bi bi-arrow-right ms-1"></i
                ></a>
                <a href="../Contact us/contactus.html" class="hm-btn-ghost"
                  >Get a Quote</a
                >
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
                  src="../Images/ElectricUpvc.jpg"
                  alt="Conduit Pipes"
                  class="hm-hero-img"
                />
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
                <a href="../Products/Products.php" class="hm-btn-primary"
                  >See Electrical Range <i class="bi bi-arrow-right ms-1"></i
                ></a>
                <a href="../Contact us/contactus.html" class="hm-btn-ghost"
                  >Get a Quote</a
                >
              </div>
              <div class="hm-slide-tags">
                <span
                  ><i class="bi bi-check-circle-fill"></i> Fire Resistant</span
                >
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
        data-bs-slide="prev"
      >
        <i class="bi bi-chevron-left"></i>
      </button>
      <button
        class="hm-carousel-btn hm-next"
        type="button"
        data-bs-target="#heroCarousel"
        data-bs-slide="next"
      >
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
          <a href="../Products/Products.php" class="hm-cat-card" data-animate>
            <div class="hm-cat-img-wrap">
              <img src="../Images/PPR-C.jpg" alt="PPR-C Pipes" />
              <div class="hm-cat-overlay">
                <i class="bi bi-arrow-right-circle-fill"></i>
              </div>
            </div>
            <div class="hm-cat-body">
              <h5>PPR-C Pipes</h5>
              <p>Hot &amp; cold water systems</p>
            </div>
          </a>

          <a href="../Products/Products.php" class="hm-cat-card" data-animate>
            <div class="hm-cat-img-wrap">
              <img src="../Images/U-pvc.jpg" alt="UPVC Pipes" />
              <div class="hm-cat-overlay">
                <i class="bi bi-arrow-right-circle-fill"></i>
              </div>
            </div>
            <div class="hm-cat-body">
              <h5>UPVC Pipes</h5>
              <p>Drainage &amp; sewerage</p>
            </div>
          </a>

          <a href="../Products/Products.php" class="hm-cat-card" data-animate>
            <div class="hm-cat-img-wrap">
              <img src="../Images/WaterTank.jpg" alt="Water Tanks" />
              <div class="hm-cat-overlay">
                <i class="bi bi-arrow-right-circle-fill"></i>
              </div>
            </div>
            <div class="hm-cat-body">
              <h5>Water Tanks</h5>
              <p>Food-grade storage</p>
            </div>
          </a>

          <a href="../Products/Products.php" class="hm-cat-card" data-animate>
            <div class="hm-cat-img-wrap">
              <img src="../Images/ElectricUpvc.jpg" alt="Electric Pipes" />
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
              <img src="../Images/PPR-C.jpg" alt="PPR-C Pipes" />
              <div class="hm-product-img-overlay">
                <a href="../Products/Products.php" class="hm-quick-view"
                  >Quick View <i class="bi bi-eye ms-1"></i
                ></a>
              </div>
            </div>
            <div class="hm-product-body">
              <h5>PPR-C (Pipes &amp; Fitting)</h5>
              <p>
                High-durability plumbing solutions for hot &amp; cold water.
              </p>
              <a href="../Products/Products.php" class="hm-product-btn"
                >View Details <i class="bi bi-arrow-right ms-1"></i
              ></a>
            </div>
          </div>

          <!-- Card 2 -->
          <div class="hm-product-card" data-animate>
            <div class="hm-product-img-wrap">
              <img src="../Images/U-pvc.jpg" alt="U-PVC Pipes" />
              <div class="hm-product-img-overlay">
                <a href="../Products/Products.php" class="hm-quick-view"
                  >Quick View <i class="bi bi-eye ms-1"></i
                ></a>
              </div>
            </div>
            <div class="hm-product-body">
              <h5>U-PVC (Pipes &amp; Fitting)</h5>
              <p>Leak-proof drainage and sewerage systems.</p>
              <a href="../Products/Products.php" class="hm-product-btn"
                >View Details <i class="bi bi-arrow-right ms-1"></i
              ></a>
            </div>
          </div>

          <!-- Card 3 -->
          <div class="hm-product-card" data-animate>
            <div class="hm-product-img-wrap">
              <img src="../Images/WaterTank.jpg" alt="Water Tank" />
              <div class="hm-product-img-overlay">
                <a href="../Products/Products.php" class="hm-quick-view"
                  >Quick View <i class="bi bi-eye ms-1"></i
                ></a>
              </div>
            </div>
            <div class="hm-product-body">
              <h5>Water Tank</h5>
              <p>Food-grade, multi-layer storage tanks.</p>
              <a href="../Products/Products.php" class="hm-product-btn"
                >View Details <i class="bi bi-arrow-right ms-1"></i
              ></a>
            </div>
          </div>
        </div>

        <div class="text-center mt-5">
          <a href="../Products/Products.php" class="hm-view-all"
            >View All Products <i class="bi bi-grid-3x3-gap-fill ms-2"></i
          ></a>
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
              <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i
              ><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i
              ><i class="bi bi-star-fill"></i>
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
              <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i
              ><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i
              ><i class="bi bi-star-fill"></i>
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
            <a href="../Contact us/contactus.html" class="hm-cta-btn-primary"
              ><i class="bi bi-telephone-fill me-2"></i> Contact Us Now</a
            >
            <a href="../Products/Products.php" class="hm-cta-btn-ghost"
              >Browse Products</a
            >
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

    <!-- ══ FOOTER ══ -->
    <footer class="footer-section text-white pt-5 mt-5">
      <div class="container">
        <div class="row gy-4">
          <div class="col-lg-4 col-md-6">
            <h4 class="fw-bold mb-4 text-danger">HASSAN TRADERS</h4>
            <p class="text-secondary">
              Premium plumbing and sanitary solutions in Sargodha. Quality
              durability you can trust for every build.
            </p>
            <div class="d-flex gap-3 mt-4">
              <a href="#" class="social-icon"><i class="bi bi-facebook"></i></a>
              <a href="#" class="social-icon"
                ><i class="bi bi-instagram"></i
              ></a>
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
              <li>
                <i class="bi bi-geo-alt-fill text-danger me-2"></i> Sargodha,
                Punjab, Pakistan
              </li>
              <li>
                <i class="bi bi-telephone-fill text-danger me-2"></i> +92 300
                0687080
              </li>
              <li>
                <i class="bi bi-envelope-fill text-danger me-2"></i>
                swsaaretheweathers@gmail.com
              </li>
            </ul>
          </div>
        </div>
        <hr class="mt-5 mb-4 border-secondary" />
        <div class="row align-items-center pb-4">
          <div class="col-md-6 text-center text-md-start">
            <p class="mb-0 text-secondary small">
              © 2026 Hassan Traders. All Rights Reserved.
            </p>
          </div>
          <div class="col-md-6 text-center text-md-end mt-3 mt-md-0">
            <p class="mb-0 text-secondary small">
              Designed by <span class="text-white">Umar Jalal</span>
            </p>
          </div>
        </div>
      </div>
    </footer>

    <!-- WHATSAPP BUTTON -->
    <a href="https://wa.me/923000687080" class="whatsapp-btn" target="_blank">
      <i class="bi bi-whatsapp"></i>
    </a>

    <script src="javascript.js"></script>
    <script src="../NavBar/navbar.js"></script>
  </body>
</html>
