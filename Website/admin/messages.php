<?php
session_start();
require_once '../includes/db.php';
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }

$pageTitle = "Messages";
$activeNav = "messages";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form_action'] ?? '') === 'delete') {
    $stmt = $pdo->prepare("DELETE FROM message WHERE message_id = ?");
    $stmt->execute([(int) $_POST['message_id']]);
}

$messages = $pdo->query("SELECT * FROM message ORDER BY timestamp DESC")->fetchAll();

include 'includes/layout_top.php';
?>

<div class="card stat-card">
  <div class="card-header bg-white fw-semibold">All Messages (<?php echo count($messages); ?>)</div>
  <div class="list-group list-group-flush">
    <?php foreach ($messages as $m): ?>
      <div class="list-group-item d-flex justify-content-between align-items-start gap-3">
        <div>
          <div class="fw-semibold small"><?php echo htmlspecialchars($m['sender_name'] ?: 'Unknown'); ?> &lt;<?php echo htmlspecialchars($m['sender_email'] ?: '-'); ?>&gt;</div>
          <div class="text-secondary small mt-1"><?php echo nl2br(htmlspecialchars($m['message_text'])); ?></div>
          <div class="text-secondary small mt-1"><?php echo date('d M Y, H:i', strtotime($m['timestamp'])); ?></div>
        </div>
        <form method="POST" onsubmit="return confirm('Delete this message?');">
          <input type="hidden" name="form_action" value="delete">
          <input type="hidden" name="message_id" value="<?php echo $m['message_id']; ?>">
          <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
        </form>
      </div>
    <?php endforeach; ?>
    <?php if (empty($messages)): ?>
      <div class="list-group-item text-center text-secondary py-4">No messages yet.</div>
    <?php endif; ?>
  </div>
</div>

<?php include '../includes/layout_bottom.php'; ?>