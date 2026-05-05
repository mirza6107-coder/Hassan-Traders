<?php session_start();
if (!isset($_SESSION['admin_role']) || $_SESSION['admin_role'] !== 'admin') {
    // If the admin session isn't found, send them back to login
    header("Location: ../login and signup/login.php");
    exit();
} ?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>All Products — Hassan Traders Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="all-products.css" />
</head>

<body>

  <!-- ══ SIDEBAR ══ -->
  <?php
  // DB connection for stats
  $conn = mysqli_connect('localhost', 'root', '', 'htss');
  if (!$conn) die("Connection failed: " . mysqli_connect_error());

  $all_result  = mysqli_query($conn, "SELECT * FROM products ORDER BY ID DESC");
  $total       = mysqli_num_rows($all_result);

  $pub_result  = mysqli_query($conn, "SELECT COUNT(*) as c FROM products WHERE Status='published'");
  $pub_row     = mysqli_fetch_assoc($pub_result);
  $published   = $pub_row['c'];

  $low_result  = mysqli_query($conn, "SELECT COUNT(*) as c FROM products WHERE Quantity > 0 AND Quantity <= 10");
  $low_row     = mysqli_fetch_assoc($low_result);
  $low_stock   = $low_row['c'];

  $out_result  = mysqli_query($conn, "SELECT COUNT(*) as c FROM products WHERE Quantity = 0");
  $out_row     = mysqli_fetch_assoc($out_result);
  $out_stock   = $out_row['c'];
  ?>

  <?php include('sidebar.php'); ?>


  <!-- ══ MAIN ══ -->
  <main class="main">

    <!-- Topbar -->
    <div class="topbar">
      <div class="topbar-title">
        <h4>All Products</h4>
        <p>Manage and monitor your entire product catalog</p>
      </div>
      <div class="topbar-actions">
        <button class="btn-outline-custom" onclick="window.print()">
          <i class="bi bi-download"></i> Export
        </button>
        <a href="add-products.html" class="btn-primary-custom">
          <i class="bi bi-plus-circle"></i> Add Product
        </a>
      </div>
    </div>

    <div class="content">

      <!-- Stats Strip -->
      <div class="stats-strip">
        <div class="stat-card">
          <div class="stat-icon red"><i class="bi bi-box-seam-fill"></i></div>
          <div>
            <div class="stat-num"><?php echo $total; ?></div>
            <div class="stat-label">Total Products</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon green"><i class="bi bi-check-circle-fill"></i></div>
          <div>
            <div class="stat-num"><?php echo $published; ?></div>
            <div class="stat-label">Published</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon yellow"><i class="bi bi-exclamation-triangle-fill"></i></div>
          <div>
            <div class="stat-num"><?php echo $low_stock; ?></div>
            <div class="stat-label">Low Stock</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon blue"><i class="bi bi-x-circle-fill"></i></div>
          <div>
            <div class="stat-num"><?php echo $out_stock; ?></div>
            <div class="stat-label">Out of Stock</div>
          </div>
        </div>
      </div>

      <!-- Filter Bar -->
      <div class="filter-bar">
        <div class="search-wrap">
          <i class="bi bi-search"></i>
          <input type="text" id="searchInput" placeholder="Search by name, category, brand..." oninput="filterTable()" />
        </div>
        <select class="filter-select" id="catFilter" onchange="filterTable()">
          <option value="">All Categories</option>
          <option>PPR-C Pipes &amp; Fittings</option>
          <option>U-PVC Pipes &amp; Fittings</option>
          <option>Water Tanks</option>
          <option>Bathroom Cabinets</option>
          <option>Faucets &amp; Mixers</option>
          <option>Shower Sets</option>
        </select>
        <select class="filter-select" id="statusFilter" onchange="filterTable()">
          <option value="">All Status</option>
          <option>Published</option>
          <option>Draft</option>
          <option>Inactive</option>
        </select>
        <div class="filter-results" id="filterResults"><?php echo $total; ?> products</div>
      </div>

      <!-- Table -->
      <div class="table-card">
        <div class="table-responsive">
          <table id="productsTable">
            <thead>
              <tr>
                <th style="width:44px"><input type="checkbox" id="selectAll" onchange="toggleAll(this)" /></th>
                <th>Product</th>
                <th>Category</th>
                <th>Sale Price</th>
                <th>Original Price</th>
                <th>Stock</th>
                <th>Status</th>
                <th style="text-align:right">Actions</th>
              </tr>
            </thead>
            <tbody id="tableBody">
              <?php
              if (mysqli_num_rows($all_result) > 0):
                mysqli_data_seek($all_result, 0);
                while ($row = mysqli_fetch_assoc($all_result)):
                  $status_lc = strtolower($row['STATUS']);
                  $badge_class = 'badge-' . ($status_lc === 'published' ? 'published' : ($status_lc === 'draft' ? 'draft' : 'inactive'));

                  $qty = intval($row['Quantity']);
                  if ($qty === 0) {
                    $stock_class = 'stock-out';
                    $stock_label = 'Out of Stock';
                  } elseif ($qty <= 10) {
                    $stock_class = 'stock-low';
                    $stock_label = $qty . ' (Low)';
                  } else {
                    $stock_class = 'stock-ok';
                    $stock_label = $qty;
                  }
              ?>
                  <tr data-name="<?php echo strtolower($row['P_name']); ?>"
                    data-cat="<?php echo strtolower($row['Category']); ?>"
                    data-status="<?php echo strtolower($row['STATUS']); ?>">
                    <td><input type="checkbox" class="row-check" /></td>
                    <td>
                      <div class="product-cell">
                        <div class="product-thumb">
                          <img src="uploads/<?php echo htmlspecialchars($row['P_image']); ?>"
                            alt="<?php echo htmlspecialchars($row['P_name']); ?>"
                            onerror="this.style.display='none'" />
                        </div>
                        <div>
                          <div class="product-name"><?php echo htmlspecialchars($row['P_name']); ?></div>
                          <div class="product-sku">#PRD-<?php echo $row['ID']; ?></div>
                        </div>
                      </div>
                    </td>
                    <td><span class="cat-pill"><?php echo htmlspecialchars($row['Category']); ?></span></td>
                    <td>
                      <div class="price-sale">Rs. <?php echo number_format($row['Price']); ?></div>
                    </td>
                    <td>
                      <div class="price-orig">Rs. <?php echo number_format($row['orignalprice']); ?></div>
                    </td>
                    <td><span class="<?php echo $stock_class; ?>"><?php echo $stock_label; ?></span></td>
                    <td>
                      <span class="badge-status <?php echo $badge_class; ?>">
                        <?php echo ucfirst($row['STATUS']); ?>
                      </span>
                    </td>
                    <td>
                      <div class="actions-cell">
                        <button class="action-btn btn-view"
                          data-bs-toggle="modal" data-bs-target="#viewModal<?php echo $row['ID']; ?>"
                          title="View">
                          <i class="bi bi-eye"></i>
                        </button>
                        <button class="action-btn btn-edit"
                          data-bs-toggle="modal" data-bs-target="#editModal<?php echo $row['ID']; ?>"
                          title="Edit">
                          <i class="bi bi-pencil"></i>
                        </button>
                        <a href="delete-products.php?id=<?php echo $row['ID']; ?>"
                          class="action-btn btn-delete"
                          onclick="return confirm('Delete this product? This cannot be undone.')"
                          title="Delete">
                          <i class="bi bi-trash3"></i>
                        </a>
                      </div>
                    </td>
                  </tr>
                <?php
                endwhile;
              else:
                ?>
                <tr>
                  <td colspan="8">
                    <div class="empty-state">
                      <i class="bi bi-inbox"></i>
                      <p>No products found. Start by adding your first product.</p>
                      <a href="add-products.html"><i class="bi bi-plus-circle me-1"></i> Add Product</a>
                    </div>
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <?php if ($total > 0): ?>
          <div class="table-footer">
            <span class="result-count">
              Showing <strong id="shownCount"><?php echo $total; ?></strong> of <strong><?php echo $total; ?></strong> products
            </span>
            <span class="result-count">
              Last updated: <?php echo date('d M Y, h:i A'); ?>
            </span>
          </div>
        <?php endif; ?>
      </div>

    </div><!-- /content -->
  </main>

  <!-- ══ MODALS ══ -->
  <?php
  if ($total > 0):
    mysqli_data_seek($all_result, 0);
    while ($row = mysqli_fetch_assoc($all_result)):
      $status_lc   = strtolower($row['STATUS']);
      $badge_class = 'badge-' . ($status_lc === 'published' ? 'published' : ($status_lc === 'draft' ? 'draft' : 'inactive'));
  ?>

      <!-- VIEW MODAL -->
      <div class="modal fade" id="viewModal<?php echo $row['ID']; ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title"><i class="bi bi-eye me-2"></i>Product Details</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <img src="uploads/<?php echo htmlspecialchars($row['P_image']); ?>"
                class="modal-product-img" alt="<?php echo htmlspecialchars($row['P_name']); ?>"
                onerror="this.style.display='none'" />
              <div class="modal-product-name"><?php echo htmlspecialchars($row['P_name']); ?></div>
              <div class="modal-product-sub"><?php echo htmlspecialchars($row['Category']); ?> · <?php echo htmlspecialchars($row['Brand']); ?></div>

              <div class="modal-detail-grid">
                <div class="modal-detail-item">
                  <div class="modal-detail-label">Sale Price</div>
                  <div class="modal-detail-val red">Rs. <?php echo number_format($row['Price']); ?></div>
                </div>
                <div class="modal-detail-item">
                  <div class="modal-detail-label">Original Price</div>
                  <div class="modal-detail-val">Rs. <?php echo number_format($row['orignalprice']); ?></div>
                </div>
                <div class="modal-detail-item">
                  <div class="modal-detail-label">Stock</div>
                  <div class="modal-detail-val"><?php echo $row['Quantity']; ?> units</div>
                </div>
                <div class="modal-detail-item">
                  <div class="modal-detail-label">Status</div>
                  <div class="modal-detail-val">
                    <span class="badge-status <?php echo $badge_class; ?>"><?php echo ucfirst($row['Status']); ?></span>
                  </div>
                </div>
              </div>

              <?php if (!empty($row['Description'])): ?>
                <div class="modal-desc-wrap">
                  <div class="modal-desc-label">Description</div>
                  <div class="modal-desc-val"><?php echo htmlspecialchars($row['Description']); ?></div>
                </div>
              <?php endif; ?>
            </div>
            <div class="modal-footer">
              <button class="btn-modal-cancel" data-bs-dismiss="modal">Close</button>
              <button class="btn-update" data-bs-dismiss="modal"
                data-bs-toggle="modal" data-bs-target="#editModal<?php echo $row['ID']; ?>">
                <i class="bi bi-pencil me-1"></i> Edit Product
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- EDIT MODAL -->
      <div class="modal fade" id="editModal<?php echo $row['ID']; ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
          <div class="modal-content">
            <form action="edit-products.php" method="POST" enctype="multipart/form-data">
              <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <input type="hidden" name="id" value="<?php echo $row['ID']; ?>">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="edit-label">Product Name</label>
                    <input type="text" name="productname" class="edit-input"
                      value="<?php echo htmlspecialchars($row['P_name']); ?>" required />
                  </div>
                  <div class="col-md-6">
                    <label class="edit-label">Category</label>
                    <select name="category" class="edit-input">
                      <?php
                      $cats = ['PPR-C Pipes & Fittings', 'U-PVC Pipes & Fittings', 'Water Tanks', 'Bathroom Cabinets', 'Faucets & Mixers', 'Shower Sets'];
                      foreach ($cats as $c):
                      ?>
                        <option <?php echo ($row['Category'] == $c) ? 'selected' : ''; ?>><?php echo $c; ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label class="edit-label">Brand</label>
                    <input type="text" name="brand" class="edit-input"
                      value="<?php echo htmlspecialchars($row['Brand']); ?>" />
                  </div>
                  <div class="col-md-6">
                    <label class="edit-label">Change Image <span style="font-weight:400;text-transform:none;font-size:11px;color:#6c757d">(Optional)</span></label>
                    <input type="file" name="productimage" class="edit-input" style="padding:7px" />
                    <div style="font-size:11px;color:#6c757d;margin-top:4px">Current: <?php echo htmlspecialchars($row['P_image']); ?></div>
                  </div>
                  <div class="col-12">
                    <label class="edit-label">Description</label>
                    <textarea name="description" class="edit-input" rows="3"><?php echo htmlspecialchars($row['Description']); ?></textarea>
                  </div>
                  <div class="col-md-4">
                    <label class="edit-label">Sale Price (Rs.)</label>
                    <input type="number" name="price" class="edit-input"
                      value="<?php echo $row['Price']; ?>" required min="1" />
                  </div>
                  <div class="col-md-4">
                    <label class="edit-label">Original Price (Rs.)</label>
                    <input type="number" name="orignalprice" class="edit-input"
                      value="<?php echo $row['orignalprice']; ?>" required min="1" />
                  </div>
                  <div class="col-md-4">
                    <label class="edit-label">Stock Quantity</label>
                    <input type="number" name="quantity" class="edit-input"
                      value="<?php echo $row['Quantity']; ?>" required min="0" />
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" name="update_product" class="btn-update">
                  <i class="bi bi-check-circle me-1"></i> Update Product
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

  <?php
    endwhile;
  endif;
  mysqli_close($conn);
  ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    /* ── Client-side filter ── */
    function filterTable() {
      const search = document.getElementById('searchInput').value.toLowerCase();
      const cat = document.getElementById('catFilter').value.toLowerCase();
      const status = document.getElementById('statusFilter').value.toLowerCase();
      const rows = document.querySelectorAll('#tableBody tr[data-name]');
      let visible = 0;

      rows.forEach(function(row) {
        const nameMatch = row.dataset.name.includes(search);
        const catMatch = !cat || row.dataset.cat.includes(cat.replace(/&amp;/g, '&'));
        const statusMatch = !status || row.dataset.status === status;

        if (nameMatch && catMatch && statusMatch) {
          row.style.display = '';
          visible++;
        } else {
          row.style.display = 'none';
        }
      });

      const el = document.getElementById('filterResults');
      const sc = document.getElementById('shownCount');
      if (el) el.textContent = visible + ' product' + (visible !== 1 ? 's' : '');
      if (sc) sc.textContent = visible;
    }

    /* ── Select all ── */
    function toggleAll(master) {
      document.querySelectorAll('.row-check').forEach(function(cb) {
        cb.checked = master.checked;
      });
    }
  </script>
</body>

</html>