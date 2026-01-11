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

// Fetch products
$conn = getDBConnection();
$category_filter = isset($_GET['category']) ? $_GET['category'] : 'all';

if ($category_filter === 'all') {
    $sql = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id";
    $result = $conn->query($sql);
} else {
    $sql = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE c.name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $category_filter);
    $stmt->execute();
    $result = $stmt->get_result();
}

$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

// Fetch categories
$categories_sql = "SELECT * FROM categories";
$categories_result = $conn->query($categories_sql);
$categories = [];
while ($row = $categories_result->fetch_assoc()) {
    $categories[] = $row;
}

// Fetch popular items (top rated)
$popular_sql = "SELECT * FROM products ORDER BY rating DESC LIMIT 5";
$popular_result = $conn->query($popular_sql);
$popular_items = [];
while ($row = $popular_result->fetch_assoc()) {
    $popular_items[] = $row;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - Macroon Morning</title>
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/menu.css">
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

    <div class="menu-container">
        <aside class="sidebar">
            <h2>Menu Categories</h2>
            <div class="category-item <?php echo $category_filter === 'all' ? 'active' : ''; ?>"
                onclick="window.location.href='menu.php?category=all'">
                All Items
            </div>
            <?php foreach ($categories as $category): ?>
                <div class="category-item <?php echo $category_filter === $category['name'] ? 'active' : ''; ?>"
                    onclick="window.location.href='menu.php?category=<?php echo $category['name']; ?>'">
                    <?php echo htmlspecialchars($category['name']); ?>
                </div>
            <?php endforeach; ?>

            <div class="popular-section">
                <h2>Popular Items</h2>
                <?php foreach ($popular_items as $item): ?>
                    <div class="popular-item" onclick="window.location.href='product.php?id=<?php echo $item['id']; ?>'">
                        <?php echo htmlspecialchars($item['name']); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </aside>

        <div class="products-grid">
            <?php foreach ($products as $product): ?>
                <div class="product-card" onclick="window.location.href='product.php?id=<?php echo $product['id']; ?>'">
                    <div class="product-image">
                        <?php if (!empty($product['image']) && file_exists(__DIR__ . '/../public/images/products/' . $product['image'])): ?>
                            <img src="../public/images/products/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <div style="display: flex; align-items: center; justify-content: center; height: 100%; font-size: 48px;">🍰</div>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <div class="product-name"><?php echo htmlspecialchars($product['name']); ?></div>
                        <div class="product-price">Rs <?php echo number_format($product['price'], 2); ?></div>
                        <button class="product-order-btn"
                            onclick="event.stopPropagation(); addToCart(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['name']); ?>', <?php echo $product['price']; ?>)">
                            Order Now
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="../js/main.js"></script>
</body>

</html>