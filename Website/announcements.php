<?php
include 'includes/db.php';

$pageTitle = "Announcements";

$announcements = $pdo->query("SELECT * FROM announcement")->fetchAll();

include 'includes/header.php';
?>

<section class="py-5">
  <div class="container">
    <h1 class="fw-bold text-center">Announcements</h1>
    <p class="text-secondary text-center mx-auto mt-2" style="max-width:600px;">
      Stay up to date with the latest news, updates, and events from Maretlagadi Welfare Centre.
    </p>

    <div class="row g-4 mt-4">
      <?php foreach ($announcements as $a): ?>
      <div class="col-md-6 col-lg-4">
        <a href="announcement.php?id=<?php echo $a['announcement_id']; ?> - announcements.php:21" class="text-decoration-none text-dark">
          <div class="programme-card h-100">
            <?php if ($a['image_path']): ?>
              <img src="<?php echo htmlspecialchars($a['image_path']); ?> - announcements.php:24" class="w-100" style="height:180px; object-fit:cover;" alt="<?php echo htmlspecialchars($a['title']); ?>">
            <?php else: ?>
              <div class="programme-img">MWC</div>
            <?php endif; ?>
            <div class="p-3">
              <div class="textsecondary small mb1 - announcements.php:29"><?php echo date('d M Y', strtotime($a['created_at'])); ?></div>
              <h5 class="fwsemibold - announcements.php:30"><?php echo htmlspecialchars($a['title']); ?></h5>
              <p class="textsecondary small mb0 - announcements.php:31"><?php echo htmlspecialchars(mb_strimwidth($a['content'], 0, 100, '...')); ?></p>
              <span class="small fw-semibold text-success mt-2 d-inline-block">Read more &rarr;</span>
            </div>
          </div>
        </a>
      </div>
      <?php endforeach; ?>

      <?php if (empty($announcements)): ?>
        <div class="col-12 text-center text-secondary py-5">
          No announcements yet — check back soon!
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>