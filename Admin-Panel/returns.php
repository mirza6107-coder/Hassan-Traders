<?php session_start(); ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Returns - Hassan Traders Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
  <link rel="stylesheet" href="orders.css">
</head>
<body>
  <?php include('sidebar.php'); ?>

  <main class="main">
    <div class="topbar">
      <div class="topbar-title">
        <h4>Product Returns</h4>
        <p>Manage returned orders & stock restoration</p>
      </div>
      <button class="btn-primary-custom" onclick="exportReturns()">
        <i class="bi bi-download"></i> Export Returns
      </button>
    </div>

    <div class="content">
      <!-- Stats Row -->
      <div class="stat-row">
        <div class="stat-card">
          <div class="stat-val" id="statTotalReturns">0</div>
          <div class="stat-label">Total Returns</div>
        </div>
        <div class="stat-card">
          <div class="stat-val" id="statReturnValue">Rs. 0</div>
          <div class="stat-label">Total Return Value</div>
        </div>
      </div>

      <div class="filter-bar">
        <div class="search-box">
          <i class="bi bi-search" style="color: var(--text-muted)"></i>
          <input type="text" id="searchInput" placeholder="Search by order ID, customer, or items..." />
        </div>
      </div>

      <div class="card-panel">
        <div class="table-responsive">
          <table>
            <thead>
              <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Items Returned</th>
                <th>Return Date</th>
                <th>Amount</th>
                <th style="text-align:right">Actions</th>
              </tr>
            </thead>
            <tbody id="returnsBody"></tbody>
          </table>
        </div>
        <div class="table-footer">
          <span class="result-count" id="resultCount">Loading returns...</span>
        </div>
      </div>
    </div>
  </main>

  <!-- View Return Modal -->
  <div class="modal-overlay" id="returnModal">
    <div class="modal-box">
      <div class="modal-header">
        <h5 id="modalReturnTitle">Return Details</h5>
        <button class="modal-close" onclick="closeModal()">×</button>
      </div>
      <div class="modal-body" id="modalReturnBody"></div>
      <div class="modal-footer">
        <button class="btn-outline-custom" onclick="closeModal()">Close</button>
      </div>
    </div>
  </div>

  <script>
    let RETURNS = [];

    function loadReturns() {
      document.getElementById("resultCount").textContent = "Loading returns...";

      fetch("get_orders.php?status=Returned")
        .then(r => r.json())
        .then(data => {
          RETURNS = Array.isArray(data) ? data : [];

          updateStats();
          renderReturns();
        })
        .catch(err => {
          document.getElementById("returnsBody").innerHTML = 
            `<tr><td colspan="6" style="text-align:center;padding:60px;color:#e55;">
              Failed to load returns. Please try again.
            </td></tr>`;
          document.getElementById("resultCount").textContent = "Error loading data";
        });
    }

    function updateStats() {
      const total = RETURNS.length;
      const totalValue = RETURNS.reduce((sum, o) => sum + (parseFloat(o.amount) || 0), 0);

      document.getElementById("statTotalReturns").textContent = total;
      document.getElementById("statReturnValue").textContent = 
        totalValue >= 1000000 
          ? "Rs. " + (totalValue / 1000000).toFixed(1) + "M" 
          : "Rs. " + totalValue.toLocaleString("en-PK");
    }

    function renderReturns() {
      const body = document.getElementById("returnsBody");
      const search = document.getElementById("searchInput").value.toLowerCase().trim();

      const filtered = RETURNS.filter(o => {
        if (!search) return true;
        return (
          String(o.id).includes(search) ||
          (o.customer || '').toLowerCase().includes(search) ||
          (o.items || '').toLowerCase().includes(search)
        );
      });

      if (filtered.length === 0) {
        body.innerHTML = `<tr><td colspan="6" style="text-align:center;padding:60px;color:var(--text-muted);">
          <i class="bi bi-arrow-return-left" style="font-size:32px;opacity:0.6"></i><br><br>
          No returned orders found.
        </td></tr>`;
        document.getElementById("resultCount").textContent = "0 returns";
        return;
      }

      body.innerHTML = filtered.map(o => `
        <tr>
          <td><strong>#ORD-${o.id}</strong></td>
          <td>
            <strong>${escHtml(o.customer || 'Unknown')}</strong><br>
            <small style="color:var(--text-muted)">${escHtml(o.city || '')}</small>
          </td>
          <td style="font-size:13px;color:#555;">${escHtml(o.items || '—')}</td>
          <td style="font-size:13px;">${escHtml(o.date || '—')}</td>
          <td><strong style="color:var(--primary);">Rs. ${(parseFloat(o.amount) || 0).toLocaleString("en-PK")}</strong></td>
          <td style="text-align:right">
            <button class="action-btn btn-view" onclick="viewReturn(${o.id})">
              <i class="bi bi-eye"></i> View Details
            </button>
          </td>
        </tr>
      `).join("");

      document.getElementById("resultCount").textContent = 
        `Showing ${filtered.length} of ${RETURNS.length} returned orders`;
    }

    function viewReturn(id) {
      const order = RETURNS.find(o => o.id === id);
      if (!order) return;

      document.getElementById("modalReturnTitle").textContent = `Return #ORD-${order.id}`;

      document.getElementById("modalReturnBody").innerHTML = `
        <div class="info-grid">
          <div class="info-block"><label>Customer</label><p><strong>${escHtml(order.customer)}</strong></p></div>
          <div class="info-block"><label>City</label><p>${escHtml(order.city || '—')}</p></div>
          <div class="info-block"><label>Phone</label><p>${escHtml(order.phone || '—')}</p></div>
          <div class="info-block"><label>Payment Method</label><p>${escHtml(order.payment || '—')}</p></div>
          <div class="info-block"><label>Return Date</label><p>${escHtml(order.date)}</p></div>
        </div>
        <hr style="margin:20px 0;">
        <label style="font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;">Items Returned</label>
        <p style="margin-top:8px;font-size:14px;line-height:1.5;">${escHtml(order.items || '—')}</p>
        
        <div style="margin-top:25px;background:#1a1a2e;padding:16px 20px;border-radius:8px;display:flex;justify-content:space-between;align-items:center;">
          <span style="font-weight:600; color: white;">Total Returned Amount</span>
          <span style="font-size:24px;font-weight:700;color:#c0392b;">
            Rs. ${(parseFloat(order.amount) || 0).toLocaleString("en-PK")}
          </span>
        </div>
        <p style="margin-top:15px;font-size:13px;color:var(--text-muted);">
          <i class="bi bi-info-circle"></i> Stock has been automatically restored to inventory.
        </p>`;

      document.getElementById("returnModal").classList.add("show");
    }

    function closeModal() {
      document.getElementById("returnModal").classList.remove("show");
    }

    function exportReturns() {
      if (RETURNS.length === 0) {
        alert("No returns to export.");
        return;
      }

      let csv = "Order ID,Customer,Items,Date,Amount\n";
      RETURNS.forEach(o => {
        csv += `#ORD-${o.id},"${o.customer || ''}","${(o.items || '').replace(/"/g, '""')}","${o.date || ''}",${o.amount || 0}\n`;
      });

      const a = document.createElement("a");
      a.href = "data:text/csv;charset=utf-8," + encodeURIComponent(csv);
      a.download = "hassan-traders-returns.csv";
      a.click();
    }

    function escHtml(str) {
      return String(str ?? "")
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;");
    }

    // Event Listeners
    document.getElementById("searchInput").addEventListener("input", renderReturns);
    document.getElementById("returnModal").addEventListener("click", function(e) {
      if (e.target === this) closeModal();
    });

    window.onload = loadReturns;
  </script>
</body>
</html>