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

    <!-- Maretlagadi Android App banner -->
    <div class="app-soon-card rounded-4 p-4 p-md-5 mt-4 text-center text-md-start d-md-flex align-items-center justify-content-between gap-3">
      <div>
        <h3 class="fw-bold text-white mb-1">Maretlagadi Mobile App</h3>
        <p class="text-white-50 mb-0">Try the app demo or download the Android app.</p>
      </div>

      <div class="d-flex flex-wrap justify-content-center justify-content-md-end gap-2 mt-3 mt-md-0">
        <button
          type="button"
          class="btn btn-brand rounded-pill px-4 py-2 fw-semibold"
          data-bs-toggle="modal"
          data-bs-target="#appDemoModal"
        >
          Try Demo
        </button>

        <a href="https://github.com/BaphethuxoloMnisi/Maretlagadi-Welfare-Centre/releases/download/v1.0.0/Maretlagadi-Welfare-Centre.apk"
           download
           target="_blank"
           rel="noopener"
           class="btn btn-light rounded-pill px-4 py-2 fw-semibold">
          Download Android App
        </a>
      </div>
    </div>

    <div class="row g-4 mt-2">
      <?php foreach ($announcements as $a): ?>
      <div class="col-md-6 col-lg-4">
        <a href="announcement.php?id=<?php echo $a['announcement_id']; ?>" class="text-decoration-none text-dark">
          <div class="programme-card h-100">
            <?php if ($a['image_path']): ?>
              <img src="<?php echo htmlspecialchars($a['image_path']); ?>" class="w-100" style="height:180px; object-fit:cover;" alt="<?php echo htmlspecialchars($a['title']); ?>">
            <?php else: ?>
              <div class="programme-img">MWC</div>
            <?php endif; ?>
            <div class="p-3">
              <div class="text-secondary small mb-1"><?php echo date('d M Y', strtotime($a['created_at'])); ?></div>
              <h5 class="fw-semibold"><?php echo htmlspecialchars($a['title']); ?></h5>
              <p class="text-secondary small mb-0"><?php echo htmlspecialchars(mb_strimwidth($a['content'], 0, 100, '...')); ?></p>
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

<!-- App demo video modal -->
<div class="modal fade" id="appDemoModal" tabindex="-1" aria-labelledby="appDemoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered app-demo-dialog">
    <div class="modal-content">
      <div class="modal-header py-2">
        <h6 class="modal-title fw-semibold mb-0" id="appDemoModalLabel">Maretlagadi App — Demo Preview</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-2">
        <video id="appDemoVideo" class="w-100 d-block rounded" controls playsinline>
          <source src="videos/app-demo.mp4" type="video/mp4">
          Your browser does not support the video tag.
        </video>
      </div>
    </div>
  </div>
</div>

<style>
  .app-soon-card { background: var(--mwc-navy); }

  /* Keep the demo video modest in size instead of filling the screen */
  .app-demo-dialog { max-width: 420px; }
  .app-demo-dialog video { max-height: 70vh; }

  @media (max-width: 480px) {
    .app-demo-dialog { max-width: 92%; }
  }
</style>

<script>
  // Pause the demo video when the modal is closed
  document.addEventListener('DOMContentLoaded', function () {
    var appDemoModal = document.getElementById('appDemoModal');
    if (appDemoModal) {
      appDemoModal.addEventListener('hidden.bs.modal', function () {
        var vid = document.getElementById('appDemoVideo');
        if (vid) { vid.pause(); vid.currentTime = 0; }
      });
    }
  });
</script>

<?php include 'includes/footer.php'; ?>
