<?php
require 'header.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

require '../config.php';

$stmt = $pdo->query("SELECT applications.*, users.full_name, users.email FROM applications
                     JOIN users ON applications.user_id = users.id ORDER BY applications.submitted_at DESC");
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
      background: #f4f4f4;
    }
    .admin-header {
      background: #007b55;
      color: white;
      padding: 20px;
      border-bottom: 4px solid #005f3d;
    }
    .admin-header h2 {
      margin: 0;
    }
    .table thead {
      background-color: #007b55;
      color: white;
    }
    .action-btns .btn {
      margin-right: 5px;
    }
    @media (max-width: 768px) {
      .table-responsive table td {
        white-space: nowrap;
      }
    }
  </style>
</head>
<body>

<div class="admin-header d-flex justify-content-between align-items-center">
  <h2>Admin Dashboard</h2>
  <a href="logout.php" class="btn btn-light btn-sm"><i class="bi bi-box-arrow-right me-1"></i> Logout</a>
</div>

<div class="container my-4">
  <?php if (empty($applications)): ?>
    <div class="alert alert-info text-center">No applications found.</div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table table-striped table-hover align-middle bg-white shadow-sm rounded">
        <thead>
          <tr>
            <th>User</th>
            <th>Email</th>
            <th>Title</th>
            <th>Category</th>
            <th>Summary</th>
            <th>File</th>
            <th>Submitted</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($applications as $app): ?>
            <tr>
              <td><?= htmlspecialchars($app['full_name']) ?></td>
              <td><?= htmlspecialchars($app['email']) ?></td>
              <td><?= htmlspecialchars($app['study_title']) ?></td>
              <td><?= htmlspecialchars($app['research_category']) ?></td>
              <td style="max-width: 250px;"><?= nl2br(htmlspecialchars($app['summary'])) ?></td>
              <td>
                <?php if (!empty($app['document_path'])): ?>
                  <a href="../uploads/<?= basename($app['document_path']) ?>" target="_blank" class="btn btn-sm btn-primary">
                    <i class="bi bi-file-earmark-arrow-down"></i> Download
                  </a>
                <?php else: ?>
                  <span class="text-muted">No File</span>
                <?php endif; ?>
              </td>
              <td><?= date('Y-m-d', strtotime($app['submitted_at'])) ?></td>
              <td class="action-btns">
                <a href="update_status.php?id=<?= $app['id'] ?>&status=approved" class="btn btn-sm btn-success">
                  <i class="bi bi-check-circle-fill"></i>
                </a>
                <a href="update_status.php?id=<?= $app['id'] ?>&status=rejected" class="btn btn-sm btn-danger">
                  <i class="bi bi-x-circle-fill"></i>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
