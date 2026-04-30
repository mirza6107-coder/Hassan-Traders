<?php session_start();
if (!isset($_SESSION['user_name']) || $_SESSION['role'] !== 'admin') {
  // If not an admin, send them to the login page or a "denied" page
  header("Location: ../login and signup/login.php");
  exit();
} ?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Add Product — Hassan Traders Admin</title>
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet" />
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
    rel="stylesheet" />
  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=DM+Sans:wght@300;400;500;600&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="add-products.css" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
  <!-- ══ SIDEBAR ══ -->
  <?php include('sidebar.php'); ?>

  <!-- ══ MAIN ══ -->
  <main class="main">
    <!-- Topbar -->
    <div class="topbar">
      <div class="topbar-left">
        <a href="all-products.php" class="topbar-back-link">
          <i class="bi bi-arrow-left"></i>
        </a>
        <div class="topbar-title">
          <h4>Add New Product</h4>
          <p>Fill in the details to add a product to your catalog</p>
        </div>
      </div>
      <div class="topbar-right">
        <div class="topbar-date" id="topbarDate"></div>
        <a href="all-products.php" class="topbar-products-link">
          <i class="bi bi-grid-3x3-gap me-1"></i> View All Products
        </a>
      </div>
    </div>

    <!-- Content -->
    <div class="content">
      <form
        id="addProductForm"
        action="add-product.php"
        method="post"
        enctype="multipart/form-data">
        <div class="form-grid">
          <!-- ════ LEFT COLUMN ════ -->
          <div class="form-col-left">
            <!-- Basic Info -->
            <div class="card-panel">
              <div class="card-panel-header">
                <div class="card-panel-icon">
                  <i class="bi bi-info-circle-fill"></i>
                </div>
                <div>
                  <h5>Basic Information</h5>
                  <p>Product name, category and description</p>
                </div>
              </div>
              <div class="card-panel-body">
                <div class="form-row-2">
                  <div class="form-group">
                    <label class="form-label">Product Name <span class="req">*</span></label>
                    <input
                      type="text"
                      id="productName"
                      name="productname"
                      class="form-control"
                      placeholder="e.g. PPR-C Pipe 25mm"
                      required
                      oninput="
                          document.getElementById('prev-name').textContent =
                            this.value || '—'
                        " />
                    <div class="field-error" id="nameError"></div>
                  </div>
                  <div class="form-group">
                    <label class="form-label">Category <span class="req">*</span></label>
                    <select id="category" name="category" class="form-control" required
                      onchange="document.getElementById('prev-cat').textContent = this.value || '—'">
                      <option value="">Select Category</option>
                      <?php
                      $conn = mysqli_connect('localhost', 'root', '', 'htss');
                      if ($conn) {
                        $cat_query = mysqli_query($conn, "SELECT name FROM categories WHERE status = 'active' ORDER BY name ASC");
                        while ($cat = mysqli_fetch_assoc($cat_query)) {
                          $selected = (isset($_POST['category']) && $_POST['category'] == $cat['name']) ? 'selected' : '';
                          echo "<option value='" . htmlspecialchars($cat['name']) . "' $selected>"
                            . htmlspecialchars($cat['name']) . "</option>";
                        }
                        mysqli_close($conn);
                      }
                      ?>
                    </select>
                  </div>
                </div>
                <div class="form-group">
                  <label class="form-label">Description <span class="opt">(Optional)</span></label>
                  <textarea
                    id="description"
                    name="description"
                    class="form-control"
                    rows="4"
                    placeholder="Product details, specifications, uses…"></textarea>
                </div>
              </div>
            </div>

            <!-- Pricing & Inventory -->
            <div class="card-panel">
              <div class="card-panel-header">
                <div class="card-panel-icon">
                  <i class="bi bi-currency-dollar"></i>
                </div>
                <div>
                  <h5>Pricing &amp; Inventory</h5>
                  <p>Set prices and stock quantity</p>
                </div>
              </div>
              <div class="card-panel-body">
                <div class="form-row-3">
                  <div class="form-group">
                    <label class="form-label">Sale Price (Rs.) <span class="req">*</span></label>
                    <div class="price-input-wrap">
                      <span class="price-prefix">Rs.</span>
                      <input
                        type="number"
                        id="basePrice"
                        name="price"
                        class="form-control price-field"
                        placeholder="0"
                        required
                        min="1"
                        oninput="updatePreviewPrice()" />
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="form-label">Original Price (Rs.) <span class="req">*</span></label>
                    <div class="price-input-wrap">
                      <span class="price-prefix">Rs.</span>
                      <input
                        type="number"
                        id="orignalPrice"
                        name="orignalprice"
                        class="form-control price-field"
                        placeholder="0"
                        required
                        min="1" />
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="form-label">Stock Qty <span class="req">*</span></label>
                    <input
                      type="number"
                      id="stockQuantity"
                      name="quantity"
                      class="form-control"
                      value="100"
                      required
                      min="0" />
                  </div>
                </div>
                <div class="form-row-2">
                  <div class="form-group">
                    <label class="form-label">Brand / Series</label>
                    <input
                      type="text"
                      id="brand"
                      name="brand"
                      class="form-control"
                      placeholder="e.g. DuraMax" />
                  </div>
                  <div class="form-group">
                    <label class="form-label">Status</label>
                    <select
                      id="productStatus"
                      name="status"
                      class="form-control">
                      <option value="published">✅ Published</option>
                      <option value="draft">📝 Draft</option>
                      <option value="inactive">⏸ Inactive</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>

            <!-- Product Image -->
            <div class="card-panel">
              <div class="card-panel-header">
                <div class="card-panel-icon">
                  <i class="bi bi-image-fill"></i>
                </div>
                <div>
                  <h5>Product Image</h5>
                  <p>JPG or PNG · Max 5MB · Recommended 800×800px</p>
                </div>
              </div>
              <div class="card-panel-body">
                <div
                  class="upload-zone"
                  id="uploadZone"
                  onclick="document.getElementById('productImage').click()"
                  ondragover="handleDragOver(event)"
                  ondrop="handleDrop(event)"
                  ondragleave="handleDragLeave(event)">
                  <div class="upload-icon-wrap">
                    <i class="bi bi-cloud-arrow-up-fill"></i>
                  </div>
                  <p class="upload-title">
                    <strong>Click to upload</strong> or drag &amp; drop
                  </p>
                  <p class="upload-sub">Supports JPG, PNG — Max 5MB</p>
                </div>
                <input
                  type="file"
                  id="productImage"
                  accept="image/*"
                  name="productimage"
                  style="display: none"
                  onchange="previewImage(event)" />
                <div
                  class="image-preview-wrap"
                  id="imagePreviewWrap"
                  style="display: none">
                  <img id="imagePreview" class="preview-img" alt="Preview" />
                  <div class="preview-actions">
                    <button
                      type="button"
                      class="btn-remove-img"
                      onclick="removeImage()">
                      <i class="bi bi-trash3"></i> Remove Image
                    </button>
                    <button
                      type="button"
                      class="btn-change-img"
                      onclick="
                          document.getElementById('productImage').click()
                        ">
                      <i class="bi bi-arrow-repeat"></i> Change Image
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="form-actions">
              <button type="submit" id="submitBtn" class="btn-submit">
                <i class="bi bi-check-circle-fill"></i> Save Product
              </button>
              <a href="all-products.php" class="btn-cancel">
                <i class="bi bi-x-lg"></i> Cancel
              </a>
            </div>
          </div>
          <!-- /left -->

          <!-- ════ RIGHT COLUMN ════ -->
          <div class="form-col-right">
            <!-- Live Preview -->
            <div class="card-panel preview-card">
              <div class="card-panel-header">
                <div class="card-panel-icon">
                  <i class="bi bi-eye-fill"></i>
                </div>
                <div>
                  <h5>Live Preview</h5>
                  <p>Updates as you type</p>
                </div>
              </div>
              <div class="card-panel-body">
                <div class="preview-thumb" id="previewThumb">
                  <i class="bi bi-image"></i>
                </div>
                <div class="preview-body">
                  <div class="preview-row">
                    <span class="preview-label">Name</span>
                    <span class="preview-val" id="prev-name">—</span>
                  </div>
                  <div class="preview-row">
                    <span class="preview-label">Category</span>
                    <span class="preview-val" id="prev-cat">—</span>
                  </div>
                  <div class="preview-row">
                    <span class="preview-label">Sale Price</span>
                    <span class="preview-val preview-price" id="prev-price">—</span>
                  </div>
                  <div class="preview-row">
                    <span class="preview-label">Status</span>
                    <span class="preview-badge" id="prev-status">Published</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Quick Tips -->
            <div class="card-panel">
              <div class="card-panel-header">
                <div class="card-panel-icon tips-icon">
                  <i class="bi bi-lightbulb-fill"></i>
                </div>
                <div>
                  <h5>Quick Tips</h5>
                  <p>Best practices for listing</p>
                </div>
              </div>
              <div class="card-panel-body tips-body">
                <div class="tip-item">
                  <div class="tip-check"><i class="bi bi-check-lg"></i></div>
                  <span>Use clear, high-resolution images on a clean
                    background.</span>
                </div>
                <div class="tip-item">
                  <div class="tip-check"><i class="bi bi-check-lg"></i></div>
                  <span>Write accurate stock quantities — low stock triggers
                    alerts.</span>
                </div>
                <div class="tip-item">
                  <div class="tip-check"><i class="bi bi-check-lg"></i></div>
                  <span>Choose the correct category so customers find products
                    easily.</span>
                </div>
                <div class="tip-item">
                  <div class="tip-check"><i class="bi bi-check-lg"></i></div>
                  <span>Include brand/series to help differentiate
                    products.</span>
                </div>
                <div class="tip-item">
                  <div class="tip-check"><i class="bi bi-check-lg"></i></div>
                  <span>Save as Draft if the product isn't ready to go live
                    yet.</span>
                </div>
              </div>
            </div>

            <!-- Form Progress -->
            <div class="card-panel">
              <div class="card-panel-header">
                <div class="card-panel-icon">
                  <i class="bi bi-bar-chart-fill"></i>
                </div>
                <div>
                  <h5>Form Completion</h5>
                  <p>Fill required fields</p>
                </div>
              </div>
              <div class="card-panel-body">
                <div class="progress-wrap">
                  <div class="progress-bar-outer">
                    <div
                      class="progress-bar-fill"
                      id="progressFill"
                      style="width: 0%"></div>
                  </div>
                  <span class="progress-pct" id="progressPct">0%</span>
                </div>
                <div class="progress-fields" id="progressFields">
                  <div class="pf-item" id="pf-name">
                    <i class="bi bi-circle"></i> Product Name
                  </div>
                  <div class="pf-item" id="pf-category">
                    <i class="bi bi-circle"></i> Category
                  </div>
                  <div class="pf-item" id="pf-price">
                    <i class="bi bi-circle"></i> Sale Price
                  </div>
                  <div class="pf-item" id="pf-orignalprice">
                    <i class="bi bi-circle"></i> Original Price
                  </div>
                  <div class="pf-item" id="pf-image">
                    <i class="bi bi-circle"></i> Product Image
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- /right -->
        </div>
      </form>
    </div>
  </main>

  <!-- Toast -->
  <div class="toast-notify" id="toast"></div>

  <script src="add-products.js"></script>
</body>

</html>