<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Hassan Traders Navbar</title>

  <link rel="stylesheet" href="navbar.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" />
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>

<body>
  <!-- ══════════════════════════════════════════
     NAVBAR
  ══════════════════════════════════════════ -->
  <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top premium-navbar" id="mainNav">
    <div class="container">

      <a class="navbar-brand d-flex align-items-center" href="../Home/home.php">
        <img src="../Images/Hassan Traders logo 2.png" alt="Hassan Traders" class="logo-img" />
      </a>

      <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse"
        data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
        aria-expanded="false" aria-label="Toggle navigation">
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
            <a class="nav-link current" href="Products.php">
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
        <div class="d-flex align-items-center me-lg-3">
          <a href="../Add to Cart and CheckOut/Cart.php" class="position-relative text-dark text-decoration-none">
            <i class="bi bi-cart3 fs-4"></i>
            <span id="cart-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
              0
            </span>
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
  <script src="navbar.js"></script>
</body>

</html>