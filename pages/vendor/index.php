<?php
require_once '../../php/config.php';
require_once '../../php/auth.php';

// Check if user is vendor and approved
if (!isLoggedIn() || $_SESSION['user_type'] !== 'vendor') {
    header('Location: ../../index.php');
    exit;
}

$conn = getDBConnection();
$vendor_id = $_SESSION['user_id'];

// Check if approved
$stmt = $conn->prepare("SELECT name, is_approved FROM users WHERE id = ?");
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$vendor = $stmt->get_result()->fetch_assoc();

if (!$vendor['is_approved']) {
    header('Location: ../vendor-pending.php');
    exit;
}

// Fetch vendor products with category names
$sql = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.vendor_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$result = $stmt->get_result();
$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

// Fetch categories for dropdown
$categories_sql = "SELECT * FROM categories";
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
    <title>My Products - Vendor</title>
    <link rel="stylesheet" href="../../css/common.css">
    <link rel="stylesheet" href="../../css/admin.css">
</head>

<body>
    <header>
        <div class="logo">
            <span class="logo-icon">🍰</span>
            <span>Macroon Morning - Vendor</span>
        </div>

        <nav>
            <a href="../../index.php">Home</a>
            <a href="../menu.php">Menu</a>
            <a href="index.php" class="active">My Products</a>
        </nav>

        <div class="login-dropdown">
            <div class="user-profile">
                <div class="user-name" onclick="toggleUserDropdown()">
                    <?php echo htmlspecialchars($vendor['name']); ?>
                </div>
                <div class="dropdown-content" id="userDropdown">
                    <a href="#" onclick="logout(); return false;">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <div class="crud-container">
        <div class="crud-header">
            <h1>My Products</h1>
            <button class="add-btn" onclick="openAddModal()">Add New Product</button>
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
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?php echo $product['id']; ?></td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td><?php echo htmlspecialchars($product['category_name'] ?? 'N/A'); ?></td>
                            <td>Rs <?php echo number_format($product['price'], 2); ?></td>
                            <td><?php echo $product['rating']; ?> ⭐</td>
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

            <form id="productForm" class="modal-form" onsubmit="saveProduct(event)" enctype="multipart/form-data">
                <input type="hidden" id="productId" name="product_id">
                <input type="hidden" id="action" name="action" value="create">
                <input type="hidden" id="existingImage" name="existing_image">

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
                    <label>Product Image</label>
                    <input type="file" id="productImageFile" name="image_file" accept="image/*">
                    <small id="currentImageText" style="color: #666; margin-top: 5px;"></small>
                </div>

                <div class="modal-actions">
                    <button type="submit" class="save-btn">Save</button>
                    <button type="button" class="cancel-btn" onclick="closeModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../../js/main.js"></script>
    <script src="../../js/vendor-products.js"></script>
</body>

</html>