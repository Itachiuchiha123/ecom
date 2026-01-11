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

        <div class="search-container">
            <input type="text" placeholder="Search food...">
            <div class="search-icon">🔍</div>
        </div>

        <nav>
            <a href="../index.php">Home</a>
            <a href="menu.php">Menu</a>
            <a href="orders.php">Orders</a>
            <a href="cart.php" class="cart-link active">
                Cart
                <span class="cart-badge">0</span>
            </a>
        </nav>

        <div class="login-dropdown">
            <button class="login-btn" onclick="toggleLoginDropdown()">Login</button>
            <div class="dropdown-content" id="loginDropdown">
                <a href="login.php?type=customer">Customer Login</a>
                <a href="login.php?type=admin">Admin Login</a>
                <a href="login.php?type=vendor">Vendor Login</a>
                <a href="signup.php">Sign Up</a>
            </div>
        </div>
    </header>

    <div class="cart-container">
        <div class="order-summary">
            <h2>Order Summary</h2>

            <div class="summary-row">
                <span>Subtotal</span>
                <span id="subtotal">$0.00</span>
            </div>

            <div class="summary-row">
                <span>Tax (10%)</span>
                <span id="tax">$0.00</span>
            </div>

            <div class="summary-row">
                <span>Delivery Fee</span>
                <span id="delivery">$3.99</span>
            </div>

            <div class="summary-divider"></div>

            <div class="summary-total">
                <span>Total</span>
                <span id="total">$3.99</span>
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