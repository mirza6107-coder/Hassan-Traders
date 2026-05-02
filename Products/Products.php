<?php session_start(); ?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Hassan Traders — Products</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">


  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="../NavBar/navbar.css" />
  <link rel="stylesheet" href="products.css" />
</head>

<body>

  <!-- ══ NAVBAR ══ -->
  <?php include('../NavBar/navbar.php'); ?>

  <?php
  /* ══ DATABASE & DATA ══ */
  $conn = mysqli_connect('localhost', 'root', '', 'htss');
  if (!$conn) die("Connection failed: " . mysqli_connect_error());

  $all_result = mysqli_query($conn, "SELECT * FROM products WHERE LOWER(Status) = 'published' ORDER BY ID DESC");
  $all_products = [];
  while ($row = mysqli_fetch_assoc($all_result)) {
    $all_products[] = $row;
  }

  $cat_result = mysqli_query($conn, "SELECT DISTINCT Category FROM products WHERE LOWER(Status) = 'published' ORDER BY Category ASC");
  $categories = [];
  while ($cat_row = mysqli_fetch_assoc($cat_result)) {
    $categories[] = $cat_row['Category'];
  }

  mysqli_close($conn);

  function catToId(string $cat)
  {
    return 'tab-' . preg_replace('/[^a-z0-9]+/', '-', strtolower(trim($cat)));
  }

  function catIcon(string $cat)
  {
    $map = [
      'pipe'     => 'bi-moisture',
      'pipes'    => 'bi-moisture',
      'tank'     => 'bi-droplet-fill',
      'tanks'    => 'bi-droplet-fill',
      'bath'     => 'bi-bucket-fill',
      'fitting'  => 'bi-tools',
      'fittings' => 'bi-tools',
      'valve'    => 'bi-sliders',
      'valves'   => 'bi-sliders',
      'sanitary' => 'bi-house-check',
      'pump'     => 'bi-fan',
      'pumps'    => 'bi-fan',
    ];
    $lower = strtolower(trim($cat));
    foreach ($map as $key => $icon) {
      if (str_contains($lower, $key)) return $icon;
    }
    return 'bi-box-seam';
  }

  function renderCards(array $products)
  {
    if (empty($products)) {
      echo '<div class="col-12 empty-state"><i class="bi bi-inbox"></i><p>No products in this category yet.</p></div>';
      return;
    }
    foreach ($products as $p) {
      $img          = htmlspecialchars($p['P_image']);
      $name         = htmlspecialchars($p['P_name']);
      $brand        = htmlspecialchars($p['Brand'] ?? '');
      $priceDisplay = number_format($p['Price']);
      $priceRaw     = (float)$p['Price'];
      $id           = (int)$p['ID'];
      $cat          = htmlspecialchars($p['Category'] ?? '');
      $jsName       = addslashes($p['P_name']);

      echo "
    <div class='col-lg-3 col-md-4 col-sm-6 mb-4 product-card-wrap'>
      <div class='product-card'>

        <div class='card-img-wrap'>
          <img src='../Admin-Panel/uploads/{$img}' alt='{$name}'
               onerror=\"this.src='../Images/no-image.png'\">
          " . ($cat ? "<span class='card-badge'>{$cat}</span>" : "") . "
          <div class='card-overlay'>
            <!-- ✅ Now opens popup, not a new page -->
            <button class='overlay-btn' onclick=\"openQuickView({$id}, '{$jsName}', {$priceRaw}, '{$img}')\">
              <i class='bi bi-eye'></i> Quick View
            </button>
          </div>
        </div>

        <div class='card-body-inner'>
          " . ($brand ? "<div class='card-brand'>{$brand}</div>" : "") . "
          <div class='card-name'>{$name}</div>

          <div class='card-footer-row d-flex justify-content-between align-items-center mt-2'>
            <div class='card-price'><span>Rs.</span> {$priceDisplay}</div>
            <button class='btn btn-sm btn-danger rounded-pill px-3'
              onclick=\"addToCart({$id}, '{$jsName}', {$priceRaw}, '{$img}')\">
              <i class='bi bi-cart-plus'></i> Buy
            </button>
          </div>
        </div>

      </div>
    </div>";
    }
  }
  ?>


  <!-- ══ HERO ══ -->
  <section class="products-hero">
    <div class="container hero-inner">
      <div class="row align-items-center">

        <div class="col-lg-7 mb-4 mb-lg-0">
          <div class="hero-label">
            <i class="bi bi-grid-3x3-gap me-2"></i>Our Catalogue
          </div>
          <h1 class="hero-title">
            Premium <span>Plumbing</span><br>&amp; Sanitary Solutions
          </h1>
          <p class="hero-sub">
            Trusted quality for every build — from PPR-C pipes to complete bath sets, sourced from top brands.
          </p>
        </div>

        <div class="col-lg-5">
          <div class="d-flex justify-content-lg-end">
            <div class="row g-0 border border-white border-opacity-10 rounded-3 overflow-hidden">
              <div class="col-auto hero-stat">
                <div class="hero-stat-num"><?php echo count($all_products); ?>+</div>
                <div class="hero-stat-label">Products</div>
              </div>
              <div class="col-auto hero-stat">
                <div class="hero-stat-num"><?php echo count($categories); ?></div>
                <div class="hero-stat-label">Categories</div>
              </div>
              <div class="col-auto hero-stat">
                <div class="hero-stat-num">10+</div>
                <div class="hero-stat-label">Top Brands</div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>


  <!-- ══ PRODUCTS SECTION ══ -->
  <section class="products-section">
    <div class="container">

      <!-- Search -->
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-5">
        <div class="search-wrapper flex-grow-1">
          <i class="bi bi-search search-icon"></i>
          <input type="search" id="productSearch"
            placeholder="Search by name, brand or category…" autocomplete="off">
          <button class="search-btn" onclick="filterCards()">
            <i class="bi bi-search"></i> Search
          </button>
        </div>
        <div id="resultCount" class="result-count"></div>
      </div>

      <div class="row g-4">

        <!-- Sidebar -->
        <div class="col-lg-2 col-md-3">
          <div class="category-sidebar">
            <div class="sidebar-title">Categories</div>
            <div class="sidebar-pills-scroll" id="v-pills-tab">

              <button class="cat-pill active" data-target="tab-all">
                <span class="cat-icon"><i class="bi bi-grid-3x3-gap"></i></span>
                All Products
                <span class="cat-count"><?php echo count($all_products); ?></span>
              </button>

              <?php foreach ($categories as $cat):
                $catCount = count(array_filter($all_products, fn($p) => $p['Category'] === $cat));
              ?>
                <button class="cat-pill" data-target="<?php echo catToId($cat); ?>">
                  <span class="cat-icon">
                    <i class="bi <?php echo catIcon($cat); ?>"></i>
                  </span>
                  <?php echo htmlspecialchars($cat); ?>
                  <span class="cat-count"><?php echo $catCount; ?></span>
                </button>
              <?php endforeach; ?>

            </div>
          </div>
        </div>

        <!-- Tab Panels -->
        <div class="col-lg-10 col-md-9">

          <div class="tab-pane show" id="tab-all">
            <div class="panel-header">
              <div class="panel-title">All <span>Products</span></div>
              <div class="panel-count"><?php echo count($all_products); ?> items</div>
            </div>
            <div class="row">
              <?php renderCards($all_products); ?>
            </div>
          </div>

          <?php foreach ($categories as $cat):
            $cat_products = array_values(array_filter($all_products, fn($p) => $p['Category'] === $cat));
          ?>
            <div class="tab-pane" id="<?php echo catToId($cat); ?>">
              <div class="panel-header">
                <div class="panel-title"><?php echo htmlspecialchars($cat); ?> <span>Products</span></div>
                <div class="panel-count"><?php echo count($cat_products); ?> items</div>
              </div>
              <div class="row">
                <?php renderCards($cat_products); ?>
              </div>
            </div>
          <?php endforeach; ?>

          <div id="noResults">
            <i class="bi bi-search"></i>
            <p>No products found for your search.<br>
              <small>Try a different keyword.</small>
            </p>
          </div>

        </div>
      </div>
    </div>
  </section>


  <!-- ══ FOOTER ══ -->
  <footer class="footer-section text-white pt-5 mt-3">
    <div class="container">
      <div class="row gy-4">

        <div class="col-lg-4 col-md-6">
          <h4 class="footer-brand-name">HASSAN TRADERS</h4>
          <p class="footer-about">
            Premium plumbing and sanitary solutions in Sargodha. Quality &amp; durability
            you can trust for every build.
          </p>
          <div class="d-flex gap-2 mt-4">
            <a href="#" class="social-icon"><i class="bi bi-facebook"></i></a>
            <a href="#" class="social-icon"><i class="bi bi-instagram"></i></a>
            <a href="#" class="social-icon"><i class="bi bi-whatsapp"></i></a>
          </div>
        </div>

        <div class="col-lg-2 col-md-6">
          <h6 class="footer-col-title">Explore</h6>
          <ul class="list-unstyled footer-links">
            <li><a href="../Home/home.php">Home</a></li>
            <li><a href="../About us/aboutus.php">About Us</a></li>
            <li><a href="../Products/Products.php">Our Products</a></li>
            <li><a href="../Contact us/contactus.php">Support</a></li>
          </ul>
        </div>

        <div class="col-lg-2 col-md-6">
          <h6 class="footer-col-title">Products</h6>
          <ul class="list-unstyled footer-links">
            <li><a href="../Products/Products.php">PPR-C Pipes</a></li>
            <li><a href="../Products/Products.php">U-PVC Fittings</a></li>
            <li><a href="../Products/Products.php">Water Tanks</a></li>
            <li><a href="../Products/Products.php">Bath Sets</a></li>
          </ul>
        </div>

        <div class="col-lg-4 col-md-6">
          <h6 class="footer-col-title">Contact Info</h6>
          <ul class="list-unstyled contact-list">
            <li><i class="bi bi-geo-alt-fill footer-icon-red"></i> Sargodha, Punjab, Pakistan</li>
            <li><i class="bi bi-telephone-fill footer-icon-red"></i> +92 300 0687080</li>
            <li><i class="bi bi-envelope-fill footer-icon-red"></i> swsaaretheweathers@gmail.com</li>
          </ul>
        </div>

      </div>

      <hr class="footer-divider" />

      <div class="row align-items-center pb-4">
        <div class="col-md-6 text-center text-md-start">
          <p class="footer-copy">© 2026 Hassan Traders. All Rights Reserved.</p>
        </div>
        <div class="col-md-6 text-center text-md-end mt-3 mt-md-0">
          <p class="footer-copy">Designed by <span class="footer-designer">Umar Jalal</span></p>
        </div>
      </div>
    </div>
  </footer>

  <!-- WhatsApp -->
  <a href="https://wa.me/923000687080" class="whatsapp-btn" target="_blank">
    <i class="bi bi-whatsapp"></i>
  </a>

  <!-- ══════════════════════════════════════════
       QUICK VIEW MODAL
  ══════════════════════════════════════════ -->
  <div class="qv-overlay" id="qvOverlay">
    <div class="qv-modal" id="qvModal">

      <!-- Close button (always visible) -->
      <button class="qv-close" onclick="closeQuickView()" title="Close">
        <i class="bi bi-x-lg"></i>
      </button>

      <!-- Dynamic content injected by JS -->
      <div id="qvContent">
        <!-- skeleton shown while loading -->
        <div class="qv-skeleton">
          <div class="qv-skel-box" style="height:300px;border-radius:16px;"></div>
        </div>
      </div>

    </div>
  </div>

  <!-- Scripts -->
  <script src="../NavBar/navbar.js"></script>
  <script src="products.js"></script>

</body>

</html>