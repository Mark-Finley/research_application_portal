<?php
session_start();
require_once __DIR__ . '/../config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Validate input
if (!isset($_POST['current_password']) || !isset($_POST['new_password']) || !isset($_POST['confirm_password'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit();
}

// Check if new passwords match
if ($_POST['new_password'] !== $_POST['confirm_password']) {
    echo json_encode(['success' => false, 'message' => 'New passwords do not match']);
    exit();
}

// Get admin details
$admin_id = $_SESSION['admin_id'];

try {
    // Check current password
    $stmt = $pdo->prepare("SELECT password FROM admin_users WHERE id = ?");
    $stmt->execute([$admin_id]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$admin) {
        echo json_encode(['success' => false, 'message' => 'Admin account not found']);
        exit();
    }
    
    // Verify current password
    if (!password_verify($_POST['current_password'], $admin['password'])) {
        echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
        exit();
    }
    
    // Hash new password
    $new_password_hash = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    
    // Update password
    $update_stmt = $pdo->prepare("UPDATE admin_users SET password = ? WHERE id = ?");
    $update_stmt->execute([$new_password_hash, $admin_id]);
    
    echo json_encode(['success' => true, 'message' => 'Password updated successfully']);
    
} catch (PDOException $e) {
    error_log("Password change error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while updating password']);
}
?>
