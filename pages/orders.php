<?php
require_once '../php/config.php';

// Check if user is logged in
$user = null;
if (isset($_SESSION['user_id'])) {
    $conn = getDBConnection();
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
}

// Fetch user orders if logged in
$orders = [];
if ($user) {
    $conn = getDBConnection();
    $user_id = $user['id'];

    $sql = "SELECT o.*, 
            (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count
            FROM orders o 
            WHERE o.user_id = ? 
            ORDER BY o.created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        // Get order items
        $order_id = $row['id'];
        $items_sql = "SELECT oi.*, p.name as product_name, p.image as product_image 
                      FROM order_items oi 
                      LEFT JOIN products p ON oi.product_id = p.id 
                      WHERE oi.order_id = ?";
        $items_stmt = $conn->prepare($items_sql);
        $items_stmt->bind_param("i", $order_id);
        $items_stmt->execute();
        $items_result = $items_stmt->get_result();

        $items = [];
        while ($item = $items_result->fetch_assoc()) {
            $items[] = $item;
        }

        $row['items'] = $items;
        $orders[] = $row;
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Orders - Macroon Morning</title>
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/orders.css">
</head>

<body>
    <header>
        <div class="logo">
            <span class="logo-icon">🍰</span>
            <span>Macroon Morning</span>
        </div>

        <nav>
            <a href="../index.php">Home</a>
            <a href="menu.php">Menu</a>
            <a href="orders.php" class="active">Orders</a>
            <?php if ($user && $user['user_type'] === 'admin'): ?>
                <a href="admin/index.php">Dashboard</a>
            <?php endif; ?>
            <a href="cart.php" class="cart-link">
                Cart
                <span class="cart-badge">0</span>
            </a>
        </nav>

        <div class="login-dropdown">
            <?php if ($user): ?>
                <div class="user-profile">
                    <div class="user-name" onclick="toggleUserDropdown()">
                        <?php echo htmlspecialchars($user['name']); ?>
                    </div>
                    <div class="dropdown-content" id="userDropdown">
                        <a href="orders.php">My Orders</a>
                        <a href="#" onclick="logout()">Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <button class="login-btn" onclick="toggleLoginDropdown()">Login</button>
                <div class="dropdown-content" id="loginDropdown">
                    <a href="login.php?type=customer">Customer Login</a>
                    <a href="login.php?type=admin">Admin Login</a>
                    <a href="login.php?type=vendor">Vendor Login</a>
                    <a href="signup.php">Sign Up</a>
                </div>
            <?php endif; ?>
        </div>
    </header>

    <div class="orders-container">
        <h1>Your Orders</h1>

        <?php if (!$user): ?>
            <div class="empty-cart">
                <div class="empty-cart-text">Please login to view your orders</div>
                <button class="start-shopping-btn" onclick="window.location.href='login.php?type=customer'">Login</button>
            </div>
        <?php elseif (empty($orders)): ?>
            <div class="empty-cart">
                <div class="empty-cart-icon">📦</div>
                <div class="empty-cart-text">You haven't placed any orders yet</div>
                <button class="start-shopping-btn" onclick="window.location.href='menu.php'">Start Shopping</button>
            </div>
        <?php else: ?>
            <div class="orders-list">
                <?php foreach ($orders as $order): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <div>
                                <h3>Order #<?php echo $order['id']; ?></h3>
                                <p><?php echo date('F j, Y', strtotime($order['created_at'])); ?></p>
                            </div>
                            <div>
                                <div class="order-status <?php echo $order['status']; ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </div>
                            </div>
                        </div>

                        <div class="order-items-list">
                            <?php foreach ($order['items'] as $item): ?>
                                <div class="summary-row" style="display: flex; align-items: center; gap: 10px;">
                                    <?php if (!empty($item['product_image']) && file_exists(__DIR__ . '/../public/images/products/' . $item['product_image'])): ?>
                                        <img src="../public/images/products/<?php echo htmlspecialchars($item['product_image']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                    <?php else: ?>
                                        <div style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; background: #f0f0f0; border-radius: 4px; font-size: 24px;">🍰</div>
                                    <?php endif; ?>
                                    <span style="flex: 1;"><?php echo htmlspecialchars($item['product_name']); ?> x <?php echo $item['quantity']; ?></span>
                                    <span>Rs <?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="summary-divider"></div>

                        <div class="summary-total">
                            <span>Total</span>
                            <span>Rs <?php echo number_format($order['total_amount'], 2); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="../js/main.js"></script>
</body>

</html>