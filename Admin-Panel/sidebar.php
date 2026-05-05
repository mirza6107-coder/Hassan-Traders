<?php
$current_page = basename($_SERVER['PHP_SELF']);
$user_name = isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : (isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Guest');
$avatar_path  = isset($_SESSION['avatar'])    ? $_SESSION['avatar']    : null;
$initials     = strtoupper(substr($user_name, 0, 2));
?>

<link rel="stylesheet" href="sidebar.css">

<aside class="sidebar">
    <div class="sidebar-logo">
        <img
            src="../Images/Hassan Traders logo 2.png"
            alt="Hassan Traders"
            onerror="this.style.display='none';" />
    </div>

    <div class="sidebar-title">Admin Panel</div>

    <nav>
        <a href="dashboard.php" class="<?php echo ($current_page == 'dashboard.php')      ? 'active' : ''; ?>">
            <i class="bi bi-speedometer2"></i><span>Dashboard</span>
        </a>
        <a href="all-products.php" class="<?php echo ($current_page == 'all-products.php')   ? 'active' : ''; ?>">
            <i class="bi bi-grid-3x3-gap"></i><span>All Products</span>
        </a>
        <a href="addNEWproducts.php" class="<?php echo ($current_page == 'addNEWproducts.php') ? 'active' : ''; ?>">
            <i class="bi bi-plus-circle"></i><span>Add NEW Product</span>
        </a>
        <a href="categories.php" class="<?php echo ($current_page == 'categories.php')     ? 'active' : ''; ?>">
            <i class="bi bi-tags"></i><span>Categories</span>
        </a>
        <a href="orders.php" class="<?php echo ($current_page == 'orders.php')         ? 'active' : ''; ?>">
            <i class="bi bi-bag-check"></i><span>Orders</span>
        </a>
        <a href="customers.php" class="<?php echo ($current_page == 'customers.php')      ? 'active' : ''; ?>">
            <i class="bi bi-people"></i><span>Customers</span>
        </a>
        <a href="inventory.php" class="<?php echo ($current_page == 'inventory.php')      ? 'active' : ''; ?>">
            <i class="bi bi-box-seam"></i><span>Inventory</span>
        </a>
        <a href="reports.php" class="<?php echo ($current_page == 'reports.php')        ? 'active' : ''; ?>">
            <i class="bi bi-bar-chart-line"></i><span>Reports</span>
        </a>
        <a href="returns.php" class="<?php echo ($current_page == 'returns.php')        ? 'active' : ''; ?>">
            <i class="bi bi-arrow-return-left"></i><span>Returns</span>
        </a>
        <a href="profile.php" class="<?php echo ($current_page == 'profile.php')        ? 'active' : ''; ?>">
            <i class="bi bi-person-circle"></i><span>Profile</span>
        </a>
        <a href="messages.php" class="<?php echo ($current_page == 'messages.php')       ? 'active' : ''; ?>">
            <i class="bi bi-envelope"></i><span>Messages</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user">

            <!-- Avatar: photo if uploaded, initials otherwise -->
            <div class="sidebar-user-avatar" id="sidebarAvatarWrap">
                <?php if ($avatar_path): ?>
                    <img
                        src="<?php echo htmlspecialchars($avatar_path); ?>"
                        alt="<?php echo htmlspecialchars($initials); ?>"
                        class="sidebar-avatar-img"
                        id="sidebarAvatarImg"
                        onerror="this.style.display='none'; document.getElementById('sidebarAvatarInitials').style.display='flex';" />
                    <span class="sidebar-avatar-initials" id="sidebarAvatarInitials" style="display:none;">
                        <?php echo $initials; ?>
                    </span>
                <?php else: ?>
                    <img src="" alt="" class="sidebar-avatar-img" id="sidebarAvatarImg" style="display:none;" />
                    <span class="sidebar-avatar-initials" id="sidebarAvatarInitials">
                        <?php echo $initials; ?>
                    </span>
                <?php endif; ?>
            </div>

            <div class="sidebar-user-info">
                <div class="sidebar-user-name" id="sidebarUserName">
                    <?php echo htmlspecialchars($user_name); ?>
                </div>
                <div class="sidebar-user-role">Administrator</div>
            </div>

            <a href="../login and signup/logout.php" class="sidebar-logout-btn" title="Logout">
                <i class="bi bi-box-arrow-right"></i>
            </a>
        </div>

        <a href="../index.php" class="btn-back">
            <i class="bi bi-arrow-left"></i> Back to Website
        </a>
    </div>
</aside>

<!--  this script updates the sidebar avatar & name without a page reload.-->
<script>
    window.syncSidebarAvatar = function(src) {
        const img = document.getElementById('sidebarAvatarImg');
        const initials = document.getElementById('sidebarAvatarInitials');
        if (!img) return;
        img.src = src;
        img.style.display = 'block';
        if (initials) initials.style.display = 'none';
    };

    window.syncSidebarName = function(name) {
        const el = document.getElementById('sidebarUserName');
        if (el) el.textContent = name;
    };
</script>