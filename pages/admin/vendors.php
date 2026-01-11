<?php
require_once '../../php/config.php';
require_once '../../php/auth.php';

// Check if user is admin
if (!isLoggedIn() || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../../index.php');
    exit;
}

// Fetch pending vendors
$conn = getDBConnection();
$sql = "SELECT id, name, email, phone, address, created_at FROM users WHERE user_type = 'vendor' AND is_approved = 0 ORDER BY created_at DESC";
$result = $conn->query($sql);
$pending_vendors = [];
while ($row = $result->fetch_assoc()) {
    $pending_vendors[] = $row;
}

// Fetch approved vendors
$sql2 = "SELECT id, name, email, phone, address, created_at, is_approved FROM users WHERE user_type = 'vendor' ORDER BY created_at DESC";
$result2 = $conn->query($sql2);
$all_vendors = [];
while ($row = $result2->fetch_assoc()) {
    $all_vendors[] = $row;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Vendors - Admin</title>
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
            <h1>Manage Vendors</h1>
        </div>

        <?php if (!empty($pending_vendors)): ?>
            <div class="crud-section">
                <h2 style="color: #ff6b6b;">Pending Approval (<?php echo count($pending_vendors); ?>)</h2>
                <div class="crud-table-container">
                    <table class="crud-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Address</th>
                                <th>Registration Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pending_vendors as $vendor): ?>
                                <tr>
                                    <td><?php echo $vendor['id']; ?></td>
                                    <td><?php echo htmlspecialchars($vendor['name']); ?></td>
                                    <td><?php echo htmlspecialchars($vendor['email']); ?></td>
                                    <td><?php echo htmlspecialchars($vendor['phone'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars(substr($vendor['address'] ?? 'N/A', 0, 30)); ?><?php echo strlen($vendor['address'] ?? '') > 30 ? '...' : ''; ?></td>
                                    <td><?php echo date('M j, Y', strtotime($vendor['created_at'])); ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="edit-btn" onclick="approveVendor(<?php echo $vendor['id']; ?>)">Approve</button>
                                            <button class="delete-btn" onclick="rejectVendor(<?php echo $vendor['id']; ?>)">Reject</button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <div class="crud-section" style="margin-top: 40px;">
            <h2>All Vendors</h2>
            <div class="crud-table-container">
                <table class="crud-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Registration Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($all_vendors as $vendor): ?>
                            <tr>
                                <td><?php echo $vendor['id']; ?></td>
                                <td><?php echo htmlspecialchars($vendor['name']); ?></td>
                                <td><?php echo htmlspecialchars($vendor['email']); ?></td>
                                <td><?php echo htmlspecialchars($vendor['phone'] ?? 'N/A'); ?></td>
                                <td>
                                    <?php if ($vendor['is_approved']): ?>
                                        <span class="badge delivered">Approved</span>
                                    <?php else: ?>
                                        <span class="badge pending">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($vendor['created_at'])); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <?php if ($vendor['is_approved']): ?>
                                            <button class="delete-btn" onclick="suspendVendor(<?php echo $vendor['id']; ?>)">Suspend</button>
                                        <?php else: ?>
                                            <button class="edit-btn" onclick="approveVendor(<?php echo $vendor['id']; ?>)">Approve</button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="../../js/main.js"></script>
    <script src="../../js/admin-vendors.js"></script>
</body>

</html>