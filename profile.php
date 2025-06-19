<?php
require 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";

// Fetch user info
$stmt = $pdo->prepare("SELECT full_name, email FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $new_password = $_POST['password'];

    if ($new_password) {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, password = ? WHERE id = ?");
        $stmt->execute([$full_name, $email, $hashed, $_SESSION['user_id']]);
    } else {
        $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ? WHERE id = ?");
        $stmt->execute([$full_name, $email, $_SESSION['user_id']]);
    }

    $message = "Profile updated successfully!";
    // Refresh user info
    $user['full_name'] = $full_name;
    $user['email'] = $email;
}
?>

<div class="container xs-md-lg my-5">
    <h4 class="mb-4">My Profile</h4>

    <?php if ($message): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST" class="card p-4 shadow-sm">
        <div class="mb-3">
            <label for="full_name" class="form-label">Full Name</label>
            <input type="text" name="full_name" id="full_name" class="form-control" required
                   value="<?= htmlspecialchars($user['full_name']) ?>">
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Institutional Email</label>
            <input type="email" name="email" id="email" class="form-control" required
                   value="<?= htmlspecialchars($user['email']) ?>">
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">New Password (leave blank to keep current)</label>
            <input type="password" name="password" id="password" class="form-control" placeholder="********">
        </div>

        <button type="submit" class="btn btn-primary">Update Profile</button>
    </form>
</div>

<?php require 'includes/footer.php'; ?>
