<?php
session_start();
require_once '../includes/db.php';
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }

$pageTitle = "Donations";
$activeNav = "donations";

$donations = $pdo->query(
    "SELECT d.donation_id, d.amount, d.date, d.payment_ref, u.name, u.surname, u.email
     FROM donation d LEFT JOIN user u ON d.user_id = u.user_id
     ORDER BY d.date DESC"
)->fetchAll();

include 'includes/layout_top.php';
?>

<div class="card stat-card">
  <div class="card-header bg-white fw-semibold">All Donations (<?php echo count($donations); ?>)</div>
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead class="table-light"><tr><th>Donor</th><th>Email</th><th>Amount</th><th>Reference</th><th>Date</th></tr></thead>
      <tbody>
        <?php foreach ($donations as $d): ?>
        <tr>
          <td><?php echo $d['name'] ? htmlspecialchars($d['name'] . ' ' . $d['surname']) : 'Anonymous'; ?></td>
          <td><?php echo htmlspecialchars($d['email'] ?: '-'); ?></td>
          <td>R<?php echo number_format($d['amount'], 2); ?></td>
          <td><?php echo htmlspecialchars($d['payment_ref']); ?></td>
          <td><?php echo date('d M Y, H:i', strtotime($d['date'])); ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($donations)): ?>
          <tr><td colspan="5" class="text-center text-secondary py-4">No donations recorded yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include '../includes/layout_bottom.php'; ?>