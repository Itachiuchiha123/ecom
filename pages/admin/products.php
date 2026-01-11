<?php
require_once '../../php/config.php';
require_once '../../php/auth.php';

// Check if user is admin
if (!isLoggedIn() || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../login.php?type=admin');
    exit;
}

$conn = getDBConnection();

// Fetch all products with category names
$sql = "SELECT p.*, c.name as category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        ORDER BY p.created_at DESC";
$result = $conn->query($sql);
$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

// Fetch categories for dropdown
$categories_sql = "SELECT * FROM categories ORDER BY name";
$categories_result = $conn->query($categories_sql);
$categories = [];
while ($row = $categories_result->fetch_assoc()) {
    $categories[] = $row;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Admin</title>
    <link rel="stylesheet" href="../../css/common.css">
    <link rel="stylesheet" href="../../css/admin.css">
</head>

<body>
    <header>
        <div class="logo">
            <span class="logo-icon">🍰</span>
            <span>Macroon Morning - Admin</span>
        </div>

        <div class="search-container">
            <input type="text" placeholder="Search...">
            <div class="search-icon">🔍</div>
        </div>

        <nav>
            <a href="../../index.php">Home</a>
            <a href="../menu.php">Menu</a>
            <a href="index.php">Dashboard</a>
        </nav>

        <div class="login-dropdown">
            <div class="user-profile">
                <div class="user-name" onclick="toggleUserDropdown()">
                    Admin
                </div>
                <div class="dropdown-content" id="userDropdown">
                    <a href="#" onclick="logout()">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <div class="crud-container">
        <div class="crud-header">
            <h1>Manage Products</h1>
            <button class="add-btn" onclick="openAddModal()">+ Add Product</button>
        </div>

        <div class="crud-table-container">
            <table class="crud-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Rating</th>
                        <th>Popular</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?php echo $product['id']; ?></td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td><?php echo htmlspecialchars($product['category_name'] ?? 'N/A'); ?></td>
                            <td>$<?php echo number_format($product['price'], 2); ?></td>
                            <td><?php echo $product['rating']; ?> ⭐</td>
                            <td><?php echo $product['is_popular'] ? '✅' : '❌'; ?></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="edit-btn" onclick='openEditModal(<?php echo json_encode($product); ?>)'>Edit</button>
                                    <button class="delete-btn" onclick="deleteProduct(<?php echo $product['id']; ?>)">Delete</button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add/Edit Product Modal -->
    <div id="productModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Add Product</h2>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>

            <form id="productForm" class="modal-form" onsubmit="saveProduct(event)">
                <input type="hidden" id="productId" name="product_id">
                <input type="hidden" id="action" name="action" value="create">

                <div class="form-row">
                    <label>Product Name *</label>
                    <input type="text" id="productName" name="name" required>
                </div>

                <div class="form-row">
                    <label>Description</label>
                    <textarea id="productDescription" name="description"></textarea>
                </div>

                <div class="form-row">
                    <label>Price *</label>
                    <input type="number" id="productPrice" name="price" step="0.01" min="0" required>
                </div>

                <div class="form-row">
                    <label>Category *</label>
                    <select id="productCategory" name="category_id" required>
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>">
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-row">
                    <label>Rating</label>
                    <input type="number" id="productRating" name="rating" step="0.1" min="0" max="5" value="0">
                </div>

                <div class="form-row">
                    <label>Image Name</label>
                    <input type="text" id="productImage" name="image" placeholder="e.g., chocolate-cake.jpg">
                </div>

                <div class="form-row">
                    <label>
                        <input type="checkbox" id="productPopular" name="is_popular" value="1">
                        Mark as Popular
                    </label>
                </div>

                <div class="modal-actions">
                    <button type="submit" class="save-btn">Save</button>
                    <button type="button" class="cancel-btn" onclick="closeModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../../js/main.js"></script>
    <script src="../../js/admin-products.js"></script>
</body>

</html>