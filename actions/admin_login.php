<?php
session_start();

$admin_username = 'admin';
$admin_password = 'admin123'; // Change in production

if ($_POST['username'] === $admin_username && $_POST['password'] === $admin_password) {
    $_SESSION['admin_logged_in'] = true;
    header("Location: ../admin/dashboard.php");
} else {
    die("Invalid admin credentials.");
}
