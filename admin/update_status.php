<?php
session_start();
require '../config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    die("Unauthorized.");
}

// Get and validate parameters
if (!isset($_GET['id']) || !isset($_GET['status'])) {
    die("Missing parameters.");
}

$id = $_GET['id'];
$status = $_GET['status'];

// Validate status
$validStatuses = ['incomplete', 'pending', 'under_review', 'approved', 'rejected'];
if (!in_array($status, $validStatuses)) {
    die("Invalid status value.");
}

// Get the application and verify it exists
$stmt = $pdo->prepare("SELECT id, user_id, status FROM applications WHERE id = ?");
$stmt->execute([$id]);
$app = $stmt->fetch();

if ($app) {
    // Update application status in applications table
    $stmt = $pdo->prepare("UPDATE applications SET status = ? WHERE id = ?");
    $result1 = $stmt->execute([$status, $id]);
    
    // Also update the user's application_status (for backward compatibility)
    $stmt = $pdo->prepare("UPDATE users SET application_status = ? WHERE id = ?");
    $result2 = $stmt->execute([$status, $app['user_id']]);
    
    // Set message based on status
    $statusMessages = [
        'under_review' => 'Application marked as Under Review',
        'approved' => 'Application approved successfully',
        'rejected' => 'Application rejected',
        'pending' => 'Application marked as Pending',
        'incomplete' => 'Application marked as Incomplete'
    ];
    
    $message = $statusMessages[$status] ?? 'Status updated';
    
    // Redirect back to dashboard with status message
    header("Location: dashboard.php?message=" . urlencode($message) . "&status=" . urlencode($app['status']));
    exit;
} else {
    die("Application not found.");
}
