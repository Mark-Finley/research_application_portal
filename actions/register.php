<?php 
require '../config.php';
session_start();

// Basic form validation
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Invalid request.");
}

$full_name = trim($_POST['full_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// 1. Check required fields
if (empty($full_name) || empty($email) || empty($password) || empty($confirm_password)) {
    die("All fields are required.");
}

// 2. Email must be institutional
if (!str_ends_with($email, '@kath.gov.gh')) {
    die("Only institutional emails (@kath.gov.gh) are allowed.");
}

// 3. Passwords must match
if ($password !== $confirm_password) {
    die("Passwords do not match.");
}

// 4. Hash password
$hashed = password_hash($password, PASSWORD_DEFAULT);

// 5. Insert into DB
$stmt = $pdo->prepare("INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)");

try {
    $stmt->execute([$full_name, $email, $hashed]);
    $_SESSION['user_id'] = $pdo->lastInsertId();
    header("Location: ../dashboard.php");
    exit;
} catch (PDOException $e) {
    // Friendly duplicate email message
    if ($e->getCode() == 23000) {
        die("An account with this email already exists.");
    }
    die("Registration failed: " . $e->getMessage());
}
