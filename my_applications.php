<?php
require 'includes/header.php';
require 'config.php';
require 'form_steps/form_config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch applications for the logged-in user
$stmt = $pdo->prepare("SELECT id, study_title, research_category, submitted_at, status, ref_code, step_completed 
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
                    <th>Reference</th>
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
                            <td><?= htmlspecialchars($app['study_title'] ?? 'Untitled') ?></td>
                            <td><?= ucfirst($app['research_category'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($app['ref_code'] ?? 'N/A') ?></td>
                            <td><?= date('Y-m-d', strtotime($app['submitted_at'])) ?></td>
                            <td>
                                <?php
                                $statusClass = match ($app['status']) {
                                    'approved' => 'bg-success',
                                    'pending' => 'bg-warning text-dark',
                                    'under_review' => 'bg-info text-dark',
                                    'rejected' => 'bg-danger',
                                    'submitted' => 'bg-warning text-dark', // Keep for backward compatibility
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
                                        <a href="application.php?step=<?= $nextStep ?>&app_id=<?= $app['id'] ?>" class="btn btn-sm btn-primary">Continue</a>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Awaiting Review</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-secondary" disabled>View</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="text-center">No applications found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require 'includes/footer.php'; ?>
