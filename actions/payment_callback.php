<?php
require '../config.php';

// Paystack secret key
$secret_key = 'sk_test_61bd9d6d05d2fdcf242fa895a1a87927973e34af';

// Ensure a reference was passed
if (!isset($_GET['reference'])) {
    die("No transaction reference supplied.");
}

$reference = $_GET['reference'];

// Initialize cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.paystack.co/transaction/verify/" . urlencode($reference));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $secret_key",
    "Cache-Control: no-cache"
]);

$response = curl_exec($ch);
$curl_error = curl_error($ch);
curl_close($ch);

// Decode the response
$result = json_decode($response, true);

// Log the full response for debugging
file_put_contents('callback_debug.txt', "Reference: $reference\nCurl Error: $curl_error\nResponse:\n" . print_r($result, true));

// If cURL failed
if ($curl_error) {
    die("Curl error: $curl_error");
}

// If response is malformed
if (!$result || !isset($result['status'])) {
    die("Invalid Paystack response:\n" . print_r($response, true));
}

// If transaction succeeded
if ($result['status'] && isset($result['data']['status']) && $result['data']['status'] === 'success') {
    $user_id = $result['data']['metadata']['user_id'] ?? null;

    if ($user_id) {
        $stmt = $pdo->prepare("UPDATE users SET has_paid = 1 WHERE id = ?");
        $stmt->execute([$user_id]);

        header("Location: ../apply.php");
        exit;
    } else {
        die("User ID missing from metadata.");
    }
} else {
    $error_message = $result['message'] ?? 'Unknown error';
    die("Payment failed or not verified. Reason: $error_message");
}
