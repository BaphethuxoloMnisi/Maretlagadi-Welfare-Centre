
<?php

// ============================================================
// MARETLAGADI WELFARE CENTRE
// PAYSTACK DONATION VERIFICATION
// ============================================================

header('Content-Type: application/json; charset=utf-8');

ini_set('display_errors', '0');
error_reporting(E_ALL);

// ============================================================
// HELPER FUNCTION - RETURN JSON
// ============================================================

function sendResponse(array $data): void
{
    echo json_encode($data, JSON_UNESCAPED_SLASHES);
    exit;
}

// ============================================================
// LOAD DATABASE + PAYSTACK CONFIG
// ============================================================

try {

    require_once __DIR__ . '/includes/db.php';
    require_once __DIR__ . '/includes/paystack_config.php';

} catch (Throwable $e) {

    error_log('Configuration error: ' . $e->getMessage());

    sendResponse([
        'success' => false,
        'message' => 'Server configuration error.'
    ]);
}

// ============================================================
// CHECK DATABASE CONNECTION
// ============================================================

if (!isset($pdo) || !($pdo instanceof PDO)) {

    error_log('PDO database connection is not available.');

    sendResponse([
        'success' => false,
        'message' => 'Database connection is not available.'
    ]);
}

// ============================================================
// READ JSON REQUEST
// ============================================================

$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);

// ============================================================
// VALIDATE JSON REQUEST
// ============================================================

if (!is_array($input)) {

    error_log('Invalid JSON received: ' . $rawInput);

    sendResponse([
        'success' => false,
        'message' => 'Invalid request received.'
    ]);
}

// ============================================================
// GET PAYMENT INFORMATION
// ============================================================

$reference = trim((string) ($input['reference'] ?? ''));
$name = trim((string) ($input['name'] ?? ''));
$email = trim((string) ($input['email'] ?? ''));
$claimedAmount = (float) ($input['amount'] ?? 0);

// ============================================================
// VALIDATE REFERENCE
// ============================================================

if ($reference === '') {

    sendResponse([
        'success' => false,
        'message' => 'Payment reference is missing.'
    ]);
}

// ============================================================
// VALIDATE EMAIL
// ============================================================

if (
    $email === '' ||
    !filter_var($email, FILTER_VALIDATE_EMAIL)
) {

    sendResponse([
        'success' => false,
        'message' => 'A valid donor email address is required.'
    ]);
}

// ============================================================
// VALIDATE AMOUNT
// ============================================================

if ($claimedAmount <= 0) {

    sendResponse([
        'success' => false,
        'message' => 'Invalid donation amount.'
    ]);
}

// ============================================================
// CHECK PAYSTACK SECRET KEY
// ============================================================

if (
    !defined('PAYSTACK_SECRET_KEY') ||
    trim(PAYSTACK_SECRET_KEY) === ''
) {

    error_log('PAYSTACK_SECRET_KEY is missing.');

    sendResponse([
        'success' => false,
        'message' => 'Paystack configuration is missing.'
    ]);
}

// ============================================================
// PAYSTACK VERIFY URL
// ============================================================

$url =
    'https://api.paystack.co/transaction/verify/' .
    rawurlencode($reference);

// ============================================================
// INITIALISE CURL
// ============================================================

$ch = curl_init();

if ($ch === false) {

    error_log('Unable to initialise cURL.');

    sendResponse([
        'success' => false,
        'message' => 'Payment verification service is unavailable.'
    ]);
}

// ============================================================
// CURL SETTINGS
// ============================================================

curl_setopt_array($ch, [

    CURLOPT_URL => $url,

    CURLOPT_RETURNTRANSFER => true,

    CURLOPT_FOLLOWLOCATION => true,

    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . trim(PAYSTACK_SECRET_KEY),
        'Accept: application/json',
        'Content-Type: application/json'
    ],

    CURLOPT_CONNECTTIMEOUT => 15,

    CURLOPT_TIMEOUT => 30,

    // LOCAL WAMP DEVELOPMENT FIX
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => 0,

    CURLOPT_USERAGENT => 'Maretlagadi-Welfare-Centre/1.0'
]);

// ============================================================
// SEND REQUEST TO PAYSTACK
// ============================================================

$response = curl_exec($ch);

// ============================================================
// GET CURL INFORMATION
// ============================================================

$curlError = curl_error($ch);
$curlErrno = curl_errno($ch);

$httpCode = curl_getinfo(
    $ch,
    CURLINFO_HTTP_CODE
);

curl_close($ch);

// ============================================================
// LOG PAYSTACK VERIFICATION INFORMATION
// ============================================================

error_log('----------------------------------------');
error_log('Paystack verification started');
error_log('Reference: ' . $reference);
error_log('HTTP status: ' . $httpCode);
error_log('cURL error number: ' . $curlErrno);
error_log('cURL error: ' . $curlError);
error_log('Paystack response: ' . $response);
error_log('----------------------------------------');

// ============================================================
// CHECK CURL CONNECTION ERROR
// ============================================================

if ($response === false || $curlError !== '') {

    sendResponse([
        'success' => false,
        'message' => 'Paystack connection failed.',
        'curl_errno' => $curlErrno,
        'curl_error' => $curlError,
        'http_code' => $httpCode
    ]);
}

// ============================================================
// CHECK HTTP RESPONSE
// ============================================================

if ($httpCode < 200 || $httpCode >= 300) {

    sendResponse([
        'success' => false,
        'message' => 'Paystack verification failed.',
        'http_code' => $httpCode
    ]);
}

// ============================================================
// DECODE PAYSTACK RESPONSE
// ============================================================

$result = json_decode($response, true);

if (!is_array($result)) {

    error_log('Paystack returned invalid JSON.');

    sendResponse([
        'success' => false,
        'message' => 'Invalid response received from Paystack.'
    ]);
}

// ============================================================
// CHECK PAYSTACK API STATUS
// ============================================================

if (
    !isset($result['status']) ||
    $result['status'] !== true
) {

    error_log('Paystack API status was unsuccessful.');

    error_log(
        'Paystack message: ' .
        ($result['message'] ?? 'Unknown error')
    );

    sendResponse([
        'success' => false,
        'message' =>
            $result['message'] ??
            'Paystack could not verify the payment.'
    ]);
}

// ============================================================
// GET TRANSACTION DATA
// ============================================================

$transaction = $result['data'] ?? null;

if (!is_array($transaction)) {

    sendResponse([
        'success' => false,
        'message' =>
            'Transaction information was not returned by Paystack.'
    ]);
}

// ============================================================
// VERIFY PAYMENT STATUS
// ============================================================

$paymentStatus = $transaction['status'] ?? '';

if ($paymentStatus !== 'success') {

    error_log(
        'Paystack transaction status: ' .
        $paymentStatus
    );

    sendResponse([
        'success' => false,
        'message' =>
            'Payment has not been completed successfully.'
    ]);
}

// ============================================================
// VERIFY PAYMENT REFERENCE
// ============================================================

$paystackReference =
    $transaction['reference'] ?? '';

if ($paystackReference !== $reference) {

    error_log('Reference mismatch.');

    sendResponse([
        'success' => false,
        'message' =>
            'Payment reference could not be verified.'
    ]);
}

// ============================================================
// GET VERIFIED AMOUNT
// ============================================================

// Paystack returns amounts in the smallest currency unit.
// Example:
// R100 = 10000
// R250 = 25000

$verifiedAmount =
    ((float) ($transaction['amount'] ?? 0)) / 100;

// ============================================================
// VERIFY PAYMENT AMOUNT
// ============================================================

if (
    abs($verifiedAmount - $claimedAmount) > 0.01
) {

    error_log('PAYMENT AMOUNT MISMATCH');

    error_log(
        'Expected amount: R' .
        $claimedAmount
    );

    error_log(
        'Paystack amount: R' .
        $verifiedAmount
    );

    sendResponse([
        'success' => false,
        'message' =>
            'The payment amount could not be verified.'
    ]);
}

// ============================================================
// VERIFY CURRENCY
// ============================================================

$currency = strtoupper(
    $transaction['currency'] ?? ''
);

if ($currency !== 'ZAR') {

    error_log(
        'Unexpected Paystack currency: ' .
        $currency
    );

    sendResponse([
        'success' => false,
        'message' =>
            'The payment currency could not be verified.'
    ]);
}

// ============================================================
// GET VERIFIED DONOR EMAIL
// ============================================================

$verifiedEmail =
    $transaction['customer']['email']
    ?? $email;

$verifiedEmail = trim($verifiedEmail);

// ============================================================
// VALIDATE VERIFIED EMAIL
// ============================================================

if (
    $verifiedEmail === '' ||
    !filter_var(
        $verifiedEmail,
        FILTER_VALIDATE_EMAIL
    )
) {

    sendResponse([
        'success' => false,
        'message' =>
            'A valid donor email could not be verified.'
    ]);
}

// ============================================================
// CHECK WHETHER DONATION ALREADY EXISTS
// ============================================================

try {

    $stmt = $pdo->prepare(
        "SELECT donation_id
         FROM donation
         WHERE payment_ref = ?
         LIMIT 1"
    );

    $stmt->execute([
        $reference
    ]);

    $existingDonation =
        $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingDonation) {

        sendResponse([
            'success' => true,
            'message' =>
                'Donation already recorded.',
            'reference' =>
                $reference
        ]);
    }

} catch (Throwable $e) {

    error_log(
        'Donation duplicate check error: ' .
        $e->getMessage()
    );

    sendResponse([
        'success' => false,
        'message' =>
            'Unable to check the donation record.'
    ]);
}

// ============================================================
// START DATABASE TRANSACTION
// ============================================================

try {

    $pdo->beginTransaction();

    // ========================================================
    // FIND EXISTING USER BY EMAIL
    // ========================================================

    $userId = null;

    $stmt = $pdo->prepare(
        "SELECT user_id
         FROM user
         WHERE email = ?
         LIMIT 1"
    );

    $stmt->execute([
        $verifiedEmail
    ]);

    $existingUser =
        $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingUser) {

        $userId =
            (int) $existingUser['user_id'];
    }

    // ========================================================
    // CREATE PUBLIC DONOR IF USER DOES NOT EXIST
    // ========================================================

    if ($userId === null) {

        // ----------------------------------------------------
        // DONOR NAME
        // ----------------------------------------------------

        $donorName =
            $name !== ''
            ? $name
            : 'Anonymous';

        // ----------------------------------------------------
        // DEFAULT SURNAME
        // ----------------------------------------------------

        $donorSurname = 'Donor';

        // ----------------------------------------------------
        // SPLIT NAME AND SURNAME
        // ----------------------------------------------------

        $nameParts = preg_split(
            '/\s+/',
            $donorName
        );

        if (
            is_array($nameParts) &&
            count($nameParts) >= 2
        ) {

            $donorSurname =
                array_pop($nameParts);

            $donorName =
                implode(
                    ' ',
                    $nameParts
                );
        }

        // ----------------------------------------------------
        // GENERATE RANDOM PASSWORD
        // ----------------------------------------------------

        $randomPassword =
            password_hash(
                bin2hex(
                    random_bytes(16)
                ),
                PASSWORD_DEFAULT
            );

        // ----------------------------------------------------
        // INSERT PUBLIC USER
        // ----------------------------------------------------

        $stmt = $pdo->prepare(
            "INSERT INTO user
            (
                name,
                surname,
                email,
                password,
                role
            )
            VALUES
            (
                ?,
                ?,
                ?,
                ?,
                'public'
            )"
        );

        $stmt->execute([
            $donorName,
            $donorSurname,
            $verifiedEmail,
            $randomPassword
        ]);

        // ----------------------------------------------------
        // GET NEW USER ID
        // ----------------------------------------------------

        $userId =
            (int) $pdo->lastInsertId();
    }

    // ========================================================
    // INSERT DONATION
    // ========================================================

    $stmt = $pdo->prepare(
        "INSERT INTO donation
        (
            user_id,
            amount,
            payment_ref
        )
        VALUES
        (
            ?,
            ?,
            ?
        )"
    );

    $stmt->execute([
        $userId,
        $verifiedAmount,
        $reference
    ]);

    // ========================================================
    // COMMIT DATABASE TRANSACTION
    // ========================================================

    $pdo->commit();

    // ========================================================
    // SUCCESS
    // ========================================================

    error_log(
        'Donation successfully recorded.'
    );

    error_log(
        'Donation reference: ' .
        $reference
    );

    error_log(
        'Donation amount: R' .
        $verifiedAmount
    );

    sendResponse([
        'success' => true,
        'message' =>
            'Donation verified and recorded successfully.',
        'reference' =>
            $reference,
        'amount' =>
            $verifiedAmount
    ]);

} catch (Throwable $e) {

    // ========================================================
    // ROLLBACK DATABASE TRANSACTION
    // ========================================================

    if (
        isset($pdo) &&
        $pdo->inTransaction()
    ) {

        $pdo->rollBack();
    }

    // ========================================================
    // LOG DATABASE ERROR
    // ========================================================

    error_log(
        'Donation database error: ' .
        $e->getMessage()
    );

    error_log(
        'Donation database error file: ' .
        $e->getFile()
    );

    error_log(
        'Donation database error line: ' .
        $e->getLine()
    );

    // ========================================================
    // RETURN SAFE RESPONSE
    // ========================================================

    sendResponse([
        'success' => false,
        'message' =>
            'Payment was successful, but we could not save the donation.'
    ]);
}

