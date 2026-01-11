<?php
require_once 'config.php';

// Handle signup
function signup($name, $email, $password, $phone, $address, $user_type = 'customer')
{
    $conn = getDBConnection();

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $stmt->close();
        $conn->close();
        return ['success' => false, 'message' => 'Email already exists'];
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Set is_approved based on user_type (vendors need approval, others are auto-approved)
    $is_approved = ($user_type === 'vendor') ? 0 : 1;

    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone, address, user_type, is_approved) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssi", $name, $email, $hashed_password, $phone, $address, $user_type, $is_approved);

    if ($stmt->execute()) {
        $user_id = $conn->insert_id;
        $stmt->close();
        $conn->close();
        return ['success' => true, 'message' => 'Account created successfully', 'user_id' => $user_id, 'user_type' => $user_type, 'is_approved' => $is_approved];
    } else {
        $stmt->close();
        $conn->close();
        return ['success' => false, 'message' => 'Error creating account'];
    }
}

// Handle login
function login($email, $password, $user_type = 'customer')
{
    $conn = getDBConnection();

    $stmt = $conn->prepare("SELECT id, name, email, password, phone, address, user_type, is_approved FROM users WHERE email = ? AND user_type = ?");
    $stmt->bind_param("ss", $email, $user_type);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $stmt->close();
        $conn->close();
        return ['success' => false, 'message' => 'Invalid email or user type'];
    }

    $user = $result->fetch_assoc();

    // Check if vendor is approved
    if ($user['user_type'] === 'vendor' && !$user['is_approved']) {
        $stmt->close();
        $conn->close();
        return ['success' => false, 'message' => 'Your account is pending approval. Please wait for admin approval.', 'pending' => true];
    }

    // Verify password
    if (password_verify($password, $user['password'])) {
        // Remove password from user data
        unset($user['password']);

        // Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_type'] = $user['user_type'];
        $_SESSION['user_email'] = $user['email'];

        $stmt->close();
        $conn->close();
        return ['success' => true, 'message' => 'Login successful', 'user' => $user];
    } else {
        $stmt->close();
        $conn->close();
        return ['success' => false, 'message' => 'Invalid password'];
    }
}

// Check if user is logged in
function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

// Get current user
function getCurrentUser()
{
    if (!isLoggedIn()) {
        return null;
    }

    $conn = getDBConnection();
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT id, name, email, phone, address, user_type FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $stmt->close();
        $conn->close();
        return $user;
    }

    $stmt->close();
    $conn->close();
    return null;
}

// Logout
function logout()
{
    // Unset all session variables
    $_SESSION = array();

    // Destroy the session cookie
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }

    // Destroy the session
    session_destroy();
}
