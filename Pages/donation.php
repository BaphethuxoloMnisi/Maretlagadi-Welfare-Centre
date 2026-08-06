<?php
include '../navigation-bar/navbar.php';
include '../database/create_database.php'; // Database connection

// ===== PayFast Config =====
// TODO: replace with your live PayFast merchant details before going live.
$pf_testing_mode = true; // set to false in production

$pf_merchant_id  = $pf_testing_mode ? '10000100' : 'YOUR_MERCHANT_ID';
$pf_merchant_key = $pf_testing_mode ? '46f0cd694581a' : 'YOUR_MERCHANT_KEY';
$pf_passphrase   = $pf_testing_mode ? '' : 'YOUR_PASSPHRASE'; // optional but recommended in production

$pf_process_url  = $pf_testing_mode
    ? 'https://sandbox.payfast.co.za/eng/process'
    : 'https://www.payfast.co.za/eng/process';

// These must be full, publicly reachable URLs on your live site
$pf_return_url  = 'https://YOURDOMAIN/pages/donation_return.php';
$pf_cancel_url  = 'https://YOURDOMAIN/pages/donation.php';
$pf_notify_url  = 'https://YOURDOMAIN/pages/donation_itn.php';

$errors = [];
$pf_fields = null; // populated on success, used to auto-submit to PayFast

function pf_generate_signature($data, $passPhrase = null)
{
    $pfOutput = '';
    foreach ($data as $key => $val) {
        if ($val !== '') {
            $pfOutput .= $key . '=' . urlencode(trim($val)) . '&';
        }
    }
    $pfOutput = substr($pfOutput, 0, -1);
    if ($passPhrase !== null && $passPhrase !== '') {
        $pfOutput .= '&passphrase=' . urlencode(trim($passPhrase));
    }
    return md5($pfOutput);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $full_name = trim($_POST['full_name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $amount    = trim($_POST['amount'] ?? '');

    if ($full_name === '') $errors[] = 'Please enter your full name.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email address.';
    if (!is_numeric($amount) || (float)$amount <= 0) $errors[] = 'Please enter a valid donation amount.';

    if (empty($errors)) {

        $amount = number_format((float)$amount, 2, '.', '');
        $m_payment_id = uniqid('DON-');

        // Log a pending donation record so the admin dashboard can track it
        $stmt = mysqli_prepare($conn, "INSERT INTO donations (full_name, email, phone, amount, payment_status, m_payment_id, created_at) VALUES (?, ?, ?, ?, 'pending', ?, NOW())");
        mysqli_stmt_bind_param($stmt, 'sssds', $full_name, $email, $phone, $amount, $m_payment_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $pf_data = [
            'merchant_id'  => $pf_merchant_id,
            'merchant_key' => $pf_merchant_key,
            'return_url'   => $pf_return_url,
            'cancel_url'   => $pf_cancel_url,
            'notify_url'   => $pf_notify_url,
            'name_first'   => $full_name,
            'email_address'=> $email,
            'm_payment_id' => $m_payment_id,
            'amount'       => $amount,
            'item_name'    => 'Donation to Maretlagadi Welfare Centre',
        ];

        $pf_data['signature'] = pf_generate_signature($pf_data, $pf_passphrase);
        $pf_fields = $pf_data;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donate | Maretlagadi Welfare Centre</title>

    <style>
        .donate-hero {
            background-image: url('../assets/images/donate-hero-bg.jpg');
            background-size: cover;
            background-position: center;
            min-height: 40vh;
            display: flex;
            align-items: center;
        }

        .donate-overlay {
            width: 100%;
            background: rgba(0, 0, 0, 0.6);
            padding: 60px 0;
            color: #fff;
            text-align: center;
        }

        .donate-overlay h1 {
            font-size: 2.4rem;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .donate-overlay p {
            max-width: 600px;
            margin: 0 auto;
            font-size: 1.1rem;
            color: #f1f1f1;
        }

        .donate-card {
            border: none;
            border-radius: 14px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.08);
        }

        .amount-options {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 15px;
        }

        .amount-btn {
            flex: 1 1 100px;
            border: 2px solid #198754;
            color: #198754;
            background: #fff;
            border-radius: 8px;
            padding: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .amount-btn:hover,
        .amount-btn.active {
            background-color: #198754;
            color: #fff;
        }

        .donate-info-card {
            border: none;
            border-radius: 14px;
            background-color: #f8f9fa;
        }

        .donate-info-card h4 {
            color: #198754;
            font-weight: 700;
        }

        .btn-donate {
            border-radius: 30px;
            padding: 12px;
            font-weight: 600;
            font-size: 1.05rem;
        }
    </style>
</head>

<body>

<section class="donate-hero">
    <div class="donate-overlay">
        <div class="container">
            <h1>Support Our Cause</h1>
            <p>
                Every contribution helps us provide welfare services, education
                and support to families and individuals in need.
            </p>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">

        <div class="row justify-content-center">

            <div class="col-lg-7 mb-4">
                <div class="card donate-card">
                    <div class="card-body p-4">

                        <h3 class="text-success mb-4">Make a Donation</h3>

                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <?php if ($pf_fields): ?>

                            <div class="alert alert-success">
                                Thank you! Redirecting you to our secure payment partner, PayFast...
                            </div>

                            <form id="payfast-form" action="<?php echo htmlspecialchars($pf_process_url); ?>" method="post">
                                <?php foreach ($pf_fields as $key => $value): ?>
                                    <input type="hidden" name="<?php echo htmlspecialchars($key); ?>" value="<?php echo htmlspecialchars($value); ?>">
                                <?php endforeach; ?>
                            </form>

                            <script>
                                document.getElementById('payfast-form').submit();
                            </script>

                        <?php else: ?>

                            <form method="POST" action="donation.php" id="donation-form">

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Choose an amount (ZAR)</label>
                                    <div class="amount-options">
                                        <button type="button" class="amount-btn" data-amount="100">R100</button>
                                        <button type="button" class="amount-btn" data-amount="250">R250</button>
                                        <button type="button" class="amount-btn" data-amount="500">R500</button>
                                        <button type="button" class="amount-btn" data-amount="1000">R1000</button>
                                    </div>
                                    <input type="number" step="0.01" min="1" class="form-control" id="amount" name="amount" placeholder="Or enter a custom amount" required
                                        value="<?php echo isset($_POST['amount']) ? htmlspecialchars($_POST['amount']) : ''; ?>">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Full Name</label>
                                    <input type="text" class="form-control" name="full_name" required
                                        value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Email Address</label>
                                    <input type="email" class="form-control" name="email" required
                                        value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Phone Number (optional)</label>
                                    <input type="text" class="form-control" name="phone"
                                        value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                                </div>

                                <button type="submit" class="btn btn-success btn-donate w-100">
                                    Proceed to Secure Payment
                                </button>

                                <p class="text-muted text-center mt-3 mb-0" style="font-size: 0.85rem;">
                                    You will be redirected to PayFast to complete your donation securely.
                                </p>

                            </form>

                        <?php endif; ?>

                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="card donate-info-card h-100">
                    <div class="card-body p-4">
                        <h4>Why Your Donation Matters</h4>
                        <p>
                            Your generosity directly funds education support,
                            skills development and welfare programmes for
                            vulnerable families in our community.
                        </p>
                        <hr>
                        <h4>Secure Payments</h4>
                        <p class="mb-0">
                            All donations are processed securely through PayFast,
                            South Africa's trusted payment gateway.
                        </p>
                    </div>
                </div>
            </div>

        </div>

    </div>
</section>

<script>
    const amountButtons = document.querySelectorAll('.amount-btn');
    const amountInput = document.getElementById('amount');

    amountButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            amountButtons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            amountInput.value = btn.dataset.amount;
        });
    });
</script>

<?php include '../footer/footer.php'; ?>

</body>
</html>