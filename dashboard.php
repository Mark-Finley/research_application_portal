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

// Count both 'pending' and legacy 'submitted' status applications
$stmtPending = $pdo->prepare("SELECT COUNT(*) FROM applications WHERE user_id = ? AND (status = 'pending' OR status = 'submitted')");
$stmtPending->execute([$user_id]);
$pendingApplications = $stmtPending->fetchColumn();

$stmtReview = $pdo->prepare("SELECT COUNT(*) FROM applications WHERE user_id = ? AND status = 'under_review'");
$stmtReview->execute([$user_id]);
$underReview = $stmtReview->fetchColumn();

$stmtRejected = $pdo->prepare("SELECT COUNT(*) FROM applications WHERE user_id = ? AND status = 'rejected'");
$stmtRejected->execute([$user_id]);
$rejectedApplications = $stmtRejected->fetchColumn();

// Count incomplete applications (those with no status or empty status)
$stmtIncomplete = $pdo->prepare("SELECT COUNT(*) FROM applications WHERE user_id = ? AND (status IS NULL OR status = '' OR status = 'incomplete')");
$stmtIncomplete->execute([$user_id]);
$incompleteApplications = $stmtIncomplete->fetchColumn();

// Recent applications (specific to user)
$recentStmt = $pdo->prepare("SELECT id, study_title, research_category, submitted_at, status, step_completed, ref_code FROM applications WHERE user_id = ? ORDER BY submitted_at DESC LIMIT 5");
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
  <?php if (isset($_GET['message']) && $_GET['message'] == 'application_in_review'): ?>
    <div class="alert alert-info alert-dismissible fade show" role="alert">
      <i class="bi bi-info-circle-fill me-2"></i>
      This application is currently awaiting review and cannot be edited.
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>
  
  <?php if (isset($_GET['submitted']) && $_GET['submitted'] == '1'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="bi bi-check-circle-fill me-2"></i>
      Your application has been successfully submitted and is pending review.
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <div class="dashboard-header mb-4">
    <h2>Dashboard</h2>
    <a href="application.php" class="btn btn-primary d-flex align-items-center call_to_action">
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
          <h6 class="text-muted">Incomplete</h6>
          <h2 class="text-secondary"><?= $incompleteApplications ?></h2>
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
          <h6 class="text-muted">Approved</h6>
          <h2 class="text-success"><?= $approvedApplications ?></h2>
        </div>
      </div>
    </div>

    <div class="col-md-4 col-12">
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
              <th>Reference</th>
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
                  <td><?= htmlspecialchars($app['study_title'] ?? 'Untitled') ?></td>
                  <td><?= ucfirst($app['research_category'] ?? 'N/A') ?></td>
                  <td><span class="badge bg-secondary"><?= htmlspecialchars($app['ref_code'] ?? 'N/A') ?></span></td>
                  <td><?= date('Y-m-d', strtotime($app['submitted_at'])) ?></td>
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
                    
                    // Set a default status text for empty or null values
                    $statusText = !empty($app['status']) ? ucfirst($app['status']) : 'Incomplete';
                    ?>
                    <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                  </td>
                  <td>
                    <?php if ($app['status'] !== 'approved' && $app['status'] !== 'rejected'): ?>
                      <?php 
                        // Determine next step for the application
                        $nextStep = isset($app['step_completed']) ? (int)$app['step_completed'] + 1 : 1;
                        // If step_completed is 7 (final step) or greater, go to step 7 (review)
                        $nextStep = $nextStep > 7 ? 7 : $nextStep;
                        
                        // Don't allow editing if the application is already pending or under review
                        $isEditable = !in_array($app['status'], ['pending', 'under_review', 'submitted']);
                      ?>
                      
                      <?php if ($isEditable): ?>
                        <a href="application.php?step=<?= $nextStep ?>&app_id=<?= $app['id'] ?>" class="btn btn-sm btn-outline-primary">Continue</a>
                      <?php else: ?>
                        <span class="text-muted">Awaiting Review</span>
                      <?php endif; ?>
                    <?php else: ?>
                      <span class="text-muted">—</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="7" class="text-center py-3">No recent applications.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>