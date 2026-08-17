<?php
require_once 'includes/db.php';
$pageTitle = "Donation Successful";

$reference = $_GET['ref'] ?? '';

$stmt = $pdo->prepare(
    "SELECT d.amount, d.date, d.payment_ref, u.name, u.surname
     FROM donation d LEFT JOIN user u ON d.user_id = u.user_id
     WHERE d.payment_ref = ?"
);
$stmt->execute([$reference]);
$donation = $stmt->fetch();

include 'includes/header.php';
?>

<section class="py-5">
  <div class="container text-center" style="max-width:520px;">
    <?php if ($donation): ?>
      <div class="mb-3" style="font-size:3rem;">✅</div>
      <h1 class="fw-bold">Thank You<?php echo $donation['name'] ? ', ' . htmlspecialchars($donation['name']) : ''; ?>!</h1>
      <p class="text-secondary mt-3 fs-5">
        You have successfully donated <strong>R<?php echo number_format($donation['amount'], 2); ?></strong>
        to Maretlagadi Welfare Centre.
      </p>
      <div class="border rounded-4 p-3 mt-4 text-start">
        <div class="d-flex justify-content-between small text-secondary"><span>Reference</span><span><?php echo htmlspecialchars($donation['payment_ref']); ?></span></div>
        <div class="d-flex justify-content-between small text-secondary mt-1"><span>Date</span><span><?php echo date('d M Y, H:i', strtotime($donation['date'])); ?></span></div>
      </div>
      <p class="text-secondary small mt-3">A receipt has also been sent to you by Paystack.</p>
    <?php else: ?>
      <h1 class="fw-bold">We couldn't find that donation</h1>
      <p class="text-secondary mt-2">If you were charged, please contact us and we'll confirm it manually.</p>
    <?php endif; ?>
    <a href="index.php" class="btn btn-brand rounded-pill px-4 mt-4">Back to Home</a>
  </div>
</section>

<?php include 'includes/footer.php'; ?>