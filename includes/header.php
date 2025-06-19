<?php
session_start();
require_once __DIR__ . '/../config.php';

// Default name
$firstName = 'Researcher';

// Only fetch from DB if user is logged in
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT full_name FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $fullName = trim($user['full_name']);
        $nameParts = explode(' ', $fullName);
        $firstName = ucfirst($nameParts[0]);
    }
}

// Get current page for active link highlighting
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Research Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="shortcut icon" href="/assets/img/favicon.ico" type="image/x-icon">
  <style>
    body {
      overflow-x: hidden;
    }
    .sidebar {
      height: 100vh;
      position: fixed;
      left: 0;
      top: 0;
      background-color: #397b56;
      color: #fff;
      width: 250px;
      transition: transform 0.3s ease-in-out;
      z-index: 1050;
    }
    .sidebar a {
      color: #fff;
      text-decoration: none;
    }
    .sidebar a.active {
      background-color: #495057;
      border-radius: 5px;
    }
    .sidebar.collapsed {
      transform: translateX(-100%);
    }
    .main-content {
      margin-left: 250px;
      transition: margin-left 0.3s ease-in-out;
    }
    .main-content.collapsed {
      margin-left: 0;
    }
    @media (max-width: 768px) {
      .sidebar {
        transform: translateX(-100%);
      }
      .sidebar.show {
        transform: translateX(0);
      }
      .main-content {
        margin-left: 0;
      }
    }
  </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar p-3" id="sidebar">
  <h4 class="mb-4">Research App</h4>
  <ul class="nav flex-column">
    <li class="nav-item mb-2">
      <a href="dashboard.php" class="nav-link <?= $currentPage == 'dashboard.php' ? 'active' : '' ?>">Dashboard</a>
    </li>
    <!-- <li class="nav-item mb-2">
      <a href="application.php" class="nav-link <?= $currentPage == 'application.php' ? 'active' : '' ?>">Start Application</a>
    </li> -->
    <li class="nav-item mb-2">
      <a href="my_applications.php" class="nav-link <?= $currentPage == 'my_applications.php' ? 'active' : '' ?>">My Applications</a>
    </li>
    <li class="nav-item mb-2">
      <a href="requisite_documents.php" class="nav-link <?= $currentPage == 'requisite_documents.php' ? 'active' : '' ?>" >Requisite Documents</a>
    </li>
    <li class="nav-item mb-2">
      <a href="profile.php" class="nav-link <?= $currentPage == 'profile.php' ? 'active' : '' ?>" >Profile</a>
    </li>
    <li class="nav-item mb-2">
      <a href="logout.php" class="nav-link">Logout</a>
    </li>
  </ul>
</div>

<!-- Main Content -->
<div class="main-content" id="mainContent">
  <header class="bg-light p-3 border-bottom d-flex justify-content-between align-items-center">
    <button class="btn btn-outline-dark d-md-none" id="toggleSidebar">☰</button>
    <h5 class="mb-0">Welcome, <?= htmlspecialchars($firstName); ?></h5>
  </header>
  <main class="p-3">
