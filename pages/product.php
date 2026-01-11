<?php
require_once '../php/config.php';

// Check if user is logged in
$user = null;
if (isset($_SESSION['user_id'])) {
    $user_conn = getDBConnection();
    $user_id = $_SESSION['user_id'];

    $user_stmt = $user_conn->prepare("SELECT * FROM users WHERE id = ?");
    $user_stmt->bind_param("i", $user_id);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();
    $user = $user_result->fetch_assoc();
    $user_conn->close();
}

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($product_id === 0) {
    header('Location: menu.php');
    exit;
}

$conn = getDBConnection();

// Fetch product details
$stmt = $conn->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: menu.php');
    exit;
}

$product = $result->fetch_assoc();

// Fetch recommended products
$recommended_sql = "SELECT * FROM products WHERE id != ? ORDER BY rating DESC LIMIT 3";
$stmt = $conn->prepare($recommended_sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$recommended_result = $stmt->get_result();
$recommended = [];
while ($row = $recommended_result->fetch_assoc()) {
    $recommended[] = $row;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - Macroon Morning</title>
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/product.css">
</head>

<body>
    <header>
        <div class="logo">
            <span class="logo-icon">🍰</span>
            <span>Macroon Morning</span>
        </div>

        <nav>
            <a href="../index.php">Home</a>
            <a href="menu.php" class="active">Menu</a>
            <a href="orders.php">Orders</a>
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

    <div class="product-detail">
        <div class="product-detail-image">
            <?php if (!empty($product['image']) && file_exists(__DIR__ . '/../public/images/products/' . $product['image'])): ?>
                <img src="../public/images/products/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">
            <?php else: ?>
                <div style="display: flex; align-items: center; justify-content: center; height: 100%; font-size: 72px;">🍰</div>
            <?php endif; ?>
        </div>

        <div class="product-detail-info">
            <h1><?php echo htmlspecialchars($product['name']); ?></h1>

            <div class="rating">
                <div class="stars">
                    ⭐⭐⭐⭐⭐
                </div>
                <span class="rating-value">(<?php echo $product['rating']; ?>)</span>
            </div>

            <div class="product-detail-price">
                Rs <?php echo number_format($product['price'], 2); ?>
            </div>

            <button class="add-to-cart-btn" onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['name']); ?>', <?php echo $product['price']; ?>)">
                Add to Cart
            </button>

            <div class="recommended-section">
                <h3>Recommended</h3>
                <div class="recommended-items">
                    <?php foreach ($recommended as $item): ?>
                        <div class="recommended-item" onclick="window.location.href='product.php?id=<?php echo $item['id']; ?>'">
                            <?php echo htmlspecialchars($item['name']); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/main.js"></script>
</body>

</html>