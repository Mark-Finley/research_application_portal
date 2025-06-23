<?php
include 'config.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$user_id = $_SESSION['user_id'];

// Dashboard stats (specific to logged-in user)
$stmtTotal = $pdo->prepare("SELECT COUNT(*) FROM applications WHERE user_id = ?");
$stmtTotal->execute([$user_id]);
$totalApplications = $stmtTotal->fetchColumn();

$stmtApproved = $pdo->prepare("SELECT COUNT(*) FROM applications WHERE user_id = ? AND status = 'approved'");
$stmtApproved->execute([$user_id]);
$approvedApplications = $stmtApproved->fetchColumn();

$stmtPending = $pdo->prepare("SELECT COUNT(*) FROM applications WHERE user_id = ? AND status = 'pending'");
$stmtPending->execute([$user_id]);
$pendingApplications = $stmtPending->fetchColumn();

$stmtReview = $pdo->prepare("SELECT COUNT(*) FROM applications WHERE user_id = ? AND status = 'under_review'");
$stmtReview->execute([$user_id]);
$underReview = $stmtReview->fetchColumn();

$stmtRejected = $pdo->prepare("SELECT COUNT(*) FROM applications WHERE user_id = ? AND status = 'rejected'");
$stmtRejected->execute([$user_id]);
$rejectedApplications = $stmtRejected->fetchColumn();

// Recent applications (specific to user)
$recentStmt = $pdo->prepare("SELECT id, study_title, research_category, submitted_at, status FROM applications WHERE user_id = ? ORDER BY submitted_at DESC LIMIT 5");
$recentStmt->execute([$user_id]);
$recentApplications = $recentStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
  .card h2 {
    font-size: 2.2rem;
    font-weight: 600;
  }

  .table td,
  .table th {
    vertical-align: middle;
  }

  .call_to_action {
    font-size: 22px;
    font-weight: 500;
    padding: 0.5rem 0.5rem;
    border-radius: 0.375rem;
    background-color: #397b56;
  }

  .call_to_action:hover {
    background-color: #2f5e43;
  }

 .dashboard-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 10px;
}

@media (max-width: 576px) {
  .dashboard-header {
    flex-direction: column;
    align-items: flex-start;
  }

  .dashboard-header h2,
  .dashboard-header .btn {
    width: 100%;
  }
}

</style>

<div class="container py-4">
  <div class="dashboard-header mb-4">
    <h2>Dashboard</h2>
    <a href="payment.php" class="btn btn-primary d-flex align-items-center call_to_action">
      <i class="fas fa-plus-circle me-2"></i> Begin New Application
    </a>
  </div>


  <!-- DASHBOARD STATS -->
  <div class="row g-3 mb-4">
    <div class="col-md-4 col-12">
      <div class="card shadow-sm h-100 border-0 bg-light">
        <div class="card-body text-center">
          <h6 class="text-muted">Total Applications</h6>
          <h2 class="text-primary"><?= $totalApplications ?></h2>
        </div>
      </div>
    </div>

    <div class="col-md-4 col-12">
      <div class="card shadow-sm h-100 border-0 bg-light">
        <div class="card-body text-center">
          <h6 class="text-muted">Under Review</h6>
          <h2 class="text-info"><?= $underReview ?></h2>
        </div>
      </div>
    </div>

    <div class="col-md-4 col-12">
      <div class="card shadow-sm h-100 border-0 bg-light">
        <div class="card-body text-center">
          <h6 class="text-muted">Pending</h6>
          <h2 class="text-warning"><?= $pendingApplications ?></h2>
        </div>
      </div>
    </div>

    <div class="col-md-6 col-12">
      <div class="card shadow-sm h-100 border-0 bg-light">
        <div class="card-body text-center">
          <h6 class="text-muted">Approved</h6>
          <h2 class="text-success"><?= $approvedApplications ?></h2>
        </div>
      </div>
    </div>

    <div class="col-md-6 col-12">
      <div class="card shadow-sm h-100 border-0 bg-light">
        <div class="card-body text-center">
          <h6 class="text-muted">Rejected</h6>
          <h2 class="text-danger"><?= $rejectedApplications ?></h2>
        </div>
      </div>
    </div>
  </div>

  <!-- RECENT APPLICATIONS -->
  <div class="card shadow-sm border-0">
    <div class="card-header bg-white">
      <h5 class="mb-0">Recent Applications</h5>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table align-middle table-hover mb-0">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Title</th>
              <th>Category</th>
              <th>Submitted</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if (count($recentApplications) > 0): ?>
              <?php foreach ($recentApplications as $index => $app): ?>
                <tr>
                  <td><?= $index + 1 ?></td>
                  <td><?= htmlspecialchars($app['study_title']) ?></td>
                  <td><?= ucfirst($app['research_category']) ?></td>
                  <td><?= date('Y-m-d', strtotime($app['submitted_at'])) ?></td>
                  <td>
                    <?php
                    $statusClass = match ($app['status']) {
                      'approved' => 'bg-success',
                      'pending' => 'bg-warning text-dark',
                      'under_review' => 'bg-info text-dark',
                      'rejected' => 'bg-danger',
                      default => 'bg-secondary'
                    };
                    ?>
                    <span class="badge <?= $statusClass ?>"><?= ucfirst($app['status']) ?></span>
                  </td>
                  <td>
                    <?php if ($app['status'] !== 'approved' && $app['status'] !== 'rejected'): ?>
                      <a href="apply.php?id=<?= $app['id'] ?>" class="btn btn-sm btn-outline-primary">Continue</a>
                    <?php else: ?>
                      <span class="text-muted">—</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="6" class="text-center py-3">No recent applications.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>