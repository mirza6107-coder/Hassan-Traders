<?php
/**
 * NavBar/navbar.php — Include as a snippet inside pages.
 * session_start() must be called in the parent page BEFORE including this.
 * If it isn't, we start it here safely.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userName = $_SESSION['user_name'] ?? null;
$initials = $userName ? strtoupper(substr(trim($userName), 0, 1)) : '';

// Inject DB cart into localStorage right after login (runs once per login)
$cartInitPath = __DIR__ . '/cart-init.php';
if (file_exists($cartInitPath)) {
    require_once $cartInitPath;
}
?>

<!-- ══════════════════ NAVBAR ══════════════════ -->
<style>
  .ht-dd-menu {
    border: 1px solid rgba(0,0,0,0.08);
    border-radius: 16px;
    box-shadow: 0 16px 48px rgba(0,0,0,0.14);
    padding: 8px;
    min-width: 220px;
    margin-top: 10px !important;
    animation: ddSlide .18s ease forwards;
  }
  @keyframes ddSlide {
    from { opacity:0; transform:translateY(-6px); }
    to   { opacity:1; transform:translateY(0); }
  }
  .ht-dd-menu .dd-user-head {
    padding: 11px 13px 9px;
    border-bottom: 1px solid #f0f0f0;
    margin-bottom: 5px;
  }
  .ht-dd-menu .dd-user-head .duh-label {
    font-size:.68rem; color:#9ca3af; font-weight:700;
    text-transform:uppercase; letter-spacing:.05em;
  }
  .ht-dd-menu .dd-user-head .duh-name {
    font-size:.92rem; font-weight:700; color:#141420;
    white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:185px;
  }
  .ht-dd-menu .dropdown-item {
    border-radius:10px; padding:9px 13px; font-weight:600;
    font-size:.85rem; color:#2c2c2c;
    display:flex; align-items:center; gap:10px;
    transition:background .15s, color .15s;
  }
  .ht-dd-menu .dropdown-item i { color:#dc3545; font-size:.9rem; width:18px; text-align:center; }
  .ht-dd-menu .dropdown-item:hover { background:rgba(220,53,69,0.08); color:#dc3545; }
  .ht-dd-menu .dropdown-item.logout-item { color:#dc3545; }
  .ht-dd-menu .dropdown-divider { margin:5px 0; border-color:#f3f3f3; }
</style>

<nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top premium-navbar" id="mainNav">
  <div class="container">

    <a class="navbar-brand d-flex align-items-center" href="../Home/home.php" id="logo">
      <img src="../Images/Hassan Traders logo 2.png" alt="Hassan Traders" class="logo-img"/>
    </a>

    <button class="navbar-toggler border-0" type="button"
            data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false"
            aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">

      <ul class="navbar-nav mx-auto align-items-center">
        <li class="nav-item">
          <a class="nav-link" href="../Home/home.php">
            <i class="bi bi-house-door-fill me-2"></i>Home
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../Products/Products.php">
            <i class="bi bi-grid me-2"></i>Products
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../About us/aboutus.php">
            <i class="bi bi-info-circle-fill me-2"></i>About Us
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../Contact us/contactus.php">
            <i class="bi bi-telephone-fill me-2"></i>Contact Us
          </a>
        </li>
      </ul>

      <!-- Cart Icon -->
      <div class="d-flex align-items-center me-lg-3">
        <a href="../Add-to-Cart-and-CheckOut/Cart.php"
           class="position-relative text-dark text-decoration-none">
          <i class="bi bi-cart3 fs-4"></i>
          <span id="cart-count"
                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: block;">
            0
          </span>
        </a>
      </div>

      <!-- Auth -->
      <div class="d-flex justify-content-center justify-content-lg-end mt-3 mt-lg-0">

        <?php if ($userName): ?>
          <div class="dropdown">
            <button class="btn btn-premium px-4 rounded-pill dropdown-toggle d-flex align-items-center gap-2"
                    type="button" id="userDropdownBtn"
                    data-bs-toggle="dropdown" aria-expanded="false">
              <span style="width:26px;height:26px;border-radius:50%;background:rgba(255,255,255,0.25);
                           display:inline-flex;align-items:center;justify-content:center;
                           font-size:.72rem;font-weight:800;flex-shrink:0;">
                <?= $initials ?>
              </span>
              <span class="fw-semibold"
                    style="max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                <?= htmlspecialchars($userName) ?>
              </span>
            </button>

            <ul class="dropdown-menu dropdown-menu-end ht-dd-menu" aria-labelledby="userDropdownBtn">
              <li>
                <div class="dd-user-head">
                  <div class="duh-label">Signed in as</div>
                  <div class="duh-name"><?= htmlspecialchars($userName) ?></div>
                </div>
              </li>
              <li>
                <a class="dropdown-item" href="../Profile/profile.php">
                  <i class="bi bi-person-badge-fill"></i> My Profile
                </a>
              </li>
              <li>
                <a class="dropdown-item" href="../Profile/profile.php#orders">
                  <i class="bi bi-bag-heart-fill"></i> My Orders
                </a>
              </li>
              <li>
                <a class="dropdown-item" href="../Add to Cart and CheckOut/Cart.php">
                  <i class="bi bi-cart3"></i> My Cart
                </a>
              </li>
              <li><hr class="dropdown-divider"/></li>
              <li>
                <a class="dropdown-item logout-item" href="../login and signup/logout.php">
                  <i class="bi bi-box-arrow-right"></i> Logout
                </a>
              </li>
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
<!-- ══════════════════ END NAVBAR ══════════════════ -->
