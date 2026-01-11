<?php
require_once '../../php/config.php';
require_once '../../php/auth.php';

// Check if user is admin
if (!isLoggedIn() || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../login.php?type=admin');
    exit;
}

$conn = getDBConnection();

// Fetch all users
$sql = "SELECT id, name, email, phone, address, user_type, created_at FROM users ORDER BY created_at DESC";
$result = $conn->query($sql);
$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin</title>
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
            <h1>Manage Users</h1>
            <button class="add-btn" onclick="openAddModal()">+ Add User</button>
        </div>

        <div class="crud-table-container">
            <table class="crud-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>User Type</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?></td>
                            <td>
                                <span class="badge <?php echo $user['user_type']; ?>">
                                    <?php echo ucfirst($user['user_type']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="edit-btn" onclick='openEditModal(<?php echo json_encode($user); ?>)'>Edit</button>
                                    <button class="delete-btn" onclick="deleteUser(<?php echo $user['id']; ?>)">Delete</button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add/Edit User Modal -->
    <div id="userModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Add User</h2>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>

            <form id="userForm" class="modal-form" onsubmit="saveUser(event)">
                <input type="hidden" id="userId" name="user_id">
                <input type="hidden" id="action" name="action" value="create">

                <div class="form-row">
                    <label>Name *</label>
                    <input type="text" id="userName" name="name" required>
                </div>

                <div class="form-row">
                    <label>Email *</label>
                    <input type="email" id="userEmail" name="email" required>
                </div>

                <div class="form-row">
                    <label>Phone</label>
                    <input type="tel" id="userPhone" name="phone">
                </div>

                <div class="form-row">
                    <label>Address</label>
                    <textarea id="userAddress" name="address"></textarea>
                </div>

                <div class="form-row">
                    <label>User Type *</label>
                    <select id="userType" name="user_type" required>
                        <option value="customer">Customer</option>
                        <option value="admin">Admin</option>
                        <option value="vendor">Vendor</option>
                    </select>
                </div>

                <div class="form-row" id="passwordRow">
                    <label>Password *</label>
                    <input type="password" id="userPassword" name="password">
                </div>

                <div class="modal-actions">
                    <button type="submit" class="save-btn">Save</button>
                    <button type="button" class="cancel-btn" onclick="closeModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../../js/main.js"></script>
    <script src="../../js/admin-users.js"></script>
</body>

</html>