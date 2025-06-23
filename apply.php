<?php
include 'includes/header.php';
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user info
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Check payment status
if (!$user['has_paid']) {
    echo '<div class="alert alert-danger m-4">You must complete payment to access the application form.</div>';
    include 'includes/footer.php';
    exit();
}
?>

<div class="container my-5">
  <h3 class="mb-4">Research Application Form</h3>

  <form action="../actions/submit_application.php" method="POST" enctype="multipart/form-data" class="card p-4 shadow-sm">
    <div class="mb-3">
      <label for="study_title" class="form-label">Study Title</label>
      <input type="text" name="study_title" id="study_title" class="form-control" required>
    </div>

    <div class="mb-3">
      <label for="research_category" class="form-label">Research Category</label>
      <select name="research_category" id="research_category" class="form-select" required>
        <option value="">Select Category</option>
        <option value="clinical">Clinical</option>
        <option value="lab">Lab Research</option>
        <option value="social">Social Sciences</option>
      </select>
    </div>

    <div class="mb-3">
      <label for="summary" class="form-label">Brief Summary</label>
      <textarea name="summary" id="summary" class="form-control" rows="4" placeholder="Provide a brief summary..." required></textarea>
    </div>

    <div class="mb-3">
      <label for="document" class="form-label">Upload Document (PDF/DOCX)</label>
      <input type="file" name="document" id="document" class="form-control" accept=".pdf,.docx" required>
    </div>

    <button type="submit" class="btn btn-primary">Submit Application</button>
  </form>
</div>

<?php include 'includes/footer.php'; ?>
