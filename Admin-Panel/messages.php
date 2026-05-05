<?php
session_start();
if (!isset($_SESSION['admin_role']) || $_SESSION['admin_role'] !== 'admin') {
    // If the admin session isn't found, send them back to login
    header("Location: ../login and signup/login.php");
    exit();
}
?>
<?php
ob_start();
ini_set('display_errors', 0);

$conn = mysqli_connect('localhost', 'root', '', 'htss');
$messages = [];
$stats = ['total' => 0, 'unread' => 0, 'today' => 0, 'replied' => 0];
$db_error = '';

if (!$conn) {
  $db_error = 'Database connection failed: ' . mysqli_connect_error();
} else {
  // Ensure the table has a 'read_status' column (add it if missing)
  mysqli_query($conn, "ALTER TABLE contact_messages ADD COLUMN IF NOT EXISTS read_status TINYINT(1) NOT NULL DEFAULT 0");
  mysqli_query($conn, "ALTER TABLE contact_messages ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");

  $result = mysqli_query($conn, "SELECT * FROM contact_messages ORDER BY id DESC");
  if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
      $messages[] = $row;
    }
  } else {
    $db_error = mysqli_error($conn);
  }

  // Stats
  $stats['total']   = count($messages);
  $stats['unread']  = count(array_filter($messages, fn($m) => empty($m['read_status'])));
  $stats['today']   = count(array_filter($messages, fn($m) => !empty($m['created_at']) && date('Y-m-d', strtotime($m['created_at'])) === date('Y-m-d')));
  $stats['replied'] = $stats['total'] - $stats['unread'];
}

// Handle mark-as-read via POST (AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
  header('Content-Type: application/json');
  ob_end_clean();
  if ($_POST['action'] === 'mark_read' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    $ok = mysqli_query($conn, "UPDATE contact_messages SET read_status = 1 WHERE id = $id");
    echo json_encode(['success' => (bool)$ok]);
  } elseif ($_POST['action'] === 'delete' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    $ok = mysqli_query($conn, "DELETE FROM contact_messages WHERE id = $id");
    echo json_encode(['success' => (bool)$ok]);
  } else {
    echo json_encode(['success' => false, 'message' => 'Unknown action']);
  }
  exit;
}
ob_end_clean();
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Messages - Hassan Traders Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
  <link rel="stylesheet" href="messages.css">
</head>

<body>

  <!-- ══ SIDEBAR ══ -->
  <?php include('sidebar.php'); ?>

  <!-- ══ MAIN ══ -->
  <main class="main">
    <div class="topbar">
      <div class="topbar-title">
        <h4>Messages</h4>
        <p>Contact form submissions from customers</p>
      </div>
      <div class="topbar-actions">
        <button class="btn-mark-all" onclick="markAllRead()">
          <i class="bi bi-check2-all"></i> Mark All Read
        </button>
      </div>
    </div>

    <div class="content">

      <?php if ($db_error): ?>
        <div class="error-banner"><i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($db_error) ?></div>
      <?php endif; ?>

      <!-- Stat Cards -->
      <div class="stat-row">
        <div class="stat-card">
          <div class="stat-icon total"><i class="bi bi-envelope-open"></i></div>
          <div>
            <div class="stat-val" id="statTotal"><?= $stats['total'] ?></div>
            <div class="stat-label">Total Messages</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon unread"><i class="bi bi-envelope-fill"></i></div>
          <div>
            <div class="stat-val" id="statUnread"><?= $stats['unread'] ?></div>
            <div class="stat-label">Unread</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon today"><i class="bi bi-calendar-check"></i></div>
          <div>
            <div class="stat-val"><?= $stats['today'] ?></div>
            <div class="stat-label">Today</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon replied"><i class="bi bi-reply-fill"></i></div>
          <div>
            <div class="stat-val"><?= $stats['replied'] ?></div>
            <div class="stat-label">Read</div>
          </div>
        </div>
      </div>

      <!-- Filter Bar -->
      <div class="filter-bar">
        <div class="search-box">
          <i class="bi bi-search" style="color:var(--text-muted)"></i>
          <input type="text" id="searchInput" placeholder="Search by name, email, subject, message…" />
        </div>
        <select id="statusFilter" class="filter-select">
          <option value="">All Messages</option>
          <option value="unread">Unread Only</option>
          <option value="read">Read Only</option>
        </select>
      </div>

      <!-- Messages Table -->
      <div class="card-panel">
        <div class="table-responsive">
          <table>
            <thead>
              <tr>
                <th style="width:28px"></th>
                <th>From</th>
                <th>Subject</th>
                <th>Message Preview</th>
                <th>Date</th>
                <th style="text-align:right">Actions</th>
              </tr>
            </thead>
            <tbody id="messagesBody">
              <?php if (empty($messages)): ?>
                <tr>
                  <td colspan="6">
                    <div class="empty-state">
                      <i class="bi bi-inbox"></i>
                      No messages yet. They'll appear here when customers contact you.
                    </div>
                  </td>
                </tr>
              <?php else: ?>
                <?php foreach ($messages as $m): ?>
                  <?php
                  $isUnread = empty($m['read_status']);
                  $date = !empty($m['created_at']) ? date('d M Y, h:i A', strtotime($m['created_at'])) : '—';
                  $subject = htmlspecialchars($m['subject'] ?? '(No subject)');
                  $preview = htmlspecialchars(mb_strimwidth($m['message'] ?? '', 0, 80, '…'));
                  $name    = htmlspecialchars($m['full_name'] ?? '—');
                  $email   = htmlspecialchars($m['email'] ?? '—');
                  ?>
                  <tr class="<?= $isUnread ? 'unread-row' : '' ?>" id="row-<?= $m['id'] ?>"
                    data-id="<?= $m['id'] ?>"
                    data-name="<?= $name ?>"
                    data-email="<?= $email ?>"
                    data-phone="<?= htmlspecialchars($m['phone'] ?? '—') ?>"
                    data-company="<?= htmlspecialchars($m['company'] ?? '—') ?>"
                    data-subject="<?= $subject ?>"
                    data-message="<?= htmlspecialchars($m['message'] ?? '') ?>"
                    data-date="<?= $date ?>"
                    data-read="<?= $isUnread ? '0' : '1' ?>">
                    <td><?php if ($isUnread): ?><span class="unread-dot"></span><?php endif; ?></td>
                    <td>
                      <strong><?= $name ?></strong>
                      <br><span style="font-size:12px;color:var(--text-muted)"><?= $email ?></span>
                    </td>
                    <td style="font-size:13px"><?= $subject ?></td>
                    <td>
                      <div class="msg-preview"><?= $preview ?></div>
                    </td>
                    <td style="font-size:12px;color:var(--text-muted);white-space:nowrap"><?= $date ?></td>
                    <td style="text-align:right;white-space:nowrap">
                      <button class="action-btn btn-view" onclick="viewMessage(<?= $m['id'] ?>)" title="View"><i class="bi bi-eye"></i></button>
                      <button class="action-btn btn-delete" onclick="deleteMessage(<?= $m['id'] ?>)" title="Delete" style="margin-left:6px"><i class="bi bi-trash"></i></button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
        <div class="table-footer">
          <span class="result-count" id="resultCount">
            <?= count($messages) ?> message<?= count($messages) !== 1 ? 's' : '' ?>
          </span>
        </div>
      </div>

    </div>
  </main>

  <!-- ══ MESSAGE DETAIL MODAL ══ -->
  <div class="modal-overlay" id="msgModal">
    <div class="modal-box">
      <div class="modal-header">
        <h5 id="modalTitle">Message Details</h5>
        <button class="modal-close" onclick="closeModal()">×</button>
      </div>
      <div class="modal-body" id="modalBody"></div>
      <div class="modal-footer">
        <button class="btn-outline-custom" onclick="closeModal()">Close</button>
        <a class="btn-reply" id="replyBtn" href="#" target="_blank">
          <i class="bi bi-reply-fill"></i> Reply via Email
        </a>
      </div>
    </div>
  </div>

  <!-- ══ TOAST ══ -->
  <div class="toast-msg" id="toastMsg"></div>

  <script>
    // ── Build JS message array from PHP ─────────────────────────────────────────
    var MESSAGES = [];
    document.querySelectorAll('tbody tr[data-id]').forEach(function(row) {
      MESSAGES.push({
        id: parseInt(row.dataset.id),
        name: row.dataset.name,
        email: row.dataset.email,
        phone: row.dataset.phone,
        company: row.dataset.company,
        subject: row.dataset.subject,
        message: row.dataset.message,
        date: row.dataset.date,
        read: row.dataset.read === '1'
      });
    });

    // ── Search & filter ──────────────────────────────────────────────────────────
    document.getElementById('searchInput').addEventListener('input', filterTable);
    document.getElementById('statusFilter').addEventListener('change', filterTable);

    function filterTable() {
      var q = document.getElementById('searchInput').value.toLowerCase().trim();
      var status = document.getElementById('statusFilter').value;
      var shown = 0;

      MESSAGES.forEach(function(m) {
        var row = document.getElementById('row-' + m.id);
        if (!row) return;
        var matchQ = !q ||
          m.name.toLowerCase().includes(q) ||
          m.email.toLowerCase().includes(q) ||
          m.subject.toLowerCase().includes(q) ||
          m.message.toLowerCase().includes(q);
        var matchS = !status ||
          (status === 'unread' && !m.read) ||
          (status === 'read' && m.read);
        var visible = matchQ && matchS;
        row.style.display = visible ? '' : 'none';
        if (visible) shown++;
      });

      document.getElementById('resultCount').textContent =
        shown + ' message' + (shown !== 1 ? 's' : '');
    }

    // ── View message modal ───────────────────────────────────────────────────────
    function viewMessage(id) {
      var m = MESSAGES.find(function(x) {
        return x.id === id;
      });
      if (!m) return;

      document.getElementById('modalTitle').textContent =
        m.subject || '(No subject)';
      document.getElementById('replyBtn').href =
        'mailto:' + m.email + '?subject=Re: ' + encodeURIComponent(m.subject);

      document.getElementById('modalBody').innerHTML =
        '<div class="info-grid">' +
        '<div class="info-block"><label>From</label><p><strong>' + m.name + '</strong></p></div>' +
        '<div class="info-block"><label>Email</label><p><a href="mailto:' + m.email + '">' + m.email + '</a></p></div>' +
        '<div class="info-block"><label>Phone</label><p>' + m.phone + '</p></div>' +
        '<div class="info-block"><label>Company</label><p>' + m.company + '</p></div>' +
        '<div class="info-block"><label>Date</label><p>' + m.date + '</p></div>' +
        '<div class="info-block"><label>Subject</label><p>' + m.subject + '</p></div>' +
        '</div>' +
        '<label style="font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">Message</label>' +
        '<div class="msg-full">' + m.message.replace(/</g, '&lt;') + '</div>';

      document.getElementById('msgModal').classList.add('show');

      // Mark as read if unread
      if (!m.read) {
        markRead(id);
      }
    }

    function closeModal() {
      document.getElementById('msgModal').classList.remove('show');
    }
    document.getElementById('msgModal').addEventListener('click', function(e) {
      if (e.target === this) closeModal();
    });

    // ── Mark single message read ─────────────────────────────────────────────────
    function markRead(id) {
      fetch('messages.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: 'action=mark_read&id=' + id
        })
        .then(function(r) {
          return r.json();
        })
        .then(function(res) {
          if (res.success) {
            var m = MESSAGES.find(function(x) {
              return x.id === id;
            });
            if (m) m.read = true;
            var row = document.getElementById('row-' + id);
            if (row) {
              row.classList.remove('unread-row');
              row.dataset.read = '1';
              var dot = row.querySelector('.unread-dot');
              if (dot) dot.remove();
            }
            updateUnreadCount();
          }
        });
    }

    // ── Mark ALL read ────────────────────────────────────────────────────────────
    function markAllRead() {
      var unread = MESSAGES.filter(function(m) {
        return !m.read;
      });
      if (!unread.length) {
        showToast('No unread messages.');
        return;
      }
      var promises = unread.map(function(m) {
        return fetch('messages.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: 'action=mark_read&id=' + m.id
        }).then(function(r) {
          return r.json();
        });
      });
      Promise.all(promises).then(function() {
        MESSAGES.forEach(function(m) {
          m.read = true;
        });
        document.querySelectorAll('tbody tr[data-id]').forEach(function(row) {
          row.classList.remove('unread-row');
          row.dataset.read = '1';
          var dot = row.querySelector('.unread-dot');
          if (dot) dot.remove();
        });
        updateUnreadCount();
        showToast('All messages marked as read.');
      });
    }

    // ── Delete message ───────────────────────────────────────────────────────────
    function deleteMessage(id) {
      if (!confirm('Delete this message permanently?')) return;
      fetch('messages.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: 'action=delete&id=' + id
        })
        .then(function(r) {
          return r.json();
        })
        .then(function(res) {
          if (res.success) {
            var row = document.getElementById('row-' + id);
            if (row) row.remove();
            MESSAGES = MESSAGES.filter(function(m) {
              return m.id !== id;
            });
            updateUnreadCount();
            document.getElementById('statTotal').textContent = MESSAGES.length;
            filterTable();
            showToast('Message deleted.');
          } else {
            showToast('Failed to delete message.', true);
          }
        });
    }

    // ── Update unread badge/count ────────────────────────────────────────────────
    function updateUnreadCount() {
      var count = MESSAGES.filter(function(m) {
        return !m.read;
      }).length;
      document.getElementById('statUnread').textContent = count;
      var badge = document.querySelector('.badge-nav');
      if (badge) {
        if (count > 0) {
          badge.textContent = count;
          badge.style.display = '';
        } else badge.style.display = 'none';
      }
    }

    // ── Toast notification ───────────────────────────────────────────────────────
    function showToast(msg, isError) {
      var t = document.getElementById('toastMsg');
      t.textContent = msg;
      t.style.background = isError ? '#991b1b' : '#1a1a2e';
      t.classList.add('show');
      setTimeout(function() {
        t.classList.remove('show');
      }, 3000);
    }
  </script>
</body>

</html>