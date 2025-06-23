<?php
require 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";
$error = "";

// Fetch user info
$stmt = $pdo->prepare("SELECT full_name, email, profile_picture FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $new_password = $_POST['password'];

    // Handle image upload
    $imagePath = $user['profile_picture'];
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['profile_picture']['tmp_name'];
        $fileName = basename($_FILES['profile_picture']['name']);
        $ext = pathinfo($fileName, PATHINFO_EXTENSION);
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array(strtolower($ext), $allowed)) {
            $newFileName = uniqid('profile_', true) . '.' . $ext;
            $destination = 'uploads/profile_pic/' . $newFileName;

            if (move_uploaded_file($fileTmp, $destination)) {
                $imagePath = $destination;
            } else {
                $error = "Failed to upload profile picture.";
            }
        } else {
            $error = "Invalid image format. Allowed: jpg, jpeg, png, gif.";
        }
    }

    if (!$error) {
        if ($new_password) {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, password = ?, profile_picture = ? WHERE id = ?");
            $stmt->execute([$full_name, $email, $hashed, $imagePath, $_SESSION['user_id']]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, profile_picture = ? WHERE id = ?");
            $stmt->execute([$full_name, $email, $imagePath, $_SESSION['user_id']]);
        }

        $message = "Profile updated successfully!";
        $user['full_name'] = $full_name;
        $user['email'] = $email;
        $user['profile_picture'] = $imagePath;
    }
}

// Handle account deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_account'])) {
    // Fetch profile picture path and email before deleting
    $stmt = $pdo->prepare("SELECT profile_picture, email, full_name FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $userToDelete = $stmt->fetch(PDO::FETCH_ASSOC);

    // 1. Delete profile picture file if it exists
    if (!empty($userToDelete['profile_picture']) && file_exists($userToDelete['profile_picture'])) {
        unlink($userToDelete['profile_picture']);
    }

    // 2. Delete user's application records
    $stmt = $pdo->prepare("DELETE FROM applications WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);

    // 3. Delete user record
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);

    // 4. Send email confirmation
    $to = $userToDelete['email'];
    $subject = "Your Account Has Been Deleted";
    $body = "Hello {$userToDelete['full_name']},\n\nYour account and all associated data have been permanently deleted from our system.\n\nIf you didn’t request this, or have concerns, please contact support.\n\nRegards,\nResearch Portal Team";
    $headers = "From: no-reply@yourdomain.com";

    @mail($to, $subject, $body, $headers); // use @ to suppress errors if mail isn't configured

    // 5. End session and redirect
    session_destroy();
    header("Location: goodbye.php");
    exit();
}
?>

<div class="container xs-md-lg my-5">
    <h4 class="mb-4">My Profile</h4>

    <?php if ($message): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="card p-4 shadow-sm">
        <div class="text-center mb-4">
            <?php if (!empty($user['profile_picture']) && file_exists($user['profile_picture'])): ?>
                <img src="<?= $user['profile_picture'] ?>" alt="Profile Picture" class="rounded-circle mb-2" width="120" height="120">
            <?php else: ?>
                <img src="assets/img/default-avatar.png" alt="Default Avatar" class="rounded-circle mb-2" width="120" height="120">
            <?php endif; ?>
            <div>
                <input type="file" name="profile_picture" accept="image/*" class="form-control mt-2" style="max-width: 300px; margin: auto;">
            </div>
        </div>

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

        <div class="d-flex justify-content-between">
            <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>

            <button type="submit" name="delete_account" class="btn btn-outline-danger"
                    onclick="return confirm('Are you sure you want to delete your account? This action cannot be undone.')">
                Delete Account
            </button>
        </div>
    </form>
</div>


<?php require 'includes/footer.php'; ?>
