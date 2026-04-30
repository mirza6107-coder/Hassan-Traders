<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('db_connect.php');

if (!isset($_SESSION['user_name']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../login and signup/login.php");
  exit();
}

$username  = $_SESSION['user_name'] ?? 'Admin';
$email     = $_SESSION['email']     ?? 'mirza6107@gmail.com';
$role      = "Administrator";
$join_date = "April 2026";
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Profile — Hassan Traders</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
  <link rel="stylesheet" href="profile.css" />
</head>
<body>

  <!-- SIDEBAR -->
  <?php include('sidebar.php'); ?>

  <!-- MAIN -->
  <main class="main">

    <!-- Topbar -->
    <div class="topbar">
      <div class="topbar-title">
        <h4>My Profile</h4>
        <p>View and manage your account</p>
      </div>
      <div class="topbar-actions">
        <button class="btn-primary-custom" onclick="alert('Profile export coming soon!')">
          <i class="bi bi-download"></i> Export Data
        </button>
      </div>
    </div>

    <!-- Content -->
    <div class="content">

      <!-- Hero Card -->
      <div class="profile-hero">
        <div class="hero-inner">
          <div class="hero-avatar">
            <i class="bi bi-person-badge-fill"></i>
          </div>
          <div class="hero-text">
            <h2><?php echo htmlspecialchars($username); ?></h2>
            <div class="role-badge">
              <i class="bi bi-shield-check" style="font-size:11px;"></i>
              <?php echo htmlspecialchars($role); ?>
            </div>
            <div class="email-line">
              <i class="bi bi-envelope-at" style="font-size:13px;"></i>
              <?php echo htmlspecialchars($email); ?>
            </div>
          </div>
          <div class="hero-stats">
            <div class="hero-stat">
              <span class="hero-stat-num">1</span>
              <span class="hero-stat-label">Months Active</span>
            </div>
            <div class="hero-stat">
              <span class="hero-stat-num">100%</span>
              <span class="hero-stat-label">Access Level</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Grid -->
      <div class="cards-grid">

        <!-- Account Details -->
        <div class="panel-card">
          <div class="panel-header">
            <h5>Account Details</h5>
            <div class="panel-header-icon"><i class="bi bi-person-lines-fill"></i></div>
          </div>
          <div class="panel-body">

            <div class="detail-row">
              <div class="detail-left">
                <div class="detail-icon"><i class="bi bi-person"></i></div>
                <span class="detail-label">Full Name</span>
              </div>
              <span class="detail-value"><?php echo htmlspecialchars($username); ?></span>
            </div>

            <div class="detail-row">
              <div class="detail-left">
                <div class="detail-icon"><i class="bi bi-envelope"></i></div>
                <span class="detail-label">Email Address</span>
              </div>
              <span class="detail-value"><?php echo htmlspecialchars($email); ?></span>
            </div>

            <div class="detail-row">
              <div class="detail-left">
                <div class="detail-icon"><i class="bi bi-shield-lock"></i></div>
                <span class="detail-label">Role</span>
              </div>
              <span class="detail-value"><?php echo htmlspecialchars($role); ?></span>
            </div>

            <div class="detail-row">
              <div class="detail-left">
                <div class="detail-icon"><i class="bi bi-calendar3"></i></div>
                <span class="detail-label">Member Since</span>
              </div>
              <span class="detail-value"><?php echo $join_date; ?></span>
            </div>

            <div class="detail-row">
              <div class="detail-left">
                <div class="detail-icon"><i class="bi bi-patch-check"></i></div>
                <span class="detail-label">Account Status</span>
              </div>
              <span class="detail-value">
                <span class="status-verified">
                  <i class="bi bi-check-circle-fill" style="font-size:11px;"></i> Verified
                </span>
              </span>
            </div>

          </div>
          <div class="actions-row">
            <button class="btn-primary-custom flex-grow-1" onclick="alert('Edit profile coming soon!')">
              <i class="bi bi-pencil-square"></i> Edit Profile
            </button>
            <button class="btn-ghost" onclick="confirm('Change password?')">
              <i class="bi bi-key"></i> Change Password
            </button>
          </div>
        </div>

        <!-- Right column -->
        <div style="display:flex; flex-direction:column; gap:24px;">

          <!-- Security -->
          <div class="panel-card">
            <div class="panel-header">
              <h5>Security</h5>
              <div class="panel-header-icon"><i class="bi bi-shield-check"></i></div>
            </div>
            <div class="panel-body">

              <div class="security-item">
                <div class="security-left">
                  <div class="security-icon"><i class="bi bi-lock"></i></div>
                  <div>
                    <div class="security-label">Password</div>
                    <div class="security-sub">Last changed 30 days ago</div>
                  </div>
                </div>
                <span class="badge-enabled">Strong</span>
              </div>

              <div class="security-item">
                <div class="security-left">
                  <div class="security-icon"><i class="bi bi-phone"></i></div>
                  <div>
                    <div class="security-label">Two-Factor Auth</div>
                    <div class="security-sub">Adds extra security</div>
                  </div>
                </div>
                <span class="badge-disabled">Not set</span>
              </div>

              <div class="security-item">
                <div class="security-left">
                  <div class="security-icon"><i class="bi bi-activity"></i></div>
                  <div>
                    <div class="security-label">Login Sessions</div>
                    <div class="security-sub">1 active device</div>
                  </div>
                </div>
                <span class="badge-enabled">Active</span>
              </div>

            </div>
            <div class="actions-row" style="padding-top:16px;">
              <button class="btn-ghost w-100" onclick="alert('Security settings coming soon!')">
                <i class="bi bi-gear"></i> Manage Security
              </button>
            </div>
          </div>

          <!-- Recent Activity -->
          <div class="panel-card">
            <div class="panel-header">
              <h5>Recent Activity</h5>
              <div class="panel-header-icon"><i class="bi bi-clock-history"></i></div>
            </div>
            <div class="panel-body">

              <div class="activity-item">
                <div class="activity-dot dot-red"><i class="bi bi-box-arrow-in-right"></i></div>
                <div class="activity-text">
                  <p>Admin login</p>
                  <span>Today, 9:41 AM · Chrome, Windows</span>
                </div>
              </div>

              <div class="activity-item">
                <div class="activity-dot dot-blue"><i class="bi bi-file-earmark-text"></i></div>
                <div class="activity-text">
                  <p>Generated sales report</p>
                  <span>Yesterday, 3:15 PM</span>
                </div>
              </div>

              <div class="activity-item">
                <div class="activity-dot dot-green"><i class="bi bi-person-plus"></i></div>
                <div class="activity-text">
                  <p>New staff account created</p>
                  <span>Apr 26, 11:02 AM</span>
                </div>
              </div>

              <div class="activity-item">
                <div class="activity-dot dot-amber"><i class="bi bi-pencil-square"></i></div>
                <div class="activity-text">
                  <p>Updated product inventory</p>
                  <span>Apr 25, 2:48 PM</span>
                </div>
              </div>

            </div>
          </div>

        </div><!-- /right column -->
      </div><!-- /cards-grid -->
    </div><!-- /content -->
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>