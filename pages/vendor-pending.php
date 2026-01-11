<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Approval - Macroon Morning</title>
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/auth.css">
    <style>
        .pending-container {
            max-width: 600px;
            margin: 100px auto;
            padding: 40px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .pending-icon {
            font-size: 80px;
            margin-bottom: 20px;
        }

        .pending-title {
            font-size: 28px;
            color: #333;
            margin-bottom: 15px;
        }

        .pending-message {
            font-size: 16px;
            color: #666;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .back-btn {
            background: #ff6b6b;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            display: inline-block;
        }

        .back-btn:hover {
            background: #ff5252;
        }
    </style>
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

    <div class="pending-container">
        <div class="pending-icon">⏳</div>
        <h1 class="pending-title">Account Pending Approval</h1>
        <p class="pending-message">
            Thank you for registering as a vendor on Macroon Morning!<br><br>
            Your account is currently under review by our admin team. You will receive an email once your account has been approved.<br><br>
            This process usually takes 24-48 hours. We appreciate your patience!
        </p>
        <a href="../index.php" class="back-btn">Back to Home</a>
    </div>

    <script src="../js/main.js"></script>
</body>

</html>