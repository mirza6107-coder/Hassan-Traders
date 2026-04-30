<?php session_start(); ?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Shopping Cart - Hassan Traders</title>

    <link rel="stylesheet" href="cart.css" />
    <link rel="stylesheet" href="../NavBar/navbar.css" />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
      integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  </head>

  <body>

    <!-- ══ NAVBAR  ══ -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top premium-navbar">
      <div class="container">
        <a class="navbar-brand d-flex align-items-center" id="logo" href="../Home/home.php">
          <img src="../Images/Hassan Traders logo 2.png" alt="Hassan Traders" class="logo-img" />
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse"
          data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
          aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav mx-auto align-items-center">
            <li class="nav-item"><a class="nav-link" href="../Home/home.php"><i class="bi bi-house-door-fill me-2"></i>Home</a></li>
            <li class="nav-item"><a class="nav-link" href="../Products/Products.php"><i class="bi bi-grid-3x3-gap-fill me-2"></i>Products</a></li>
            <li class="nav-item"><a class="nav-link" href="../About us/aboutus.php"><i class="bi bi-info-circle-fill me-2"></i>About Us</a></li>
            <li class="nav-item"><a class="nav-link" href="../Contact us/contactus.php"><i class="bi bi-telephone-fill me-2"></i>Contact Us</a></li>
          </ul>
          <div class="d-flex align-items-center me-lg-3">
            <a href="../Add to Cart and CheckOut/Cart.php" class="position-relative text-dark text-decoration-none">
              <i class="bi bi-cart3 fs-4"></i>
              <span id="cart-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">0</span>
            </a>
          </div>
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

    <!-- ══ PAGE HERO ══ -->
    <div class="ct-hero">
      <div class="ct-hero-grid"></div>
      <div class="container ct-hero-inner">
        <div class="ct-breadcrumb">
          <a href="../Home/home.html">Home</a>
          <i class="bi bi-chevron-right"></i>
          <a href="../Products/Products.php">Products</a>
          <i class="bi bi-chevron-right"></i>
          <span>Cart</span>
        </div>
        <h1 class="ct-hero-title">Shopping <em>Cart</em></h1>
        <p class="ct-hero-sub">Review your items and proceed to checkout</p>
      </div>
      <div class="ct-hero-cut"></div>
    </div>

    <!-- ══ MAIN CART SECTION ══ -->
    <section class="ct-main">
      <div class="container">
        <div class="row g-4 align-items-start">

          <!-- ════ LEFT: CART ITEMS ════ -->
          <div class="col-lg-8">

            <!-- Section header row -->
            <div class="ct-items-header">
              <h2 class="ct-items-title" id="cart-heading">Your Items</h2>
              <a href="../Products/Products.php" class="ct-continue-link">
                <i class="bi bi-arrow-left me-1"></i> Continue Shopping
              </a>
            </div>

            <!-- ── Desktop Table ── -->
            <div class="ct-table-wrap d-none d-md-block">
              <table class="ct-table">
                <thead>
                  <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody id="cart-items-container"></tbody>
              </table>
            </div>

            <!-- ── Mobile Cards ── -->
            <div class="d-md-none ct-mobile-list" id="cart-items-mobile"></div>

          </div>
          <!-- /col-lg-8 -->

          <!-- ════ RIGHT: ORDER SUMMARY ════ -->
          <div class="col-lg-4">
            <div class="ct-summary-card">
              <div class="ct-summary-header">
                <i class="bi bi-receipt-cutoff ct-summary-icon"></i>
                <h3>Order Summary</h3>
              </div>

              <div class="ct-summary-body">
                <div class="ct-summary-row">
                  <span class="ct-sum-label">Subtotal</span>
                  <span class="ct-sum-value">Rs. <span id="subtotal">0</span></span>
                </div>
                <div class="ct-summary-row">
                  <span class="ct-sum-label">Shipping</span>
                  <span class="ct-sum-free">Free</span>
                </div>
                <div class="ct-summary-row">
                  <span class="ct-sum-label">Tax</span>
                  <span class="ct-sum-value">Included</span>
                </div>
              </div>

              <div class="ct-summary-divider"></div>

              <div class="ct-total-row">
                <span class="ct-total-label">Total Amount</span>
                <span class="ct-total-value">Rs. <span id="grand-total">0</span></span>
              </div>

              <a href="checkout.php" class="ct-checkout-btn">
                <i class="bi bi-lock-fill me-2"></i> Proceed to Checkout
              </a>
              <a href="../Products/Products.php" class="ct-back-btn">
                <i class="bi bi-arrow-left me-1"></i> Continue Shopping
              </a>
            </div>

            <!-- Trust badges -->
            <div class="ct-trust-grid">
              <div class="ct-trust-item">
                <div class="ct-trust-icon"><i class="bi bi-truck"></i></div>
                <div>
                  <div class="ct-trust-title">Free Delivery</div>
                  <div class="ct-trust-sub">Orders above Rs. 50,000</div>
                </div>
              </div>
              <div class="ct-trust-item">
                <div class="ct-trust-icon"><i class="bi bi-arrow-repeat"></i></div>
                <div>
                  <div class="ct-trust-title">Easy Returns</div>
                  <div class="ct-trust-sub">7-day hassle-free policy</div>
                </div>
              </div>
              <div class="ct-trust-item">
                <div class="ct-trust-icon"><i class="bi bi-shield-lock-fill"></i></div>
                <div>
                  <div class="ct-trust-title">Secure Payment</div>
                  <div class="ct-trust-sub">Your data is protected</div>
                </div>
              </div>
              <div class="ct-trust-item">
                <div class="ct-trust-icon"><i class="bi bi-headset"></i></div>
                <div>
                  <div class="ct-trust-title">24/7 Support</div>
                  <div class="ct-trust-sub">+92 300 0687080</div>
                </div>
              </div>
            </div>
          </div>
          <!-- /col-lg-4 -->

        </div>
      </div>
    </section>

    <!-- ══ FOOTER (unchanged) ══ -->
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

   

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="cart.js"></script>
    <script src="../NavBar/navbar.js"></script>
  </body>
</html>
