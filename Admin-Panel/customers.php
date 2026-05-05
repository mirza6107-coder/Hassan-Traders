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
  <title>Customers - Hassan Traders Admin</title>
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet" />
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
    rel="stylesheet" />
  <link rel="stylesheet" href="customers.css">
</head>

<body>
  <?php include('sidebar.php'); ?>

  <main class="main">
    <div class="topbar">
      <div class="topbar-title">
        <h4>Customers</h4>
        <p>Manage your customer database</p>
      </div>
      <button class="btn-primary-custom" onclick="exportCustomers()">
        <i class="bi bi-download"></i> Export CSV
      </button>
    </div>

    <div class="content">
      <div class="stat-row">
        <div class="stat-card">
          <div class="stat-val" id="statTotal">8</div>
          <div class="stat-label">Total Customers</div>
        </div>
        <div class="stat-card">
          <div class="stat-val green" id="statVip">3</div>
          <div class="stat-label">VIP Customers</div>
        </div>
        <div class="stat-card">
          <div class="stat-val" id="statCities">4</div>
          <div class="stat-label">Cities Covered</div>
        </div>
        <div class="stat-card">
          <div class="stat-val" id="statRevenue">Rs. 1.3M+</div>
          <div class="stat-label">Total Revenue</div>
        </div>
      </div>

      <div class="filter-bar">
        <div class="search-box">
          <i class="bi bi-search" style="color: var(--text-muted)"></i>
          <input
            type="text"
            id="searchInput"
            placeholder="Search by name, city, phone..." />
        </div>
        <select id="cityFilter" class="filter-select">
          <option value="">All Cities</option>
          <option>Sargodha</option>
          <option>Faisalabad</option>
          <option>Lahore</option>
          <option>Khushab</option>
        </select>
        <select id="typeFilter" class="filter-select">
          <option value="">All Types</option>
          <option value="VIP">VIP</option>
          <option value="Regular">Regular</option>
        </select>
      </div>

      <div class="card-panel">
        <div class="table-responsive">
          <table>
            <thead>
              <tr>
                <th>Customer</th>
                <th>Phone</th>
                <th>City</th>
                <th>Total Orders</th>
                <th>Total Spent</th>
                <th>Type</th>
                <th style="text-align: right">Actions</th>
              </tr>
            </thead>
            <tbody id="custBody"></tbody>
          </table>
        </div>
        <div class="table-footer">
          <span class="result-count" id="resultCount">Loading...</span>
        </div>
      </div>
    </div>
  </main>

  <div class="modal-overlay" id="custModal">
    <div class="modal-box">
      <div class="modal-header">
        <h5 id="modalName">Customer Details</h5>
        <button class="modal-close" onclick="closeModal()">×</button>
      </div>
      <div class="modal-body" id="modalBody"></div>
      <div class="modal-footer">
        <button class="btn-outline-custom" onclick="closeModal()">
          Close
        </button>
      </div>
    </div>
  </div>

  <script>
    var COLORS = [
      "#c0392b", "#2980b9", "#27ae60", "#8e44ad",
      "#f39c12", "#16a085", "#d35400", "#2c3e50",
    ];

    // ALL customers loaded from server
    var CUSTOMERS = [];
    // Currently visible (after filters)
    var filtered = [];

    /* ── Helpers ── */
    function initials(name) {
      return name.split(" ").map(function(w) {
          return w[0];
        })
        .join("").substring(0, 2).toUpperCase();
    }

    function colorFor(id) {
      return COLORS[id % COLORS.length];
    }

    /* Customer type: VIP if spent >= 50000 OR orders >= 8 */
    function customerType(c) {
      return (c.spent >= 50000 || c.orders >= 8) ? "VIP" : "Regular";
    }

    /* ── Load data from get_customers.php ── */
    function loadCustomers() {
      var body = document.getElementById("custBody");
      body.innerHTML =
        '<tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted);">' +
        '<i class="bi bi-arrow-repeat" style="font-size:24px;"></i><br>Loading customers…</td></tr>';

      fetch("get_customers.php")
        .then(function(res) {
          if (!res.ok) throw new Error("HTTP " + res.status);
          return res.json();
        })
        .then(function(data) {
          if (data.error) throw new Error(data.error);

          CUSTOMERS = data.map(function(c) {
            return {
              id: c.id,
              name: c.name,
              phone: c.phone,
              email: c.email || '—',
              address: c.address || '—',
              city: c.city || '—',
              orders: c.orders || 0,
              spent: c.spent || 0,
              joined: c.joined || '—',
              type: customerType(c)
            };
          });

          updateStats();
          populateCityFilter();
          applyFilters();
        })
        .catch(function(err) {
          body.innerHTML =
            '<tr><td colspan="7" style="text-align:center;padding:40px;color:#c0392b;">' +
            '<i class="bi bi-exclamation-triangle" style="font-size:24px;"></i><br>' +
            'Failed to load customers: ' + err.message + '</td></tr>';
          document.getElementById("resultCount").textContent = "Error loading data";
        });
    }

    /* ── Update stat cards from real data ── */
    function updateStats() {
      var total = CUSTOMERS.length;
      var vip = CUSTOMERS.filter(function(c) {
        return c.type === "VIP";
      }).length;
      var cities = new Set(CUSTOMERS.map(function(c) {
        return c.city;
      })).size;
      var revenue = CUSTOMERS.reduce(function(s, c) {
        return s + c.spent;
      }, 0);

      document.getElementById("statTotal").textContent = total;
      document.getElementById("statVip").textContent = vip;
      document.getElementById("statCities").textContent = cities;

      // Format revenue: show as "Rs. 1.3M+" style if >= 1M, else "Rs. 125K+"
      var revEl = document.getElementById("statRevenue");
      if (revenue >= 1000000) {
        revEl.textContent = "Rs. " + (revenue / 1000000).toFixed(1) + "M+";
      } else if (revenue >= 1000) {
        revEl.textContent = "Rs. " + Math.round(revenue / 1000) + "K+";
      } else {
        revEl.textContent = "Rs. " + revenue.toLocaleString("en-PK");
      }
    }

    /* ── Populate city filter dropdown from real data ── */
    function populateCityFilter() {
      var select = document.getElementById("cityFilter");
      // Keep only "All Cities" option, remove old hard-coded ones
      select.innerHTML = '<option value="">All Cities</option>';
      var cities = [...new Set(CUSTOMERS.map(function(c) {
          return c.city;
        }))]
        .filter(function(c) {
          return c && c !== "—";
        })
        .sort();
      cities.forEach(function(city) {
        var opt = document.createElement("option");
        opt.value = city;
        opt.textContent = city;
        select.appendChild(opt);
      });
    }

    /* ── Filter + render ── */
    function applyFilters() {
      var search = document.getElementById("searchInput").value.toLowerCase().trim();
      var city = document.getElementById("cityFilter").value;
      var type = document.getElementById("typeFilter").value;

      filtered = CUSTOMERS.filter(function(c) {
        return (
          (!search ||
            c.name.toLowerCase().includes(search) ||
            c.city.toLowerCase().includes(search) ||
            c.phone.includes(search)) &&
          (!city || c.city === city) &&
          (!type || c.type === type)
        );
      });
      render();
    }

    function render() {
      var body = document.getElementById("custBody");

      if (filtered.length === 0) {
        body.innerHTML =
          '<tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted);">' +
          '<i class="bi bi-people" style="font-size:24px;"></i><br>No customers found.</td></tr>';
        document.getElementById("resultCount").textContent = "No results";
        return;
      }

      body.innerHTML = filtered.map(function(c) {
        return (
          "<tr>" +
          '<td><div class="customer-cell">' +
          '<div class="avatar" style="background:' + colorFor(c.id) + ';">' + initials(c.name) + '</div>' +
          '<div><strong>' + c.name + '</strong>' +
          '<br><span style="font-size:12px;color:var(--text-muted);">Joined ' + c.joined + '</span></div>' +
          '</div></td>' +
          "<td>" + c.phone + "</td>" +
          "<td>" + c.city + "</td>" +
          "<td>" + c.orders + " order" + (c.orders !== 1 ? "s" : "") + "</td>" +
          '<td><strong style="color:var(--primary);">Rs. ' + c.spent.toLocaleString("en-PK") + '</strong></td>' +
          '<td><span class="' + (c.type === "VIP" ? "badge-vip" : "badge-regular") + '">' + c.type + '</span></td>' +
          '<td style="text-align:right;"><button class="action-btn btn-view" onclick="viewCustomer(' + c.id + ')"><i class="bi bi-eye"></i> View</button></td>' +
          "</tr>"
        );
      }).join("");

      document.getElementById("resultCount").textContent =
        "Showing " + filtered.length + " of " + CUSTOMERS.length + " customers";
    }

    function viewCustomer(id) {
      var c = CUSTOMERS.find(function(x) {
        return x.id === id;
      });
      if (!c) return;
      document.getElementById("modalName").textContent = c.name;
      document.getElementById("modalBody").innerHTML =
        /* Avatar + name header */
        '<div style="display:flex;align-items:center;gap:16px;margin-bottom:24px;">' +
        '<div class="avatar" style="width:56px;height:56px;border-radius:50%;background:' + colorFor(c.id) + ';display:flex;align-items:center;justify-content:center;font-weight:700;font-size:20px;color:white;">' + initials(c.name) + '</div>' +
        '<div>' +
        '<div style="font-size:18px;font-weight:700;">' + c.name + '</div>' +
        '<div style="font-size:13px;color:var(--text-muted);">Customer since ' + c.joined + '</div>' +
        '<span class="' + (c.type === 'VIP' ? 'badge-vip' : 'badge-regular') + '" style="margin-top:6px;display:inline-block;">' + c.type + '</span>' +
        '</div>' +
        '</div>' +

        /* Info grid */
        '<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">' +

        /* Phone */
        '<div style="background:var(--bg);border-radius:8px;padding:12px;">' +
        '<div style="font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;">Phone</div>' +
        '<div style="margin-top:4px;font-size:14px;">' +
        '<a href="tel:' + c.phone + '" style="color:inherit;text-decoration:none;">' + c.phone + '</a>' +
        '</div>' +
        '</div>' +

        /* City */
        '<div style="background:var(--bg);border-radius:8px;padding:12px;">' +
        '<div style="font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;">City</div>' +
        '<div style="margin-top:4px;font-size:14px;">' + c.city + '</div>' +
        '</div>' +

        /* Email — full width */
        '<div style="background:var(--bg);border-radius:8px;padding:12px;grid-column:1/-1;">' +
        '<div style="font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;">Email</div>' +
        '<div style="margin-top:4px;font-size:14px;">' +
        (c.email && c.email !== '—' ?
          '<a href="mailto:' + c.email + '" style="color:var(--primary);text-decoration:none;">' + c.email + '</a>' :
          '<span style="color:var(--text-muted);">Not provided</span>') +
        '</div>' +
        '</div>' +

        /* Address — full width */
        '<div style="background:var(--bg);border-radius:8px;padding:12px;grid-column:1/-1;">' +
        '<div style="font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;">Delivery Address</div>' +
        '<div style="margin-top:4px;font-size:14px;">' +
        (c.address && c.address !== '—' ?
          c.address :
          '<span style="color:var(--text-muted);">Not provided</span>') +
        '</div>' +
        '</div>' +

        /* Orders */
        '<div style="background:var(--bg);border-radius:8px;padding:12px;">' +
        '<div style="font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;">Total Orders</div>' +
        '<div style="margin-top:4px;font-size:22px;font-weight:700;color:var(--primary);">' + c.orders + '</div>' +
        '</div>' +

        /* Spent */
        '<div style="background:var(--bg);border-radius:8px;padding:12px;">' +
        '<div style="font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;">Total Spent</div>' +
        '<div style="margin-top:4px;font-size:22px;font-weight:700;color:var(--primary);">Rs. ' + c.spent.toLocaleString("en-PK") + '</div>' +
        '</div>' +

        '</div>';

      document.getElementById("custModal").classList.add("show");
    }

    /* ── Export CSV ── */
    function exportCustomers() {
      var rows = [
        ["Name", "Phone", "City", "Orders", "Spent", "Type", "Joined"]
      ];
      CUSTOMERS.forEach(function(c) {
        rows.push([c.name, c.phone, c.city, c.orders, c.spent, c.type, c.joined]);
      });
      var csv = rows.map(function(r) {
        return r.join(",");
      }).join("\n");
      var a = document.createElement("a");
      a.href = "data:text/csv;charset=utf-8," + encodeURIComponent(csv);
      a.download = "hassan-traders-customers.csv";
      a.click();
    }

    /* ── Modal close ── */
    function closeModal() {
      document.getElementById("custModal").classList.remove("show");
    }
    document.getElementById("custModal").addEventListener("click", function(e) {
      if (e.target === this) closeModal();
    });

    /* ── Event listeners ── */
    document.getElementById("searchInput").addEventListener("input", applyFilters);
    document.getElementById("cityFilter").addEventListener("change", applyFilters);
    document.getElementById("typeFilter").addEventListener("change", applyFilters);

    /* ── Boot ── */
    window.onload = loadCustomers;
  </script>
</body>

</html>