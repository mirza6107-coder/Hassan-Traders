<?php session_start(); ?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Checkout - Hassan Traders</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

  <link rel="stylesheet" href="../NavBar/navbar.css" />
  <link rel="stylesheet" href="checkout.css" />
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>

<body>

  <!-- ══ NAVBAR (unchanged) ══ -->
  <?php include('../NavBar/navbar.php'); ?>


  <!-- ══ PAGE HERO ══ -->
  <div class="co-hero">
    <div class="co-hero-grid"></div>
    <div class="container co-hero-inner">
      <div class="co-breadcrumb">
        <a href="../Home/home.php">Home</a>
        <i class="bi bi-chevron-right"></i>
        <a href="../Add to Cart and CheckOut/Cart.php">Cart</a>
        <i class="bi bi-chevron-right"></i>
        <span>Checkout</span>
      </div>
      <h1 class="co-hero-title">Secure <em>Checkout</em></h1>
      <p class="co-hero-sub">Complete your order in just a few steps</p>

      <!-- Progress steps -->
      <div class="co-steps">
        <div class="co-step done">
          <div class="co-step-circle"><i class="bi bi-cart-check-fill"></i></div>
          <div class="co-step-label">Cart</div>
        </div>
        <div class="co-step-line done"></div>
        <div class="co-step active">
          <div class="co-step-circle"><i class="bi bi-person-fill"></i></div>
          <div class="co-step-label">Details</div>
        </div>
        <div class="co-step-line"></div>
        <div class="co-step">
          <div class="co-step-circle"><i class="bi bi-check-lg"></i></div>
          <div class="co-step-label">Confirm</div>
        </div>
      </div>
    </div>
    <div class="co-hero-cut"></div>
  </div>

  <!-- ══ MAIN CHECKOUT ══ -->
  <section class="co-main">
    <div class="container">
      <div class="row g-4 align-items-start">

        <!-- ════ LEFT: FORMS ════ -->
        <div class="col-lg-7">

          <!-- 1. Customer Info -->
          <div class="co-form-card">
            <div class="co-form-card-header">
              <div class="co-form-icon"><i class="bi bi-person-fill"></i></div>
              <div class="co-form-card-titles">
                <h3>Customer Information</h3>
                <p>Tell us who you are</p>
              </div>
              <div class="co-step-num">01</div>
            </div>
            <div class="co-form-body">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="co-label">Full Name <span class="co-req">*</span></label>
                  <input type="text" id="userName" class="co-input" placeholder="e.g. Muhammad Hassan" required />
                </div>
                <div class="col-md-6">
                  <label class="co-label">Phone Number <span class="co-req">*</span></label>
                  <input type="tel" id="userPhone" class="co-input" placeholder="+92 3XX XXXXXXX" required />
                </div>
                <div class="col-md-6">
                  <label class="co-label">Email Address <span class="co-optional">(Optional)</span></label>
                  <input type="email" id="userEmail" class="co-input" placeholder="hassan@email.com" />
                </div>
                <div class="col-md-6">
                  <label class="co-label">CNIC <span class="co-optional">(Optional)</span></label>
                  <input type="text" class="co-input" placeholder="XXXXX-XXXXXXX-X" />
                </div>
              </div>
            </div>
          </div>

          <!-- 2. Delivery Address -->
          <div class="co-form-card">
            <div class="co-form-card-header">
              <div class="co-form-icon"><i class="bi bi-geo-alt-fill"></i></div>
              <div class="co-form-card-titles">
                <h3>Delivery Address</h3>
                <p>Where should we deliver?</p>
              </div>
              <div class="co-step-num">02</div>
            </div>
            <div class="co-form-body">
              <div class="row g-3">
                <div class="col-12">
                  <label class="co-label">Street Address <span class="co-req">*</span></label>
                  <input type="text" id="userAddress" class="co-input" placeholder="House/Plot No., Street Name, Area" required />
                </div>
                <div class="col-md-6">
                  <label class="co-label">City <span class="co-req">*</span></label>
                  <select id="userCity" class="co-input co-select">
                    <option>Sargodha</option>
                    <option>Lahore</option>
                    <option>Faisalabad</option>
                    <option>Rawalpindi</option>
                    <option>Other</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="co-label">Postal Code <span class="co-optional">(Optional)</span></label>
                  <input type="text" class="co-input" placeholder="40100" />
                </div>
                <div class="col-12">
                  <label class="co-label">Order Notes <span class="co-optional">(Optional)</span></label>
                  <textarea class="co-input co-textarea" rows="3" placeholder="Any special delivery instructions..."></textarea>
                </div>
              </div>
            </div>
          </div>

          <!-- 3. Payment Method -->
          <div class="co-form-card">
            <div class="co-form-card-header">
              <div class="co-form-icon"><i class="bi bi-credit-card-fill"></i></div>
              <div class="co-form-card-titles">
                <h3>Payment Method</h3>
                <p>Choose how you'd like to pay</p>
              </div>
              <div class="co-step-num">03</div>
            </div>
            <div class="co-form-body">
              <div class="co-pay-grid">
                <div class="co-pay-card active" onclick="selectPayment(this,'cod')">
                  <div class="co-pay-check"><i class="bi bi-check-lg"></i></div>
                  <div class="co-pay-icon">💵</div>
                  <div class="co-pay-name">Cash on Delivery</div>
                  <div class="co-pay-sub">Pay when delivered</div>
                </div>
                <div class="co-pay-card" onclick="selectPayment(this,'easypaisa')">
                  <div class="co-pay-check"><i class="bi bi-check-lg"></i></div>
                  <div class="co-pay-icon">📱</div>
                  <div class="co-pay-name">Easypaisa</div>
                  <div class="co-pay-sub">Mobile wallet</div>
                </div>
                <div class="co-pay-card" onclick="selectPayment(this,'jazzcash')">
                  <div class="co-pay-check"><i class="bi bi-check-lg"></i></div>
                  <div class="co-pay-icon">💰</div>
                  <div class="co-pay-name">JazzCash</div>
                  <div class="co-pay-sub">Mobile wallet</div>
                </div>
                <div class="co-pay-card" onclick="selectPayment(this,'bank')">
                  <div class="co-pay-check"><i class="bi bi-check-lg"></i></div>
                  <div class="co-pay-icon">🏦</div>
                  <div class="co-pay-name">Bank Transfer</div>
                  <div class="co-pay-sub">Direct transfer</div>
                </div>
              </div>

              <!-- Payment detail panels -->
              <div id="pay-detail-easypaisa" class="co-pay-detail" style="display:none;">
                <div class="co-pay-detail-grid">
                  <div><span class="co-detail-label">Name</span><span class="co-detail-val">Umar Jalal</span></div>
                  <div><span class="co-detail-label">Number</span><span class="co-detail-val">0321-7703083</span></div>
                </div>
              </div>
              <div id="pay-detail-jazzcash" class="co-pay-detail" style="display:none;">
                <div class="co-pay-detail-grid">
                  <div><span class="co-detail-label">Name</span><span class="co-detail-val">Hassan Jalal</span></div>
                  <div><span class="co-detail-label">Number</span><span class="co-detail-val">0300-0687080</span></div>
                </div>
              </div>
              <div id="pay-detail-bank" class="co-pay-detail" style="display:none;">
                <div class="co-pay-detail-grid">
                  <div><span class="co-detail-label">Bank</span><span class="co-detail-val">Alfalah </span></div>
                  <div><span class="co-detail-label">Account</span><span class="co-detail-val">5534-5000587637</span></div>
                  <div><span class="co-detail-label">Account Title</span><span class="co-detail-val">Hassan Jalal</span></div>
                </div>
              </div>
            </div>
          </div>

        </div>
        <!-- /col-lg-7 -->

        <!-- ════ RIGHT: ORDER SUMMARY ════ -->
        <div class="col-lg-5">
          <div class="co-summary-card">
            <div class="co-summary-header">
              <i class="bi bi-receipt-cutoff co-receipt-icon"></i>
              <h3>Order Summary</h3>
            </div>

            <div class="co-summary-items" id="summary-items">
              <p class="co-empty-msg">Loading cart…</p>
            </div>

            <div class="co-summary-divider"></div>

            <div class="co-summary-rows">
              <div class="co-sum-row">
                <span class="co-sum-label">Subtotal</span>
                <span class="co-sum-val">Rs. <span id="sum-subtotal">0</span></span>
              </div>
              <div class="co-sum-row">
                <span class="co-sum-label">Shipping</span>
                <span class="co-sum-free">Free</span>
              </div>
              <div class="co-sum-row">
                <span class="co-sum-label">Tax</span>
                <span class="co-sum-val">Included</span>
              </div>
            </div>

            <div class="co-total-row">
              <span class="co-total-label">Total Amount</span>
              <span class="co-total-val">Rs. <span id="sum-total">0</span></span>
            </div>

            <div class="co-summary-footer">
              <button onclick="handlePlaceOrder()" class="co-place-btn">
                <i class="bi bi-lock-fill me-2"></i> Place Order Now
              </button>
              <a href="../Add to Cart and CheckOut/Cart.php" class="co-back-btn">
                <i class="bi bi-arrow-left me-1"></i> Back to Cart
              </a>
            </div>
          </div>

          <!-- Trust badges -->
          <div class="co-trust-row">
            <div class="co-trust-badge"><i class="bi bi-shield-lock-fill"></i> Secure</div>
            <div class="co-trust-badge"><i class="bi bi-truck"></i> Free Delivery</div>
            <div class="co-trust-badge"><i class="bi bi-arrow-repeat"></i> Easy Returns</div>
          </div>

          <!-- Contact note -->
          <div class="co-contact-note">
            <div class="co-contact-icon-wrap"><i class="bi bi-headset"></i></div>
            <div>
              <div class="co-contact-title">Need Help?</div>
              <div class="co-contact-sub">Call us at <strong>+92 300 0687080</strong><br>Mon–Sun · 9AM–8PM · Fri Closed</div>
            </div>
          </div>
        </div>
        <!-- /col-lg-5 -->

      </div>
    </div>
  </section>

  <!-- ══ SUCCESS OVERLAY ══ -->
  <div class="co-success-overlay" id="successOverlay" style="display:none;">
    <div class="co-success-modal">
      <div class="co-success-anim">
        <div class="co-success-ring"></div>
        <div class="co-success-check"><i class="bi bi-check-lg"></i></div>
      </div>
      <h2>Order Placed!</h2>
      <p>Thank you! Your order <strong><span id="successOrderId"></span></strong> has been successfully placed with <strong>Hassan Traders</strong>. We'll contact you shortly to confirm delivery details.</p>
      <a href="../Home/home.php" class="co-success-btn">Back to Home <i class="bi bi-arrow-right ms-1"></i></a>
    </div>
  </div>

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

  <script src="../NavBar/navbar.js"></script>
  <script src="checkout.js"></script>
</body>

</html>