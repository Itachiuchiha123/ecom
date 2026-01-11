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

        <div class="search-container">
            <input type="text" placeholder="Search food...">
            <div class="search-icon">🔍</div>
        </div>

        <nav>
            <a href="index.php" class="active">Home</a>
            <a href="pages/menu.php">Menu</a>
            <a href="pages/orders.php">Orders</a>
            <a href="pages/cart.php" class="cart-link">
                Cart
                <span class="cart-badge">0</span>
            </a>
        </nav>

        <div class="login-dropdown">
            <button class="login-btn" onclick="toggleLoginDropdown()">Login</button>
            <div class="dropdown-content" id="loginDropdown">
                <a href="pages/login.php?type=customer">Customer Login</a>
                <a href="pages/login.php?type=admin">Admin Login</a>
                <a href="pages/login.php?type=vendor">Vendor Login</a>
                <a href="pages/signup.php">Sign Up</a>
            </div>
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