<?php
session_start();

$conn = mysqli_connect('localhost', 'root', '', 'htss');
if (!$conn) die("Connection failed: " . mysqli_connect_error());

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = mysqli_prepare($conn, "SELECT * FROM products WHERE ID = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$p = mysqli_fetch_assoc($result);

if (!$p) {
  mysqli_close($conn);
  header("Location: Products.php");
  exit();
}

// Related products (same category, exclude current)
$cat  = mysqli_real_escape_string($conn, $p['Category']);
$relQ = mysqli_query($conn, "SELECT * FROM products WHERE Category='$cat' AND ID!=$id AND LOWER(Status)='published' ORDER BY ID DESC LIMIT 4");
$related = [];
while ($r = mysqli_fetch_assoc($relQ)) $related[] = $r;
mysqli_close($conn);

// ── Description parser ──────────────────────────────────────────────────────
function parseDesc(string $raw): array
{
  $out = ['intro' => '', 'features' => [], 'specs' => [], 'ideal' => [], 'protip' => '', 'extra' => []];
  if (!trim($raw)) return $out;
  $lines = explode("\n", $raw);
  $current = 'intro';
  $buffer  = [];

  $flush = function () use (&$buffer, &$current, &$out) {
    $items = [];
    $acc = '';
    foreach ($buffer as $l) {
      $t = trim($l);
      if (!$t) {
        if ($acc) $items[] = trim($acc);
        $acc = '';
        continue;
      }
      if (preg_match('/^[•\-\*]/', $t) || preg_match('/^\d+[\.\)]/', $t)) {
        if ($acc) $items[] = trim($acc);
        $acc = preg_replace('/^[•\-\*\d\.\)]+\s*/', '', $t);
      } else {
        $acc .= ($acc ? ' ' : '') . $t;
      }
    }
    if ($acc) $items[] = trim($acc);
    $items = array_values(array_filter($items));

    if ($current === 'intro')    $out['intro']    = trim(implode("\n", $buffer));
    elseif ($current === 'features') $out['features'] = $items;
    elseif ($current === 'specs')    $out['specs']    = $items;
    elseif ($current === 'ideal')    $out['ideal']    = $items;
    elseif ($current === 'protip')   $out['protip']   = trim(implode(' ', $buffer));
    else if ($items)                  $out['extra'][]  = ['title' => $current, 'items' => $items];
    $buffer = [];
  };

  foreach ($lines as $line) {
    $t = trim($line);
    if (preg_match('/^key\s*features?\s*:/i',   $t)) {
      $flush();
      $current = 'features';
    } elseif (preg_match('/^technical\s*spec/i',       $t)) {
      $flush();
      $current = 'specs';
    } elseif (preg_match('/^ideal\s*for\s*:/i',        $t)) {
      $flush();
      $current = 'ideal';
    } elseif (preg_match('/^(pro.?tip|installation\s*pro)/i', $t)) {
      $flush();
      $current = 'protip';
    } elseif (preg_match('/^[A-Z][^.!?]{3,60}:$/', $t) && strlen($t) < 70) {
      $flush();
      $current = rtrim($t, ':');
    } else {
      $buffer[] = $line;
    }
  }
  $flush();
  return $out;
}

function splitItem(string $text): array
{
  if (preg_match('/^([^:]{2,60}):\s+([\s\S]+)/', $text, $m))
    return ['label' => trim($m[1]), 'body' => trim($m[2])];
  return ['label' => null, 'body' => $text];
}

$desc     = parseDesc($p['Description'] ?? '');
$hasOrig  = (float)($p['orignalprice'] ?? 0) > 0 && (float)$p['orignalprice'] !== (float)$p['Price'];
$discount = $hasOrig ? round((((float)$p['orignalprice'] - (float)$p['Price']) / (float)$p['orignalprice']) * 100) : 0;
$stock    = (int)$p['Quantity'];
$sLabel   = $stock === 0 ? 'Out of Stock' : ($stock < 20 ? 'Low Stock' : 'In Stock');
$sClass   = $stock === 0 ? 'out' : ($stock < 20 ? 'low' : 'ok');
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title><?= htmlspecialchars($p['P_name']) ?> — Hassan Traders</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
  <link rel="stylesheet" href="../NavBar/navbar.css" />
  <link rel="stylesheet" href="products.css" />

  <style>
    :root {
      --primary: #C8102E;
      --primary-dark: #9E0B22;
      --primary-light: rgba(200, 16, 46, 0.09);
      --cream: #FAF8F5;
      --border: #E8E2D9;
      --text: #1A1A1A;
      --text-muted: #6B6B6B;
      --white: #fff;
      --font: 'Plus Jakarta Sans', system-ui, sans-serif;
      --serif: 'Playfair Display', serif;
      --mono: 'DM Mono', monospace;
      --radius: 18px;
      --shadow: 0 4px 24px rgba(0, 0, 0, 0.07);
    }

    body {
      font-family: var(--font);
      background: var(--cream);
      color: var(--text);
      -webkit-font-smoothing: antialiased;
    }

    /* BREADCRUMB */
    .vp-crumb {
      padding: 18px 0 0;
      font-size: 12.5px;
      color: var(--text-muted);
      font-weight: 500;
    }

    .vp-crumb a {
      color: var(--text-muted);
      text-decoration: none;
      transition: color .18s;
    }

    .vp-crumb a:hover {
      color: var(--primary);
    }

    .vp-crumb .sep {
      margin: 0 7px;
      opacity: .4;
    }

    .vp-crumb .cur {
      color: var(--text);
      font-weight: 700;
    }

    /* MAIN SECTION */
    .vp-main {
      padding: 28px 0 60px;
    }

    /* ── GALLERY ── */
    .vp-gallery {
      position: sticky;
      top: 88px;
    }

    .vp-main-img {
      border-radius: var(--radius);
      overflow: hidden;
      background: #F2EDE8;
      aspect-ratio: 1/1;
      border: 1.5px solid var(--border);
      position: relative;
      box-shadow: var(--shadow);
    }

    .vp-main-img img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
      transition: transform .6s cubic-bezier(.16, 1, .3, 1);
    }

    .vp-main-img:hover img {
      transform: scale(1.04);
    }

    .vp-badge-cat {
      position: absolute;
      top: 14px;
      left: 14px;
      background: var(--primary);
      color: #fff;
      font-size: 10px;
      font-weight: 800;
      letter-spacing: .1em;
      text-transform: uppercase;
      padding: 5px 13px;
      border-radius: 50px;
      z-index: 2;
    }

    .vp-badge-disc {
      position: absolute;
      top: 14px;
      right: 14px;
      background: #1A1A1A;
      color: #fff;
      font-size: 11px;
      font-weight: 800;
      padding: 5px 13px;
      border-radius: 50px;
      z-index: 2;
    }

    /* Thumbnails */
    .vp-thumbs {
      display: flex;
      gap: 10px;
      margin-top: 14px;
      flex-wrap: wrap;
    }

    .vp-thumb {
      width: 76px;
      height: 76px;
      border-radius: 10px;
      overflow: hidden;
      border: 2px solid transparent;
      cursor: pointer;
      background: #F2EDE8;
      flex-shrink: 0;
      transition: border-color .18s, transform .18s;
      box-shadow: 0 2px 8px rgba(0, 0, 0, .06);
    }

    .vp-thumb img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }

    .vp-thumb.active {
      border-color: var(--primary);
    }

    .vp-thumb:hover {
      transform: scale(1.06);
      border-color: var(--primary);
    }

    /* Zoom hint */
    .vp-zoom-hint {
      text-align: center;
      margin-top: 10px;
      font-size: 11.5px;
      color: var(--text-muted);
      font-weight: 500;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 5px;
    }

    /* ── INFO PANEL ── */
    .vp-info {
      padding-left: 10px;
    }

    .vp-pills {
      display: flex;
      gap: 8px;
      margin-bottom: 14px;
      flex-wrap: wrap;
    }

    .vp-pill-cat {
      background: var(--primary-light);
      color: var(--primary);
      font-size: 11px;
      font-weight: 700;
      letter-spacing: .1em;
      text-transform: uppercase;
      padding: 4px 13px;
      border-radius: 50px;
    }

    .vp-pill-brand {
      background: #F2EDE8;
      color: var(--text-muted);
      font-size: 11px;
      font-weight: 700;
      letter-spacing: .08em;
      text-transform: uppercase;
      padding: 4px 13px;
      border-radius: 50px;
      border: 1px solid var(--border);
    }

    .vp-title {
      font-family: var(--serif);
      font-size: clamp(1.55rem, 3.5vw, 2.4rem);
      font-weight: 900;
      line-height: 1.2;
      color: var(--text);
      margin-bottom: 18px;
    }

    .vp-price-row {
      display: flex;
      align-items: baseline;
      gap: 13px;
      margin-bottom: 18px;
      flex-wrap: wrap;
    }

    .vp-price {
      font-family: var(--serif);
      font-size: 2.2rem;
      font-weight: 700;
      color: var(--text);
    }

    .vp-orig {
      font-size: 15px;
      color: var(--text-muted);
      text-decoration: line-through;
      font-family: var(--mono);
    }

    .vp-disc-tag {
      background: var(--primary);
      color: #fff;
      font-size: 11px;
      font-weight: 800;
      padding: 4px 11px;
      border-radius: 50px;
    }

    .vp-stock {
      display: inline-flex;
      align-items: center;
      gap: 7px;
      padding: 7px 16px;
      border-radius: 50px;
      font-size: 12.5px;
      font-weight: 700;
      margin-bottom: 22px;
    }

    .vp-stock.ok {
      background: #f0fdf4;
      color: #16a34a;
      border: 1px solid #bbf7d0;
    }

    .vp-stock.low {
      background: #fffbeb;
      color: #d97706;
      border: 1px solid #fde68a;
    }

    .vp-stock.out {
      background: #fef2f2;
      color: var(--primary);
      border: 1px solid #fecaca;
    }

    .vp-hr {
      border: none;
      border-top: 1px solid var(--border);
      margin: 20px 0;
    }

    .vp-intro {
      font-size: 14px;
      color: #4a4a4a;
      line-height: 1.82;
      margin-bottom: 22px;
    }

    /* Quick spec chips */
    .vp-chips {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 10px;
      margin-bottom: 4px;
    }

    .vp-chip {
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: 10px;
      padding: 10px 14px;
    }

    .vp-chip-lbl {
      font-size: 10px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .08em;
      color: var(--text-muted);
      margin-bottom: 3px;
    }

    .vp-chip-val {
      font-size: 13.5px;
      font-weight: 700;
      color: var(--text);
      font-family: var(--mono);
    }

    /* CTA */
    .vp-cta {
      display: flex;
      gap: 11px;
      margin-bottom: 26px;
      flex-wrap: wrap;
    }

    .vp-btn-cart {
      flex: 1;
      min-width: 180px;
      background: var(--primary);
      color: #fff;
      border: none;
      border-radius: 50px;
      padding: 15px 24px;
      font-family: var(--font);
      font-size: 15px;
      font-weight: 700;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 9px;
      transition: background .2s, transform .18s, box-shadow .2s;
      box-shadow: 0 8px 24px rgba(200, 16, 46, .22);
    }

    .vp-btn-cart:hover {
      background: var(--primary-dark);
      transform: translateY(-2px);
      box-shadow: 0 12px 32px rgba(200, 16, 46, .3);
    }

    .vp-btn-cart:disabled {
      opacity: .5;
      cursor: not-allowed;
      transform: none;
    }

    .vp-btn-wa {
      display: flex;
      align-items: center;
      gap: 8px;
      background: #25D366;
      color: #fff;
      border: none;
      border-radius: 50px;
      padding: 15px 22px;
      font-family: var(--font);
      font-size: 14px;
      font-weight: 700;
      cursor: pointer;
      text-decoration: none;
      transition: background .2s, transform .18s;
      box-shadow: 0 8px 24px rgba(37, 211, 102, .2);
    }

    .vp-btn-wa:hover {
      background: #1da851;
      color: #fff;
      transform: translateY(-2px);
    }

    .vp-btn-back {
      display: inline-flex;
      align-items: center;
      gap: 7px;
      font-size: 13px;
      color: var(--text-muted);
      font-weight: 600;
      text-decoration: none;
      transition: color .18s;
    }

    .vp-btn-back:hover {
      color: var(--primary);
    }

    /* Trust row */
    .vp-trust {
      display: flex;
      gap: 20px;
      flex-wrap: wrap;
      font-size: 12.5px;
      color: var(--text-muted);
      font-weight: 600;
    }

    .vp-trust span {
      display: flex;
      align-items: center;
      gap: 5px;
    }

    .vp-trust i {
      color: var(--primary);
    }

    /* ── TABS ── */
    .vp-tabs-wrap {
      background: var(--white);
      border-radius: var(--radius);
      border: 1.5px solid var(--border);
      overflow: hidden;
      box-shadow: var(--shadow);
      margin: 40px 0 0;
    }

    .vp-tab-nav {
      display: flex;
      border-bottom: 1.5px solid var(--border);
      background: var(--cream);
      overflow-x: auto;
    }

    .vp-tab-nav::-webkit-scrollbar {
      display: none;
    }

    .vp-tab-btn {
      padding: 15px 22px;
      font-size: 13px;
      font-weight: 700;
      font-family: var(--font);
      color: var(--text-muted);
      background: transparent;
      border: none;
      cursor: pointer;
      white-space: nowrap;
      border-bottom: 3px solid transparent;
      margin-bottom: -1.5px;
      transition: color .18s, border-color .18s;
    }

    .vp-tab-btn:hover {
      color: var(--text);
    }

    .vp-tab-btn.active {
      color: var(--primary);
      border-bottom-color: var(--primary);
    }

    .vp-tab-panel {
      display: none;
      padding: 32px;
    }

    .vp-tab-panel.active {
      display: block;
    }

    /* Description inner styles */
    .vp-sec-head {
      font-size: 10.5px;
      font-weight: 800;
      letter-spacing: .15em;
      text-transform: uppercase;
      color: var(--text-muted);
      margin: 0 0 14px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .vp-sec-head i {
      color: var(--primary);
      font-size: 13px;
    }

    .vp-sec-head::after {
      content: '';
      flex: 1;
      height: 1px;
      background: var(--border);
    }

    .vp-feat-ul {
      list-style: none;
      padding: 0;
      margin: 0 0 28px;
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .vp-feat-ul li {
      display: flex;
      gap: 12px;
      align-items: flex-start;
      font-size: 14px;
      color: #3a3a3a;
      line-height: 1.72;
    }

    .vp-feat-dot {
      width: 24px;
      height: 24px;
      border-radius: 7px;
      background: var(--primary-light);
      color: var(--primary);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 10px;
      flex-shrink: 0;
      margin-top: 2px;
      font-weight: 700;
    }

    .vp-feat-lbl {
      font-weight: 700;
      color: var(--text);
    }

    .vp-ideal-ul {
      list-style: none;
      padding: 0;
      margin: 0 0 26px;
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .vp-ideal-ul li {
      display: flex;
      gap: 8px;
      align-items: flex-start;
      font-size: 14px;
      color: #3a3a3a;
      line-height: 1.65;
    }

    .vp-ideal-arr {
      color: var(--primary);
      font-size: 20px;
      line-height: 1;
      margin-top: 1px;
      flex-shrink: 0;
    }

    .vp-ideal-lbl {
      font-weight: 700;
      color: var(--text);
    }

    .vp-protip {
      background: #fffbeb;
      border: 1px solid #fde68a;
      border-left: 4px solid #f59e0b;
      border-radius: 12px;
      padding: 16px 20px;
      margin-top: 8px;
      font-size: 13.5px;
      color: #78350f;
      line-height: 1.72;
    }

    .vp-protip p {
      margin: 0;
    }

    .vp-protip-head {
      font-weight: 800;
      font-size: 11px;
      letter-spacing: .08em;
      text-transform: uppercase;
      color: #92400e;
      margin-bottom: 7px;
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .vp-protip-head i {
      color: #f59e0b;
    }

    .vp-spec-tbl {
      width: 100%;
      border-collapse: collapse;
      border-radius: 12px;
      overflow: hidden;
      border: 1px solid var(--border);
    }

    .vp-spec-tbl thead th {
      background: #1A1A1A;
      color: #fff;
      font-size: 10px;
      font-weight: 800;
      letter-spacing: .12em;
      text-transform: uppercase;
      padding: 12px 18px;
      text-align: left;
    }

    .vp-spec-tbl tbody tr {
      border-bottom: 1px solid var(--border);
    }

    .vp-spec-tbl tbody tr:last-child {
      border-bottom: none;
    }

    .vp-spec-tbl tbody tr:nth-child(even) {
      background: var(--cream);
    }

    .vp-spec-tbl td {
      padding: 11px 18px;
      font-size: 13.5px;
      vertical-align: middle;
    }

    .vp-spec-tbl .sk {
      font-weight: 700;
      color: var(--text);
      width: 40%;
    }

    .vp-spec-tbl .sv {
      color: #4a4a4a;
      font-family: var(--mono);
      font-size: 13px;
    }

    /* ── RELATED ── */
    .vp-related {
      padding: 52px 0 72px;
    }

    .vp-rel-title {
      font-family: var(--serif);
      font-size: 1.85rem;
      font-weight: 700;
      color: var(--text);
      margin-bottom: 28px;
    }

    .vp-rel-title span {
      color: var(--primary);
    }

    .vp-rel-card {
      background: var(--white);
      border-radius: 16px;
      border: 1.5px solid var(--border);
      overflow: hidden;
      transition: transform .28s, box-shadow .28s, border-color .28s;
      text-decoration: none;
      color: inherit;
      display: block;
    }

    .vp-rel-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 18px 48px rgba(0, 0, 0, .10);
      border-color: rgba(200, 16, 46, .2);
      color: inherit;
    }

    .vp-rel-img {
      height: 175px;
      background: #F2EDE8;
      overflow: hidden;
    }

    .vp-rel-img img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform .5s;
      display: block;
    }

    .vp-rel-card:hover .vp-rel-img img {
      transform: scale(1.06);
    }

    .vp-rel-body {
      padding: 14px 16px 18px;
    }

    .vp-rel-brand {
      font-size: 10px;
      font-weight: 700;
      letter-spacing: .12em;
      text-transform: uppercase;
      color: var(--primary);
      margin-bottom: 5px;
    }

    .vp-rel-name {
      font-size: 14px;
      font-weight: 700;
      color: var(--text);
      line-height: 1.4;
      margin-bottom: 10px;
    }

    .vp-rel-price {
      font-family: var(--serif);
      font-size: 1.1rem;
      font-weight: 700;
      color: var(--text);
    }

    .vp-rel-price span {
      font-size: 12px;
      font-family: var(--font);
      color: var(--text-muted);
      font-weight: 400;
    }

    /* ANIMATIONS */
    @keyframes fadeUp {
      from {
        opacity: 0;
        transform: translateY(18px)
      }

      to {
        opacity: 1;
        transform: translateY(0)
      }
    }

    .vp-gallery {
      animation: fadeUp .42s .04s ease both;
    }

    .vp-info {
      animation: fadeUp .42s .12s ease both;
    }

    .vp-tabs-wrap {
      animation: fadeUp .42s .22s ease both;
    }

    .vp-rel-card:nth-child(1) {
      animation: fadeUp .38s .06s ease both
    }

    .vp-rel-card:nth-child(2) {
      animation: fadeUp .38s .12s ease both
    }

    .vp-rel-card:nth-child(3) {
      animation: fadeUp .38s .18s ease both
    }

    .vp-rel-card:nth-child(4) {
      animation: fadeUp .38s .24s ease both
    }

    @media(max-width:768px) {
      .vp-gallery {
        position: static;
        margin-bottom: 28px;
      }

      .vp-info {
        padding-left: 0;
      }

      .vp-chips {
        grid-template-columns: 1fr;
      }

      .vp-tab-panel {
        padding: 18px 14px;
      }
    }
  </style>
</head>

<body>

  <!-- NAVBAR -->
  <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top premium-navbar" id="mainNav">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="../Home/home.php">
        <img src="../Images/Hassan Traders logo 2.png" alt="Hassan Traders" class="logo-img" />
      </a>
      <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#nbMain">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="nbMain">
        <ul class="navbar-nav mx-auto align-items-center">
          <li class="nav-item"><a class="nav-link" href="../Home/home.php"><i class="bi bi-house-door-fill me-2"></i>Home</a></li>
          <li class="nav-item"><a class="nav-link current" href="Products.php"><i class="bi bi-grid me-2"></i>Products</a></li>
          <li class="nav-item"><a class="nav-link" href="../About us/aboutus.php"><i class="bi bi-info-circle-fill me-2"></i>About Us</a></li>
          <li class="nav-item"><a class="nav-link" href="../Contact us/contactus.php"><i class="bi bi-telephone-fill me-2"></i>Contact Us</a></li>
        </ul>
        <div class="d-flex align-items-center me-lg-3">
          <a href="../Add to Cart and CheckOut/Cart.php" class="position-relative text-dark text-decoration-none">
            <i class="bi bi-cart3 fs-4"></i>
            <span id="cart-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">0</span>
          </a>
        </div>
        <div class="d-flex mt-3 mt-lg-0">
          <?php if (isset($_SESSION['user_name'])): ?>
            <div class="dropdown">
              <button class="btn btn-premium px-4 rounded-pill dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle me-2"></i><?= htmlspecialchars($_SESSION['user_name']) ?>
              </button>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="../login and signup/logout.php">Logout</a></li>
              </ul>
            </div>
          <?php else: ?>
            <a href="../login and signup/login.php" class="text-decoration-none">
              <button class="btn btn-premium px-5 rounded-pill d-flex align-items-center gap-2">
                <i class="bi bi-person-circle fs-5"></i><span>LOGIN</span>
              </button>
            </a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </nav>

  <div style="padding-top:78px;">
    <div class="container">

      <!-- Breadcrumb -->
      <nav class="vp-crumb">
        <a href="../Home/home.php">Home</a><span class="sep">›</span>
        <a href="Products.php">Products</a><span class="sep">›</span>
        <a href="Products.php"><?= htmlspecialchars($p['Category'] ?? '') ?></a><span class="sep">›</span>
        <span class="cur"><?= htmlspecialchars($p['P_name']) ?></span>
      </nav>

      <!-- ════ MAIN PRODUCT LAYOUT ════ -->
      <section class="vp-main">
        <div class="row g-5">

          <!-- ── LEFT: Image Gallery ── -->
          <div class="col-lg-5">
            <div class="vp-gallery">

              <div class="vp-main-img" id="mainWrap">
                <img id="mainImg"
                  src="../Admin-Panel/uploads/<?= htmlspecialchars($p['P_image']) ?>"
                  alt="<?= htmlspecialchars($p['P_name']) ?>"
                  onerror="this.src='../Images/no-image.png'">
                <?php if ($p['Category']): ?>
                  <span class="vp-badge-cat"><?= htmlspecialchars($p['Category']) ?></span>
                <?php endif; ?>
                <?php if ($discount > 0): ?>
                  <span class="vp-badge-disc"><?= $discount ?>% Off</span>
                <?php endif; ?>
              </div>

              <!-- Thumbnail strip -->
              <div class="vp-thumbs" id="thumbs">
                <div class="vp-thumb active" onclick="switchImg('../Admin-Panel/uploads/<?= htmlspecialchars($p['P_image']) ?>', this)">
                  <img src="../Admin-Panel/uploads/<?= htmlspecialchars($p['P_image']) ?>"
                    onerror="this.src='../Images/no-image.png'" alt="Main">
                </div>
                <!-- Additional placeholder thumbnails if only one image available -->
                <div class="vp-thumb" onclick="switchImg('../Admin-Panel/uploads/<?= htmlspecialchars($p['P_image']) ?>', this)" style="opacity:.5;">
                  <img src="../Admin-Panel/uploads/<?= htmlspecialchars($p['P_image']) ?>"
                    onerror="this.src='../Images/no-image.png'" alt="View 2">
                </div>
                <div class="vp-thumb" onclick="switchImg('../Admin-Panel/uploads/<?= htmlspecialchars($p['P_image']) ?>', this)" style="opacity:.3;">
                  <img src="../Admin-Panel/uploads/<?= htmlspecialchars($p['P_image']) ?>"
                    onerror="this.src='../Images/no-image.png'" alt="View 3">
                </div>
              </div>

              <div class="vp-zoom-hint">
                <i class="bi bi-zoom-in"></i> Hover image to zoom
              </div>
            </div>
          </div>

          <!-- ── RIGHT: Product Info ── -->
          <div class="col-lg-7">
            <div class="vp-info">

              <!-- Back link -->
              <a href="Products.php" class="vp-btn-back mb-3 d-inline-flex">
                <i class="bi bi-arrow-left"></i> Back to Products
              </a>

              <!-- Pills -->
              <div class="vp-pills">
                <?php if ($p['Category']): ?>
                  <span class="vp-pill-cat"><?= htmlspecialchars($p['Category']) ?></span>
                <?php endif; ?>
                <?php if ($p['Brand']): ?>
                  <span class="vp-pill-brand"><?= htmlspecialchars($p['Brand']) ?></span>
                <?php endif; ?>
              </div>

              <!-- Title -->
              <h1 class="vp-title"><?= htmlspecialchars($p['P_name']) ?></h1>

              <!-- Price -->
              <div class="vp-price-row">
                <div class="vp-price">Rs.&nbsp;<?= number_format($p['Price']) ?></div>
                <?php if ($hasOrig): ?>
                  <div class="vp-orig">Rs.&nbsp;<?= number_format($p['orignalprice']) ?></div>
                  <span class="vp-disc-tag"><?= $discount ?>% Off</span>
                <?php endif; ?>
              </div>

              <!-- Stock -->
              <div class="vp-stock <?= $sClass ?>">
                <i class="bi bi-<?= $sClass === 'ok' ? 'check-circle-fill' : ($sClass === 'low' ? 'exclamation-circle-fill' : 'x-circle-fill') ?>"></i>
                <?= $sLabel ?>
              </div>

              <!-- Intro text -->
              <?php if ($desc['intro']): ?>
                <p class="vp-intro"><?= nl2br(htmlspecialchars($desc['intro'])) ?></p>
              <?php endif; ?>

              <hr class="vp-hr">

              <!-- Quick spec chips -->
              <div class="vp-chips">
                <?php if ($p['Brand']): ?>
                  <div class="vp-chip">
                    <div class="vp-chip-lbl">Brand</div>
                    <div class="vp-chip-val"><?= htmlspecialchars($p['Brand']) ?></div>
                  </div>
                <?php endif; ?>
                <?php if ($p['Category']): ?>
                  <div class="vp-chip">
                    <div class="vp-chip-lbl">Category</div>
                    <div class="vp-chip-val"><?= htmlspecialchars($p['Category']) ?></div>
                  </div>
                <?php endif; ?>
                <div class="vp-chip">
                  <div class="vp-chip-lbl">Availability</div>
                  <div class="vp-chip-val"><?= $sLabel ?></div>
                </div>
                <?php if ($hasOrig): ?>
                  <div class="vp-chip">
                    <div class="vp-chip-lbl">You Save</div>
                    <div class="vp-chip-val" style="color:var(--primary);">Rs.&nbsp;<?= number_format((float)$p['orignalprice'] - (float)$p['Price']) ?></div>
                  </div>
                <?php endif; ?>
              </div>

              <hr class="vp-hr">

              <!-- CTA -->
              <div class="vp-cta">
                <?php if ($stock > 0): ?>
                  <button class="vp-btn-cart" onclick="addToCartAndGo(<?= (int)$p['ID'] ?>,'<?= addslashes($p['P_name']) ?>',<?= (float)$p['Price'] ?>,'<?= htmlspecialchars($p['P_image']) ?>')">
                    <i class="bi bi-cart-plus-fill"></i> Add to Cart
                  </button>
                <?php else: ?>
                  <button class="vp-btn-cart" disabled>
                    <i class="bi bi-x-circle"></i> Out of Stock
                  </button>
                <?php endif; ?>
                <a href="https://wa.me/923000687080?text=Hi!%20I'm%20interested%20in%20<?= urlencode($p['P_name']) ?>" class="vp-btn-wa" target="_blank">
                  <i class="bi bi-whatsapp"></i> Enquire
                </a>
              </div>

              <!-- Trust row -->
              <div class="vp-trust">
                <span><i class="bi bi-shield-check-fill"></i>Quality Assured</span>
                <span><i class="bi bi-award-fill"></i>Top Brand</span>
                <span><i class="bi bi-headset"></i>Expert Support</span>
                <span><i class="bi bi-geo-alt-fill"></i>Sargodha, PK</span>
              </div>

            </div>
          </div>

        </div>
      </section>

      <!-- ════ DESCRIPTION TABS ════ -->
      <div class="vp-tabs-wrap">
        <div class="vp-tab-nav">
          <button class="vp-tab-btn active" data-tab="overview">
            <i class="bi bi-grid-1x2 me-1"></i>Overview
          </button>
          <?php if (!empty($desc['features'])): ?>
            <button class="vp-tab-btn" data-tab="features">
              <i class="bi bi-stars me-1"></i>Key Features
            </button>
          <?php endif; ?>
          <?php if (!empty($desc['specs'])): ?>
            <button class="vp-tab-btn" data-tab="techspecs">
              <i class="bi bi-table me-1"></i>Tech Specs
            </button>
          <?php endif; ?>
          <?php if (!empty($desc['ideal'])): ?>
            <button class="vp-tab-btn" data-tab="idealfor">
              <i class="bi bi-lightning-charge-fill me-1"></i>Ideal For
            </button>
          <?php endif; ?>
        </div>

        <!-- Overview -->
        <div class="vp-tab-panel active" id="tab-overview">
          <?php if ($desc['intro']): ?>
            <p style="font-size:14.5px;color:#3d3d3d;line-height:1.84;margin-bottom:28px;">
              <?= nl2br(htmlspecialchars($desc['intro'])) ?>
            </p>
          <?php endif; ?>

          <?php if (!empty($desc['features'])): ?>
            <div class="vp-sec-head"><i class="bi bi-stars"></i>Key Features</div>
            <ul class="vp-feat-ul">
              <?php foreach (array_slice($desc['features'], 0, 5) as $f):
                $fi = splitItem($f); ?>
                <li>
                  <span class="vp-feat-dot"><i class="bi bi-check2"></i></span>
                  <span>
                    <?php if ($fi['label']): ?>
                      <span class="vp-feat-lbl"><?= htmlspecialchars($fi['label']) ?>:</span>
                      <?= htmlspecialchars($fi['body']) ?>
                    <?php else: ?>
                      <?= htmlspecialchars($fi['body']) ?>
                    <?php endif; ?>
                  </span>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>

          <div class="vp-sec-head"><i class="bi bi-info-circle-fill"></i>Specifications</div>
          <table class="vp-spec-tbl">
            <thead>
              <tr>
                <th>Feature</th>
                <th>Specification</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($p['Brand']): ?>
                <tr>
                  <td class="sk">Brand</td>
                  <td class="sv"><?= htmlspecialchars($p['Brand']) ?></td>
                </tr>
              <?php endif; ?>
              <?php if ($p['Category']): ?>
                <tr>
                  <td class="sk">Category</td>
                  <td class="sv"><?= htmlspecialchars($p['Category']) ?></td>
                </tr>
              <?php endif; ?>
              <tr>
                <td class="sk">Selling Price</td>
                <td class="sv">Rs. <?= number_format($p['Price']) ?></td>
              </tr>
              <?php if ($hasOrig): ?>
                <tr>
                  <td class="sk">Original Price</td>
                  <td class="sv">Rs. <?= number_format($p['orignalprice']) ?></td>
                </tr>
                <tr>
                  <td class="sk">Discount</td>
                  <td class="sv"><?= $discount ?>% Off</td>
                </tr>
              <?php endif; ?>
              <tr>
                <td class="sk">Availability</td>
                <td class="sv"><?= $sLabel ?></td>
              </tr>
              <?php if ($p['STATUS']): ?>
                <tr>
                  <td class="sk">Status</td>
                  <td class="sv"><?= ucfirst($p['STATUS']) ?></td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>

          <?php if ($desc['protip']): ?>
            <div class="vp-protip" style="margin-top:24px;">
              <div class="vp-protip-head"><i class="bi bi-lightbulb-fill"></i>Pro Tip</div>
              <p><?= nl2br(htmlspecialchars($desc['protip'])) ?></p>
            </div>
          <?php endif; ?>
        </div>

        <!-- Key Features -->
        <?php if (!empty($desc['features'])): ?>
          <div class="vp-tab-panel" id="tab-features">
            <div class="vp-sec-head"><i class="bi bi-stars"></i>Key Features</div>
            <ul class="vp-feat-ul">
              <?php foreach ($desc['features'] as $f):
                $fi = splitItem($f); ?>
                <li>
                  <span class="vp-feat-dot"><i class="bi bi-check2"></i></span>
                  <span>
                    <?php if ($fi['label']): ?>
                      <span class="vp-feat-lbl"><?= htmlspecialchars($fi['label']) ?>:</span>
                      <?= htmlspecialchars($fi['body']) ?>
                    <?php else: ?>
                      <?= htmlspecialchars($fi['body']) ?>
                    <?php endif; ?>
                  </span>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <!-- Tech Specs -->
        <?php if (!empty($desc['specs'])): ?>
          <div class="vp-tab-panel" id="tab-techspecs">
            <div class="vp-sec-head"><i class="bi bi-table"></i>Technical Specifications</div>
            <table class="vp-spec-tbl">
              <thead>
                <tr>
                  <th>Feature</th>
                  <th>Specification</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($desc['specs'] as $s):
                  $si = splitItem($s); ?>
                  <tr>
                    <td class="sk"><?= $si['label'] ? htmlspecialchars($si['label']) : '—' ?></td>
                    <td class="sv"><?= htmlspecialchars($si['body']) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>

        <!-- Ideal For -->
        <?php if (!empty($desc['ideal'])): ?>
          <div class="vp-tab-panel" id="tab-idealfor">
            <div class="vp-sec-head"><i class="bi bi-lightning-charge-fill"></i>Ideal For</div>
            <ul class="vp-ideal-ul">
              <?php foreach ($desc['ideal'] as $item):
                $ii = splitItem($item); ?>
                <li>
                  <i class="bi bi-arrow-right-short vp-ideal-arr"></i>
                  <span>
                    <?php if ($ii['label']): ?>
                      <span class="vp-ideal-lbl"><?= htmlspecialchars($ii['label']) ?>:</span>
                      <?= htmlspecialchars($ii['body']) ?>
                    <?php else: ?>
                      <?= htmlspecialchars($ii['body']) ?>
                    <?php endif; ?>
                  </span>
                </li>
              <?php endforeach; ?>
            </ul>
            <?php if ($desc['protip']): ?>
              <div class="vp-protip">
                <div class="vp-protip-head"><i class="bi bi-lightbulb-fill"></i>Pro Tip</div>
                <p><?= nl2br(htmlspecialchars($desc['protip'])) ?></p>
              </div>
            <?php endif; ?>
          </div>
        <?php endif; ?>

      </div><!-- /tabs -->

    </div><!-- /container -->

    <!-- ════ RELATED PRODUCTS ════ -->
    <?php if (!empty($related)): ?>
      <section class="vp-related">
        <div class="container">
          <h2 class="vp-rel-title">More in <span><?= htmlspecialchars($p['Category']) ?></span></h2>
          <div class="row g-4">
            <?php foreach ($related as $rel): ?>
              <div class="col-lg-3 col-md-4 col-sm-6">
                <a href="view-products.php?id=<?= (int)$rel['ID'] ?>" class="vp-rel-card">
                  <div class="vp-rel-img">
                    <img src="../Admin-Panel/uploads/<?= htmlspecialchars($rel['P_image']) ?>"
                      alt="<?= htmlspecialchars($rel['P_name']) ?>"
                      onerror="this.src='../Images/no-image.png'">
                  </div>
                  <div class="vp-rel-body">
                    <?php if ($rel['Brand']): ?>
                      <div class="vp-rel-brand"><?= htmlspecialchars($rel['Brand']) ?></div>
                    <?php endif; ?>
                    <div class="vp-rel-name"><?= htmlspecialchars($rel['P_name']) ?></div>
                    <div class="vp-rel-price"><span>Rs.</span> <?= number_format($rel['Price']) ?></div>
                  </div>
                </a>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </section>
    <?php endif; ?>

    <!-- FOOTER -->
    <footer class="footer-section text-white pt-5">
      <div class="container">
        <div class="row gy-4">
          <div class="col-lg-4 col-md-6">
            <h4 class="footer-brand-name">HASSAN TRADERS</h4>
            <p class="footer-about">Premium plumbing and sanitary solutions in Sargodha. Quality &amp; durability you can trust for every build.</p>
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
              <li><a href="Products.php">Products</a></li>
              <li><a href="../Contact us/contactus.php">Support</a></li>
            </ul>
          </div>
          <div class="col-lg-2 col-md-6">
            <h6 class="footer-col-title">Products</h6>
            <ul class="list-unstyled footer-links">
              <li><a href="Products.php">PPR-C Pipes</a></li>
              <li><a href="Products.php">U-PVC Fittings</a></li>
              <li><a href="Products.php">Water Tanks</a></li>
              <li><a href="Products.php">Bath Sets</a></li>
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

  </div><!-- /padding-top wrapper -->

  <!-- WhatsApp float -->
  <a href="https://wa.me/923000687080" class="whatsapp-btn" target="_blank">
    <i class="bi bi-whatsapp"></i>
  </a>

  <script src="../bootstrap-5.3.8-dist/bootstrap-5.3.8-dist/js/bootstrap.bundle.js"></script>
  <script src="../NavBar/navbar.js"></script>
  <script>
    // Navbar scroll
    window.addEventListener("scroll", () => {
      document.getElementById("mainNav").classList.toggle("scrolled", window.scrollY > 20);
    });

    // Cart
    function updateCartIcon() {
      const cart = JSON.parse(localStorage.getItem("cart")) || [];
      const el = document.getElementById("cart-count");
      if (el) el.innerText = cart.length;
    }
    updateCartIcon();

    function addToCartAndGo(id, name, price, image) {
      let cart = JSON.parse(localStorage.getItem("cart")) || [];
      const idx = cart.findIndex(i => i.id === id);
      if (idx > -1) cart[idx].quantity += 1;
      else cart.push({
        id,
        name,
        price: parseFloat(price),
        image,
        quantity: 1
      });
      localStorage.setItem("cart", JSON.stringify(cart));
      updateCartIcon();
      window.location.href = "../Add to Cart and CheckOut/Cart.php";
    }

    // Image switcher
    function switchImg(src, el) {
      document.getElementById("mainImg").src = src;
      document.querySelectorAll(".vp-thumb").forEach(t => {
        t.classList.remove("active");
        t.style.opacity = ".35";
      });
      el.classList.add("active");
      el.style.opacity = "1";
    }

    // Tab switching
    document.querySelectorAll(".vp-tab-btn").forEach(btn => {
      btn.addEventListener("click", () => {
        document.querySelectorAll(".vp-tab-btn").forEach(b => b.classList.remove("active"));
        document.querySelectorAll(".vp-tab-panel").forEach(p => p.classList.remove("active"));
        btn.classList.add("active");
        const panel = document.getElementById("tab-" + btn.dataset.tab);
        if (panel) panel.classList.add("active");
      });
    });
  </script>
</body>

</html>