<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$user_id = $_POST['user_id'] ?? 0;
$total_amount = $_POST['total_amount'] ?? 0;
$delivery_fee = $_POST['delivery_fee'] ?? 3.99;
$items = isset($_POST['items']) ? json_decode($_POST['items'], true) : [];

if ($user_id === 0 || empty($items)) {
    echo json_encode(['success' => false, 'message' => 'Invalid order data']);
    exit;
}

$conn = getDBConnection();

// Start transaction
$conn->begin_transaction();

try {
    // Insert order
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, delivery_fee, status) VALUES (?, ?, ?, 'pending')");
    $stmt->bind_param("idd", $user_id, $total_amount, $delivery_fee);
    $stmt->execute();
    $order_id = $conn->insert_id;

    // Insert order items
    $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");

    foreach ($items as $item) {
        $stmt->bind_param("iiid", $order_id, $item['id'], $item['quantity'], $item['price']);
        $stmt->execute();
    }

    // Commit transaction
    $conn->commit();

    $stmt->close();
    $conn->close();

    echo json_encode(['success' => true, 'message' => 'Order placed successfully', 'order_id' => $order_id]);
} catch (Exception $e) {
    // Rollback on error
    $conn->rollback();
    $conn->close();
    echo json_encode(['success' => false, 'message' => 'Error placing order: ' . $e->getMessage()]);
}
