<?php
$user_type = isset($_GET['type']) ? $_GET['type'] : 'customer';
$user_type_label = ucfirst($user_type);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $user_type_label; ?> Login - Macroon Morning</title>
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/auth.css">
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
            <a href="cart.php" class="cart-link">
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

    <div class="auth-container">
        <div class="auth-box">
            <h1>Login</h1>
            <p class="auth-subtitle">Please enter your login details to sign in</p>

            <div id="messageContainer"></div>

            <form id="loginForm" onsubmit="handleLogin(event)">
                <input type="hidden" name="user_type" value="<?php echo htmlspecialchars($user_type); ?>">

                <div class="form-group">
                    <label>Email Address</label>
                    <div class="input-wrapper">
                        <input type="email" name="email" placeholder="Enter your email" required>
                        <span class="input-icon">✉️</span>
                    </div>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <div class="input-wrapper">
                        <input type="password" name="password" placeholder="Enter your password" required>
                    </div>
                </div>

                <div class="form-options">
                    <label class="remember-me">
                        <input type="checkbox" name="remember">
                        <span>Keep me logged in</span>
                    </label>
                    <a href="#" class="forgot-password">Forgot password?</a>
                </div>

                <button type="submit" class="submit-btn">Log In</button>
            </form>

            <div class="auth-footer">
                Don't have an account? <a href="signup.php">Sign up</a>
            </div>
        </div>
    </div>

    <script src="../js/main.js"></script>
    <script>
        function handleLogin(event) {
            event.preventDefault();

            const form = event.target;
            const formData = new FormData(form);

            fetch('../php/login_handler.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    const messageContainer = document.getElementById('messageContainer');

                    if (data.success) {
                        // Save user to localStorage
                        User.save(data.user);

                        messageContainer.innerHTML = '<div class="message success">Login successful! Redirecting...</div>';

                        setTimeout(() => {
                            // Redirect based on user type
                            if (data.user.user_type === 'admin') {
                                window.location.href = 'admin/index.php';
                            } else if (data.user.user_type === 'vendor') {
                                window.location.href = 'vendor/index.php';
                            } else {
                                window.location.href = '../index.php';
                            }
                        }, 1000);
                    } else {
                        // Check if vendor is pending approval
                        if (data.pending) {
                            messageContainer.innerHTML = '<div class="message error">' + data.message + '</div>';
                        } else {
                            messageContainer.innerHTML = '<div class="message error">' + data.message + '</div>';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('messageContainer').innerHTML =
                        '<div class="message error">An error occurred. Please try again.</div>';
                });
        }
    </script>
</body>

</html>