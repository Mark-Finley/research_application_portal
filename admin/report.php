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

// Get monthly application counts for the chart (last 6 months)
$monthlyData = [];
$months = [];
$values = [];

for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $nextMonth = date('Y-m', strtotime("-" . ($i-1) . " months"));
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM applications WHERE submitted_at >= ? AND submitted_at < ?");
    $stmt->execute([$month . '-01', $nextMonth . '-01']);
    $count = $stmt->fetchColumn();
    
    $months[] = date('M Y', strtotime($month));
    $values[] = $count;
}

// Get research category statistics
$stmt = $pdo->query("SELECT research_category, COUNT(*) as count FROM applications WHERE research_category IS NOT NULL AND research_category != '' GROUP BY research_category ORDER BY count DESC");
$categoryData = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Reports</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    .card {
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
      transition: all 0.3s ease;
      border: none;
      overflow: hidden;
      margin-bottom: 20px;
    }
    .card-header {
      background-color: #f8f9fa;
      border-bottom: 1px solid #e9ecef;
      padding: 15px 20px;
    }
    .card-title {
      margin: 0;
      font-weight: 600;
      font-size: 1.1rem;
    }
    .chart-container {
      position: relative;
      height: 300px;
      padding: 20px;
    }
  </style>
</head>
<body>

<div class="admin-header d-flex justify-content-between align-items-center">
  <div class="d-flex align-items-center">
    <h2><i class="bi bi-bar-chart me-2"></i> Application Reports</h2>
  </div>
  <div>
    <a href="dashboard.php" class="btn btn-light btn-sm me-2"><i class="bi bi-speedometer2 me-1"></i> Dashboard</a>
    <a href="logout.php" class="btn btn-light btn-sm"><i class="bi bi-box-arrow-right me-1"></i> Logout</a>
  </div>
</div>

<div class="container my-4">
  <!-- Application Status Overview -->
  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="card">
        <div class="card-header">
          <h5 class="card-title"><i class="bi bi-pie-chart me-2"></i>Application Status Overview</h5>
        </div>
        <div class="card-body">
          <div class="chart-container">
            <canvas id="statusChart"></canvas>
          </div>
        </div>
      </div>
    </div>
    
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">
          <h5 class="card-title"><i class="bi bi-graph-up me-2"></i>Monthly Applications</h5>
        </div>
        <div class="card-body">
          <div class="chart-container">
            <canvas id="monthlyChart"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-md-6">
      <div class="card">
        <div class="card-header">
          <h5 class="card-title"><i class="bi bi-tags me-2"></i>Research Categories</h5>
        </div>
        <div class="card-body">
          <div class="chart-container">
            <canvas id="categoryChart"></canvas>
          </div>
        </div>
      </div>
    </div>
    
    <div class="col-md-6">
      <div class="card">
        <div class="card-header">
          <h5 class="card-title"><i class="bi bi-clipboard-data me-2"></i>Application Statistics</h5>
        </div>
        <div class="card-body">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>Metric</th>
                <th>Count</th>
                <th>Percentage</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><i class="bi bi-grid-3x3-gap text-primary me-2"></i> Total Applications</td>
                <td><?= $totalApplications ?></td>
                <td>100%</td>
              </tr>
              <tr>
                <td><i class="bi bi-hourglass-split text-warning me-2"></i> Pending</td>
                <td><?= $pendingApplications ?></td>
                <td><?= round(($pendingApplications / max(1, $totalApplications)) * 100, 1) ?>%</td>
              </tr>
              <tr>
                <td><i class="bi bi-search text-info me-2"></i> Under Review</td>
                <td><?= $underReviewApplications ?></td>
                <td><?= round(($underReviewApplications / max(1, $totalApplications)) * 100, 1) ?>%</td>
              </tr>
              <tr>
                <td><i class="bi bi-check-circle text-success me-2"></i> Approved</td>
                <td><?= $approvedApplications ?></td>
                <td><?= round(($approvedApplications / max(1, $totalApplications)) * 100, 1) ?>%</td>
              </tr>
              <tr>
                <td><i class="bi bi-x-circle text-danger me-2"></i> Rejected</td>
                <td><?= $rejectedApplications ?></td>
                <td><?= round(($rejectedApplications / max(1, $totalApplications)) * 100, 1) ?>%</td>
              </tr>
              <tr>
                <td><i class="bi bi-exclamation-circle text-secondary me-2"></i> Incomplete</td>
                <td><?= $incompleteApplications ?></td>
                <td><?= round(($incompleteApplications / max(1, $totalApplications)) * 100, 1) ?>%</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Actions -->
  <div class="row mb-4">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h5 class="card-title"><i class="bi bi-gear me-2"></i>Report Actions</h5>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-4">
              <a href="export_applications.php?format=excel" class="btn btn-primary w-100">
                <i class="bi bi-file-earmark-excel me-2"></i> Export All Data
              </a>
            </div>
            <div class="col-md-4">
              <a href="#" id="printReport" class="btn btn-secondary w-100">
                <i class="bi bi-printer me-2"></i> Print Report
              </a>
            </div>
            <div class="col-md-4">
              <a href="dashboard.php" class="btn btn-success w-100">
                <i class="bi bi-arrow-left me-2"></i> Back to Dashboard
              </a>
            </div>
          </div>
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
    // Status Pie Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    const statusChart = new Chart(statusCtx, {
      type: 'pie',
      data: {
        labels: ['Pending', 'Under Review', 'Approved', 'Rejected', 'Incomplete'],
        datasets: [{
          data: [
            <?= $pendingApplications ?>,
            <?= $underReviewApplications ?>,
            <?= $approvedApplications ?>,
            <?= $rejectedApplications ?>,
            <?= $incompleteApplications ?>
          ],
          backgroundColor: [
            '#ffc107',
            '#0dcaf0',
            '#198754',
            '#dc3545',
            '#6c757d'
          ],
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'right'
          }
        }
      }
    });

    // Monthly Applications Chart
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    const monthlyChart = new Chart(monthlyCtx, {
      type: 'bar',
      data: {
        labels: <?= json_encode($months) ?>,
        datasets: [{
          label: 'Applications',
          data: <?= json_encode($values) ?>,
          backgroundColor: 'rgba(0, 123, 85, 0.6)',
          borderColor: '#007b55',
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              precision: 0
            }
          }
        }
      }
    });

    // Category Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    const categoryLabels = [];
    const categoryValues = [];
    
    <?php foreach ($categoryData as $category): ?>
      categoryLabels.push('<?= ucfirst($category['research_category']) ?>');
      categoryValues.push(<?= $category['count'] ?>);
    <?php endforeach; ?>
    
    const categoryChart = new Chart(categoryCtx, {
      type: 'doughnut',
      data: {
        labels: categoryLabels,
        datasets: [{
          data: categoryValues,
          backgroundColor: [
            '#4c1d95',
            '#5b21b6',
            '#7c3aed',
            '#8b5cf6',
            '#a78bfa',
            '#c4b5fd',
            '#ddd6fe'
          ],
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'right'
          }
        }
      }
    });
    
    // Print report button
    document.getElementById('printReport').addEventListener('click', function(e) {
      e.preventDefault();
      window.print();
    });
  });
</script>
</body>
</html>
