<?php
/**
 * Application Status Monitor Utility
 * 
 * This script checks for inconsistencies in application statuses and reports them.
 * It can be run periodically by administrators to ensure data integrity.
 */
require 'header.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

require '../config.php';
require '../form_steps/form_config.php';

$fix = isset($_GET['fix']) && $_GET['fix'] == '1';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Application Status Monitor</title>
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
    }
    .table-container {
      background: white;
      border-radius: 10px;
      padding: 20px;
      margin-bottom: 30px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }
    .table-dark {
      background-color: #007b55 !important;
      border-color: #007b55;
    }
    .status-card {
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
      transition: all 0.3s ease;
      border: none;
      overflow: hidden;
      margin-bottom: 20px;
    }
    .status-card .card-body {
      padding: 1.5rem;
      background: white;
    }
    .status-card h3 {
      font-size: 2.2rem;
      font-weight: 700;
      margin-bottom: 5px;
    }
    .btn-fix {
      background-color: #007b55;
      border-color: #007b55;
    }
    .btn-fix:hover {
      background-color: #005f3d;
      border-color: #005f3d;
    }
  </style>
</head>
<body>

<div class="admin-header d-flex justify-content-between align-items-center">
  <div class="d-flex align-items-center">
    <h2><i class="bi bi-gear-fill me-2"></i> Status Monitor</h2>
  </div>
  <div>
    <a href="dashboard.php" class="btn btn-light btn-sm"><i class="bi bi-speedometer2 me-1"></i> Back to Dashboard</a>
  </div>
</div>

<div class="container my-4">
  <!-- Page Header -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <h4 class="card-title"><i class="bi bi-shield-check me-2"></i>Data Integrity Monitor</h4>
          <p class="text-muted mb-0">This utility checks for inconsistencies in application statuses and helps maintain data integrity in the research portal database.</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Status Summary -->
  <div class="row mb-4">
    <?php
    // Get status counts for the summary cards
    $stmt = $pdo->query("SELECT status, COUNT(*) as count FROM applications GROUP BY status");
    $statusCounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate totals
    $totalApps = 0;
    foreach ($statusCounts as $row) {
      $totalApps += $row['count'];
    }
    
    // Display total applications card
    echo '<div class="col-md-4 mb-3">
            <div class="status-card">
              <div class="card-body text-center">
                <div class="text-muted mb-2">TOTAL APPLICATIONS</div>
                <h3 class="text-primary">' . $totalApps . '</h3>
              </div>
            </div>
          </div>';
    
    // Get count of applications with issues
    $stmtIssues = $pdo->query("SELECT COUNT(*) as count FROM applications 
                              WHERE (step_completed >= 7 AND (status IS NULL OR status = '' OR status = 'incomplete'))
                              OR ((status = 'pending' OR status = 'submitted') AND (step_completed < 7 OR step_completed IS NULL))");
    $issueCount = $stmtIssues->fetchColumn();
    
    // Display issues card
    echo '<div class="col-md-4 mb-3">
            <div class="status-card">
              <div class="card-body text-center">
                <div class="text-muted mb-2">STATUS ISSUES</div>
                <h3 class="' . ($issueCount > 0 ? 'text-danger' : 'text-success') . '">' . $issueCount . '</h3>
              </div>
            </div>
          </div>';
    
    // Get count of users with multiple applications
    $stmtMulti = $pdo->query("SELECT COUNT(*) as count FROM 
                              (SELECT user_id FROM applications GROUP BY user_id HAVING COUNT(*) > 1) as subquery");
    $multiCount = $stmtMulti->fetchColumn();
    
    // Display multiple applications card
    echo '<div class="col-md-4 mb-3">
            <div class="status-card">
              <div class="card-body text-center">
                <div class="text-muted mb-2">USERS WITH MULTIPLE APPS</div>
                <h3 class="text-info">' . $multiCount . '</h3>
              </div>
            </div>
          </div>';
    ?>
  </div>

<?php

// Check for completed apps (step 7+) marked as incomplete
$stmt = $pdo->query("SELECT id, user_id, status, step_completed, ref_code 
                    FROM applications 
                    WHERE step_completed >= 7 
                    AND (status IS NULL OR status = '' OR status = 'incomplete')");
$incompleteCompleted = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<div class='card shadow-sm border-0 mb-4'>
        <div class='card-header bg-white py-3'>
          <h5 class='card-title mb-0'><i class='bi bi-exclamation-triangle-fill text-warning me-2'></i>Completed Applications Marked as Incomplete</h5>
        </div>
        <div class='card-body'>";

if (empty($incompleteCompleted)) {
    echo "<div class='alert alert-success d-flex align-items-center'>
            <i class='bi bi-check-circle-fill me-2 fs-4'></i>
            <div>No issues found - all completed applications are properly marked.</div>
          </div>";
} else {
    echo "<div class='alert alert-warning d-flex align-items-center'>
            <i class='bi bi-exclamation-triangle-fill me-2 fs-4'></i>
            <div>Found <strong>" . count($incompleteCompleted) . "</strong> applications that have completed all steps but aren't marked as pending/completed.</div>
          </div>";
    
    echo "<div class='table-responsive'>
            <table class='table table-striped table-hover align-middle mb-0'>
              <thead class='table-dark'>
                <tr>
                  <th>App ID</th>
                  <th>User ID</th>
                  <th>Status</th>
                  <th>Step</th>
                  <th>Ref Code</th>
                </tr>
              </thead>
              <tbody>";
    
    foreach ($incompleteCompleted as $app) {
        echo "<tr>
                <td>{$app['id']}</td>
                <td>{$app['user_id']}</td>
                <td><span class='badge bg-secondary'>" . ($app['status'] ?: 'NULL') . "</span></td>
                <td><span class='badge bg-success'>{$app['step_completed']}/7</span></td>
                <td>{$app['ref_code']}</td>
              </tr>";
    }
    
    echo "</tbody></table>
          </div>";
    
    if ($fix) {
        echo "<div class='alert alert-info mt-3 d-flex align-items-center'>
                <i class='bi bi-info-circle-fill me-2 fs-4'></i>
                <div>Fixing these applications...</div>
              </div>";
        
        foreach ($incompleteCompleted as $app) {
            $update = $pdo->prepare("UPDATE applications SET status = 'pending' WHERE id = ?");
            $result = $update->execute([$app['id']]);
            
            if ($result) {
                echo "<div class='alert alert-success mt-2 d-flex align-items-center'>
                        <i class='bi bi-check-circle-fill me-2'></i>
                        <div>Updated application ID {$app['id']} to status 'pending'</div>
                      </div>";
            } else {
                echo "<div class='alert alert-danger mt-2 d-flex align-items-center'>
                        <i class='bi bi-x-circle-fill me-2'></i>
                        <div>Failed to update application ID {$app['id']}</div>
                      </div>";
            }
        }
    } else {
        echo "<div class='mt-3'>
                <a href='?fix=1' class='btn btn-warning'>
                  <i class='bi bi-wrench me-2'></i>Fix These Issues
                </a>
              </div>";
    }
}
echo "</div></div>";

// Check for incomplete apps (below step 7) marked as pending
$stmt = $pdo->query("SELECT id, user_id, status, step_completed, ref_code 
                    FROM applications 
                    WHERE (status = 'pending' OR status = 'submitted') 
                    AND (step_completed < 7 OR step_completed IS NULL)");
$pendingIncomplete = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<div class='card shadow-sm border-0 mb-4'>
        <div class='card-header bg-white py-3'>
          <h5 class='card-title mb-0'><i class='bi bi-exclamation-triangle-fill text-danger me-2'></i>Incomplete Applications Marked as Pending</h5>
        </div>
        <div class='card-body'>";

if (empty($pendingIncomplete)) {
    echo "<div class='alert alert-success d-flex align-items-center'>
            <i class='bi bi-check-circle-fill me-2 fs-4'></i>
            <div>No issues found - all pending applications have completed all steps.</div>
          </div>";
} else {
    echo "<div class='alert alert-warning d-flex align-items-center'>
            <i class='bi bi-exclamation-triangle-fill me-2 fs-4'></i>
            <div>Found <strong>" . count($pendingIncomplete) . "</strong> applications that are marked as pending but haven't completed all steps.</div>
          </div>";
    
    echo "<div class='table-responsive'>
            <table class='table table-striped table-hover align-middle mb-0'>
              <thead class='table-dark'>
                <tr>
                  <th>App ID</th>
                  <th>User ID</th>
                  <th>Status</th>
                  <th>Step</th>
                  <th>Ref Code</th>
                </tr>
              </thead>
              <tbody>";
    
    foreach ($pendingIncomplete as $app) {
        echo "<tr>
                <td>{$app['id']}</td>
                <td>{$app['user_id']}</td>
                <td><span class='badge bg-warning text-dark'>{$app['status']}</span></td>
                <td><span class='badge bg-danger'>" . ($app['step_completed'] ?: '0') . "/7</span></td>
                <td>{$app['ref_code']}</td>
              </tr>";
    }
    
    echo "</tbody></table>
          </div>";
    
    if ($fix) {
        echo "<div class='alert alert-info mt-3 d-flex align-items-center'>
                <i class='bi bi-info-circle-fill me-2 fs-4'></i>
                <div>Fixing these applications...</div>
              </div>";
        
        foreach ($pendingIncomplete as $app) {
            $update = $pdo->prepare("UPDATE applications SET status = 'incomplete' WHERE id = ?");
            $result = $update->execute([$app['id']]);
            
            if ($result) {
                echo "<div class='alert alert-success mt-2 d-flex align-items-center'>
                        <i class='bi bi-check-circle-fill me-2'></i>
                        <div>Updated application ID {$app['id']} to status 'incomplete'</div>
                      </div>";
            } else {
                echo "<div class='alert alert-danger mt-2 d-flex align-items-center'>
                        <i class='bi bi-x-circle-fill me-2'></i>
                        <div>Failed to update application ID {$app['id']}</div>
                      </div>";
            }
        }
    } else {
        echo "<div class='mt-3'>
                <a href='?fix=1' class='btn btn-warning'>
                  <i class='bi bi-wrench me-2'></i>Fix These Issues
                </a>
              </div>";
    }
}
echo "</div></div>";

// Check for users with multiple applications
$stmt = $pdo->query("SELECT user_id, COUNT(*) as app_count 
                    FROM applications 
                    GROUP BY user_id 
                    HAVING app_count > 1");
$multipleApps = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<div class='card shadow-sm border-0 mb-4'>
        <div class='card-header bg-white py-3'>
          <h5 class='card-title mb-0'><i class='bi bi-people-fill text-info me-2'></i>Users with Multiple Applications</h5>
        </div>
        <div class='card-body'>";

if (empty($multipleApps)) {
    echo "<div class='alert alert-success d-flex align-items-center'>
            <i class='bi bi-check-circle-fill me-2 fs-4'></i>
            <div>No users found with multiple applications.</div>
          </div>";
} else {
    echo "<div class='alert alert-info d-flex align-items-center'>
            <i class='bi bi-info-circle-fill me-2 fs-4'></i>
            <div>Found <strong>" . count($multipleApps) . "</strong> users with multiple applications. This is not necessarily an issue, but worth monitoring.</div>
          </div>";
    
    echo "<div class='table-responsive'>
            <table class='table table-striped table-hover align-middle mb-0'>
              <thead class='table-dark'>
                <tr>
                  <th>User ID</th>
                  <th>Application Count</th>
                  <th>Details</th>
                </tr>
              </thead>
              <tbody>";
    
    foreach ($multipleApps as $multi) {
        echo "<tr>
                <td>{$multi['user_id']}</td>
                <td><span class='badge bg-primary'>{$multi['app_count']}</span></td>
                <td><button type='button' class='btn btn-sm btn-outline-primary' data-bs-toggle='collapse' data-bs-target='#user_{$multi['user_id']}'>
                    <i class='bi bi-eye me-1'></i> View Applications
                    </button></td>
              </tr>";
        
        echo "<tr class='collapse' id='user_{$multi['user_id']}'>
                <td colspan='3'>
                  <div class='card card-body bg-light border-0'>
                    <table class='table table-sm table-hover mb-0'>
                      <thead>
                        <tr>
                          <th>App ID</th>
                          <th>Status</th>
                          <th>Step</th>
                          <th>Ref Code</th>
                          <th>Submitted At</th>
                        </tr>
                      </thead>
                      <tbody>";
        
        $user_id = $multi['user_id'];
        $apps = $pdo->prepare("SELECT id, status, step_completed, ref_code, submitted_at 
                             FROM applications 
                             WHERE user_id = ? 
                             ORDER BY id ASC");
        $apps->execute([$user_id]);
        $userApps = $apps->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($userApps as $app) {
            // Set status class
            $statusClass = match ($app['status']) {
                'approved' => 'bg-success',
                'pending' => 'bg-warning text-dark',
                'under_review' => 'bg-info text-dark',
                'rejected' => 'bg-danger',
                'submitted' => 'bg-warning text-dark',
                'incomplete' => 'bg-secondary',
                default => 'bg-secondary'
            };
            
            echo "<tr>
                    <td>{$app['id']}</td>
                    <td><span class='badge {$statusClass}'>" . ($app['status'] ?: 'incomplete') . "</span></td>
                    <td>{$app['step_completed']}/7</td>
                    <td>{$app['ref_code']}</td>
                    <td>" . date('Y-m-d H:i', strtotime($app['submitted_at'])) . "</td>
                  </tr>";
        }
        
        echo "    </tbody>
                    </table>
                  </div>
                </td>
              </tr>";
    }
    
    echo "</tbody></table>
          </div>";
}
echo "</div></div>";

// Status summary
$stmt = $pdo->query("SELECT status, COUNT(*) as count FROM applications GROUP BY status");
$statusCounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<div class='card shadow-sm border-0 mb-4'>
        <div class='card-header bg-white py-3'>
          <h5 class='card-title mb-0'><i class='bi bi-pie-chart-fill text-primary me-2'></i>Application Status Summary</h5>
        </div>
        <div class='card-body'>
          <div class='table-responsive'>
            <table class='table table-striped table-hover align-middle mb-0'>
              <thead class='table-dark'>
                <tr>
                  <th>Status</th>
                  <th>Count</th>
                  <th>Percentage</th>
                  <th>Visualization</th>
                </tr>
              </thead>
              <tbody>";

$totalCount = 0;
foreach ($statusCounts as $row) {
    $totalCount += $row['count'];
}

foreach ($statusCounts as $row) {
    $statusText = $row['status'] ?: 'NULL/Empty';
    $percentage = ($row['count'] / $totalCount) * 100;
    
    // Set status class
    $statusClass = match ($row['status']) {
        'approved' => 'bg-success',
        'pending' => 'bg-warning text-dark',
        'under_review' => 'bg-info text-dark',
        'rejected' => 'bg-danger',
        'submitted' => 'bg-warning text-dark',
        'incomplete' => 'bg-secondary',
        null, '' => 'bg-secondary',
        default => 'bg-secondary'
    };
    
    $barClass = match ($row['status']) {
        'approved' => 'bg-success',
        'pending' => 'bg-warning',
        'under_review' => 'bg-info',
        'rejected' => 'bg-danger',
        'submitted' => 'bg-warning',
        'incomplete' => 'bg-secondary',
        null, '' => 'bg-secondary',
        default => 'bg-secondary'
    };

    echo "<tr>
            <td><span class='badge {$statusClass}'>" . htmlspecialchars($statusText) . "</span></td>
            <td><strong>{$row['count']}</strong></td>
            <td>" . number_format($percentage, 1) . "%</td>
            <td>
              <div class='progress' style='height: 10px;'>
                <div class='progress-bar {$barClass}' role='progressbar' style='width: {$percentage}%' 
                     aria-valuenow='{$percentage}' aria-valuemin='0' aria-valuemax='100'></div>
              </div>
            </td>
          </tr>";
}

echo "    </tbody>
        </table>
      </div>
    </div>
  </div>
    
  <div class='d-flex justify-content-between mb-4'>
    <a href='dashboard.php' class='btn btn-primary'>
      <i class='bi bi-arrow-left me-2'></i>Return to Admin Dashboard
    </a>";
    
if ($issueCount > 0) {
    echo "<a href='?fix=1' class='btn btn-warning'>
            <i class='bi bi-tools me-2'></i>Fix All Issues
          </a>";
}
    
echo "</div>
</div>

<!-- Footer -->
<footer class='bg-light py-4 mt-auto'>
  <div class='container text-center'>
    <p class='text-muted mb-0'>Research Application Portal - Status Monitor Tool &copy; " . date('Y') . "</p>
  </div>
</footer>

<script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'></script>
</body>
</html>";
