<?php session_start(); ?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Shopping Cart - Hassan Traders</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

  <link rel="stylesheet" href="cart.css" />
  <link rel="stylesheet" href="../NavBar/navbar.css" />
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>

<body>

  <!-- ══ NAVBAR  ══ -->
  <?php include('../NavBar/navbar.php'); ?>


  <!-- ══ PAGE HERO ══ -->
  <div class="ct-hero">
    <div class="ct-hero-grid"></div>
    <div class="container ct-hero-inner">
      <div class="ct-breadcrumb">
        <a href="../Home/home.php">Home</a>
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
  <?php include('../Footer/footer.php'); ?>

  <!-- WHATSAPP BUTTON -->
  <a href="https://wa.me/923000687080" class="whatsapp-btn" target="_blank">
    <i class="bi bi-whatsapp"></i>
  </a>



  <script src="../NavBar/navbar.js"></script>
  <script src="cart.js"></script>

</body>

</html>