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
$vendor_id = $_POST['vendor_id'] ?? 0;

$conn = getDBConnection();

switch ($action) {
    case 'approve':
        $stmt = $conn->prepare("UPDATE users SET is_approved = 1 WHERE id = ? AND user_type = 'vendor'");
        $stmt->bind_param("i", $vendor_id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Vendor approved successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to approve vendor']);
        }
        break;

    case 'suspend':
        $stmt = $conn->prepare("UPDATE users SET is_approved = 0 WHERE id = ? AND user_type = 'vendor'");
        $stmt->bind_param("i", $vendor_id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Vendor suspended successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to suspend vendor']);
        }
        break;

    case 'reject':
        // Check if vendor has products
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM products WHERE vendor_id = ?");
        $stmt->bind_param("i", $vendor_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if ($result['count'] > 0) {
            echo json_encode(['success' => false, 'message' => 'Cannot delete vendor with existing products']);
            break;
        }

        // Delete vendor
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND user_type = 'vendor'");
        $stmt->bind_param("i", $vendor_id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Vendor rejected and deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete vendor']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

$conn->close();
