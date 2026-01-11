<?php
require_once 'php/config.php';

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
    <title>Macroon Morning - Home</title>
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/home.css">
</head>

<body>
    <header>
        <div class="logo">
            <span class="logo-icon">🍰</span>
            <span>Macroon Morning</span>
        </div>

        <nav>
            <a href="index.php" class="active">Home</a>
            <a href="pages/menu.php">Menu</a>
            <a href="pages/orders.php">Orders</a>
            <?php if ($user && $user['user_type'] === 'admin'): ?>
                <a href="pages/admin/index.php">Dashboard</a>
            <?php endif; ?>
            <a href="pages/cart.php" class="cart-link">
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
                        <a href="pages/orders.php">My Orders</a>
                        <a href="#" onclick="logout(); return false;">Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <button class="login-btn" onclick="toggleLoginDropdown()">Login</button>
                <div class="dropdown-content" id="loginDropdown">
                    <a href="pages/login.php?type=customer">Customer Login</a>
                    <a href="pages/login.php?type=admin">Admin Login</a>
                    <a href="pages/login.php?type=vendor">Vendor Login</a>
                    <a href="pages/signup.php">Sign Up</a>
                </div>
            <?php endif; ?>
        </div>
    </header>

    <section class="hero">
        <div class="hero-content">
            <h1>
                Enjoy your food<br>
                at <span class="highlight">your place</span>
            </h1>
            <button class="order-btn" onclick="window.location.href='pages/menu.php'">Order Now</button>
        </div>

        <div class="hero-image">
            Delicious Food
        </div>
    </section>

    <script src="js/main.js"></script>
</body>

</html>