<?php
session_start();
require_once '../includes/db.php';
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }

$pageTitle = "Volunteers";
$activeNav = "volunteers";
$success = "";

// DELETE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form_action'] ?? '') === 'delete') {
    $volunteerId = (int) ($_POST['volunteer_id'] ?? 0);

    // Deleting from `volunteer` removes their volunteer record and any linked
    // volunteer_shift rows (ON DELETE CASCADE in the schema). The base `user`
    // record is kept, so their message/donation history isn't orphaned.
    $stmt = $pdo->prepare("DELETE FROM volunteer WHERE volunteer_id = ?");
    $stmt->execute([$volunteerId]);
    $success = "Volunteer removed.";
}

$volunteers = $pdo->query(
    "SELECT v.volunteer_id, u.name, u.surname, u.email, u.phone, v.skills, v.availability, v.total_hours 
     FROM volunteer v JOIN user u ON v.user_id = u.user_id 
     ORDER BY v.volunteer_id DESC"
)->fetchAll();

include 'includes/layout_top.php';
?>

<?php if ($success): ?><div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>

<div class="card stat-card">
  <div class="card-header bg-white fw-semibold">All Volunteers (<?php echo count($volunteers); ?>)</div>
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead class="table-light"><tr><th>Name</th><th>Email</th><th>Phone</th><th>Skills</th><th>Availability</th><th>Hours</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($volunteers as $v): ?>
        <tr>
          <td><?php echo htmlspecialchars($v['name'] . ' ' . $v['surname']); ?></td>
          <td><?php echo htmlspecialchars($v['email']); ?></td>
          <td><?php echo htmlspecialchars($v['phone'] ?: '-'); ?></td>
          <td><?php echo htmlspecialchars($v['skills']); ?></td>
          <td><?php echo htmlspecialchars($v['availability']); ?></td>
          <td><?php echo $v['total_hours']; ?></td>
          <td class="text-end">
            <form method="POST" onsubmit="return confirm('Remove <?php echo htmlspecialchars(addslashes($v['name'] . ' ' . $v['surname'])); ?> from the volunteer programme?');">
              <input type="hidden" name="form_action" value="delete">
              <input type="hidden" name="volunteer_id" value="<?php echo $v['volunteer_id']; ?>">
              <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($volunteers)): ?>
          <tr><td colspan="7" class="text-center text-secondary py-4">No volunteers registered yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include '../includes/layout_bottom.php'; ?>