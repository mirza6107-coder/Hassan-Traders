<?php
session_start();
if (!isset($_SESSION['admin_role']) || $_SESSION['admin_role'] !== 'admin') {
    // If the admin session isn't found, send them back to login
    header("Location: ../login and signup/login.php");
    exit();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// db_connect.php
$host     = "localhost";
$dbname   = "htss";   // ← CHANGE THIS
$user     = "root"; 
$pass     = ""; 

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");


if (!isset($_SESSION['user_name']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../login and signup/login.php");
  exit();
}

$username  = $_SESSION['user_name'] ?? 'Admin';
$email     = $_SESSION['email']     ?? 'mirza6107@gmail.com';
$role      = "Administrator";
$join_date = "April 2026";

// Avatar path: stored in session or default
$avatar_path = $_SESSION['avatar'] ?? null;

if (empty($avatar_path)) {
  // Optional: Load from DB as backup (if you have user_id in session)
  if (!empty($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT profile_image FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
      $avatar_path = $row['profile_image'];
      $_SESSION['avatar'] = $avatar_path;   // Cache it
    }
    $stmt->close();
  }
}

// Flash messages from redirects
$success_msg = $_SESSION['success_msg'] ?? null;
$error_msg   = $_SESSION['error_msg']   ?? null;
unset($_SESSION['success_msg'], $_SESSION['error_msg']);
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

    <!-- Flash Messages -->
    <?php if ($success_msg): ?>
      <div class="flash-banner flash-success">
        <i class="bi bi-check-circle-fill"></i> <?php echo htmlspecialchars($success_msg); ?>
        <button class="flash-close" onclick="this.parentElement.remove()"><i class="bi bi-x"></i></button>
      </div>
    <?php endif; ?>
    <?php if ($error_msg): ?>
      <div class="flash-banner flash-error">
        <i class="bi bi-exclamation-circle-fill"></i> <?php echo htmlspecialchars($error_msg); ?>
        <button class="flash-close" onclick="this.parentElement.remove()"><i class="bi bi-x"></i></button>
      </div>
    <?php endif; ?>

    <!-- Content -->
    <div class="content">

      <!-- Hero Card -->
      <div class="profile-hero">
        <div class="hero-inner">

          <!-- Avatar with Upload Trigger -->
          <div class="hero-avatar-wrap">
            <div class="hero-avatar" id="heroAvatar">
              <?php if ($avatar_path): ?>
                <img src="<?php echo htmlspecialchars($avatar_path); ?>" alt="Avatar" id="avatarPreview" />
              <?php else: ?>
                <i class="bi bi-person-badge-fill" id="avatarIcon"></i>
                <img src="" alt="Avatar" id="avatarPreview" style="display:none;" />
              <?php endif; ?>
            </div>
            <!-- Upload overlay button -->
            <button class="avatar-upload-btn" onclick="document.getElementById('avatarInput').click()" title="Change photo">
              <i class="bi bi-camera-fill"></i>
            </button>
            <!-- Hidden file input -->
            <form id="avatarForm" action="update_avatar.php" method="POST" enctype="multipart/form-data">
              <input type="file" id="avatarInput" name="avatar" accept="image/jpeg,image/png,image/webp,image/gif" style="display:none;" />
            </form>
          </div>

          <div class="hero-text">
            <h2 id="heroName"><?php echo htmlspecialchars($username); ?></h2>
            <div class="role-badge">
              <i class="bi bi-shield-check" style="font-size:11px;"></i>
              <?php echo htmlspecialchars($role); ?>
            </div>
            <div class="email-line">
              <i class="bi bi-envelope-at" style="font-size:13px;"></i>
              <span id="heroEmail"><?php echo htmlspecialchars($email); ?></span>
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
              <span class="detail-value" id="detailName"><?php echo htmlspecialchars($username); ?></span>
            </div>

            <div class="detail-row">
              <div class="detail-left">
                <div class="detail-icon"><i class="bi bi-envelope"></i></div>
                <span class="detail-label">Email Address</span>
              </div>
              <span class="detail-value" id="detailEmail"><?php echo htmlspecialchars($email); ?></span>
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
            <button class="btn-primary-custom flex-grow-1" onclick="openEditModal()">
              <i class="bi bi-pencil-square"></i> Edit Profile
            </button>
            <button class="btn-ghost" onclick="openPasswordModal()">
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
              <button class="btn-ghost w-100" onclick="openPasswordModal()">
                <i class="bi bi-key"></i> Change Password
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

  <!-- ═══════════════════════════════════════════
       EDIT PROFILE MODAL
  ═══════════════════════════════════════════ -->
  <div class="modal-backdrop" id="editModalBackdrop" onclick="closeEditModal()"></div>
  <div class="modal-panel" id="editModal" role="dialog" aria-modal="true" aria-labelledby="editModalTitle">
    <div class="modal-header">
      <div class="modal-title-group">
        <div class="modal-icon"><i class="bi bi-pencil-square"></i></div>
        <div>
          <h5 id="editModalTitle">Edit Profile</h5>
          <p>Update your name and email address</p>
        </div>
      </div>
      <button class="modal-close-btn" onclick="closeEditModal()" aria-label="Close"><i class="bi bi-x-lg"></i></button>
    </div>

    <form action="update_profile.php" method="POST" id="editForm">
      <div class="modal-body">

        <div class="form-group">
          <label class="form-label" for="editName">Full Name</label>
          <div class="input-wrap">
            <span class="input-icon"><i class="bi bi-person"></i></span>
            <input type="text" id="editName" name="username" class="form-input"
              value="<?php echo htmlspecialchars($username); ?>"
              placeholder="Enter your full name" required maxlength="80" />
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="editEmail">Email Address</label>
          <div class="input-wrap">
            <span class="input-icon"><i class="bi bi-envelope"></i></span>
            <input type="email" id="editEmail" name="email" class="form-input"
              value="<?php echo htmlspecialchars($email); ?>"
              placeholder="Enter your email" required maxlength="120" />
          </div>
        </div>

        <div id="editFormMsg" class="form-msg" style="display:none;"></div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn-ghost" onclick="closeEditModal()">Cancel</button>
        <button type="submit" class="btn-primary-custom" id="editSubmitBtn">
          <span class="btn-label"><i class="bi bi-check-lg"></i> Save Changes</span>
          <span class="btn-spinner" style="display:none;"><i class="bi bi-arrow-repeat spin"></i> Saving…</span>
        </button>
      </div>
    </form>
  </div>

  <!-- ═══════════════════════════════════════════
       CHANGE PASSWORD MODAL
  ═══════════════════════════════════════════ -->
  <div class="modal-backdrop" id="pwModalBackdrop" onclick="closePasswordModal()"></div>
  <div class="modal-panel" id="pwModal" role="dialog" aria-modal="true" aria-labelledby="pwModalTitle">
    <div class="modal-header">
      <div class="modal-title-group">
        <div class="modal-icon"><i class="bi bi-key-fill"></i></div>
        <div>
          <h5 id="pwModalTitle">Change Password</h5>
          <p>Choose a strong, unique password</p>
        </div>
      </div>
      <button class="modal-close-btn" onclick="closePasswordModal()" aria-label="Close"><i class="bi bi-x-lg"></i></button>
    </div>

    <form action="change_password.php" method="POST" id="pwForm">
      <div class="modal-body">

        <div class="form-group">
          <label class="form-label" for="currentPw">Current Password</label>
          <div class="input-wrap">
            <span class="input-icon"><i class="bi bi-lock"></i></span>
            <input type="password" id="currentPw" name="current_password" class="form-input"
              placeholder="Enter current password" required />
            <button type="button" class="toggle-pw" onclick="togglePw('currentPw', this)" tabindex="-1">
              <i class="bi bi-eye"></i>
            </button>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="newPw">New Password</label>
          <div class="input-wrap">
            <span class="input-icon"><i class="bi bi-lock-fill"></i></span>
            <input type="password" id="newPw" name="new_password" class="form-input"
              placeholder="Min. 8 characters" required minlength="8" oninput="checkStrength(this.value)" />
            <button type="button" class="toggle-pw" onclick="togglePw('newPw', this)" tabindex="-1">
              <i class="bi bi-eye"></i>
            </button>
          </div>
          <!-- Strength bar -->
          <div class="strength-bar-wrap">
            <div class="strength-bar" id="strengthBar"></div>
          </div>
          <div class="strength-label" id="strengthLabel"></div>
        </div>

        <div class="form-group">
          <label class="form-label" for="confirmPw">Confirm New Password</label>
          <div class="input-wrap">
            <span class="input-icon"><i class="bi bi-lock-fill"></i></span>
            <input type="password" id="confirmPw" name="confirm_password" class="form-input"
              placeholder="Repeat new password" required minlength="8" />
            <button type="button" class="toggle-pw" onclick="togglePw('confirmPw', this)" tabindex="-1">
              <i class="bi bi-eye"></i>
            </button>
          </div>
        </div>

        <div id="pwFormMsg" class="form-msg" style="display:none;"></div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn-ghost" onclick="closePasswordModal()">Cancel</button>
        <button type="submit" class="btn-primary-custom" id="pwSubmitBtn">
          <span class="btn-label"><i class="bi bi-shield-check"></i> Update Password</span>
          <span class="btn-spinner" style="display:none;"><i class="bi bi-arrow-repeat spin"></i> Updating…</span>
        </button>
      </div>
    </form>
  </div>

  <!-- ═══════════════════════════════════════════
       AVATAR CROP MODAL
  ═══════════════════════════════════════════ -->
  <div class="modal-backdrop" id="avatarModalBackdrop" onclick="closeAvatarModal()"></div>
  <div class="modal-panel avatar-modal-panel" id="avatarModal" role="dialog" aria-modal="true">
    <div class="modal-header">
      <div class="modal-title-group">
        <div class="modal-icon"><i class="bi bi-camera"></i></div>
        <div>
          <h5>Update Profile Photo</h5>
          <p>Preview your selected image</p>
        </div>
      </div>
      <button class="modal-close-btn" onclick="closeAvatarModal()" aria-label="Close"><i class="bi bi-x-lg"></i></button>
    </div>
    <div class="modal-body" style="display:flex; flex-direction:column; align-items:center; gap:20px;">
      <div class="avatar-preview-circle">
        <img id="avatarCropPreview" src="" alt="Preview" />
      </div>
      <p style="font-size:13px; color:#64748b; text-align:center;">This is how your photo will appear on your profile.</p>
      <div id="avatarUploadMsg" class="form-msg" style="display:none; width:100%;"></div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn-ghost" onclick="closeAvatarModal()">Cancel</button>
      <button type="button" class="btn-primary-custom" id="avatarSaveBtn" onclick="submitAvatar()">
        <span class="btn-label"><i class="bi bi-cloud-upload"></i> Upload Photo</span>
        <span class="btn-spinner" style="display:none;"><i class="bi bi-arrow-repeat spin"></i> Uploading…</span>
      </button>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    /* ── Modal helpers ── */
    function openModal(backdropId, modalId) {
      document.getElementById(backdropId).classList.add('active');
      document.getElementById(modalId).classList.add('active');
      document.body.style.overflow = 'hidden';
    }

    function closeModal(backdropId, modalId) {
      document.getElementById(backdropId).classList.remove('active');
      document.getElementById(modalId).classList.remove('active');
      document.body.style.overflow = '';
    }

    function openEditModal() {
      openModal('editModalBackdrop', 'editModal');
    }

    function closeEditModal() {
      closeModal('editModalBackdrop', 'editModal');
      clearMsg('editFormMsg');
    }

    function openPasswordModal() {
      openModal('pwModalBackdrop', 'pwModal');
    }

    function closePasswordModal() {
      closeModal('pwModalBackdrop', 'pwModal');
      clearMsg('pwFormMsg');
      resetStrength();
    }

    function closeAvatarModal() {
      closeModal('avatarModalBackdrop', 'avatarModal');
    }

    /* ESC key closes any open modal */
    document.addEventListener('keydown', e => {
      if (e.key === 'Escape') {
        closeEditModal();
        closePasswordModal();
        closeAvatarModal();
      }
    });

    /* ── Edit Profile Form (AJAX) ── */
    document.getElementById('editForm').addEventListener('submit', function(e) {
      e.preventDefault();
      const btn = document.getElementById('editSubmitBtn');
      setLoading(btn, true);
      clearMsg('editFormMsg');

      fetch('update_profile.php', {
          method: 'POST',
          body: new FormData(this)
        })
        .then(r => r.json())
        .then(data => {
          setLoading(btn, false);
          if (data.success) {
            // Update UI live
            document.getElementById('detailName').textContent = data.username;
            document.getElementById('detailEmail').textContent = data.email;
            document.getElementById('heroName').textContent = data.username;
            document.getElementById('heroEmail').textContent = data.email;
            // Sync sidebar name live
            if (typeof window.syncSidebarName === 'function') window.syncSidebarName(data.username);
            showMsg('editFormMsg', data.message, 'success');
            setTimeout(closeEditModal, 1400);
          } else {
            showMsg('editFormMsg', data.message, 'error');
          }
        })
        .catch(() => {
          setLoading(btn, false);
          showMsg('editFormMsg', 'Server error. Please try again.', 'error');
        });
    });

    /* ── Change Password Form (AJAX) ── */
    document.getElementById('pwForm').addEventListener('submit', function(e) {
      e.preventDefault();
      const np = document.getElementById('newPw').value;
      const cp = document.getElementById('confirmPw').value;
      if (np !== cp) {
        showMsg('pwFormMsg', 'New passwords do not match.', 'error');
        return;
      }

      const btn = document.getElementById('pwSubmitBtn');
      setLoading(btn, true);
      clearMsg('pwFormMsg');

      fetch('change_password.php', {
          method: 'POST',
          body: new FormData(this)
        })
        .then(r => r.json())
        .then(data => {
          setLoading(btn, false);
          if (data.success) {
            showMsg('pwFormMsg', data.message, 'success');
            document.getElementById('pwForm').reset();
            resetStrength();
            setTimeout(closePasswordModal, 1600);
          } else {
            showMsg('pwFormMsg', data.message, 'error');
          }
        })
        .catch(() => {
          setLoading(btn, false);
          showMsg('pwFormMsg', 'Server error. Please try again.', 'error');
        });
    });

    /* ── Avatar upload ── */
    document.getElementById('avatarInput').addEventListener('change', function() {
      const file = this.files[0];
      if (!file) return;
      if (file.size > 2 * 1024 * 1024) {
        alert('Image must be under 2 MB.');
        this.value = '';
        return;
      }
      const reader = new FileReader();
      reader.onload = e => {
        document.getElementById('avatarCropPreview').src = e.target.result;
        openModal('avatarModalBackdrop', 'avatarModal');
      };
      reader.readAsDataURL(file);
    });

    function submitAvatar() {
      const input = document.getElementById('avatarInput');
      if (!input.files[0]) return;

      const btn = document.getElementById('avatarSaveBtn');
      setLoading(btn, true);
      clearMsg('avatarUploadMsg');

      const fd = new FormData();
      fd.append('avatar', input.files[0]);

      fetch('update_avatar.php', {
          method: 'POST',
          body: fd
        })
        .then(r => r.json())
        .then(data => {
          setLoading(btn, false);
          if (data.success) {
            // Apply new avatar everywhere
            const src = data.avatar_url + '?t=' + Date.now();
            applyAvatar(src);
            showMsg('avatarUploadMsg', data.message, 'success');
            setTimeout(closeAvatarModal, 1200);
          } else {
            showMsg('avatarUploadMsg', data.message, 'error');
          }
        })
        .catch(() => {
          setLoading(btn, false);
          showMsg('avatarUploadMsg', 'Upload failed. Try again.', 'error');
        });
    }

    function applyAvatar(src) {
      // Hero avatar
      const icon = document.getElementById('avatarIcon');
      const preview = document.getElementById('avatarPreview');
      if (icon) icon.style.display = 'none';
      if (preview) {
        preview.src = src;
        preview.style.display = 'block';
      }
      // Sync sidebar avatar live
      if (typeof window.syncSidebarAvatar === 'function') window.syncSidebarAvatar(src);
    }

    /* ── Password strength checker ── */
    function checkStrength(pw) {
      const bar = document.getElementById('strengthBar');
      const label = document.getElementById('strengthLabel');
      let score = 0;
      if (pw.length >= 8) score++;
      if (pw.length >= 12) score++;
      if (/[A-Z]/.test(pw)) score++;
      if (/[0-9]/.test(pw)) score++;
      if (/[^A-Za-z0-9]/.test(pw)) score++;

      const levels = [{
          cls: 'strength-weak',
          text: 'Weak',
          color: '#ef4444'
        },
        {
          cls: 'strength-fair',
          text: 'Fair',
          color: '#f97316'
        },
        {
          cls: 'strength-good',
          text: 'Good',
          color: '#eab308'
        },
        {
          cls: 'strength-strong',
          text: 'Strong',
          color: '#22c55e'
        },
        {
          cls: 'strength-great',
          text: 'Very Strong',
          color: '#16a34a'
        },
      ];
      const idx = Math.min(Math.max(score - 1, 0), 4);
      const lvl = pw.length === 0 ? null : levels[idx];

      if (!lvl) {
        bar.style.width = '0';
        bar.style.background = '';
        label.textContent = '';
        return;
      }
      bar.style.width = ((idx + 1) / 5 * 100) + '%';
      bar.style.background = lvl.color;
      label.textContent = lvl.text;
      label.style.color = lvl.color;
    }

    function resetStrength() {
      document.getElementById('strengthBar').style.width = '0';
      document.getElementById('strengthLabel').textContent = '';
    }

    /* ── Toggle password visibility ── */
    function togglePw(inputId, btn) {
      const inp = document.getElementById(inputId);
      const ico = btn.querySelector('i');
      if (inp.type === 'password') {
        inp.type = 'text';
        ico.className = 'bi bi-eye-slash';
      } else {
        inp.type = 'password';
        ico.className = 'bi bi-eye';
      }
    }

    /* ── Utility ── */
    function setLoading(btn, loading) {
      btn.querySelector('.btn-label').style.display = loading ? 'none' : '';
      btn.querySelector('.btn-spinner').style.display = loading ? 'inline-flex' : 'none';
      btn.disabled = loading;
    }

    function showMsg(id, text, type) {
      const el = document.getElementById(id);
      el.textContent = text;
      el.className = 'form-msg form-msg-' + type;
      el.style.display = 'block';
    }

    function clearMsg(id) {
      const el = document.getElementById(id);
      el.style.display = 'none';
      el.textContent = '';
      el.className = 'form-msg';
    }
  </script>
</body>

</html>