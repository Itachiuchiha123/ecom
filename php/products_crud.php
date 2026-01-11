<?php
require_once 'config.php';
require_once 'auth.php';

header('Content-Type: application/json');

// Check if user is admin
if (!isLoggedIn() || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$action = $_POST['action'] ?? '';
$conn = getDBConnection();

switch ($action) {
    case 'create':
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $price = $_POST['price'] ?? 0;
        $category_id = $_POST['category_id'] ?? 0;
        $rating = $_POST['rating'] ?? 0;
        $image = $_POST['image'] ?? '';

        if (empty($name) || $price <= 0 || $category_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Name, price, and category are required']);
            exit;
        }

        $stmt = $conn->prepare("INSERT INTO products (name, description, price, category_id, rating, image) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdids", $name, $description, $price, $category_id, $rating, $image);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Product created successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create product']);
        }
        break;

    case 'update':
        $product_id = $_POST['product_id'] ?? 0;
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $price = $_POST['price'] ?? 0;
        $category_id = $_POST['category_id'] ?? 0;
        $rating = $_POST['rating'] ?? 0;
        $image = $_POST['image'] ?? '';

        if (empty($name) || $price <= 0 || $category_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Name, price, and category are required']);
            exit;
        }

        $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, category_id = ?, rating = ?, image = ? WHERE id = ?");
        $stmt->bind_param("ssdidsi", $name, $description, $price, $category_id, $rating, $image, $product_id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Product updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update product']);
        }
        break;

    case 'delete':
        $product_id = $_POST['product_id'] ?? 0;

        // Check if product is in any orders
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM order_items WHERE product_id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if ($result['count'] > 0) {
            echo json_encode(['success' => false, 'message' => 'Cannot delete product that is in orders']);
            exit;
        }

        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $product_id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Product deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete product']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

$conn->close();
