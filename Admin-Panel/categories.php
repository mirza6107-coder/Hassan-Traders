<?php session_start(); ?>
<?php
ob_start();
ini_set('display_errors', 1);

// ── Handle AJAX actions (POST) ────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    ob_end_clean();

    $conn = mysqli_connect('localhost', 'root', '', 'htss');
    if (!$conn) {
        echo json_encode(['success' => false, 'message' => 'DB connection failed: ' . mysqli_connect_error()]);
        exit;
    }

    // Ensure categories table exists
    mysqli_query($conn, "CREATE TABLE IF NOT EXISTS categories (
        id          INT AUTO_INCREMENT PRIMARY KEY,
        name        VARCHAR(150) NOT NULL UNIQUE,
        description TEXT,
        status      ENUM('active','inactive') NOT NULL DEFAULT 'active',
        created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $action = $_POST['action'];

    if ($action === 'add') {
        $name   = trim($_POST['name']   ?? '');
        $desc   = trim($_POST['desc']   ?? '');
        $status = in_array($_POST['status'] ?? '', ['active','inactive']) ? $_POST['status'] : 'active';
        if (!$name) { echo json_encode(['success' => false, 'message' => 'Name is required']); exit; }
        $name = mysqli_real_escape_string($conn, $name);
        $desc = mysqli_real_escape_string($conn, $desc);
        $ok = mysqli_query($conn, "INSERT INTO categories (name, description, status) VALUES ('$name','$desc','$status')");
        if ($ok) {
            echo json_encode(['success' => true, 'id' => mysqli_insert_id($conn)]);
        } else {
            $err = mysqli_error($conn);
            echo json_encode(['success' => false, 'message' => strpos($err, 'Duplicate') !== false ? 'A category with that name already exists.' : $err]);
        }

    } elseif ($action === 'edit') {
        $id     = (int)($_POST['id'] ?? 0);
        $name   = trim($_POST['name']   ?? '');
        $desc   = trim($_POST['desc']   ?? '');
        $status = in_array($_POST['status'] ?? '', ['active','inactive']) ? $_POST['status'] : 'active';
        if (!$id || !$name) { echo json_encode(['success' => false, 'message' => 'ID and Name are required']); exit; }

        // Also update products table so Category column stays in sync
        $oldRow = mysqli_fetch_assoc(mysqli_query($conn, "SELECT name FROM categories WHERE id = $id"));
        $oldName = $oldRow ? mysqli_real_escape_string($conn, $oldRow['name']) : '';
        $name = mysqli_real_escape_string($conn, $name);
        $desc = mysqli_real_escape_string($conn, $desc);

        // Detect product category column name
        $prodCols = [];
        $r = mysqli_query($conn, "SHOW COLUMNS FROM products");
        if ($r) while ($c = mysqli_fetch_assoc($r)) $prodCols[] = $c['Field'];
        $catCol = in_array('Category', $prodCols) ? 'Category' : (in_array('category', $prodCols) ? 'category' : null);
        if ($catCol && $oldName && $oldName !== $name) {
            mysqli_query($conn, "UPDATE products SET `$catCol` = '$name' WHERE `$catCol` = '$oldName'");
        }

        $ok = mysqli_query($conn, "UPDATE categories SET name='$name', description='$desc', status='$status' WHERE id=$id");
        echo json_encode(['success' => (bool)$ok, 'message' => $ok ? '' : mysqli_error($conn)]);

    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) { echo json_encode(['success' => false, 'message' => 'Invalid ID']); exit; }
        $ok = mysqli_query($conn, "DELETE FROM categories WHERE id = $id");
        echo json_encode(['success' => (bool)$ok, 'message' => $ok ? '' : mysqli_error($conn)]);

    } elseif ($action === 'toggle') {
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) { echo json_encode(['success' => false, 'message' => 'Invalid ID']); exit; }
        $ok = mysqli_query($conn, "UPDATE categories SET status = IF(status='active','inactive','active') WHERE id=$id");
        $row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT status FROM categories WHERE id=$id"));
        echo json_encode(['success' => (bool)$ok, 'new_status' => $row['status'] ?? 'active']);

    } else {
        echo json_encode(['success' => false, 'message' => 'Unknown action']);
    }

    mysqli_close($conn);
    exit;
}

// ── Load data for page render ─────────────────────────────────────────────────
$conn      = mysqli_connect('localhost', 'root', '', 'htss');
$categories = [];
$db_error   = '';
$totalProds = 0;

if (!$conn) {
    $db_error = 'Database connection failed: ' . mysqli_connect_error();
} else {
    // Ensure table exists
    mysqli_query($conn, "CREATE TABLE IF NOT EXISTS categories (
        id          INT AUTO_INCREMENT PRIMARY KEY,
        name        VARCHAR(150) NOT NULL UNIQUE,
        description TEXT,
        status      ENUM('active','inactive') NOT NULL DEFAULT 'active',
        created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Detect product category column
    $prodCols = [];
    $r = mysqli_query($conn, "SHOW COLUMNS FROM products");
    if ($r) while ($c = mysqli_fetch_assoc($r)) $prodCols[] = $c['Field'];
    $catCol = in_array('Category', $prodCols) ? 'Category' : (in_array('category', $prodCols) ? 'category' : null);

    // Load categories + live product count per category
    if ($catCol) {
        $sql = "SELECT c.id, c.name, c.description, c.status,
                    COUNT(p.ID) as product_count
                FROM categories c
                LEFT JOIN products p ON p.`$catCol` = c.name
                GROUP BY c.id
                ORDER BY c.name ASC";
    } else {
        $sql = "SELECT id, name, description, status, 0 as product_count FROM categories ORDER BY name ASC";
    }

    $result = mysqli_query($conn, $sql);
    if (!$result) {
        $db_error = 'Query failed: ' . mysqli_error($conn);
    } else {
        while ($row = mysqli_fetch_assoc($result)) {
            $categories[] = $row;
        }
    }

    // Total products
    $pr = mysqli_query($conn, "SELECT COUNT(*) as n FROM products");
    if ($pr) $totalProds = (int)mysqli_fetch_assoc($pr)['n'];

    mysqli_close($conn);
}

$totalCats  = count($categories);
$activeCats = count(array_filter($categories, fn($c) => $c['status'] === 'active'));

ob_end_clean();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Categories - Hassan Traders Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>
  <link rel="stylesheet" href="categories.css">
</head>
<body>

<!-- ══ SIDEBAR ══ -->
<?php include('sidebar.php'); ?>

<!-- ══ MAIN ══ -->
<main class="main">
  <div class="topbar">
    <div class="topbar-title">
      <h4>Categories</h4>
      <p>Manage product categories</p>
    </div>
    <button class="btn-primary-custom" onclick="showModal()">
      <i class="bi bi-plus-circle"></i> Add Category
    </button>
  </div>

  <div class="content">

    <?php if ($db_error): ?>
      <div class="error-banner"><i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($db_error) ?></div>
    <?php endif; ?>

    <!-- Stat Cards -->
    <div class="stat-row">
      <div class="stat-card">
        <div class="stat-val" id="statTotal"><?= $totalCats ?></div>
        <div class="stat-label">Total Categories</div>
      </div>
      <div class="stat-card">
        <div class="stat-val green" id="statActive"><?= $activeCats ?></div>
        <div class="stat-label">Active Categories</div>
      </div>
      <div class="stat-card">
        <div class="stat-val" id="statProds"><?= $totalProds ?></div>
        <div class="stat-label">Total Products</div>
      </div>
    </div>

    <!-- Categories Table -->
    <div class="card-panel">
      <div class="card-panel-header">
        <h5>All Categories</h5>
        <span style="font-size:13px;color:var(--text-muted)" id="catCount">
          <?= $totalCats ?> categor<?= $totalCats !== 1 ? 'ies' : 'y' ?>
        </span>
      </div>
      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>Category Name</th>
              <th>Description</th>
              <th>Products</th>
              <th>Status</th>
              <th style="text-align:right">Actions</th>
            </tr>
          </thead>
          <tbody id="catBody">
            <?php if (empty($categories)): ?>
              <tr><td colspan="5">
                <div class="empty-state">
                  <i class="bi bi-tags"></i>
                  No categories yet. Click <strong>Add Category</strong> to create your first one.
                </div>
              </td></tr>
            <?php else: ?>
              <?php foreach ($categories as $cat): ?>
                <tr id="row-<?= $cat['id'] ?>"
                    data-id="<?= $cat['id'] ?>"
                    data-name="<?= htmlspecialchars($cat['name']) ?>"
                    data-desc="<?= htmlspecialchars($cat['description'] ?? '') ?>"
                    data-status="<?= $cat['status'] ?>">
                  <td><strong><?= htmlspecialchars($cat['name']) ?></strong></td>
                  <td style="color:var(--text-muted);font-size:13px"><?= htmlspecialchars($cat['description'] ?: '—') ?></td>
                  <td><span class="prod-badge" id="pcount-<?= $cat['id'] ?>"><?= (int)$cat['product_count'] ?></span></td>
                  <td>
                    <span class="<?= $cat['status'] === 'active' ? 'badge-active' : 'badge-inactive' ?>"
                          id="status-badge-<?= $cat['id'] ?>">
                      <?= $cat['status'] === 'active' ? 'Active' : 'Inactive' ?>
                    </span>
                  </td>
                  <td style="text-align:right;white-space:nowrap">
                    <button class="action-btn btn-edit" onclick="editCat(<?= $cat['id'] ?>)" title="Edit" style="margin-right:4px">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <button class="action-btn btn-toggle" onclick="toggleCat(<?= $cat['id'] ?>)" title="Toggle Status" style="margin-right:4px">
                      <i class="bi bi-toggle-<?= $cat['status'] === 'active' ? 'on' : 'off' ?>"></i>
                    </button>
                    <button class="action-btn btn-delete" onclick="deleteCat(<?= $cat['id'] ?>)" title="Delete">
                      <i class="bi bi-trash"></i>
                    </button>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</main>

<!-- ══ ADD / EDIT MODAL ══ -->
<div class="modal-overlay" id="catModal">
  <div class="modal-box">
    <div class="modal-header">
      <h5 id="modalTitle">Add Category</h5>
      <button class="modal-close" onclick="closeModal()">×</button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="editId"/>
      <div class="form-group">
        <label class="form-label">Category Name <span>*</span></label>
        <input type="text" id="catName" class="form-control" placeholder="e.g. PPR-C Pipes &amp; Fittings"/>
      </div>
      <div class="form-group">
        <label class="form-label">Description</label>
        <textarea id="catDesc" class="form-control" rows="3" placeholder="Brief description of this category…"></textarea>
      </div>
      <div class="form-group">
        <label class="form-label">Status</label>
        <select id="catStatus" class="form-control">
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
        </select>
      </div>
      <div id="modalError" style="display:none;background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:10px 14px;color:#991b1b;font-size:13px;margin-top:4px;"></div>
    </div>
    <div class="modal-footer">
      <button class="btn-outline-custom" onclick="closeModal()">Cancel</button>
      <button class="btn-primary-custom" id="saveBtn" onclick="saveCategory()">
        <i class="bi bi-check-circle"></i> Save Category
      </button>
    </div>
  </div>
</div>

<!-- ══ TOAST ══ -->
<div class="toast-msg" id="toastMsg"></div>

<script>
  // ── Build JS array from PHP rows ─────────────────────────────────────────────
  var categories = [];
  document.querySelectorAll('tbody tr[data-id]').forEach(function(row) {
    categories.push({
      id:     parseInt(row.dataset.id),
      name:   row.dataset.name,
      desc:   row.dataset.desc,
      status: row.dataset.status
    });
  });

  var editingId = null;

  // ── Modal helpers ────────────────────────────────────────────────────────────
  function showModal(id) {
    editingId = id || null;
    document.getElementById('modalTitle').textContent = id ? 'Edit Category' : 'Add Category';
    document.getElementById('modalError').style.display = 'none';

    if (id) {
      var c = categories.find(function(x) { return x.id === id; });
      document.getElementById('editId').value    = c.id;
      document.getElementById('catName').value   = c.name;
      document.getElementById('catDesc').value   = c.desc;
      document.getElementById('catStatus').value = c.status;
    } else {
      document.getElementById('editId').value    = '';
      document.getElementById('catName').value   = '';
      document.getElementById('catDesc').value   = '';
      document.getElementById('catStatus').value = 'active';
    }
    document.getElementById('catModal').classList.add('show');
    setTimeout(function() { document.getElementById('catName').focus(); }, 100);
  }

  function closeModal() {
    document.getElementById('catModal').classList.remove('show');
    editingId = null;
  }
  document.getElementById('catModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
  });

  function editCat(id) { showModal(id); }

  // ── Save (add or edit) ───────────────────────────────────────────────────────
  function saveCategory() {
    var name   = document.getElementById('catName').value.trim();
    var desc   = document.getElementById('catDesc').value.trim();
    var status = document.getElementById('catStatus').value;
    var errEl  = document.getElementById('modalError');

    if (!name) {
      errEl.textContent = 'Category name is required.';
      errEl.style.display = 'block';
      document.getElementById('catName').focus();
      return;
    }
    errEl.style.display = 'none';

    var btn = document.getElementById('saveBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Saving…';

    var body = 'action=' + (editingId ? 'edit' : 'add') +
      '&name=' + encodeURIComponent(name) +
      '&desc=' + encodeURIComponent(desc) +
      '&status=' + status +
      (editingId ? '&id=' + editingId : '');

    fetch('categories.php', {
      method:  'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body:    body
    })
    .then(function(r) { return r.json(); })
    .then(function(res) {
      if (res.success) {
        closeModal();
        if (editingId) {
          // Update existing row in DOM
          var c = categories.find(function(x) { return x.id === editingId; });
          if (c) { c.name = name; c.desc = desc; c.status = status; }
          var row = document.getElementById('row-' + editingId);
          if (row) {
            row.cells[0].innerHTML = '<strong>' + escHtml(name) + '</strong>';
            row.cells[1].textContent = desc || '—';
            var badge = document.getElementById('status-badge-' + editingId);
            badge.className  = status === 'active' ? 'badge-active' : 'badge-inactive';
            badge.textContent = status === 'active' ? 'Active' : 'Inactive';
            row.dataset.name   = name;
            row.dataset.desc   = desc;
            row.dataset.status = status;
          }
          showToast('Category updated successfully.');
        } else {
          // Add new row to DOM
          categories.push({ id: res.id, name: name, desc: desc, status: status });
          addRow({ id: res.id, name: name, description: desc, status: status, product_count: 0 });
          showToast('Category "' + name + '" added successfully.');
        }
        updateStats();
      } else {
        errEl.textContent = res.message || 'An error occurred.';
        errEl.style.display = 'block';
      }
    })
    .catch(function(err) {
      errEl.textContent = 'Network error: ' + err.message;
      errEl.style.display = 'block';
    })
    .finally(function() {
      btn.disabled = false;
      btn.innerHTML = '<i class="bi bi-check-circle"></i> Save Category';
    });
  }

  // ── Toggle status ────────────────────────────────────────────────────────────
  function toggleCat(id) {
    fetch('categories.php', {
      method:  'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body:    'action=toggle&id=' + id
    })
    .then(function(r) { return r.json(); })
    .then(function(res) {
      if (res.success) {
        var c = categories.find(function(x) { return x.id === id; });
        if (c) c.status = res.new_status;

        var badge = document.getElementById('status-badge-' + id);
        badge.className  = res.new_status === 'active' ? 'badge-active' : 'badge-inactive';
        badge.textContent = res.new_status === 'active' ? 'Active' : 'Inactive';

        var row = document.getElementById('row-' + id);
        if (row) {
          row.dataset.status = res.new_status;
          var toggleBtn = row.querySelector('.btn-toggle i');
          if (toggleBtn) toggleBtn.className = 'bi bi-toggle-' + (res.new_status === 'active' ? 'on' : 'off');
        }

        updateStats();
        showToast('Status changed to ' + res.new_status + '.');
      } else {
        showToast('Failed to toggle status.', true);
      }
    })
    .catch(function(err) { showToast('Network error: ' + err.message, true); });
  }

  // ── Delete ───────────────────────────────────────────────────────────────────
  function deleteCat(id) {
    var c = categories.find(function(x) { return x.id === id; });
    if (!c || !confirm('Delete category "' + c.name + '"?\n\nProducts in this category will NOT be deleted, but they will no longer be associated with this category.')) return;

    fetch('categories.php', {
      method:  'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body:    'action=delete&id=' + id
    })
    .then(function(r) { return r.json(); })
    .then(function(res) {
      if (res.success) {
        categories = categories.filter(function(x) { return x.id !== id; });
        var row = document.getElementById('row-' + id);
        if (row) row.remove();
        updateStats();
        showToast('Category deleted.');
        // Show empty state if no categories left
        if (categories.length === 0) {
          document.getElementById('catBody').innerHTML =
            '<tr><td colspan="5"><div class="empty-state"><i class="bi bi-tags"></i>No categories yet. Click <strong>Add Category</strong> to create your first one.</div></td></tr>';
        }
      } else {
        showToast('Failed to delete: ' + (res.message || 'Unknown error'), true);
      }
    })
    .catch(function(err) { showToast('Network error: ' + err.message, true); });
  }

  // ── Add row to table DOM ─────────────────────────────────────────────────────
  function addRow(cat) {
    // Remove empty-state row if present
    var empty = document.querySelector('#catBody tr td[colspan]');
    if (empty) empty.closest('tr').remove();

    var tr = document.createElement('tr');
    tr.id = 'row-' + cat.id;
    tr.dataset.id     = cat.id;
    tr.dataset.name   = cat.name;
    tr.dataset.desc   = cat.description || '';
    tr.dataset.status = cat.status;

    tr.innerHTML =
      '<td><strong>' + escHtml(cat.name) + '</strong></td>' +
      '<td style="color:var(--text-muted);font-size:13px">' + escHtml(cat.description || '—') + '</td>' +
      '<td><span class="prod-badge" id="pcount-' + cat.id + '">0</span></td>' +
      '<td><span class="' + (cat.status === 'active' ? 'badge-active' : 'badge-inactive') + '" id="status-badge-' + cat.id + '">' +
        (cat.status === 'active' ? 'Active' : 'Inactive') + '</span></td>' +
      '<td style="text-align:right;white-space:nowrap">' +
        '<button class="action-btn btn-edit" onclick="editCat(' + cat.id + ')" title="Edit" style="margin-right:4px"><i class="bi bi-pencil"></i></button>' +
        '<button class="action-btn btn-toggle" onclick="toggleCat(' + cat.id + ')" title="Toggle Status" style="margin-right:4px"><i class="bi bi-toggle-' + (cat.status === 'active' ? 'on' : 'off') + '"></i></button>' +
        '<button class="action-btn btn-delete" onclick="deleteCat(' + cat.id + ')" title="Delete"><i class="bi bi-trash"></i></button>' +
      '</td>';
    document.getElementById('catBody').appendChild(tr);
  }

  // ── Update stat counters ─────────────────────────────────────────────────────
  function updateStats() {
    document.getElementById('statTotal').textContent  = categories.length;
    document.getElementById('statActive').textContent = categories.filter(function(c) { return c.status === 'active'; }).length;
    var s = categories.length;
    document.getElementById('catCount').textContent   = s + ' categor' + (s !== 1 ? 'ies' : 'y');
  }

  // ── Helpers ──────────────────────────────────────────────────────────────────
  function escHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }

  function showToast(msg, isError) {
    var t = document.getElementById('toastMsg');
    t.textContent = msg;
    t.style.background = isError ? '#991b1b' : '#1a1a2e';
    t.classList.add('show');
    setTimeout(function() { t.classList.remove('show'); }, 3500);
  }
</script>
</body>
</html>
