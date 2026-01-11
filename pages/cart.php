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
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart - Macroon Morning</title>
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/cart.css">
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
            <a href="orders.php">Orders</a>
            <?php if ($user && $user['user_type'] === 'admin'): ?>
                <a href="admin/index.php">Dashboard</a>
            <?php endif; ?>
            <a href="cart.php" class="cart-link active">
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
                        <a href="#" onclick="logout(); return false;">Logout</a>
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

    <div class="cart-container">
        <div class="order-summary">
            <h2>Order Summary</h2>

            <div class="summary-row">
                <span>Subtotal</span>
                <span id="subtotal">Rs 0.00</span>
            </div>

            <div class="summary-row">
                <span>Delivery Fee</span>
                <span id="delivery">Rs 3.99</span>
            </div>

            <div class="summary-divider"></div>

            <div class="summary-total">
                <span>Total</span>
                <span id="total">Rs 3.99</span>
            </div>

            <button class="checkout-btn" onclick="checkout()">Checkout Now</button>
        </div>

        <div class="cart-items">
            <h2>Your Cart (<span id="itemCount">0</span> items)</h2>

            <div id="cartContent">
                <div class="empty-cart">
                    <div class="empty-cart-icon">😢</div>
                    <div class="empty-cart-text">Your cart is empty</div>
                    <button class="start-shopping-btn" onclick="window.location.href='menu.php'">Start Shopping</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/main.js"></script>
    <script src="../js/cart.js"></script>
</body>

</html>