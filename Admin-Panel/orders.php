<?php session_start(); ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Orders - Hassan Traders Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
  <link rel="stylesheet" href="orders.css">
</head>
<body>
  <?php include('sidebar.php'); ?>

  <main class="main">
    <div class="topbar">
      <div class="topbar-title">
        <h4>Orders Management</h4>
        <p>Track and manage all customer orders</p>
      </div>
      <div class="topbar-actions">
        <button class="btn-primary-custom" onclick="exportOrders()">
          <i class="bi bi-download"></i> Export CSV
        </button>
      </div>
    </div>

    <div class="content">
      <div class="stat-row">
        <div class="stat-card" onclick="filterByStat('Pending')" style="cursor:pointer">
          <div class="stat-val pending" id="cntPending">0</div>
          <div class="stat-label">Pending</div>
        </div>
        <div class="stat-card" onclick="filterByStat('Processing')" style="cursor:pointer">
          <div class="stat-val processing" id="cntProcessing">0</div>
          <div class="stat-label">Processing</div>
        </div>
        <div class="stat-card" onclick="filterByStat('Shipped')" style="cursor:pointer">
          <div class="stat-val shipped" id="cntShipped">0</div>
          <div class="stat-label">Shipped</div>
        </div>
        <div class="stat-card" onclick="filterByStat('Delivered')" style="cursor:pointer">
          <div class="stat-val delivered" id="cntDelivered">0</div>
          <div class="stat-label">Delivered</div>
        </div>
        <div class="stat-card" onclick="filterByStat('Cancelled')" style="cursor:pointer">
          <div class="stat-val cancelled" id="cntCancelled">0</div>
          <div class="stat-label">Cancelled</div>
        </div>
        <div class="stat-card" onclick="filterByStat('Returned')" style="cursor:pointer">
          <div class="stat-val returned" id="cntReturned">0</div>
          <div class="stat-label">Returned</div>
        </div>
        <div class="stat-card" onclick="filterByStat('')" style="cursor:pointer" title="Show all orders">
          <div class="stat-val" id="cntAll" style="color:var(--text-muted,#888)">0</div>
          <div class="stat-label">All</div>
        </div>
      </div>

      <div class="filter-bar">
        <div class="search-box">
          <i class="bi bi-search" style="color: var(--text-muted)"></i>
          <input type="text" id="searchInput" placeholder="Search by order ID, customer, city..." />
        </div>
        <select id="statusFilter" class="filter-select">
          <option value="">All Status</option>
          <option>Pending</option>
          <option>Processing</option>
          <option>Shipped</option>
          <option>Delivered</option>
          <option>Cancelled</option>
          <option>Returned</option>
        </select>
        <select id="cityFilter" class="filter-select">
          <option value="">All Cities</option>
        </select>
      </div>

      <div class="card-panel">
        <div class="table-responsive">
          <table>
            <thead>
              <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Items</th>
                <th>Date</th>
                <th>Amount</th>
                <th>Status</th>
                <th style="text-align: right">Actions</th>
              </tr>
            </thead>
            <tbody id="ordersBody"></tbody>
          </table>
        </div>
        <div class="table-footer">
          <span class="result-count" id="resultCount">Loading...</span>
        </div>
      </div>
    </div>
  </main>

  <!-- ORDER DETAIL MODAL -->
  <div class="modal-overlay" id="orderModal">
    <div class="modal-box">
      <div class="modal-header">
        <h5 id="modalOrderId">Order Details</h5>
        <button class="modal-close" onclick="closeModal()">×</button>
      </div>
      <div class="modal-body" id="modalBody"></div>
      <div class="modal-footer">
        <button class="btn-outline-custom" onclick="closeModal()">Close</button>
        <button class="btn-success-custom" id="deliverBtn" onclick="markDelivered()">
          <i class="bi bi-check2-all"></i> Mark Delivered
        </button>
        <button class="btn-warning-custom" id="returnBtn" onclick="markReturned()">
          <i class="bi bi-arrow-return-left"></i> Mark as Returned
        </button>
        <button class="btn-danger-custom" id="cancelBtn" onclick="cancelOrder()">
          <i class="bi bi-x-circle"></i> Cancel Order
        </button>
      </div>
    </div>
  </div>

  <script>
    var ORDERS = [];
    var filtered = [];
    var selectedOrderId = null;

    const ALLOWED_STATUSES = ["Pending", "Processing", "Shipped", "Delivered", "Cancelled", "Returned"];

    function normaliseStatus(raw) {
      if (typeof raw === "number" || /^\d+$/.test(raw)) {
        const map = {0:"Pending",1:"Processing",2:"Shipped",3:"Delivered",4:"Cancelled",5:"Returned"};
        return map[parseInt(raw)] || "Pending";
      }
      const clean = String(raw).trim();
      return ALLOWED_STATUSES.find(s => s.toLowerCase() === clean.toLowerCase()) || "Pending";
    }

    function init() {
      document.getElementById("resultCount").textContent = "Loading…";
      fetch("get_orders.php")
        .then(r => r.json())
        .then(data => {
          ORDERS = data.map(o => ({
            id:      parseInt(o.id),
            customer: o.customer  || "Unknown",
            city:    o.city       || "—",
            phone:   o.phone      || "—",
            items:   o.items      || (o.item_count + " item(s)"),
            date:    o.date       || "—",
            amount:  parseFloat(o.amount) || 0,
            status:  normaliseStatus(o.status),
            payment: o.payment    || "—"
          }));

          populateCityFilter();
          updateStats();
          applyFilters();
        })
        .catch(err => showTableError("Could not load orders: " + err.message));
    }

    function populateCityFilter() {
      const sel = document.getElementById("cityFilter");
      const cities = [...new Set(ORDERS.map(o => o.city).filter(c => c && c !== "—"))].sort();
      sel.innerHTML = '<option value="">All Cities</option>' +
        cities.map(c => `<option>${escHtml(c)}</option>`).join("");
    }

    function updateStats() {
      ALLOWED_STATUSES.forEach(s => {
        const el = document.getElementById("cnt" + s);
        if (el) el.textContent = ORDERS.filter(o => o.status === s).length;
      });
      document.getElementById("cntAll").textContent = ORDERS.length;
    }

    function applyFilters() {
      const search = document.getElementById("searchInput").value.toLowerCase().trim();
      const status = document.getElementById("statusFilter").value;
      const city   = document.getElementById("cityFilter").value;

      filtered = ORDERS.filter(o => {
        const matchSearch = !search ||
          o.customer.toLowerCase().includes(search) ||
          String(o.id).includes(search) ||
          o.city.toLowerCase().includes(search);
        const matchStatus = !status || o.status === status;
        const matchCity   = !city   || o.city === city;
        return matchSearch && matchStatus && matchCity;
      });

      render();
    }

    function render() {
      const body = document.getElementById("ordersBody");

      if (!filtered.length) {
        body.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted);">No orders found</td></tr>';
        document.getElementById("resultCount").textContent = "0 orders";
        return;
      }

      body.innerHTML = filtered.map(o => {
        let actionBtns = '';

        if (['Cancelled', 'Returned'].includes(o.status)) {
          actionBtns = `<button class="action-btn btn-view" onclick="viewOrder(${o.id})"><i class="bi bi-eye"></i></button>`;
        } else if (o.status === 'Delivered') {
          actionBtns = `
            <button class="action-btn btn-view" onclick="viewOrder(${o.id})" style="margin-right:6px;">
              <i class="bi bi-eye"></i>
            </button>
            <button class="action-btn btn-warning" onclick="markReturnedFromTable(${o.id})">
              <i class="bi bi-arrow-return-left"></i> Return
            </button>
          `;
        } else {
          const nextStatus = { Pending: "Processing", Processing: "Shipped", Shipped: "Delivered" }[o.status];
          actionBtns = `
            <button class="action-btn btn-view" onclick="viewOrder(${o.id})" style="margin-right:6px;">
              <i class="bi bi-eye"></i>
            </button>
            ${nextStatus ? `<button class="action-btn btn-confirm" onclick="advanceOrder(${o.id}, '${nextStatus}')">→ ${nextStatus}</button>` : ''}
            <button class="action-btn btn-danger" onclick="cancelOrderFromTable(${o.id})" style="margin-left:6px;">
              <i class="bi bi-x-circle"></i> Cancel
            </button>
          `;
        }

        return `
          <tr>
            <td><strong>#ORD-${o.id}</strong></td>
            <td>
              <strong>${escHtml(o.customer)}</strong><br>
              <span style="font-size:12px;color:var(--text-muted);">${escHtml(o.city)}</span>
            </td>
            <td style="font-size:13px;color:#555;">${escHtml(o.items)}</td>
            <td style="font-size:13px;">${escHtml(o.date)}</td>
            <td><strong style="color:var(--primary);">Rs. ${o.amount.toLocaleString("en-PK")}</strong></td>
            <td><span class="badge-status badge-${o.status.toLowerCase()}">${o.status}</span></td>
            <td style="text-align:right;white-space:nowrap;">
              ${actionBtns}
            </td>
          </tr>`;
      }).join("");

      document.getElementById("resultCount").textContent = `Showing ${filtered.length} of ${ORDERS.length} orders`;
    }

    function viewOrder(id) {
      const o = ORDERS.find(x => x.id === id);
      if (!o) return;

      selectedOrderId = id;
      document.getElementById("modalOrderId").textContent = `Order #ORD-${o.id}`;

      const isFinal = ['Delivered', 'Cancelled', 'Returned'].includes(o.status);
      const canReturn = o.status === 'Delivered';

      document.getElementById("deliverBtn").style.display = (o.status !== 'Delivered' && !isFinal) ? "inline-flex" : "none";
      document.getElementById("returnBtn").style.display = canReturn ? "inline-flex" : "none";
      document.getElementById("cancelBtn").style.display = !isFinal ? "inline-flex" : "none";

      document.getElementById("modalBody").innerHTML = `
        <div class="info-grid">
          <div class="info-block"><label>Customer</label><p><strong>${escHtml(o.customer)}</strong></p></div>
          <div class="info-block"><label>City</label><p>${escHtml(o.city)}</p></div>
          <div class="info-block"><label>Phone</label><p>${escHtml(o.phone)}</p></div>
          <div class="info-block"><label>Payment</label><p>${escHtml(o.payment)}</p></div>
          <div class="info-block"><label>Order Date</label><p>${escHtml(o.date)}</p></div>
          <div class="info-block"><label>Status</label>
            <p><span class="badge-status badge-${o.status.toLowerCase()}">${o.status}</span></p>
          </div>
        </div>
        <hr>
        <label style="font-size:11px;font-weight:700;color:var(--text-muted);">Items Ordered</label>
        <p style="margin-top:8px;font-size:14px;">${escHtml(o.items)}</p>
        <div style="margin-top:20px;background:var(--primary-light);padding:14px 18px;border-radius:8px;display:flex;justify-content:space-between;align-items:center;">
          <span style="font-weight:600;">Total Amount</span>
          <span style="font-size:22px;font-weight:700;color:var(--primary);">Rs. ${o.amount.toLocaleString("en-PK")}</span>
        </div>`;

      document.getElementById("orderModal").classList.add("show");
    }

    function advanceOrder(id, newStatus) {
      if (!confirm(`Move order #ORD-${id} to "${newStatus}"?`)) return;
      updateStatusInDB(id, newStatus);
    }

    function markDelivered() {
      if (!confirm("Mark this order as Delivered?")) return;
      updateStatusInDB(selectedOrderId, "Delivered");
    }

    function markReturned() {
      if (!confirm("Mark this order as RETURNED?\n\nStock will be restored to inventory.")) return;
      updateStatusInDB(selectedOrderId, "Returned");
    }

    function markReturnedFromTable(id) {
      if (!confirm("Mark order #ORD-" + id + " as RETURNED?\nStock will be restored.")) return;
      updateStatusInDB(id, "Returned");
    }

    function cancelOrder() {
      if (!confirm("Are you sure you want to CANCEL this order? Stock will be restored.")) return;
      updateStatusInDB(selectedOrderId, "Cancelled");
    }

    function cancelOrderFromTable(id) {
      if (!confirm(`Cancel order #ORD-${id}? Stock will be restored.`)) return;
      updateStatusInDB(id, "Cancelled");
    }

    function updateStatusInDB(id, newStatus) {
      fetch("update_order_status.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id: id, status: newStatus })
      })
      .then(r => r.json())
      .then(res => {
        if (res.success) {
          const o = ORDERS.find(x => x.id === id);
          if (o) o.status = newStatus;

          closeModal();
          updateStats();
          applyFilters();
          alert(res.message || "Order updated successfully");
        } else {
          alert("Failed: " + (res.message || "Unknown error"));
        }
      })
      .catch(err => alert("Network error: " + err.message));
    }

    function closeModal() {
      document.getElementById("orderModal").classList.remove("show");
    }

    function filterByStat(status) {
      document.getElementById("statusFilter").value = status;
      applyFilters();
    }

    function showTableError(msg) {
      document.getElementById("ordersBody").innerHTML = `<tr><td colspan="7" style="text-align:center;padding:40px;color:#e55;">⚠ ${msg}</td></tr>`;
    }

    function escHtml(str) {
      return String(str ?? "")
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;");
    }

    // Event listeners
    document.getElementById("searchInput").addEventListener("input", applyFilters);
    document.getElementById("statusFilter").addEventListener("change", applyFilters);
    document.getElementById("cityFilter").addEventListener("change", applyFilters);

    document.getElementById("orderModal").addEventListener("click", function(e) {
      if (e.target === this) closeModal();
    });

    window.onload = init;
  </script>
</body>
</html>