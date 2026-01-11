<?php
require_once 'config.php';
require_once 'auth.php';

header('Content-Type: application/json');

// Check if user is admin
if (!isLoggedIn() || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$conn = getDBConnection();

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'get_details':
        $order_id = $_GET['order_id'] ?? 0;

        // Get order details
        $stmt = $conn->prepare("SELECT o.*, u.name as customer_name, u.email as customer_email, u.phone as customer_phone 
                                FROM orders o 
                                LEFT JOIN users u ON o.user_id = u.id 
                                WHERE o.id = ?");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $order = $stmt->get_result()->fetch_assoc();

        // Get order items
        $stmt = $conn->prepare("SELECT oi.*, p.name as product_name 
                                FROM order_items oi 
                                LEFT JOIN products p ON oi.product_id = p.id 
                                WHERE oi.order_id = ?");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }

        $order['items'] = $items;
        echo json_encode(['success' => true, 'order' => $order]);
        break;

    case 'update_status':
        $order_id = $_POST['order_id'] ?? 0;
        $status = $_POST['status'] ?? '';

        if (!in_array($status, ['pending', 'processing', 'delivered', 'cancelled'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid status']);
            exit;
        }

        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $order_id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Order status updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update order status']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

$conn->close();
