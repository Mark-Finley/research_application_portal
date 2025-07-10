<?php
session_start();
require_once __DIR__ . '/../config.php';

// Redirect if not logged in as admin
if (!isset($_SESSION['admin_logged_in']) && basename($_SERVER['PHP_SELF']) !== 'login.php') {
    header("Location: login.php");
    exit();
}

// Get admin information if available
$adminName = isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Administrator';

// Get current page for active link highlighting
$currentPage = basename($_SERVER['PHP_SELF']);

// Function to check if a menu item should be active
function isActive($pageName) {
    global $currentPage;
    return $currentPage === $pageName ? 'active' : '';
}

// Function to check if a status filter is active
function isStatusActive($status) {
    return (isset($_GET['status']) && $_GET['status'] === $status) ? 'active' : '';
}

// Get application statistics for badges
$stats = [
    'pending' => 0,
    'under_review' => 0,
    'approved' => 0,
    'rejected' => 0,
    'incomplete' => 0
];

if (isset($pdo) && isset($_SESSION['admin_logged_in'])) {
    // Get pending applications count
    $stmt = $pdo->query("SELECT COUNT(*) FROM applications WHERE status = 'pending' OR status = 'submitted'");
    $stats['pending'] = $stmt->fetchColumn();
    
    // Get under review applications count
    $stmt = $pdo->query("SELECT COUNT(*) FROM applications WHERE status = 'under_review'");
    $stats['under_review'] = $stmt->fetchColumn();
    
    // Get count of applications with issues (for status monitor badge)
    $stmt = $pdo->query("SELECT COUNT(*) FROM applications WHERE 
        (ref_code IS NULL OR ref_code = '') OR 
        (status IS NULL OR status = '') OR
        (user_id IS NULL)");
    $issueCount = $stmt->fetchColumn();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Research Portal - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="shortcut icon" href="../assets/img/favicon.ico" type="image/x-icon">
  <style>
    :root {
      --primary: #007b55;
      --primary-dark: #005f3d;
      --primary-light: #e9f7f2;
      --secondary: #6c757d;
      --success: #198754;
      --info: #0dcaf0;
      --warning: #ffc107;
      --danger: #dc3545;
      --white: #ffffff;
      --sidebar-width: 280px;
      --header-height: 60px;
      --footer-height: 50px;
    }
    
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f8f9fa;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      overflow-x: hidden;
      position: relative;
    }
    
    /* Admin Header Styles */
    .admin-header {
      position: fixed;
      top: 0;
      right: 0;
      left: var(--sidebar-width);
      height: var(--header-height);
      background: white;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      z-index: 999;
      display: flex;
      align-items: center;
      justify-content: space-between;
      transition: left 0.3s ease;
      padding: 0 20px;
    }
    
    .admin-header.expanded {
      left: 70px;
    }
    
    .admin-header .breadcrumb {
      margin-bottom: 0;
    }
    
    /* Sidebar Styles */
    .sidebar {
      position: fixed;
      left: 0;
      top: 0;
      bottom: 0;
      width: var(--sidebar-width);
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
      color: var(--white);
      z-index: 1000;
      transition: all 0.3s ease;
      overflow-y: auto;
      scrollbar-width: thin;
      box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1);
    }
    
    .sidebar.collapsed {
      width: 70px;
    }
    
    .sidebar-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 15px 20px;
      height: var(--header-height);
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .sidebar-header h5 {
      margin: 0;
      font-weight: 600;
      font-size: 1.2rem;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    
    .sidebar-header .logo-icon {
      width: 30px;
      height: 30px;
      border-radius: 8px;
      background-color: rgba(255, 255, 255, 0.9);
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 10px;
      flex-shrink: 0;
    }
    
    .sidebar-header .logo-icon i {
      color: var(--primary);
      font-size: 18px;
    }
    
    .sidebar-header .logo-text {
      display: flex;
      align-items: center;
    }
    
    .sidebar-toggle {
      background: transparent;
      border: none;
      color: var(--white);
      cursor: pointer;
      font-size: 1.2rem;
      padding: 5px;
      transition: transform 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .sidebar.collapsed .sidebar-toggle {
      transform: rotate(180deg);
    }
    
    .sidebar-menu {
      padding: 15px 0;
    }
    
    .sidebar-menu-item {
      padding: 0 20px;
      margin-bottom: 5px;
    }
    
    .sidebar-link {
      display: flex;
      align-items: center;
      padding: 12px 15px;
      color: rgba(255, 255, 255, 0.8);
      text-decoration: none;
      border-radius: 8px;
      transition: all 0.2s ease;
      position: relative;
    }
    
    .sidebar-link:hover {
      color: var(--white);
      background: rgba(255, 255, 255, 0.1);
    }
    
    .sidebar-link.active {
      color: var(--white);
      background: rgba(255, 255, 255, 0.2);
      font-weight: 500;
    }
    
    .sidebar-icon {
      font-size: 1.25rem;
      min-width: 35px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .sidebar-text {
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    
    .sidebar.collapsed .sidebar-text,
    .sidebar.collapsed .sidebar-header h5 {
      display: none;
    }
    
    .sidebar-badge {
      position: absolute;
      right: 15px;
      background-color: var(--danger);
      color: var(--white);
      font-size: 0.75rem;
      font-weight: 600;
      padding: 0.25rem 0.5rem;
      border-radius: 50px;
      min-width: 24px;
      text-align: center;
    }
    
    .sidebar.collapsed .sidebar-badge {
      right: 5px;
      top: 5px;
      padding: 0.15rem 0.4rem;
    }
    
    /* Section Divider in Sidebar */
    .sidebar-section-divider {
      padding: 0 20px;
      margin-top: 20px;
      margin-bottom: 10px;
    }
    
    .sidebar-section-title {
      font-size: 0.75rem;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: rgba(255, 255, 255, 0.5);
      font-weight: 600;
      margin-bottom: 0;
    }
    
    .sidebar.collapsed .sidebar-section-divider {
      padding: 0 5px;
      margin-top: 15px;
      margin-bottom: 15px;
      text-align: center;
    }
    
    .sidebar.collapsed .sidebar-section-title {
      display: none;
    }
    
    .sidebar.collapsed .sidebar-section-divider::after {
      content: "";
      display: block;
      height: 1px;
      background: rgba(255, 255, 255, 0.2);
      margin: 0 auto;
      width: 80%;
    }
    
    /* Content Area */
    .main-content {
      margin-left: var(--sidebar-width);
      margin-top: var(--header-height);
      padding: 20px;
      transition: margin-left 0.3s ease;
      min-height: calc(100vh - var(--header-height) - var(--footer-height));
      display: flex;
      flex-direction: column;
    }
    
    .main-content.expanded {
      margin-left: 70px;
    }
    
    /* Admin Footer */
    .admin-footer {
      background-color: #fff;
      padding: 15px 20px;
      border-top: 1px solid rgba(0, 0, 0, 0.05);
      margin-left: var(--sidebar-width);
      transition: margin-left 0.3s ease;
      height: var(--footer-height);
      font-size: 0.875rem;
      color: var(--secondary);
    }
    
    .admin-footer.expanded {
      margin-left: 70px;
    }
    
    /* User Profile Dropdown */
    .admin-profile-dropdown .dropdown-toggle::after {
      display: none;
    }
    
    .admin-profile-dropdown .dropdown-menu {
      right: 0;
      left: auto;
      min-width: 240px;
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      border: none;
      padding: 0;
      overflow: hidden;
    }
    
    .admin-profile-header {
      background-color: var(--primary-light);
      padding: 15px;
      text-align: center;
    }
    
    .admin-avatar {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      background-color: var(--primary);
      color: var(--white);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
      font-weight: 600;
      margin: 0 auto 10px;
    }
    
    .admin-name {
      font-weight: 600;
      margin-bottom: 5px;
    }
    
    .admin-role {
      font-size: 0.85rem;
      color: var(--secondary);
    }
    
    .dropdown-divider {
      margin: 0;
    }
    
    .dropdown-item {
      padding: 12px 15px;
    }
    
    .dropdown-item i {
      margin-right: 10px;
      font-size: 1.1rem;
      width: 20px;
      text-align: center;
    }
    
    .dropdown-item.danger {
      color: var(--danger);
    }
    
    /* Status Badges */
    .status-badge {
      padding: 0.35em 0.65em;
      font-size: 0.75em;
      font-weight: 600;
      border-radius: 50rem;
      display: inline-block;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    
    .status-badge-pending {
      background-color: rgba(255, 193, 7, 0.15);
      color: #997404;
    }
    
    .status-badge-under-review {
      background-color: rgba(13, 202, 240, 0.15);
      color: #0aa2c0;
    }
    
    .status-badge-approved {
      background-color: rgba(25, 135, 84, 0.15);
      color: #0f5132;
    }
    
    .status-badge-rejected {
      background-color: rgba(220, 53, 69, 0.15);
      color: #842029;
    }
    
    .status-badge-incomplete {
      background-color: rgba(108, 117, 125, 0.15);
      color: #495057;
    }
    
    /* Mobile Responsive Styles */
    @media (max-width: 992px) {
      .sidebar {
        transform: translateX(-100%);
        width: var(--sidebar-width);
      }
      
      .sidebar.collapsed {
        transform: translateX(0);
        width: var(--sidebar-width);
      }
      
      .sidebar.collapsed .sidebar-text,
      .sidebar.collapsed .sidebar-header h5,
      .sidebar.collapsed .sidebar-section-title {
        display: block;
      }
      
      .admin-header {
        left: 0;
      }
      
      .admin-header.expanded {
        left: 0;
      }
      
      .main-content {
        margin-left: 0;
      }
      
      .main-content.expanded {
        margin-left: 0;
      }
      
      .admin-footer {
        margin-left: 0;
      }
      
      .admin-footer.expanded {
        margin-left: 0;
      }
      
      .mobile-toggle {
        display: block !important;
      }
    }
    
    .mobile-toggle {
      display: none;
    }
    
    /* Quick Action Button */
    .quick-action-btn {
      border-radius: 50%;
      width: 40px;
      height: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    
    /* Notification bell animation */
    @keyframes bell-ring {
      0% { transform: rotate(0); }
      10% { transform: rotate(15deg); }
      20% { transform: rotate(-15deg); }
      30% { transform: rotate(15deg); }
      40% { transform: rotate(-15deg); }
      50% { transform: rotate(0); }
      100% { transform: rotate(0); }
    }
    
    .bell-animate {
      animation: bell-ring 2s ease infinite;
      transform-origin: top center;
    }
    
    /* Notification indicator */
    .notification-indicator {
      position: absolute;
      top: 3px;
      right: 3px;
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background-color: var(--danger);
      border: 2px solid white;
    }
  </style>
</head>
<body>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
  <div class="sidebar-header">
    <div class="logo-text">
      <div class="logo-icon">
        <i class="bi bi-clipboard-data"></i>
      </div>
      <h5>Research Portal</h5>
    </div>
    <button type="button" class="sidebar-toggle" id="sidebarToggle" data-bs-toggle="tooltip" data-bs-placement="right" title="Toggle Sidebar">
      <i class="bi bi-chevron-left"></i>
    </button>
  </div>
  
  <div class="sidebar-menu">
    <div class="sidebar-menu-item">
      <a href="dashboard.php" class="sidebar-link <?= isActive('dashboard.php') && !isset($_GET['status']) ? 'active' : '' ?>">
        <span class="sidebar-icon"><i class="bi bi-speedometer2"></i></span>
        <span class="sidebar-text">Dashboard</span>
      </a>
    </div>
    
    <div class="sidebar-section-divider">
      <p class="sidebar-section-title">Applications</p>
    </div>
    
    <div class="sidebar-menu-item">
      <a href="dashboard.php?status=pending" class="sidebar-link <?= isStatusActive('pending') ?>">
        <span class="sidebar-icon"><i class="bi bi-hourglass-split"></i></span>
        <span class="sidebar-text">Pending Applications</span>
        <?php if ($stats['pending'] > 0): ?>
        <span class="sidebar-badge"><?= $stats['pending'] ?></span>
        <?php endif; ?>
      </a>
    </div>
    
    <div class="sidebar-menu-item">
      <a href="dashboard.php?status=under_review" class="sidebar-link <?= isStatusActive('under_review') ?>">
        <span class="sidebar-icon"><i class="bi bi-search"></i></span>
        <span class="sidebar-text">Under Review</span>
        <?php if ($stats['under_review'] > 0): ?>
        <span class="sidebar-badge"><?= $stats['under_review'] ?></span>
        <?php endif; ?>
      </a>
    </div>
    
    <div class="sidebar-menu-item">
      <a href="dashboard.php?status=approved" class="sidebar-link <?= isStatusActive('approved') ?>">
        <span class="sidebar-icon"><i class="bi bi-check-circle"></i></span>
        <span class="sidebar-text">Approved</span>
      </a>
    </div>
    
    <div class="sidebar-menu-item">
      <a href="dashboard.php?status=rejected" class="sidebar-link <?= isStatusActive('rejected') ?>">
        <span class="sidebar-icon"><i class="bi bi-x-circle"></i></span>
        <span class="sidebar-text">Rejected</span>
      </a>
    </div>
    
    <div class="sidebar-menu-item">
      <a href="dashboard.php?status=incomplete" class="sidebar-link <?= isStatusActive('incomplete') ?>">
        <span class="sidebar-icon"><i class="bi bi-exclamation-triangle"></i></span>
        <span class="sidebar-text">Incomplete</span>
      </a>
    </div>
    
    <div class="sidebar-section-divider">
      <p class="sidebar-section-title">Tools</p>
    </div>
    
    <div class="sidebar-menu-item">
      <a href="status_monitor.php" class="sidebar-link <?= isActive('status_monitor.php') ?>">
        <span class="sidebar-icon"><i class="bi bi-gear"></i></span>
        <span class="sidebar-text">Status Monitor</span>
        <?php if (isset($issueCount) && $issueCount > 0): ?>
        <span class="sidebar-badge"><?= $issueCount ?></span>
        <?php endif; ?>
      </a>
    </div>
    
    <div class="sidebar-menu-item">
      <a href="export_applications.php" class="sidebar-link <?= isActive('export_applications.php') ?>">
        <span class="sidebar-icon"><i class="bi bi-file-earmark-excel"></i></span>
        <span class="sidebar-text">Export Data</span>
      </a>
    </div>
    
    <div class="sidebar-menu-item">
      <a href="settings.php" class="sidebar-link <?= isActive('settings.php') ?>">
        <span class="sidebar-icon"><i class="bi bi-sliders"></i></span>
        <span class="sidebar-text">System Settings</span>
      </a>
    </div>
  </div>
</aside>

<!-- Main Header -->
<header class="admin-header" id="adminHeader">
  <button class="btn btn-sm btn-light mobile-toggle" id="mobileToggle" data-bs-toggle="tooltip" data-bs-placement="right" title="Toggle Menu">
    <i class="bi bi-list"></i>
  </button>
  
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="dashboard.php">Admin</a></li>
      <?php
      // Generate breadcrumb based on current page
      $pageTitle = match($currentPage) {
          'dashboard.php' => 'Dashboard',
          'status_monitor.php' => 'Status Monitor',
          'settings.php' => 'System Settings',
          'export_applications.php' => 'Export Data',
          'login.php' => 'Login',
          default => ucfirst(str_replace('.php', '', $currentPage))
      };
      
      // Add status filter to breadcrumb if applicable
      if ($currentPage === 'dashboard.php' && isset($_GET['status'])) {
        echo '<li class="breadcrumb-item">Dashboard</li>';
        echo '<li class="breadcrumb-item active">' . ucfirst(str_replace('_', ' ', $_GET['status'])) . '</li>';
      } else {
        echo '<li class="breadcrumb-item active">' . $pageTitle . '</li>';
      }
      ?>
    </ol>
  </nav>
  
  <div class="d-flex align-items-center">
    <!-- Quick Actions Dropdown -->
    <div class="dropdown me-3">
      <button class="btn btn-light btn-sm quick-action-btn" type="button" id="quickActionsDropdown" data-bs-toggle="dropdown" aria-expanded="false" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Quick Actions">
        <i class="bi bi-lightning-charge"></i>
      </button>
      <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="quickActionsDropdown">
        <li><h6 class="dropdown-header">Quick Actions</h6></li>
        <li><a class="dropdown-item" href="dashboard.php?status=pending"><i class="bi bi-hourglass-split"></i> View Pending</a></li>
        <li><a class="dropdown-item" href="export_applications.php"><i class="bi bi-file-earmark-excel"></i> Export Data</a></li>
        <li><a class="dropdown-item" href="status_monitor.php"><i class="bi bi-tools"></i> Status Monitor</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item" href="settings.php"><i class="bi bi-gear"></i> Settings</a></li>
      </ul>
    </div>
    
    <!-- User Profile Dropdown -->
    <div class="dropdown admin-profile-dropdown">
      <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="adminDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <div class="d-flex align-items-center">
          <div class="me-2 d-none d-md-block text-end">
            <div class="fw-semibold"><?= htmlspecialchars($adminName) ?></div>
            <div class="small text-muted">Administrator</div>
          </div>
          <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-weight: 600;">
            <?= strtoupper(substr($adminName, 0, 1)) ?>
          </div>
        </div>
      </a>
      <ul class="dropdown-menu" aria-labelledby="adminDropdown">
        <li>
          <div class="admin-profile-header">
            <div class="admin-avatar">
              <?= strtoupper(substr($adminName, 0, 1)) ?>
            </div>
            <div class="admin-name"><?= htmlspecialchars($adminName) ?></div>
            <div class="admin-role">Administrator</div>
          </div>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item" href="settings.php"><i class="bi bi-gear"></i> Settings</a></li>
        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#changePasswordModal"><i class="bi bi-shield-lock"></i> Change Password</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item danger" href="logout.php"><i class="bi bi-box-arrow-right"></i> Sign Out</a></li>
      </ul>
    </div>
  </div>
</header>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="passwordAlerts"></div>
        <form id="changePasswordForm">
          <div class="mb-3">
            <label for="currentPassword" class="form-label">Current Password</label>
            <input type="password" class="form-control" id="currentPassword" name="current_password" required>
          </div>
          <div class="mb-3">
            <label for="newPassword" class="form-label">New Password</label>
            <input type="password" class="form-control" id="newPassword" name="new_password" required>
            <div class="form-text">Password must be at least 8 characters long and include at least one number and one special character.</div>
          </div>
          <div class="mb-3">
            <label for="confirmPassword" class="form-label">Confirm New Password</label>
            <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="changePasswordBtn">Change Password</button>
      </div>
    </div>
  </div>
</div>

<!-- Main Content -->
<main class="main-content" id="mainContent">
