<?php
session_start();
require '../config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    die("Unauthorized.");
}

$id = $_GET['id'];
$status = $_GET['status'];

$stmt = $pdo->prepare("SELECT user_id FROM applications WHERE id = ?");
$stmt->execute([$id]);
$app = $stmt->fetch();

if ($app) {
    // Update application status
    $pdo->prepare("UPDATE users SET application_status = ? WHERE id = ?")
        ->execute([$status, $app['user_id']]);
    
    header("Location: dashboard.php");
} else {
    die("Application not found.");
}
