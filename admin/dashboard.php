<?php
require 'header.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

require '../config.php';

// Get application statistics
$stmtTotal = $pdo->query("SELECT COUNT(*) FROM applications");
$totalApplications = $stmtTotal->fetchColumn();

$stmtPending = $pdo->query("SELECT COUNT(*) FROM applications WHERE status = 'pending' OR status = 'submitted'");
$pendingApplications = $stmtPending->fetchColumn();

$stmtUnderReview = $pdo->query("SELECT COUNT(*) FROM applications WHERE status = 'under_review'");
$underReviewApplications = $stmtUnderReview->fetchColumn();

$stmtApproved = $pdo->query("SELECT COUNT(*) FROM applications WHERE status = 'approved'");
$approvedApplications = $stmtApproved->fetchColumn();

$stmtRejected = $pdo->query("SELECT COUNT(*) FROM applications WHERE status = 'rejected'");
$rejectedApplications = $stmtRejected->fetchColumn();

$stmtIncomplete = $pdo->query("SELECT COUNT(*) FROM applications WHERE status = 'incomplete' OR status IS NULL OR status = ''");
$incompleteApplications = $stmtIncomplete->fetchColumn();

// Set default filter to show all applications
$statusFilter = isset($_GET['status']) ? $_GET['status'] : 'all';
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$itemsPerPage = 10; // Number of applications to display per page

// Build the query based on the status filter and search
$query = "SELECT applications.*, users.full_name, users.email FROM applications
          JOIN users ON applications.user_id = users.id";

// Add WHERE clause based on filters
$whereConditions = [];
$params = [];

// Status filter
if ($statusFilter !== 'all') {
    $whereConditions[] = "applications.status = :status";
    $params[':status'] = $statusFilter;
}

// Search filter
if (!empty($searchQuery)) {
    $whereConditions[] = "(users.full_name LIKE :search OR users.email LIKE :search OR applications.ref_code LIKE :search OR applications.study_title LIKE :search)";
    $params[':search'] = "%$searchQuery%";
}

// Combine WHERE conditions if any
if (!empty($whereConditions)) {
    $query .= " WHERE " . implode(" AND ", $whereConditions);
}

// Count total applications for pagination
$countQuery = str_replace("applications.*, users.full_name, users.email", "COUNT(*) as total", $query);
$countStmt = $pdo->prepare($countQuery);
foreach ($params as $key => $value) {
    $countStmt->bindValue($key, $value);
}
$countStmt->execute();
$totalApplicationsFiltered = $countStmt->fetchColumn();

// Calculate pagination
$totalPages = ceil($totalApplicationsFiltered / $itemsPerPage);
$offset = ($currentPage - 1) * $itemsPerPage;

// Add order by and pagination
$query .= " ORDER BY applications.submitted_at DESC LIMIT :offset, :limit";

// Prepare and execute the query for paginated results
$stmt = $pdo->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);
$stmt->execute();
$applications = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background: #f8f9fa;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .admin-header {
      background: linear-gradient(135deg, #007b55 0%, #005f3d 100%);
      color: white;
      padding: 20px;
      border-bottom: 4px solid #005f3d;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    .admin-header h2 {
      margin: 0;
      font-weight: 600;
      font-size: 1.75rem;
    }
    .table thead {
      background-color: #007b55;
      color: white;
    }
    .action-btns .btn {
      margin-right: 5px;
      border-radius: 4px;
    }
    .stats-card {
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
      transition: all 0.3s ease;
      border: none;
      overflow: hidden;
      height: 100%;
    }
    .stats-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
    }
    .stats-card .card-body {
      padding: 1.5rem;
      background: white;
      position: relative;
      z-index: 1;
      height: 100%;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }
    .stats-card h3 {
      font-size: 2.5rem;
      font-weight: 700;
      margin-bottom: 5px;
      line-height: 1;
    }
    .stats-card .stats-icon {
      position: absolute;
      top: 15px;
      right: 15px;
      font-size: 2rem;
      opacity: 0.15;
    }
    .stats-card .text-muted {
      font-weight: 500;
      text-transform: uppercase;
      font-size: 0.85rem;
      letter-spacing: 0.5px;
    }
    .stats-card.total-card {
      border-bottom: 4px solid #0d6efd;
    }
    .stats-card.pending-card {
      border-bottom: 4px solid #ffc107;
    }
    .stats-card.review-card {
      border-bottom: 4px solid #0dcaf0;
    }
    .stats-card.approved-card {
      border-bottom: 4px solid #198754;
    }
    .stats-card.rejected-card {
      border-bottom: 4px solid #dc3545;
    }
    .stats-card.incomplete-card {
      border-bottom: 4px solid #6c757d;
    }
    .filter-btn {
      margin-right: 5px;
      margin-bottom: 5px;
      border-radius: 20px;
      padding: 8px 16px;
      font-weight: 500;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
      transition: all 0.2s;
    }
    .filter-btn.active {
      background-color: #007b55;
      color: white;
      border-color: #007b55;
      box-shadow: 0 4px 10px rgba(0, 123, 85, 0.3);
    }
    .filter-btn:hover:not(.active) {
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .table {
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }
    .table thead th {
      font-weight: 600;
      text-transform: uppercase;
      font-size: 0.85rem;
      letter-spacing: 0.5px;
      padding: 12px 15px;
    }
    .table tbody td {
      padding: 12px 15px;
      vertical-align: middle;
    }
    .table-striped tbody tr:nth-of-type(odd) {
      background-color: rgba(0, 0, 0, 0.02);
    }
    .btn-group .btn {
      border-radius: 4px;
    }
    .badge {
      padding: 0.5em 0.8em;
      font-weight: 500;
    }
    .modal-content {
      border-radius: 12px;
      overflow: hidden;
      border: none;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }
    .modal-header {
      background-color: #007b55;
      color: white;
    }
    .modal-title {
      font-weight: 600;
    }
    .alert {
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }
    .search-box {
      position: relative;
      max-width: 300px;
    }
    .search-box .form-control {
      padding-left: 2.5rem;
      border-radius: 20px;
      border: 1px solid rgba(0, 0, 0, 0.1);
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }
    .search-box .search-icon {
      position: absolute;
      left: 1rem;
      top: 0.6rem;
      color: #6c757d;
    }
    .quick-actions {
      background: white;
      border-radius: 10px;
      padding: 15px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
      margin-bottom: 20px;
    }
    .quick-action-btn {
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 12px;
      border-radius: 8px;
      transition: all 0.2s;
      color: #495057;
      text-decoration: none;
      height: 100%;
    }
    .quick-action-btn:hover {
      background-color: #f8f9fa;
      color: #007b55;
      transform: translateY(-3px);
    }
    .quick-action-btn i {
      font-size: 1.8rem;
      margin-bottom: 8px;
      color: #007b55;
    }
    .pagination {
      justify-content: center;
    }
    .pagination .page-item .page-link {
      color: #007b55;
      border-radius: 50%;
      margin: 0 3px;
      width: 36px;
      height: 36px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .pagination .page-item.active .page-link {
      background-color: #007b55;
      border-color: #007b55;
    }
    .document-link {
      display: flex;
      align-items: center;
      padding: 10px;
      border-radius: 8px;
      transition: all 0.2s;
      margin-bottom: 10px;
      text-decoration: none;
      color: #495057;
      border: 1px solid rgba(0, 0, 0, 0.1);
    }
    .document-link:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
      color: #007b55;
      border-color: #007b55;
    }
    .document-link i {
      font-size: 1.5rem;
      margin-right: 10px;
      color: #007b55;
    }
    /* Mobile responsive styles */
    @media (max-width: 992px) {
      .admin-header h2 {
        font-size: 1.5rem;
      }
      .admin-header .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
      }
      .stats-card {
        margin-bottom: 15px;
      }
      .stats-card h3 {
        font-size: 2rem;
      }
      .card-header h5 {
        font-size: 1rem;
      }
      .search-filter-row {
        flex-direction: column;
      }
      .search-box {
        margin-bottom: 15px;
        max-width: 100%;
      }
      .filter-container {
        overflow-x: auto;
        white-space: nowrap;
        padding-bottom: 10px;
        width: 100%;
      }
      .modal-footer {
        flex-direction: column;
      }
      .modal-footer .btn {
        margin-bottom: 5px;
        width: 100%;
      }
    }
    
    @media (max-width: 768px) {
      .card-responsive-stacked {
        flex-direction: column;
      }
      .card-responsive-stacked .col-md-6:first-child {
        border-right: none !important;
        border-bottom: 1px solid rgba(0,0,0,.125);
        padding-bottom: 15px;
        margin-bottom: 15px;
      }
      .table-responsive {
        border: none;
      }
      .mobile-stack-btn-group {
        display: flex;
        flex-direction: column;
      }
      .mobile-stack-btn-group .btn {
        margin-bottom: 5px;
        width: 100%;
      }
      .mobile-action-dropdown .dropdown-menu {
        width: 100%;
      }
      .table-mobile-responsive td {
        display: block;
        width: 100%;
        text-align: left;
        padding: 8px 15px;
        position: relative;
      }
      .table-mobile-responsive td:before {
        content: attr(data-label);
        float: left;
        font-weight: bold;
        text-transform: uppercase;
        font-size: 0.75rem;
      }
      .table-mobile-responsive td .d-flex {
        display: flex !important;
        justify-content: flex-start;
      }
      .table-mobile-responsive thead {
        display: none;
      }
      .table-mobile-responsive tr {
        display: block;
        margin-bottom: 15px;
        border: 1px solid rgba(0,0,0,.125);
        border-radius: 8px;
        background-color: #fff;
        box-shadow: 0 2px 5px rgba(0,0,0,.05);
      }
      .card-app-details .row {
        flex-direction: column;
      }
      .quick-action-btn {
        margin-bottom: 10px;
      }
      .admin-header {
        padding: 15px;
      }
    }
    
    @media (max-width: 576px) {
      .stats-row .col-6 {
        width: 100%;
      }
      .pagination .page-item .page-link {
        width: 30px;
        height: 30px;
        font-size: 0.8rem;
      }
    }
    
    /* Card for mobile applications */
    .mobile-app-card {
      display: none;
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,.1);
      margin-bottom: 15px;
      padding: 15px;
    }
    .mobile-app-card .app-title {
      font-weight: 600;
      font-size: 1.1rem;
      margin-bottom: 5px;
    }
    .mobile-app-card .app-meta {
      display: flex;
      justify-content: space-between;
      margin-bottom: 10px;
    }
    .mobile-app-card .app-action-buttons {
      display: flex;
      flex-direction: column;
      gap: 5px;
      margin-top: 10px;
    }
    
    @media (max-width: 768px) {
      .mobile-app-card {
        display: block;
      }
      .desktop-app-table {
        display: none;
      }
    }
  </style>
</head>
<body>

<div class="admin-header d-flex justify-content-between align-items-center">
  <div class="d-flex align-items-center">
    <h2><i class="bi bi-speedometer2 me-2"></i> Admin Dashboard</h2>
  </div>
  <div>
    <a href="status_monitor.php" class="btn btn-light btn-sm me-2"><i class="bi bi-gear-fill me-1"></i> Status Monitor</a>
    <a href="logout.php" class="btn btn-light btn-sm"><i class="bi bi-box-arrow-right me-1"></i> Logout</a>
  </div>
</div>

<div class="container my-4">
  <!-- Quick Actions Toolbar -->
  <div class="row mb-4">
    <div class="col-md-12">
      <div class="quick-actions">
        <div class="row g-3">
          <div class="col-lg-2 col-md-4 col-6">
            <a href="status_monitor.php" class="quick-action-btn">
              <i class="bi bi-gear"></i>
              <span>Status Monitor</span>
            </a>
          </div>
          <div class="col-lg-2 col-md-4 col-6">
            <a href="#" id="exportData" class="quick-action-btn">
              <i class="bi bi-file-earmark-excel"></i>
              <span>Export Data</span>
            </a>
          </div>
          <div class="col-lg-2 col-md-4 col-6">
            <a href="#" id="refreshData" class="quick-action-btn">
              <i class="bi bi-arrow-clockwise"></i>
              <span>Refresh Data</span>
            </a>
          </div>
          <div class="col-lg-2 col-md-4 col-6">
            <a href="report.php" class="quick-action-btn">
              <i class="bi bi-bar-chart"></i>
              <span>Reports</span>
            </a>
          </div>
          <div class="col-lg-2 col-md-4 col-6">
            <a href="#" id="notifyApplicants" class="quick-action-btn">
              <i class="bi bi-envelope"></i>
              <span>Notify Users</span>
            </a>
          </div>
          <div class="col-lg-2 col-md-4 col-6">
            <a href="settings.php" class="quick-action-btn">
              <i class="bi bi-sliders"></i>
              <span>Settings</span>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Status Messages -->
  <?php if (isset($_GET['message'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="bi bi-check-circle-fill me-2"></i>
      <?= htmlspecialchars($_GET['message']) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <!-- Statistics Cards -->
  <div class="row g-3 mb-4 stats-row">
    <div class="col-lg-4 col-md-6 col-sm-6 col-6">
      <div class="card stats-card total-card">
        <div class="card-body text-center">
          <div class="stats-icon"><i class="bi bi-clipboard-data"></i></div>
          <div class="text-muted mb-2">Total Applications</div>
          <h3 class="text-primary"><?= $totalApplications ?></h3>
        </div>
      </div>
    </div>
    <div class="col-lg-4 col-md-6 col-sm-6 col-6">
      <div class="card stats-card pending-card">
        <div class="card-body text-center">
          <div class="stats-icon"><i class="bi bi-hourglass-split"></i></div>
          <div class="text-muted mb-2">Pending Review</div>
          <h3 class="text-warning"><?= $pendingApplications ?></h3>
        </div>
      </div>
    </div>
    <div class="col-lg-4 col-md-6 col-sm-6 col-6">
      <div class="card stats-card review-card">
        <div class="card-body text-center">
          <div class="stats-icon"><i class="bi bi-search"></i></div>
          <div class="text-muted mb-2">Under Review</div>
          <h3 class="text-info"><?= $underReviewApplications ?></h3>
        </div>
      </div>
    </div>
    <div class="col-lg-4 col-md-6 col-sm-6 col-6">
      <div class="card stats-card approved-card">
        <div class="card-body text-center">
          <div class="stats-icon"><i class="bi bi-check-circle"></i></div>
          <div class="text-muted mb-2">Approved</div>
          <h3 class="text-success"><?= $approvedApplications ?></h3>
        </div>
      </div>
    </div>
    <div class="col-lg-4 col-md-6 col-sm-6 col-6">
      <div class="card stats-card rejected-card">
        <div class="card-body text-center">
          <div class="stats-icon"><i class="bi bi-x-circle"></i></div>
          <div class="text-muted mb-2">Rejected</div>
          <h3 class="text-danger"><?= $rejectedApplications ?></h3>
        </div>
      </div>
    </div>
    <div class="col-lg-4 col-md-6 col-sm-6 col-6">
      <div class="card stats-card incomplete-card">
        <div class="card-body text-center">
          <div class="stats-icon"><i class="bi bi-exclamation-circle"></i></div>
          <div class="text-muted mb-2">Incomplete</div>
          <h3 class="text-secondary"><?= $incompleteApplications ?></h3>
        </div>
      </div>
    </div>
  </div>

  <!-- Status Filter Buttons -->
  <div class="card mb-4 border-0 shadow-sm">
    <div class="card-body">
      <div class="row search-filter-row">
        <div class="col-lg-6">
          <h5 class="card-title mb-3"><i class="bi bi-funnel me-2"></i>Filter Applications</h5>
          <div class="filter-container">
            <div class="d-flex flex-wrap">
              <a href="dashboard.php" class="btn btn-outline-dark filter-btn <?= $statusFilter === 'all' ? 'active' : '' ?>">
                <i class="bi bi-grid-3x3-gap me-1"></i> All
              </a>
              <a href="dashboard.php?status=pending" class="btn btn-outline-warning filter-btn <?= $statusFilter === 'pending' ? 'active' : '' ?>">
                <i class="bi bi-hourglass-split me-1"></i> Pending
              </a>
              <a href="dashboard.php?status=under_review" class="btn btn-outline-info filter-btn <?= $statusFilter === 'under_review' ? 'active' : '' ?>">
                <i class="bi bi-search me-1"></i> Under Review
              </a>
              <a href="dashboard.php?status=approved" class="btn btn-outline-success filter-btn <?= $statusFilter === 'approved' ? 'active' : '' ?>">
                <i class="bi bi-check-circle me-1"></i> Approved
              </a>
              <a href="dashboard.php?status=rejected" class="btn btn-outline-danger filter-btn <?= $statusFilter === 'rejected' ? 'active' : '' ?>">
                <i class="bi bi-x-circle me-1"></i> Rejected
              </a>
              <a href="dashboard.php?status=incomplete" class="btn btn-outline-secondary filter-btn <?= $statusFilter === 'incomplete' ? 'active' : '' ?>">
                <i class="bi bi-exclamation-circle me-1"></i> Incomplete
              </a>
            </div>
          </div>
        </div>
        <div class="col-lg-6 mt-lg-0 mt-4">
          <h5 class="card-title mb-3"><i class="bi bi-search me-2"></i>Search Applications</h5>
          <form action="dashboard.php" method="GET" class="search-box w-100">
            <div class="input-group">
              <span class="search-icon">
                <i class="bi bi-search"></i>
              </span>
              <input type="text" name="search" class="form-control" placeholder="Search by name, email, or ref code..." 
                value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
              <?php if (isset($_GET['status'])): ?>
                <input type="hidden" name="status" value="<?= htmlspecialchars($_GET['status']) ?>">
              <?php endif; ?>
              <button type="submit" class="btn btn-primary">Search</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <?php if (empty($applications)): ?>
    <div class="alert alert-info text-center">
      <i class="bi bi-info-circle me-2"></i>
      No applications found for the selected filter.
    </div>
  <?php else: ?>
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-header bg-white py-3">
        <h5 class="card-title mb-0">
          <i class="bi bi-list-ul me-2"></i>
          <?php
            $title = match($statusFilter) {
              'pending' => 'Pending Applications',
              'under_review' => 'Applications Under Review',
              'approved' => 'Approved Applications',
              'rejected' => 'Rejected Applications',
              'incomplete' => 'Incomplete Applications',
              default => 'All Applications'
            };
            echo $title;
          ?>
          <span class="badge bg-primary ms-2"><?= count($applications) ?></span>
        </h5>
      </div>
      <div class="card-body p-0">
        <!-- Desktop Table View -->
        <div class="table-responsive desktop-app-table">
          <table class="table table-striped table-hover align-middle mb-0">
            <thead>
              <tr>
                <th><i class="bi bi-hash me-1"></i>Ref Code</th>
                <th><i class="bi bi-person me-1"></i>Applicant</th>
                <th><i class="bi bi-journal-text me-1"></i>Study</th>
                <th><i class="bi bi-tag me-1"></i>Category</th>
                <th><i class="bi bi-flag me-1"></i>Status</th>
                <th><i class="bi bi-calendar me-1"></i>Submitted</th>
                <th><i class="bi bi-gear me-1"></i>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($applications as $app): ?>
                <tr>
                  <td><span class="badge bg-secondary"><?= htmlspecialchars($app['ref_code']) ?></span></td>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="avatar-placeholder bg-primary text-white rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; font-weight: 600;">
                        <?= strtoupper(substr($app['full_name'] ?? 'U', 0, 1)) ?>
                      </div>
                      <div>
                        <div class="fw-medium"><?= htmlspecialchars($app['full_name']) ?></div>
                        <div class="small text-muted"><?= htmlspecialchars($app['email']) ?></div>
                      </div>
                    </div>
              </td>
              <td>
                <div class="fw-medium text-truncate" style="max-width: 200px;" title="<?= htmlspecialchars($app['study_title']) ?>">
                  <?= htmlspecialchars($app['study_title'] ?: 'Untitled Study') ?>
                </div>
              </td>
              <td>
                <span class="badge bg-light text-dark border">
                  <?= htmlspecialchars(ucfirst($app['research_category'] ?: 'Unspecified')) ?>
                </span>
              </td>
              <td>
                <?php
                $statusClass = match ($app['status']) {
                  'approved' => 'bg-success',
                  'pending' => 'bg-warning text-dark',
                  'under_review' => 'bg-info text-dark',
                  'rejected' => 'bg-danger',
                  'submitted' => 'bg-warning text-dark',
                  'incomplete' => 'bg-secondary',
                  default => 'bg-secondary'
                };
                
                $statusIcon = match ($app['status']) {
                  'approved' => '<i class="bi bi-check-circle-fill me-1"></i>',
                  'pending' => '<i class="bi bi-hourglass-split me-1"></i>',
                  'under_review' => '<i class="bi bi-search me-1"></i>',
                  'rejected' => '<i class="bi bi-x-circle-fill me-1"></i>',
                  'submitted' => '<i class="bi bi-hourglass-split me-1"></i>',
                  'incomplete' => '<i class="bi bi-exclamation-circle-fill me-1"></i>',
                  default => '<i class="bi bi-exclamation-circle-fill me-1"></i>'
                };
                
                // Set a default status text for empty or null values
                $statusText = !empty($app['status']) ? ucfirst($app['status']) : 'Incomplete';
                ?>
                <span class="badge <?= $statusClass ?>"><?= $statusIcon . $statusText ?></span>
              </td>
              <td><?= date('M d, Y', strtotime($app['submitted_at'])) ?></td>
              <td class="action-btns">
                <div class="btn-group">
                  <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#viewModal<?= $app['id'] ?>">
                    <i class="bi bi-eye"></i> <span class="d-none d-md-inline">View</span>
                  </button>
                  
                  <button type="button" class="btn btn-sm btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="visually-hidden">Toggle Dropdown</span>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                    <li><h6 class="dropdown-header">Change Status</h6></li>
                    <li><a class="dropdown-item" href="update_status.php?id=<?= $app['id'] ?>&status=under_review">
                      <i class="bi bi-search text-info me-2"></i> Mark as Under Review
                    </a></li>
                    <li><a class="dropdown-item" href="update_status.php?id=<?= $app['id'] ?>&status=approved">
                      <i class="bi bi-check-circle text-success me-2"></i> Approve Application
                    </a></li>
                    <li><a class="dropdown-item" href="update_status.php?id=<?= $app['id'] ?>&status=rejected">
                      <i class="bi bi-x-circle text-danger me-2"></i> Reject Application
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#viewModal<?= $app['id'] ?>">
                      <i class="bi bi-eye text-primary me-2"></i> View Details
                    </a></li>
                  </ul>
                </div>
              </td>
            </tr>
            
            <!-- Modal for each application -->
            <div class="modal fade" id="viewModal<?= $app['id'] ?>" tabindex="-1" aria-labelledby="viewModalLabel<?= $app['id'] ?>" aria-hidden="true">
              <div class="modal-dialog modal-lg">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="viewModalLabel<?= $app['id'] ?>">
                      <i class="bi bi-file-earmark-text me-2"></i>Application Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <!-- Status Badge -->
                    <div class="mb-4 text-center">
                      <span class="badge <?= $statusClass ?> py-2 px-4" style="font-size: 1rem;">
                        <?= $statusIcon . $statusText ?>
                      </span>
                    </div>
                    
                    <!-- Basic Info -->
                    <div class="card border-0 shadow-sm mb-4">
                      <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Basic Information</h6>
                      </div>
                      <div class="card-body">
                        <div class="row">
                          <div class="col-md-6">
                            <div class="mb-3">
                              <label class="text-muted small text-uppercase">Reference Code</label>
                              <div class="fw-medium"><?= htmlspecialchars($app['ref_code']) ?></div>
                            </div>
                            <div class="mb-3">
                              <label class="text-muted small text-uppercase">Applicant</label>
                              <div class="fw-medium"><?= htmlspecialchars($app['full_name']) ?></div>
                            </div>
                            <div class="mb-3">
                              <label class="text-muted small text-uppercase">Email</label>
                              <div class="fw-medium"><?= htmlspecialchars($app['email']) ?></div>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="mb-3">
                              <label class="text-muted small text-uppercase">Submitted</label>
                              <div class="fw-medium"><?= date('F d, Y - h:i A', strtotime($app['submitted_at'])) ?></div>
                            </div>
                            <div class="mb-3">
                              <label class="text-muted small text-uppercase">Category</label>
                              <div class="fw-medium"><?= htmlspecialchars(ucfirst($app['research_category'] ?: 'Not specified')) ?></div>
                            </div>
                            <div class="mb-3">
                              <label class="text-muted small text-uppercase">Completion</label>
                              <div class="progress" style="height: 10px;">
                                <?php 
                                $completionPercent = ($app['step_completed'] / 7) * 100;
                                $progressClass = match(true) {
                                  $completionPercent < 50 => 'bg-danger',
                                  $completionPercent < 100 => 'bg-warning',
                                  default => 'bg-success'
                                };
                                ?>
                                <div class="progress-bar <?= $progressClass ?>" role="progressbar" style="width: <?= $completionPercent ?>%" 
                                     aria-valuenow="<?= $completionPercent ?>" aria-valuemin="0" aria-valuemax="100"></div>
                              </div>
                              <div class="small mt-1 text-muted"><?= $app['step_completed'] ?> of 7 steps completed</div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    
                    <!-- Study Details -->
                    <div class="card border-0 shadow-sm mb-4">
                      <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="bi bi-journal-text me-2"></i>Study Details</h6>
                      </div>
                      <div class="card-body">
                        <div class="mb-3">
                          <label class="text-muted small text-uppercase">Study Title</label>
                          <div class="fw-medium"><?= htmlspecialchars($app['study_title'] ?: 'Not specified') ?></div>
                        </div>
                        
                        <?php if (!empty($app['expected_completion_date'])): ?>
                        <div class="mb-3">
                          <label class="text-muted small text-uppercase">Expected Completion</label>
                          <div class="fw-medium"><?= date('F d, Y', strtotime($app['expected_completion_date'])) ?></div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="mb-3">
                          <label class="text-muted small text-uppercase">Summary</label>
                          <div class="card p-3 bg-light rounded">
                            <?= nl2br(htmlspecialchars($app['summary'] ?: 'No summary provided.')) ?>
                          </div>
                        </div>
                      </div>
                    </div>
                    
                    <!-- Documents -->
                    <div class="card border-0 shadow-sm">
                      <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="bi bi-file-earmark me-2"></i>Documents</h6>
                      </div>
                      <div class="card-body">
                        <div class="row">
                          <?php if (!empty($app['document_path'])): ?>
                            <div class="col-md-12 mb-2">
                              <a href="../uploads/<?= basename($app['document_path']) ?>" target="_blank" class="document-link">
                                <i class="bi bi-file-earmark-pdf"></i>
                                <div>
                                  <strong>Research Proposal</strong>
                                  <div class="small text-muted">Uploaded: <?= date('M d, Y', filemtime('../uploads/' . basename($app['document_path']))) ?></div>
                                </div>
                                <span class="ms-auto"><i class="bi bi-box-arrow-up-right"></i></span>
                              </a>
                            </div>
                          <?php endif; ?>
                          <?php if (!empty($app['consent_form_path'])): ?>
                            <div class="col-md-12 mb-2">
                              <a href="../uploads/<?= basename($app['consent_form_path']) ?>" target="_blank" class="document-link">
                                <i class="bi bi-file-earmark-text"></i>
                                <div>
                                  <strong>Consent Form</strong>
                                  <div class="small text-muted">Uploaded: <?= date('M d, Y', filemtime('../uploads/' . basename($app['consent_form_path']))) ?></div>
                                </div>
                                <span class="ms-auto"><i class="bi bi-box-arrow-up-right"></i></span>
                              </a>
                            </div>
                          <?php endif; ?>
                          <?php if (!empty($app['ethics_approval_path'])): ?>
                            <div class="col-md-12 mb-2">
                              <a href="../uploads/<?= basename($app['ethics_approval_path']) ?>" target="_blank" class="document-link">
                                <i class="bi bi-file-earmark-check"></i>
                                <div>
                                  <strong>Ethics Approval</strong>
                                  <div class="small text-muted">Uploaded: <?= date('M d, Y', filemtime('../uploads/' . basename($app['ethics_approval_path']))) ?></div>
                                </div>
                                <span class="ms-auto"><i class="bi bi-box-arrow-up-right"></i></span>
                              </a>
                            </div>
                          <?php endif; ?>
                          
                          <?php if (empty($app['document_path']) && empty($app['consent_form_path']) && empty($app['ethics_approval_path'])): ?>
                            <div class="col-12 text-center py-3">
                              <div class="text-muted">
                                <i class="bi bi-exclamation-circle me-2"></i>
                                No documents uploaded
                              </div>
                            </div>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer mobile-stack-btn-group">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                      <i class="bi bi-x me-1"></i> Close
                    </button>
                    <div class="d-flex flex-wrap gap-2 w-100 mt-2 mt-md-0">
                      <a href="update_status.php?id=<?= $app['id'] ?>&status=under_review" class="btn btn-info flex-fill">
                        <i class="bi bi-search me-1"></i> Mark as Under Review
                      </a>
                      <a href="update_status.php?id=<?= $app['id'] ?>&status=approved" class="btn btn-success flex-fill">
                        <i class="bi bi-check-circle me-1"></i> Approve
                      </a>
                      <a href="update_status.php?id=<?= $app['id'] ?>&status=rejected" class="btn btn-danger flex-fill">
                        <i class="bi bi-x-circle me-1"></i> Reject
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    
    <!-- Mobile Card View -->
    <div class="mobile-app-cards px-3 py-2">
      <?php foreach ($applications as $app): 
        $statusClass = match ($app['status']) {
          'approved' => 'bg-success',
          'pending' => 'bg-warning text-dark',
          'under_review' => 'bg-info text-dark',
          'rejected' => 'bg-danger',
          'submitted' => 'bg-warning text-dark',
          'incomplete' => 'bg-secondary',
          default => 'bg-secondary'
        };
        
        $statusIcon = match ($app['status']) {
          'approved' => '<i class="bi bi-check-circle-fill me-1"></i>',
          'pending' => '<i class="bi bi-hourglass-split me-1"></i>',
          'under_review' => '<i class="bi bi-search me-1"></i>',
          'rejected' => '<i class="bi bi-x-circle-fill me-1"></i>',
          'submitted' => '<i class="bi bi-hourglass-split me-1"></i>',
          'incomplete' => '<i class="bi bi-exclamation-circle-fill me-1"></i>',
          default => '<i class="bi bi-exclamation-circle-fill me-1"></i>'
        };
        
        $statusText = !empty($app['status']) ? ucfirst($app['status']) : 'Incomplete';
      ?>
        <div class="mobile-app-card">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="badge bg-secondary"><?= htmlspecialchars($app['ref_code']) ?></span>
            <span class="badge <?= $statusClass ?>"><?= $statusIcon . $statusText ?></span>
          </div>
          
          <div class="app-title"><?= htmlspecialchars($app['study_title'] ?: 'Untitled Study') ?></div>
          
          <div class="d-flex align-items-center mt-2 mb-3">
            <div class="avatar-placeholder bg-primary text-white rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-weight: 600;">
              <?= strtoupper(substr($app['full_name'] ?? 'U', 0, 1)) ?>
            </div>
            <div>
              <div class="fw-medium"><?= htmlspecialchars($app['full_name']) ?></div>
              <div class="small text-muted"><?= htmlspecialchars($app['email']) ?></div>
            </div>
          </div>
          
          <div class="app-meta mb-2">
            <div>
              <small class="text-muted">Category:</small>
              <div><?= htmlspecialchars(ucfirst($app['research_category'] ?: 'Unspecified')) ?></div>
            </div>
            <div class="text-end">
              <small class="text-muted">Submitted:</small>
              <div><?= date('M d, Y', strtotime($app['submitted_at'])) ?></div>
            </div>
          </div>
          
          <div class="app-action-buttons">
            <button type="button" class="btn btn-sm btn-primary w-100 mb-2" data-bs-toggle="modal" data-bs-target="#viewModal<?= $app['id'] ?>">
              <i class="bi bi-eye"></i> View Details
            </button>
            <div class="btn-group w-100">
              <a href="update_status.php?id=<?= $app['id'] ?>&status=under_review" class="btn btn-sm btn-info flex-grow-1">
                <i class="bi bi-search"></i> Review
              </a>
              <a href="update_status.php?id=<?= $app['id'] ?>&status=approved" class="btn btn-sm btn-success flex-grow-1">
                <i class="bi bi-check-circle"></i> Approve
              </a>
              <a href="update_status.php?id=<?= $app['id'] ?>&status=rejected" class="btn btn-sm btn-danger flex-grow-1">
                <i class="bi bi-x-circle"></i> Reject
              </a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
  <div class="card-footer bg-white py-3">
    <div class="row align-items-center">
      <div class="col-lg-6 text-lg-start text-center mb-lg-0 mb-3">
        <span class="text-muted">Showing <?= count($applications) ?> of <?= $totalApplicationsFiltered ?> applications</span>
      </div>
      <div class="col-lg-6">
        <?php if ($totalPages > 1): ?>
          <nav aria-label="Page navigation">
            <ul class="pagination mb-0 justify-content-lg-end justify-content-center">
              <!-- Previous button -->
              <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                <a class="page-link" href="?status=<?= $statusFilter ?>&search=<?= urlencode($searchQuery) ?>&page=<?= $currentPage - 1 ?>" aria-label="Previous">
                  <i class="bi bi-chevron-left"></i>
                </a>
              </li>
              
              <!-- First page -->
              <?php if ($currentPage > 3): ?>
                <li class="page-item">
                  <a class="page-link" href="?status=<?= $statusFilter ?>&search=<?= urlencode($searchQuery) ?>&page=1">1</a>
                </li>
                <?php if ($currentPage > 4): ?>
                  <li class="page-item disabled">
                    <span class="page-link">...</span>
                  </li>
                <?php endif; ?>
              <?php endif; ?>
              
              <!-- Page numbers -->
              <?php
              $startPage = max(1, $currentPage - 1);
              $endPage = min($totalPages, $currentPage + 1);
              
              for ($i = $startPage; $i <= $endPage; $i++): ?>
                <li class="page-item <?= ($i == $currentPage) ? 'active' : '' ?>">
                  <a class="page-link" href="?status=<?= $statusFilter ?>&search=<?= urlencode($searchQuery) ?>&page=<?= $i ?>"><?= $i ?></a>
                </li>
              <?php endfor; ?>
              
              <!-- Last page -->
              <?php if ($currentPage < $totalPages - 2): ?>
                <?php if ($currentPage < $totalPages - 3): ?>
                  <li class="page-item disabled">
                    <span class="page-link">...</span>
                  </li>
                <?php endif; ?>
                <li class="page-item">
                  <a class="page-link" href="?status=<?= $statusFilter ?>&search=<?= urlencode($searchQuery) ?>&page=<?= $totalPages ?>"><?= $totalPages ?></a>
                </li>
              <?php endif; ?>
              
              <!-- Next button -->
              <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                <a class="page-link" href="?status=<?= $statusFilter ?>&search=<?= urlencode($searchQuery) ?>&page=<?= $currentPage + 1 ?>" aria-label="Next">
                  <i class="bi bi-chevron-right"></i>
                </a>
              </li>
            </ul>
          </nav>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
  <?php endif; ?>

  <!-- Quick Links -->
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
      <h5 class="card-title mb-0"><i class="bi bi-link-45deg me-2"></i>Quick Links</h5>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-4">
          <a href="status_monitor.php" class="btn btn-outline-primary w-100">
            <i class="bi bi-gear-fill me-2"></i> Status Monitor
          </a>
        </div>
        <div class="col-md-4">
          <a href="../admin/export_applications.php" class="btn btn-outline-primary w-100">
            <i class="bi bi-file-earmark-excel me-2"></i> Export Applications
          </a>
        </div>
        <div class="col-md-4">
          <a href="settings.php" class="btn btn-outline-primary w-100">
            <i class="bi bi-sliders me-2"></i> System Settings
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Footer -->
<footer class="bg-light py-4 mt-auto">
  <div class="container text-center">
    <p class="text-muted mb-0">Research Application Portal Admin Dashboard &copy; <?= date('Y') ?></p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
      const alerts = document.querySelectorAll('.alert.alert-success');
      alerts.forEach(alert => {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
      });
    }, 5000);

    // Handle refresh data click
    document.getElementById('refreshData').addEventListener('click', function(e) {
      e.preventDefault();
      window.location.reload();
    });

    // Handle export data click
    document.getElementById('exportData').addEventListener('click', function(e) {
      e.preventDefault();
      const status = '<?= $statusFilter ?>';
      const search = '<?= $searchQuery ?>';
      let url = 'export_applications.php?format=excel';
      
      if (status !== 'all') {
        url += '&status=' + status;
      }
      
      if (search) {
        url += '&search=' + encodeURIComponent(search);
      }
      
      window.location.href = url;
    });

    // Handle notify applicants click
    document.getElementById('notifyApplicants').addEventListener('click', function(e) {
      e.preventDefault();
      
      // Check if there are any applications to notify
      const appCount = <?= count($applications) ?>;
      if (appCount === 0) {
        alert('No applications selected to notify.');
        return;
      }
      
      if (confirm('This will send a notification email to all ' + appCount + ' applicants in the current view. Continue?')) {
        // Here you would normally redirect to a notification form or send directly
        alert('Notification system will be implemented soon.');
      }
    });
    
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl);
    });
  });
</script>
</body>
</html>
