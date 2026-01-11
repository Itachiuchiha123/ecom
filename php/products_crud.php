<?php
// Suppress warnings to prevent breaking JSON output
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '0');

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

// Function to handle image upload
function handleImageUpload($file, $existingImage = '')
{
    $uploadDir = __DIR__ . '/../public/images/products/';

    // If no new file uploaded, keep existing image
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return $existingImage;
    }

    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['error' => 'Upload error occurred'];
    }

    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    // Use file extension as fallback for Windows compatibility
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($extension, $allowedExtensions)) {
        return ['error' => 'Invalid file type. Only JPG, PNG, GIF, and WEBP images are allowed'];
    }

    // Try to get mime type, use extension check as fallback
    if (function_exists('mime_content_type')) {
        $fileType = @mime_content_type($file['tmp_name']);
        if ($fileType && !in_array($fileType, $allowedTypes)) {
            return ['error' => 'Invalid file type detected'];
        }
    }

    // Validate file size (max 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        return ['error' => 'File size exceeds 5MB limit'];
    }

    // Generate unique filename
    $filename = uniqid('product_') . '_' . time() . '.' . $extension;
    $targetPath = $uploadDir . $filename;

    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        // Delete old image if exists and is different
        if (!empty($existingImage) && $existingImage !== $filename) {
            $oldPath = $uploadDir . $existingImage;
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }
        return $filename;
    } else {
        return ['error' => 'Failed to move uploaded file'];
    }
}

switch ($action) {
    case 'create':
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $price = $_POST['price'] ?? 0;
        $category_id = $_POST['category_id'] ?? 0;
        $rating = $_POST['rating'] ?? 0;

        if (empty($name) || $price <= 0 || $category_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Name, price, and category are required']);
            exit;
        }

        // Handle image upload
        $image = '';
        if (isset($_FILES['image_file'])) {
            $uploadResult = handleImageUpload($_FILES['image_file']);
            if (is_array($uploadResult) && isset($uploadResult['error'])) {
                echo json_encode(['success' => false, 'message' => $uploadResult['error']]);
                exit;
            }
            $image = $uploadResult;
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
        $existingImage = $_POST['existing_image'] ?? '';

        if (empty($name) || $price <= 0 || $category_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Name, price, and category are required']);
            exit;
        }

        // Handle image upload
        $image = $existingImage;
        if (isset($_FILES['image_file'])) {
            $uploadResult = handleImageUpload($_FILES['image_file'], $existingImage);
            if (is_array($uploadResult) && isset($uploadResult['error'])) {
                echo json_encode(['success' => false, 'message' => $uploadResult['error']]);
                exit;
            }
            $image = $uploadResult;
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

        // Get product image before deleting
        $stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $product = $stmt->get_result()->fetch_assoc();

        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $product_id);

        if ($stmt->execute()) {
            // Delete image file if exists
            if (!empty($product['image'])) {
                $imagePath = __DIR__ . '/../public/images/products/' . $product['image'];
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
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
