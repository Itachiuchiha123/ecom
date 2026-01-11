<?php
// Database configuration
// Update these values according to your friend's laptop database settings
define('DB_HOST', 'YOUR_FRIENDS_LAPTOP_IP'); // e.g., '192.168.1.100' or 'localhost' if same machine
define('DB_USER', 'root'); // Default XAMPP user
define('DB_PASS', ''); // Default XAMPP password is empty
define('DB_NAME', 'macroon_morning');

// Create database connection
function getDBConnection()
{
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
