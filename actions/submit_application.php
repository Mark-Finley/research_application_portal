<?php
session_start();
require '../config.php';

if (!isset($_SESSION['user_id'])) {
    die("Not authorized");
}

$user_id = $_SESSION['user_id'];
$study_title = $_POST['study_title'];
$category = $_POST['research_category'];
$summary = $_POST['summary'];

// Handle file upload
$upload_dir = "../uploads/";
if (!is_dir($upload_dir)) mkdir($upload_dir);

$filename = basename($_FILES['document']['name']);
$target_file = $upload_dir . time() . "_" . $filename;

if (move_uploaded_file($_FILES['document']['tmp_name'], $target_file)) {
    $stmt = $pdo->prepare("INSERT INTO applications (user_id, study_title, research_category, summary, document_path)
                           VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $study_title, $category, $summary, $target_file]);

    // Update user application status
    $pdo->prepare("UPDATE users SET application_status = 'pending' WHERE id = ?")->execute([$user_id]);

    header("Location: ../dashboard.php?submitted=1");
} else {
    die("File upload failed.");
}
