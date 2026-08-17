<?php
require_once 'includes/db.php';
require_once 'includes/paystack_config.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$reference = $input['reference'] ?? '';
$name = trim($input['name'] ?? '');
$email = trim($input['email'] ?? '');
$claimedAmount = (float) ($input['amount'] ?? 0);

if ($reference === '') {
    echo json_encode(['success' => false, 'message' => 'Missing payment reference.']);
    exit;
}

// Verify the transaction directly with Paystack - never trust the amount sent from the browser
$ch = curl_init("https://api.paystack.co/transaction/verify/" . rawurlencode($reference));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer " . PAYSTACK_SECRET_KEY,
    "Cache-Control: no-cache"
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    echo json_encode(['success' => false, 'message' => 'Could not reach payment verification service.']);
    exit;
}

$result = json_decode($response, true);

if (!$result || !$result['status'] || $result['data']['status'] !== 'success') {
    echo json_encode(['success' => false, 'message' => 'Payment was not successful.']);
    exit;
}

$verifiedAmount = $result['data']['amount'] / 100; // Paystack returns cents
$verifiedEmail = $result['data']['customer']['email'];

// Guard: check this reference hasn't already been saved (prevents duplicate saves on refresh/replay)
$stmt = $pdo->prepare("SELECT donation_id FROM donation WHERE payment_ref = ?");
$stmt->execute([$reference]);
if ($stmt->fetch()) {
    echo json_encode(['success' => true, 'message' => 'Already recorded.']); // idempotent - not an error
    exit;
}

try {
    $pdo->beginTransaction();

    $userId = null;
    if ($verifiedEmail) {
        $stmt = $pdo->prepare("SELECT user_id FROM user WHERE email = ?");
        $stmt->execute([$verifiedEmail]);
        $existing = $stmt->fetch();

        if ($existing) {
            $userId = $existing['user_id'];
        } else {
            $parts = explode(' ', $name !== '' ? $name : 'Anonymous Donor', 2);
            $firstName = $parts[0];
            $surname = $parts[1] ?? '';
            $tempPassword = password_hash(bin2hex(random_bytes(4)), PASSWORD_DEFAULT);

            $stmt = $pdo->prepare(
                "INSERT INTO user (name, surname, email, password, role) VALUES (?, ?, ?, ?, 'public')"
            );
            $stmt->execute([$firstName, $surname, $verifiedEmail, $tempPassword]);
            $userId = $pdo->lastInsertId();
        }
    }

    // Use the AMOUNT PAYSTACK CONFIRMED, never the client-supplied one
    $stmt = $pdo->prepare(
        "INSERT INTO donation (user_id, amount, payment_ref) VALUES (?, ?, ?)"
    );
    $stmt->execute([$userId, $verifiedAmount, $reference]);

    $pdo->commit();
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    $pdo->rollBack();
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Payment verified but saving failed. Please contact us.']);
}