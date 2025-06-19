<?php
require 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch applications for the logged-in user
$stmt = $pdo->prepare("SELECT id, study_title, research_category, submitted_at, status 
                       FROM applications WHERE user_id = ? ORDER BY submitted_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4">
    <h4 class="mb-4">My Applications</h4>

    <div class="table-responsive">
        <table class="table table-hover table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Study Title</th>
                    <th>Category</th>
                    <th>Date Submitted</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($applications): ?>
                    <?php foreach ($applications as $index => $app): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($app['study_title']) ?></td>
                            <td><?= ucfirst($app['research_category']) ?></td>
                            <td><?= date('Y-m-d', strtotime($app['submitted_at'])) ?></td>
                            <td>
                                <?php if ($app['status']): ?>
                                    <span class="badge bg-success">Complete</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">Incomplete</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!$app['status']): ?>
                                    <a href="continue_application.php?id=<?= $app['id'] ?>" class="btn btn-sm btn-primary">Continue</a>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-secondary" disabled>View</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center">No applications found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require 'includes/footer.php'; ?>
