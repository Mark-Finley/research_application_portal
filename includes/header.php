<?php
session_start();
require_once __DIR__ . '/../config.php';

// Default values
$firstName = 'Researcher';
$profilePic = 'assets/img/default-avatar.png';

// Only fetch from DB if user is logged in
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT full_name, profile_picture FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $fullName = trim($user['full_name']);
        $nameParts = explode(' ', $fullName);
        $firstName = ucfirst($nameParts[0]);

        // If user has a profile picture and it exists
        if (!empty($user['profile_picture']) && file_exists($user['profile_picture'])) {
            $profilePic = $user['profile_picture'];
        }
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
      background-color: #f8f9fa;
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
      display: flex;
      flex-direction: column;
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
<div class="sidebar d-flex flex-column p-3" id="sidebar">
  <h4 class="mb-3">Research App</h4>

  <?php if (isset($_SESSION['user_id'])): ?>
    <div class="text-center mb-4">
      <a href="profile.php">
        <img src="<?= htmlspecialchars($profilePic) ?>" alt="Profile Picture"
             class="rounded-circle border" style="width: 70px; height: 70px; object-fit: cover;">
      </a>
      <p class="mt-2 mb-0"><?= htmlspecialchars($firstName) ?></p>
    </div>
  <?php endif; ?>

  <!-- Navigation -->
  <div class="flex-grow-1">
    <ul class="nav flex-column">
      <li class="nav-item mb-2">
        <a href="dashboard.php" class="nav-link <?= $currentPage == 'dashboard.php' ? 'active' : '' ?>">
          <i class="bi bi-speedometer2 me-2"></i> Dashboard
        </a>
      </li>
      <li class="nav-item mb-2">
        <a href="my_applications.php" class="nav-link <?= $currentPage == 'my_applications.php' ? 'active' : '' ?>">
          <i class="bi bi-folder2-open me-2"></i> My Applications
        </a>
      </li>
      <li class="nav-item mb-2">
        <a href="requisite_documents.php" class="nav-link <?= $currentPage == 'requisite_documents.php' ? 'active' : '' ?>">
          <i class="bi bi-journal-text me-2"></i> Requisite Documents
        </a>
      </li>
      <li class="nav-item mb-2">
        <a href="profile.php" class="nav-link <?= $currentPage == 'profile.php' ? 'active' : '' ?>">
          <i class="bi bi-person me-2"></i> Profile
        </a>
      </li>
    </ul>
  </div>

  <!-- Logout at bottom -->
  <div>
    <a href="logout.php" class="nav-link d-flex align-items-center">
      <i class="bi bi-box-arrow-right me-2"></i> Logout
    </a>
  </div>
</div>

<!-- Main Content -->
<div class="main-content" id="mainContent">
  <header class="bg-light p-3 border-bottom d-flex justify-content-between align-items-center">
    <button class="btn btn-outline-dark d-md-none" id="toggleSidebar">☰</button>
    <div class="d-flex align-items-center gap-3">
      <h5 class="mb-0">Welcome, <?= htmlspecialchars($firstName); ?></h5>
    </div>
  </header>
  <main class="p-3">
