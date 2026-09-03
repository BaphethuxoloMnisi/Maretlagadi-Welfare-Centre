<?php
require_once 'includes/db.php';

$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM announcement WHERE announcement_id = ?");
$stmt->execute([$id]);
$announcement = $stmt->fetch();

if (!$announcement) {
    http_response_code(404);
    $pageTitle = "Announcement Not Found";
    include 'includes/header.php';
    ?>
    <section class="py-5 text-center">
      <div class="container">
        <h1 class="fw-bold">Announcement Not Found</h1>
        <p class="text-secondary mt-2">This announcement may have been removed or the link is incorrect.</p>
        <a href="announcements.php" class="btn btn-brand rounded-pill px-4 mt-3">Back to Announcements</a>
      </div>
    </section>
    <?php
    include 'includes/footer.php';
    exit;
}

$pageTitle = $announcement['title'];

// Fetch 3 other recent announcements for a "More announcements" section
$stmt = $pdo->prepare(
    "SELECT announcement_id, title, image_path, created_at 
     FROM announcement 
     WHERE announcement_id != ? 
     ORDER BY created_at DESC LIMIT 3"
);
$stmt->execute([$id]);
$others = $stmt->fetchAll();

include 'includes/header.php';
?>

<article class="py-5">
  <div class="container" style="max-width:760px;">

    <a href="announcements.php" class="text-secondary small text-decoration-none">&larr; Back to Announcements</a>

    <div class="mt-3">
      <span class="badge bg-light text-dark border"><?php echo date('d M Y', strtotime($announcement['created_at'])); ?></span>
      <?php if ($announcement['updated_at'] !== $announcement['created_at']): ?>
        <span class="text-secondary small ms-2">Updated <?php echo date('d M Y', strtotime($announcement['updated_at'])); ?></span>
      <?php endif; ?>
    </div>

    <h1 class="fw-bold mt-3" style="line-height:1.2;"><?php echo htmlspecialchars($announcement['title']); ?></h1>

    <?php if ($announcement['image_path']): ?>
      <img src="<?php echo htmlspecialchars($announcement['image_path']); ?>"
           class="w-100 rounded-4 mt-4" style="max-height:420px; object-fit:cover;"
           alt="<?php echo htmlspecialchars($announcement['title']); ?>">
    <?php endif; ?>

    <div class="mt-4 fs-5 lh-lg" style="color:#333;">
      <?php echo nl2br(htmlspecialchars($announcement['content'])); ?>
    </div>

    <hr class="my-5">

    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
      <div class="text-secondary small">Maretlagadi Welfare Centre</div>
      <a href="announcements.php" class="btn btn-outline-dark rounded-pill px-4">Back to Announcements</a>
    </div>
  </div>

  <?php if (!empty($others)): ?>
  <div class="container mt-5" style="max-width:960px;">
    <h5 class="fw-bold mb-3">More Announcements</h5>
    <div class="row g-4">
      <?php foreach ($others as $o): ?>
      <div class="col-md-4">
        <a href="announcement.php?id=<?php echo $o['announcement_id']; ?>" class="text-decoration-none text-dark">
          <div class="programme-card h-100">
            <?php if ($o['image_path']): ?>
              <img src="<?php echo htmlspecialchars($o['image_path']); ?>" class="w-100" style="height:140px; object-fit:cover;">
            <?php else: ?>
              <div class="programme-img" style="height:140px;">MWC</div>
            <?php endif; ?>
            <div class="p-3">
              <div class="text-secondary small mb-1"><?php echo date('d M Y', strtotime($o['created_at'])); ?></div>
              <div class="fw-semibold small"><?php echo htmlspecialchars($o['title']); ?></div>
            </div>
          </div>
        </a>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>
</article>

<?php include 'includes/footer.php'; ?>