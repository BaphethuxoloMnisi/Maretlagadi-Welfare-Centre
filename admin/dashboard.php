<?php
session_start();
require_once '../includes/db.php';
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }

$pageTitle = "Dashboard";
$activeNav = "dashboard";

$volunteerCount = $pdo->query("SELECT COUNT(*) FROM volunteer")->fetchColumn();
$programCount = $pdo->query("SELECT COUNT(*) FROM program")->fetchColumn();
$donationTotal = $pdo->query("SELECT COALESCE(SUM(amount),0) FROM donation")->fetchColumn();
$messageCount = $pdo->query("SELECT COUNT(*) FROM message")->fetchColumn();
$announcementCount = $pdo->query("SELECT COUNT(*) FROM announcement")->fetchColumn();

$volunteers = $pdo->query(
    "SELECT u.name, u.surname, u.email, v.skills, v.availability, v.total_hours 
     FROM volunteer v JOIN user u ON v.user_id = u.user_id 
     ORDER BY v.volunteer_id DESC LIMIT 5"
)->fetchAll();

$recentAnnouncements = $pdo->query(
    "SELECT * FROM announcement ORDER BY created_at DESC LIMIT 3"
)->fetchAll();

include 'includes/layout_top.php';
?>

<div class="row g-3 mb-4">
  <div class="col-md-3 col-6">
    <div class="card stat-card p-3 text-center"><div class="stat-number"><?php echo $volunteerCount; ?></div><div class="small text-secondary">Volunteers</div></div>
  </div>
  <div class="col-md-3 col-6">
    <div class="card stat-card p-3 text-center"><div class="stat-number"><?php echo $programCount; ?></div><div class="small text-secondary">Programmes</div></div>
  </div>
  <div class="col-md-3 col-6">
    <div class="card stat-card p-3 text-center"><div class="stat-number">R<?php echo number_format($donationTotal, 2); ?></div><div class="small text-secondary">Donations</div></div>
  </div>
  <div class="col-md-3 col-6">
    <div class="card stat-card p-3 text-center"><div class="stat-number"><?php echo $messageCount; ?></div><div class="small text-secondary">Messages</div></div>
  </div>
</div>

<div class="row g-4">
  <div class="col-lg-7">
    <div class="card stat-card">
      <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
        Recent Volunteers <a href="volunteers.php" class="small">View all &rarr;</a>
      </div>
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="table-light"><tr><th>Name</th><th>Email</th><th>Skills</th><th>Availability</th><th>Hours</th></tr></thead>
          <tbody>
            <?php foreach ($volunteers as $v): ?>
            <tr>
              <td><?php echo htmlspecialchars($v['name'] . ' ' . $v['surname']); ?></td>
              <td><?php echo htmlspecialchars($v['email']); ?></td>
              <td><?php echo htmlspecialchars($v['skills']); ?></td>
              <td><?php echo htmlspecialchars($v['availability']); ?></td>
              <td><?php echo $v['total_hours']; ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($volunteers)): ?>
              <tr><td colspan="5" class="text-center text-secondary py-4">No volunteers registered yet.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-lg-5">
    <div class="card stat-card">
      <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
        Announcements (<?php echo $announcementCount; ?>) <a href="announcements.php" class="small">Manage &rarr;</a>
      </div>
      <div class="list-group list-group-flush">
        <?php foreach ($recentAnnouncements as $a): ?>
          <div class="list-group-item d-flex gap-3 align-items-center">
            <?php if ($a['image_path']): ?>
              <img src="../<?php echo htmlspecialchars($a['image_path']); ?>" class="announcement-thumb">
            <?php else: ?>
              <div class="announcement-thumb d-flex align-items-center justify-content-center text-secondary" style="font-size:.6rem;">No image</div>
            <?php endif; ?>
            <div>
              <div class="fw-semibold small"><?php echo htmlspecialchars($a['title']); ?></div>
              <div class="text-secondary small"><?php echo date('d M Y', strtotime($a['created_at'])); ?></div>
            </div>
          </div>
        <?php endforeach; ?>
        <?php if (empty($recentAnnouncements)): ?>
          <div class="list-group-item text-center text-secondary py-4">No announcements posted yet.</div>
        <?php endif; ?>
      </div>
      <div class="card-footer bg-white text-center">
        <a href="announcements.php" class="btn btn-sm btn-brand rounded-pill px-3">+ Post Announcement</a>
      </div>
    </div>
  </div>
</div>

<?php include '../includes/layout_bottom.php'; ?>