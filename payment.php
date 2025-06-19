<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user info
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user || empty($user['email'])) {
    die("User not found or missing email.");
}

$amount = 20000; // in kobo
$reference = uniqid('txn_');
$callback_url = "https://curvy-cases-go.loca.lt/actions/payment_callback.php";

$metadata = [
  "user_id" => $user["id"],
  "full_name" => $user["full_name"],
  "email" => $user["email"],
  "session" => session_id()
];

$fields = [
  'email' => $user['email'],
  'amount' => $amount,
  'reference' => $reference,
  'callback_url' => $callback_url,
  'metadata' => $metadata
];

$fields_string = json_encode($fields);

// Initialize cURL
$ch = curl_init('https://api.paystack.co/transaction/initialize');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
  "Authorization: Bearer sk_test_61bd9d6d05d2fdcf242fa895a1a87927973e34af", 
  "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For local testing only

$response = curl_exec($ch);
$err = curl_error($ch);
curl_close($ch);

if ($err) {
    die("Curl error: " . $err);
}

$result = json_decode($response, true);

// Debug log
file_put_contents("payment_log.txt", date("Y-m-d H:i:s") . " - " . $response . PHP_EOL, FILE_APPEND);

if ($result && $result['status']) {
    header('Location: ' . $result['data']['authorization_url']);
    exit();
} else {
    echo "<pre>";
    echo "Payment initialization failed: " . ($result['message'] ?? 'Unknown error') . "\n";
    print_r($result);
    echo "</pre>";
    exit();
}
?>
