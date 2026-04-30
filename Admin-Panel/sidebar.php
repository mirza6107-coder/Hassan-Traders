<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<link rel="stylesheet" href="sidebar.css">

<aside class="sidebar">
    <div class="sidebar-logo">
        <img
            src="../Images/Hassan Traders logo 2.png"
            alt="Hassan Traders"
            onerror="this.style.display='none'; this.nextElementSibling.style.display='block';" />
    </div>

    <div class="sidebar-title">Admin Panel</div>

    <nav>
        <a href="dashboard.php" class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
        </a>
        <a href="all-products.php" class="<?php echo ($current_page == 'all-products.php') ? 'active' : ''; ?>">
            <i class="bi bi-grid-3x3-gap"></i>
            <span>All Products</span>
        </a>
        <a href="addNEWproducts.php" class="<?php echo ($current_page == 'add-products.php') ? 'active' : ''; ?>">
            <i class="bi bi-plus-circle"></i>
            <span>Add NEW Product</span>
        </a>
        <a href="categories.php" class="<?php echo ($current_page == 'categories.php') ? 'active' : ''; ?>">
            <i class="bi bi-tags"></i>
            <span>Categories</span>
        </a>
        <a href="orders.php" class="<?php echo ($current_page == 'orders.php') ? 'active' : ''; ?>">
            <i class="bi bi-bag-check"></i>
            <span>Orders</span>
        </a>
        <a href="customers.php" class="<?php echo ($current_page == 'customers.php') ? 'active' : ''; ?>">
            <i class="bi bi-people"></i>
            <span>Customers</span>
        </a>
        <a href="inventory.php" class="<?php echo ($current_page == 'inventory.php') ? 'active' : ''; ?>">
            <i class="bi bi-box-seam"></i>
            <span>Inventory</span>
        </a>
        <a href="reports.php" class="<?php echo ($current_page == 'reports.php') ? 'active' : ''; ?>">
            <i class="bi bi-bar-chart-line"></i>
            <span>Reports</span>
        </a>
        <a href="returns.php" class="<?php echo ($current_page == 'returns.php') ? 'active' : ''; ?>">
            <i class="bi bi-arrow-return-left"></i>
            <span>Returns</span>
        </a>
        <a href="profile.php" class="<?php echo ($current_page == 'profile.php') ? 'active' : ''; ?>">
            <i class="bi bi-person-circle"></i>
            <span>Profile</span>
        </a>
        <a href="messages.php" class="<?php echo ($current_page == 'messages.php') ? 'active' : ''; ?>">
            <i class="bi bi-envelope"></i>
            <span>Messages</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="sidebar-user-avatar">
                <?php
                $user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Admin';
                echo strtoupper(substr($user_name, 0, 2));
                ?>
            </div>
            <div>
                <div class="sidebar-user-name"><?php echo htmlspecialchars($user_name); ?></div>
                <div class="sidebar-user-role">Administrator</div>
            </div>
            <a href="../login and signup/logout.php" class="text-danger ms-auto" title="Logout">
                <i class="bi bi-box-arrow-right"></i>
            </a>
        </div>

        <a href="../Home/home.php" class="btn-back">
            <i class="bi bi-arrow-left"></i> Back to Website
        </a>
    </div>
</aside>