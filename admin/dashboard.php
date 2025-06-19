<?php
require '../includes/header.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

require '../config.php';

$stmt = $pdo->query("SELECT applications.*, users.full_name, users.email FROM applications
                     JOIN users ON applications.user_id = users.id ORDER BY applications.submitted_at DESC");
$applications = $stmt->fetchAll();
if (empty($applications)) {
    echo '<div class="alert alert-info">No applications found.</div>';
    include '../includes/footer.php';
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>Admin Dashboard</h2>
      <a href="logout.php" class="btn btn-outline-danger">Logout</a>
    </div>

    <div class="table-responsive">
      <table class="table table-bordered table-hover align-middle bg-white">
        <thead class="table-dark">
          <tr>
            <th>User</th>
            <th>Email</th>
            <th>Title</th>
            <th>Category</th>
            <th>Summary</th>
            <th>File</th>
            <th>Submitted</th>
            <!-- <th>Status</th> -->
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
              <td><?= nl2br(htmlspecialchars($app['summary'])) ?></td>
              <td><a href="../uploads/<?= basename($app['document_path']) ?>" target="_blank" class="btn btn-sm btn-primary">Download</a></td>
              <td><?= date('Y-m-d', strtotime($app['submitted_at'])) ?></td>
              <!-- <td><?= $app['application_status'] ?></td> -->
              <td>
                <a href="update_status.php?id=<?= $app['id'] ?>&status=approved" class="btn btn-sm btn-success">✅ Approve</a>
                <a href="update_status.php?id=<?= $app['id'] ?>&status=rejected" class="btn btn-sm btn-danger">❌ Reject</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

<?php include '../includes/footer.php'; ?>