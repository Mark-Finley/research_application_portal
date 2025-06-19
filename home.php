<?php
session_start();

// If user is logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

// If admin is logged in, redirect to admin dashboard
if (isset($_SESSION['admin_logged_in'])) {
    header("Location: admin/dashboard.php");
    exit();
}

// Default: redirect to login page
header("Location: login.php");
exit();
?>
