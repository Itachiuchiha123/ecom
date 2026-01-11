<?php
require_once '../../php/config.php';
require_once '../../php/auth.php';

// Check if user is admin
if (!isLoggedIn() || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../login.php?type=admin');
    exit;
}

$conn = getDBConnection();

// Fetch all orders with user information
$sql = "SELECT o.*, u.name as customer_name, u.email as customer_email,
        (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count
        FROM orders o 
        LEFT JOIN users u ON o.user_id = u.id 
        ORDER BY o.created_at DESC";
$result = $conn->query($sql);
$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Admin</title>
    <link rel="stylesheet" href="../../css/common.css">
    <link rel="stylesheet" href="../../css/admin.css">
</head>

<body>
    <header>
        <div class="logo">
            <span class="logo-icon">🍰</span>
            <span>Macroon Morning - Admin</span>
        </div>

        <nav>
            <a href="../../index.php">Home</a>
            <a href="../menu.php">Menu</a>
            <a href="index.php">Dashboard</a>
            <a href="vendors.php">Vendors</a>
        </nav>

        <div class="login-dropdown">
            <div class="user-profile">
                <div class="user-name" onclick="toggleUserDropdown()">
                    Admin
                </div>
                <div class="dropdown-content" id="userDropdown">
                    <a href="#" onclick="logout(); return false;">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <div class="crud-container">
        <div class="crud-header">
            <h1>Manage Orders</h1>
        </div>

        <div class="crud-table-container">
            <table class="crud-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td>
                                <?php echo htmlspecialchars($order['customer_name']); ?><br>
                                <small><?php echo htmlspecialchars($order['customer_email']); ?></small>
                            </td>
                            <td><?php echo $order['item_count']; ?> items</td>
                            <td>Rs <?php echo number_format($order['total_amount'], 2); ?></td>
                            <td>
                                <span class="badge <?php echo $order['status']; ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M j, Y g:i A', strtotime($order['created_at'])); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="edit-btn" onclick='viewOrderDetails(<?php echo $order['id']; ?>)'>View</button>
                                    <button class="edit-btn" onclick='updateOrderStatus(<?php echo $order['id']; ?>, "<?php echo $order['status']; ?>")'>Update Status</button>
                                    <button class="delete-btn" onclick='deleteOrder(<?php echo $order['id']; ?>)'>Delete</button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Order Details Modal -->
    <div id="orderModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Order Details</h2>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>

            <div id="orderDetailsContent">
                <!-- Order details will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Update Status Modal -->
    <div id="statusModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Update Order Status</h2>
                <button class="close-btn" onclick="closeStatusModal()">&times;</button>
            </div>

            <form id="statusForm" class="modal-form" onsubmit="saveOrderStatus(event)">
                <input type="hidden" id="orderId" name="order_id">

                <div class="form-row">
                    <label>Order Status *</label>
                    <select id="orderStatus" name="status" required>
                        <option value="pending">Pending</option>
                        <option value="processing">Processing</option>
                        <option value="delivered">Delivered</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>

                <div class="modal-actions">
                    <button type="submit" class="save-btn">Update</button>
                    <button type="button" class="cancel-btn" onclick="closeStatusModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../../js/main.js"></script>
    <script src="../../js/admin-orders.js"></script>
</body>

</html>