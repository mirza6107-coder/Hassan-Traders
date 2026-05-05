<?php
session_start();
if (!isset($_SESSION['admin_role']) || $_SESSION['admin_role'] !== 'admin') {
    // If the admin session isn't found, send them back to login
    header("Location: ../login and signup/login.php");
    exit();
}
ob_start();
ini_set('display_errors', 0);

// ── Handle AJAX requests (Restock + Refresh + Stock Update) ─────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
  header('Content-Type: application/json');
  ob_end_clean();

  $conn = mysqli_connect('localhost', 'root', '', 'htss');
  if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'DB connection failed']);
    exit;
  }

  $action = $_POST['action'];

  if ($action === 'restock') {
    // Existing restock logic
    $id  = (int)$_POST['id'];
    $qty = (int)$_POST['qty'];
    if ($id < 1 || $qty < 1) {
      echo json_encode(['success' => false, 'message' => 'Invalid ID or quantity']);
      mysqli_close($conn);
      exit;
    }

    $cols = [];
    $r = mysqli_query($conn, "SHOW COLUMNS FROM products");
    while ($c = mysqli_fetch_assoc($r)) $cols[] = $c['Field'];

    $qtyCol = in_array('Quantity', $cols) ? 'Quantity' : (in_array('stock', $cols) ? 'stock' : 'Quantity');
    $idCol  = in_array('ID', $cols) ? 'ID' : 'id';

    $ok = mysqli_query($conn, "UPDATE products SET `$qtyCol` = `$qtyCol` + $qty WHERE `$idCol` = $id");

    if ($ok) {
      $row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT `$qtyCol` as stock FROM products WHERE `$idCol` = $id"));
      echo json_encode(['success' => true, 'new_stock' => (int)$row['stock']]);
    } else {
      echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
    }
  } elseif ($action === 'refresh') {
    // Return fresh stock data for all products
    $cols = [];
    $r = mysqli_query($conn, "SHOW COLUMNS FROM products");
    while ($c = mysqli_fetch_assoc($r)) $cols[] = $c['Field'];

    $idCol  = in_array('ID', $cols) ? 'ID' : 'id';
    $nameCol = in_array('P_name', $cols) ? 'P_name' : (in_array('name', $cols) ? 'name' : 'P_name');
    $qtyCol = in_array('Quantity', $cols) ? 'Quantity' : (in_array('stock', $cols) ? 'stock' : 'Quantity');

    $result = mysqli_query(
      $conn,
      "SELECT `$idCol` as id, `$nameCol` as name, `$qtyCol` as stock 
             FROM products ORDER BY `$idCol` DESC"
    );

    $products = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $products[] = [
        'id'    => (int)$row['id'],
        'name'  => $row['name'] ?? '—',
        'stock' => (int)$row['stock']
      ];
    }

    echo json_encode(['success' => true, 'products' => $products]);
  }

  mysqli_close($conn);
  exit;
}

// ── Load products for initial page render ─────────────────────────────────────
$conn = mysqli_connect('localhost', 'root', '', 'htss');
$products  = [];
$db_error  = '';

if (!$conn) {
  $db_error = 'Database connection failed: ' . mysqli_connect_error();
} else {
  $cols = [];
  $r = mysqli_query($conn, "SHOW COLUMNS FROM products");
  while ($c = mysqli_fetch_assoc($r)) $cols[] = $c['Field'];

  $idCol    = in_array('ID', $cols) ? 'ID' : 'id';
  $nameCol  = in_array('P_name', $cols) ? 'P_name' : (in_array('name', $cols) ? 'name' : 'P_name');
  $catCol   = in_array('Category', $cols) ? 'Category' : (in_array('category', $cols) ? 'category' : 'Category');
  $brandCol = in_array('Brand', $cols) ? 'Brand' : (in_array('brand', $cols) ? 'brand' : 'Brand');
  $qtyCol   = in_array('Quantity', $cols) ? 'Quantity' : (in_array('stock', $cols) ? 'stock' : 'Quantity');
  $priceCol = in_array('Price', $cols) ? 'Price' : (in_array('price', $cols) ? 'price' : 'Price');
  $origPriceCol = in_array('orignalprice', $cols) ? 'orignalprice' : (in_array('orignalprice', $cols) ? 'orignalprice' : 'orignalprice');

  $result = mysqli_query(
    $conn,
    "SELECT `$idCol` as id, `$nameCol` as name, `$catCol` as category,
            `$brandCol` as brand, `$qtyCol` as stock, `$priceCol` as price, 
            `$origPriceCol` as orignalprice
     FROM products ORDER BY `$idCol` DESC"
  );

  if (!$result) {
    $db_error = 'Query failed: ' . mysqli_error($conn);
  } else {
    while ($row = mysqli_fetch_assoc($result)) {
      $products[] = [
        'id'       => (int)$row['id'],
        'name'     => $row['name'] ?? '—',
        'category' => $row['category'] ?? '—',
        'brand'    => $row['brand'] ?? 'Local',
        'stock'    => (int)$row['stock'],
        'price'    => (float)$row['price'],
        'orignalprice' => (float)($row['orignalprice'] ?? 0), 
      ];
    }
  }
  mysqli_close($conn);
}

// Stats
$total   = count($products);
$instock = count(array_filter($products, fn($p) => $p['stock'] >= 20));
$low     = count(array_filter($products, fn($p) => $p['stock'] > 0 && $p['stock'] < 20));
$out     = count(array_filter($products, fn($p) => $p['stock'] === 0));

ob_end_clean();
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Inventory - Hassan Traders Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
  <link rel="stylesheet" href="inventory.css">
</head>

<body>

  <?php include('sidebar.php'); ?>

  <main class="main">
    <div class="topbar">
      <div class="topbar-title">
        <h4>Inventory Management</h4>
        <p>Track stock levels across all products</p>
      </div>
      <div>
        <button class="btn-primary-custom" onclick="exportInventory()">
          <i class="bi bi-download"></i> Export Report
        </button>
        <button class="btn-primary-custom" onclick="refreshInventory()" style="margin-left: 8px;">
          <i class="bi bi-arrow-repeat"></i> Refresh Stock
        </button>
      </div>
    </div>

    <div class="content">

      <?php if ($db_error): ?>
        <div class="error-banner"><i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($db_error) ?></div>
      <?php endif; ?>

      <div class="alert-bar <?= ($out || $low) ? 'alert-warn' : 'alert-ok' ?>" id="alertBar">
        <i class="bi bi-<?= ($out || $low) ? 'exclamation-triangle-fill' : 'check-circle-fill' ?>" style="font-size:18px"></i>
        <span id="alertText">
          <?php if ($out || $low): ?>
            <strong>Attention:</strong>
            <?= $out ? "$out product(s) are out of stock. " : '' ?>
            <?= $low ? "$low product(s) are running low (&lt; 20 units). " : '' ?>
            <a href="add-products.html" style="color:var(--primary);font-weight:600;">Add stock →</a>
          <?php else: ?>
            All products are well stocked.
          <?php endif; ?>
        </span>
      </div>

      <div class="stat-row">
        <div class="stat-card">
          <div class="stat-val" id="statTotal"><?= $total ?></div>
          <div class="stat-label">Total Products</div>
        </div>
        <div class="stat-card">
          <div class="stat-val green" id="statInStock"><?= $instock ?></div>
          <div class="stat-label">In Stock</div>
        </div>
        <div class="stat-card">
          <div class="stat-val orange" id="statLow"><?= $low ?></div>
          <div class="stat-label">Low Stock (&lt;20)</div>
        </div>
        <div class="stat-card">
          <div class="stat-val" id="statOut"><?= $out ?></div>
          <div class="stat-label">Out of Stock</div>
        </div>
      </div>

      <div class="filter-bar">
        <div class="search-box">
          <i class="bi bi-search" style="color:var(--text-muted)"></i>
          <input type="text" id="searchInput" placeholder="Search product name or category…" />
        </div>
        <select id="stockFilter" class="filter-select">
          <option value="">All Stock Status</option>
          <option value="ok">In Stock</option>
          <option value="low">Low Stock</option>
          <option value="out">Out of Stock</option>
        </select>
        <select id="catFilter" class="filter-select">
          <option value="">All Categories</option>
          <?php
          $cats = array_unique(array_column($products, 'category'));
          foreach ($cats as $cat) {
            echo '<option>' . htmlspecialchars($cat) . '</option>';
          }
          ?>
        </select>
      </div>

      <div class="card-panel">
        <div class="table-responsive">
          <table>
            <thead>
              <tr>
                <th>Product</th>
                <th>Category</th>
                <th>Brand</th>
                <th style="min-width:180px">Stock Level</th>
                <th>Unit Price</th>
                <th>Unit Original Price</th>
                <th>Stock Value</th>
                <th style="text-align:right">Action</th>
              </tr>
            </thead>
            <tbody id="invBody">
              <?php if (empty($products)): ?>
                <tr>
                  <td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted);">
                    No products found.
                  </td>
                </tr>
              <?php else: ?>
                <?php foreach ($products as $p):
                  $stock     = $p['stock'];
                  $max       = 500;
                  $pct       = min(100, round(($stock / $max) * 100));
                  $fillClass = $stock === 0 ? 'fill-red' : ($stock < 20 ? 'fill-orange' : 'fill-green');
                  $valClass  = $stock === 0 ? 'stock-out' : ($stock < 20 ? 'stock-low' : 'stock-ok');
                  $label     = $stock === 0 ? 'Out of Stock' : ($stock < 20 ? $stock . ' (Low)' : $stock);
                  $rowClass  = $stock === 0 ? 'out-row' : ($stock < 20 ? 'low-row' : '');
                  $value     = number_format($p['price'] * $stock);
                ?>
                  <tr class="<?= $rowClass ?>" id="row-<?= $p['id'] ?>"
                    data-id="<?= $p['id'] ?>"
                    data-name="<?= htmlspecialchars($p['name']) ?>"
                    data-cat="<?= htmlspecialchars($p['category']) ?>"
                    data-stock="<?= $stock ?>"
                    data-price="<?= $p['price'] ?>">
                    <td><strong><?= htmlspecialchars($p['name']) ?></strong></td>
                    <td style="font-size:13px;color:var(--text-muted)"><?= htmlspecialchars($p['category']) ?></td>
                    <td style="font-size:13px"><?= htmlspecialchars($p['brand']) ?></td>
                    <td>
                      <div class="stock-bar-wrap">
                        <div class="stock-bar">
                          <div class="stock-bar-fill <?= $fillClass ?>" style="width:<?= $pct ?>%"></div>
                        </div>
                        <span class="stock-val <?= $valClass ?>" id="stockval-<?= $p['id'] ?>"><?= $label ?></span>
                      </div>
                    </td>
                    <td>Rs. <?= number_format($p['price']) ?></td>
                    <td>Rs. <?= number_format($p['orignalprice'] ?? 0) ?></td>
                    <td style="font-weight:600" id="stockvalue-<?= $p['id'] ?>">Rs. <?= $value ?></td>
                    <td style="text-align:right">
                      <button class="action-btn btn-restock" onclick="showRestock(<?= $p['id'] ?>)">
                        <i class="bi bi-plus-circle"></i> Restock
                      </button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
        <div class="table-footer">
          <span class="result-count" id="resultCount"><?= $total ?> product<?= $total !== 1 ? 's' : '' ?></span>
        </div>
      </div>
    </div>
  </main>

  <!-- Restock Modal (unchanged) -->
  <div class="modal-overlay" id="restockModal">
    <div class="modal-box">
      <div class="modal-header">
        <h5>Restock Product</h5>
        <button class="modal-close" onclick="closeModal()">×</button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="restockId" />
        <div class="form-group">
          <label class="form-label">Product</label>
          <div id="restockName" style="font-weight:600;font-size:15px;padding:8px 0"></div>
        </div>
        <div class="form-group">
          <label class="form-label">Current Stock</label>
          <div id="restockCurrent" style="font-size:14px;color:var(--text-muted);padding:4px 0"></div>
        </div>
        <div class="form-group">
          <label class="form-label">Add Quantity *</label>
          <input type="number" id="restockQty" class="form-control" min="1" placeholder="e.g. 100" />
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn-outline-custom" onclick="closeModal()">Cancel</button>
        <button class="btn-primary-custom" onclick="applyRestock()">
          <i class="bi bi-plus-circle"></i> Add Stock
        </button>
      </div>
    </div>
  </div>

  <div class="toast-msg" id="toastMsg"></div>

  <script>
    var products = [];
    document.querySelectorAll('tbody tr[data-id]').forEach(function(row) {
      products.push({
        id: parseInt(row.dataset.id),
        name: row.dataset.name,
        category: row.dataset.cat,
        stock: parseInt(row.dataset.stock),
        price: parseFloat(row.dataset.price),
        original_price: parseFloat(row.querySelector('td:nth-child(6)').textContent.replace('Rs. ', '').replace(/,/g, ''))
      });
    });

    // Event listeners
    document.getElementById('searchInput').addEventListener('input', applyFilters);
    document.getElementById('stockFilter').addEventListener('change', applyFilters);
    document.getElementById('catFilter').addEventListener('change', applyFilters);

    function applyFilters() {
      var q = document.getElementById('searchInput').value.toLowerCase().trim();
      var stk = document.getElementById('stockFilter').value;
      var cat = document.getElementById('catFilter').value;
      var shown = 0;

      products.forEach(function(p) {
        var row = document.getElementById('row-' + p.id);
        if (!row) return;
        var matchQ = !q || p.name.toLowerCase().includes(q) || p.category.toLowerCase().includes(q);
        var matchCat = !cat || p.category === cat;
        var matchStk = !stk ||
          (stk === 'out' && p.stock === 0) ||
          (stk === 'low' && p.stock > 0 && p.stock < 20) ||
          (stk === 'ok' && p.stock >= 20);
        var visible = matchQ && matchCat && matchStk;
        row.style.display = visible ? '' : 'none';
        if (visible) shown++;
      });
      document.getElementById('resultCount').textContent = 'Showing ' + shown + ' of ' + products.length + ' products';
    }

    function showRestock(id) {
      var p = products.find(x => x.id === id);
      if (!p) return;
      document.getElementById('restockId').value = id;
      document.getElementById('restockName').textContent = p.name;
      document.getElementById('restockCurrent').textContent = p.stock + ' units currently in stock';
      document.getElementById('restockQty').value = '';
      document.getElementById('restockModal').classList.add('show');
    }

    function closeModal() {
      document.getElementById('restockModal').classList.remove('show');
    }

    function applyRestock() {
      // ... your existing restock code (unchanged) ...
      var id = parseInt(document.getElementById('restockId').value);
      var qty = parseInt(document.getElementById('restockQty').value);

      if (!qty || qty < 1) {
        alert('Please enter a valid quantity');
        return;
      }

      var btn = document.querySelector('#restockModal .btn-primary-custom');
      btn.disabled = true;
      btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Saving…';

      fetch('inventory.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: 'action=restock&id=' + id + '&qty=' + qty
        })
        .then(r => r.json())
        .then(res => {
          if (res.success) {
            var p = products.find(x => x.id === id);
            if (p) p.stock = res.new_stock;
            updateRow(id, res.new_stock);
            updateStats();
            closeModal();
            showToast('Stock updated successfully! (' + qty + ' units added)');
          } else {
            showToast('Error: ' + (res.message || 'Failed'), true);
          }
        })
        .finally(() => {
          btn.disabled = false;
          btn.innerHTML = '<i class="bi bi-plus-circle"></i> Add Stock';
        });
    }

    function updateRow(id, newStock) {
      var row = document.getElementById('row-' + id);
      if (!row) return;

      row.dataset.stock = newStock;
      var p = products.find(x => x.id === id);
      if (!p) return;

      var pct = Math.min(100, Math.round((newStock / 500) * 100));
      var fill = row.querySelector('.stock-bar-fill');
      if (fill) {
        fill.className = 'stock-bar-fill ' + (newStock === 0 ? 'fill-red' : newStock < 20 ? 'fill-orange' : 'fill-green');
        fill.style.width = pct + '%';
      }

      var val = document.getElementById('stockval-' + id);
      if (val) {
        val.className = 'stock-val ' + (newStock === 0 ? 'stock-out' : newStock < 20 ? 'stock-low' : 'stock-ok');
        val.textContent = newStock === 0 ? 'Out of Stock' : (newStock < 20 ? newStock + ' (Low)' : newStock);
      }

      var valueCell = document.getElementById('stockvalue-' + id);
      if (valueCell) valueCell.textContent = 'Rs. ' + (p.price * newStock).toLocaleString('en-PK');
    }

    function refreshInventory() {
      fetch('inventory.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: 'action=refresh'
        })
        .then(r => r.json())
        .then(res => {
          if (res.success) {
            res.products.forEach(p => {
              var row = document.getElementById('row-' + p.id);
              if (row) {
                updateRow(p.id, p.stock);
                var prod = products.find(x => x.id === p.id);
                if (prod) prod.stock = p.stock;
              }
            });
            updateStats();
            showToast('Inventory refreshed successfully!');
          }
        })
        .catch(() => {
          showToast('Failed to refresh inventory', true);
        });
    }

    function updateStats() {
      var instock = products.filter(p => p.stock >= 20).length;
      var low = products.filter(p => p.stock > 0 && p.stock < 20).length;
      var out = products.filter(p => p.stock === 0).length;

      document.getElementById('statInStock').textContent = instock;
      document.getElementById('statLow').textContent = low;
      document.getElementById('statOut').textContent = out;

      var bar = document.getElementById('alertBar');
      var txt = document.getElementById('alertText');
      if (out || low) {
        bar.className = 'alert-bar alert-warn';
        txt.innerHTML = '<strong>Attention:</strong> ' +
          (out ? out + ' product(s) are out of stock. ' : '') +
          (low ? low + ' product(s) are running low (&lt; 20 units). ' : '') +
          '<a href="add-products.html" style="color:var(--primary);font-weight:600;">Add stock →</a>';
      } else {
        bar.className = 'alert-bar alert-ok';
        txt.textContent = 'All products are well stocked.';
      }
    }

    function exportInventory() {
      var rows = [
        ['Product', 'Category', 'Stock', 'Unit Price (Rs)', 'Stock Value (Rs)']
      ];
      products.forEach(p => {
        rows.push(['"' + p.name + '"', '"' + p.category + '"', p.stock, p.price, p.price * p.stock]);
      });
      var csv = rows.map(r => r.join(',')).join('\n');
      var a = document.createElement('a');
      a.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csv);
      a.download = 'hassan-traders-inventory.csv';
      a.click();
    }

    function showToast(msg, isError = false) {
      var t = document.getElementById('toastMsg');
      t.textContent = msg;
      t.style.background = isError ? '#991b1b' : '#1a1a2e';
      t.classList.add('show');
      setTimeout(() => t.classList.remove('show'), 3500);
    }

    // Modal click outside to close
    document.getElementById('restockModal').addEventListener('click', function(e) {
      if (e.target === this) closeModal();
    });

    window.onload = function() {
      applyFilters();
    };
    // Auto refresh inventory when coming back from orders page
    window.addEventListener('focus', function() {
      // Optional: refresh only if more than 5 seconds passed since last load
      refreshInventory();
    });
  </script>
</body>

</html>