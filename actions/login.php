<?php
require '../config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Invalid request method.");
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Basic validations
if (empty($email) || empty($password)) {
    die("Please fill in all required fields.");
}

if (!str_ends_with($email, '@kath.gov.gh')) {
    die("Only institutional emails are allowed.");
}



// Retrieve user
$stmt = $pdo->prepare("SELECT id, password FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    header("Location: ../dashboard.php");
    exit;
} else {
    die("Invalid email or password.");
}
