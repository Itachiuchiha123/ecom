<?php
require_once '../../php/config.php';
require_once '../../php/auth.php';

// Check if user is admin
if (!isLoggedIn() || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../login.php?type=admin');
    exit;
}

$conn = getDBConnection();

// Get statistics
$stats = [];

// Total users
$result = $conn->query("SELECT COUNT(*) as count FROM users WHERE user_type = 'customer'");
$stats['users'] = $result->fetch_assoc()['count'];

// Total products
$result = $conn->query("SELECT COUNT(*) as count FROM products");
$stats['products'] = $result->fetch_assoc()['count'];

// Total orders
$result = $conn->query("SELECT COUNT(*) as count FROM orders");
$stats['orders'] = $result->fetch_assoc()['count'];

// Total revenue
$result = $conn->query("SELECT SUM(total_amount) as total FROM orders WHERE status != 'cancelled'");
$stats['revenue'] = $result->fetch_assoc()['total'] ?? 0;

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Macroon Morning</title>
    <link rel="stylesheet" href="../../css/common.css">
    <link rel="stylesheet" href="../../css/admin.css">
</head>

<body>
    <header>
        <div class="logo">
            <span class="logo-icon">🍰</span>
            <span>Macroon Morning - Admin</span>
        </div>

        <nav>
            <a href="../../index.php">Home</a>
            <a href="../menu.php">Menu</a>
            <a href="index.php" class="active">Dashboard</a>
            <a href="vendors.php">Vendors</a>
        </nav>

        <div class="login-dropdown">
            <div class="user-profile">
                <div class="user-name" onclick="toggleUserDropdown()">
                    Admin
                </div>
                <div class="dropdown-content" id="userDropdown">
                    <a href="#" onclick="logout(); return false;">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <div class="admin-dashboard">
        <div class="admin-header">
            <h1>Admin Dashboard</h1>
        </div>

        <div class="admin-stats">
            <div class="stat-card">
                <h3>Total Customers</h3>
                <div class="stat-value"><?php echo $stats['users']; ?></div>
            </div>

            <div class="stat-card">
                <h3>Total Products</h3>
                <div class="stat-value"><?php echo $stats['products']; ?></div>
            </div>

            <div class="stat-card">
                <h3>Total Orders</h3>
                <div class="stat-value"><?php echo $stats['orders']; ?></div>
            </div>

            <div class="stat-card">
                <h3>Total Revenue</h3>
                <div class="stat-value">Rs <?php echo number_format($stats['revenue'], 2); ?></div>
            </div>
        </div>

        <div class="admin-actions">
            <div class="action-card" onclick="window.location.href='users.php'">
                <div class="action-card-icon">👥</div>
                <h3>Manage Users</h3>
                <p>View, add, edit, and delete users</p>
            </div>

            <div class="action-card" onclick="window.location.href='products.php'">
                <div class="action-card-icon">🍰</div>
                <h3>Manage Products</h3>
                <p>View, add, edit, and delete products</p>
            </div>

            <div class="action-card" onclick="window.location.href='orders.php'">
                <div class="action-card-icon">📦</div>
                <h3>Manage Orders</h3>
                <p>View and update order status</p>
            </div>
        </div>
    </div>

    <script src="../../js/main.js"></script>
</body>

</html>