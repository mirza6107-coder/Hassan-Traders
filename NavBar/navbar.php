<?php
/**
 * NavBar/navbar.php
 * Include this file at the top of every page: <?php include '../NavBar/navbar.php'; ?>
 * Make sure session_start() is called BEFORE including this file.
 */
?>
<link rel="stylesheet" href="<?= $navbarBase ?? '../NavBar/' ?>navbar.css"/>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
      integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

<style>
/* ── Dropdown menu ── */
.ht-dropdown {
  border: 1px solid rgba(0,0,0,0.09);
  border-radius: 16px;
  box-shadow: 0 16px 48px rgba(0,0,0,0.13);
  padding: 8px;
  min-width: 220px;
  margin-top: 10px !important;
  animation: ddFade .18s ease;
}
@keyframes ddFade { from{opacity:0;transform:translateY(-6px)} to{opacity:1;transform:translateY(0)} }

.ht-dropdown .dd-user {
  padding: 12px 14px 10px;
  border-bottom: 1px solid #f0f0f0;
  margin-bottom: 6px;
}
.ht-dropdown .dd-user .greeting { font-size:.7rem; color:#9ca3af; font-weight:700; text-transform:uppercase; letter-spacing:.05em; }
.ht-dropdown .dd-user .uname    { font-size:.93rem; font-weight:700; color:#141420; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:180px; }

.ht-dropdown .dropdown-item {
  border-radius: 10px;
  padding: 10px 14px;
  font-weight: 600;
  font-size: .86rem;
  color: #2c2c2c;
  display: flex;
  align-items: center;
  gap: 10px;
  transition: background .18s, color .18s;
}
.ht-dropdown .dropdown-item i { color: #dc3545; font-size: .95rem; width: 18px; text-align: center; }
.ht-dropdown .dropdown-item:hover { background: rgba(220,53,69,0.07); color: #dc3545; }
.ht-dropdown .dropdown-item:hover i { color: #dc3545; }
.ht-dropdown .dropdown-item.text-danger { color: #dc3545; }
.ht-dropdown .dropdown-divider { margin: 6px 0; border-color: #f3f3f3; }

/* ── Cart badge animation ── */
#cart-count.bounce {
  animation: cartBounce .3s ease;
}
@keyframes cartBounce { 0%,100%{transform:scale(1) translate(-50%,-50%)} 50%{transform:scale(1.4) translate(-36%,-36%)} }
</style>

<?php
// Determine root path relative to current page for links
// Pages at root level use ''  ; pages in subfolders use '../'
// We detect by checking the current file's depth:
$depth   = substr_count(str_replace('\\','/',realpath($_SERVER['SCRIPT_FILENAME'])), '/') -
           substr_count(str_replace('\\','/',realpath($_SERVER['DOCUMENT_ROOT'])), '/');
$root    = str_repeat('../', max(0, $depth - 1));
// Easier approach: just use relative paths since all pages are one level deep
$r = '../'; // one level up from any subfolder (Home/, Products/, etc.)

$userName = $_SESSION['user_name'] ?? null;
$userId   = $_SESSION['user_id']   ?? null;
$initials = $userName ? strtoupper(substr($userName, 0, 1)) : '';

// Detect active page
$current = basename($_SERVER['PHP_SELF']);
$folder  = basename(dirname($_SERVER['PHP_SELF']));
?>

<nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top premium-navbar" id="mainNav">
  <div class="container">

    <!-- LOGO -->
    <a class="navbar-brand d-flex align-items-center" href="<?= $r ?>Home/home.php" id="logo">
      <img src="<?= $r ?>Images/Hassan Traders logo 2.png" alt="Hassan Traders" class="logo-img"/>
    </a>

    <!-- MOBILE TOGGLE -->
    <div class="d-flex align-items-center gap-3 d-lg-none">
      <!-- Mobile cart icon -->
      <a href="<?= $r ?>Add to Cart and CheckOut/Cart.php"
         class="position-relative text-dark text-decoration-none">
        <i class="bi bi-cart3 fs-5"></i>
        <span id="cart-count-mobile"
              class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
              style="font-size:.6rem;">
          <?= isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'qty')) : 0 ?>
        </span>
      </a>
      <button class="navbar-toggler border-0" type="button"
              data-bs-toggle="collapse" data-bs-target="#navbarMain"
              aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
    </div>

    <!-- COLLAPSE -->
    <div class="collapse navbar-collapse" id="navbarMain">
      <ul class="navbar-nav mx-auto align-items-center">

        <li class="nav-item">
          <a class="nav-link <?= $folder==='Home' ? 'active' : '' ?>"
             href="<?= $r ?>Home/home.php">
            <i class="bi bi-house-door-fill me-2"></i>Home
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link <?= $folder==='Products' ? 'active' : '' ?>"
             href="<?= $r ?>Products/Products.php">
            <i class="bi bi-grid-fill me-2"></i>Products
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link <?= $folder==='About us' ? 'active' : '' ?>"
             href="<?= $r ?>About us/aboutus.php">
            <i class="bi bi-info-circle-fill me-2"></i>About Us
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link <?= $folder==='Contact us' ? 'active' : '' ?>"
             href="<?= $r ?>Contact us/contactus.php">
            <i class="bi bi-telephone-fill me-2"></i>Contact Us
          </a>
        </li>

      </ul>

      <!-- Desktop cart -->
      <div class="d-none d-lg-flex align-items-center me-3">
        <a href="<?= $r ?>Add to Cart and CheckOut/Cart.php"
           class="position-relative text-dark text-decoration-none">
          <i class="bi bi-cart3 fs-4"></i>
          <span id="cart-count"
                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
            <?= isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'qty')) : 0 ?>
          </span>
        </a>
      </div>

      <!-- AUTH BUTTON -->
      <div class="d-flex justify-content-center justify-content-lg-end mt-3 mt-lg-0">

        <?php if ($userName): ?>
          <!-- ── LOGGED IN ── -->
          <div class="dropdown">
            <button class="btn btn-premium px-4 rounded-pill dropdown-toggle d-flex align-items-center gap-2"
                    data-bs-toggle="dropdown" aria-expanded="false" style="min-width:unset;">
              <!-- Avatar initials -->
              <span style="width:26px;height:26px;border-radius:50%;background:rgba(255,255,255,0.25);
                           display:flex;align-items:center;justify-content:center;
                           font-size:.75rem;font-weight:800;flex-shrink:0;">
                <?= $initials ?>
              </span>
              <span class="fw-semibold d-none d-sm-inline" style="max-width:110px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                <?= htmlspecialchars($userName) ?>
              </span>
            </button>

            <ul class="dropdown-menu dropdown-menu-end ht-dropdown">

              <!-- User header -->
              <li>
                <div class="dd-user">
                  <div class="greeting">Signed in as</div>
                  <div class="uname"><?= htmlspecialchars($userName) ?></div>
                </div>
              </li>

              <!-- My Profile -->
              <li>
                <a class="dropdown-item" href="<?= $r ?>Profile/profile.php">
                  <i class="bi bi-person-badge-fill"></i> My Profile
                </a>
              </li>

              <!-- My Orders -->
              <li>
                <a class="dropdown-item" href="<?= $r ?>Profile/profile.php#orders">
                  <i class="bi bi-bag-heart-fill"></i> My Orders
                </a>
              </li>

              <!-- Cart -->
              <li>
                <a class="dropdown-item" href="<?= $r ?>Add to Cart and CheckOut/Cart.php">
                  <i class="bi bi-cart3"></i> My Cart
                </a>
              </li>

              <!-- Checkout -->
              <li>
                <a class="dropdown-item" href="<?= $r ?>Add to Cart and CheckOut/checkout.php">
                  <i class="bi bi-bag-check-fill"></i> Checkout
                </a>
              </li>

              <li><hr class="dropdown-divider"/></li>

              <!-- Logout -->
              <li>
                <a class="dropdown-item text-danger" href="<?= $r ?>login and signup/logout.php">
                  <i class="bi bi-box-arrow-right"></i> Logout
                </a>
              </li>

            </ul>
          </div>

        <?php else: ?>
          <!-- ── NOT LOGGED IN ── -->
          <a href="<?= $r ?>login and signup/login.php" class="text-decoration-none">
            <button class="btn btn-premium px-5 rounded-pill d-flex align-items-center gap-2">
              <i class="bi bi-person-circle fs-5"></i>
              <span class="fw-semibold">LOGIN</span>
            </button>
          </a>
        <?php endif; ?>

      </div>
    </div><!-- /collapse -->

  </div><!-- /container -->
</nav>

<script src="<?= $r ?>NavBar/navbar.js"></script>