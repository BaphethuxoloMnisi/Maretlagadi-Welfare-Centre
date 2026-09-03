<?php
$pageTitle = "Gallery";
include 'includes/header.php';

// Load image files directly from the images folder.
$galleryDir = __DIR__ . '/images';
$allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
$galleryImages = [];

if (is_dir($galleryDir)) {
    foreach (scandir($galleryDir) as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }

        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (!in_array($extension, $allowedExtensions, true)) {
            continue;
        }

        // Keep branding/hero assets out of the photo gallery.
        $lowerName = strtolower($file);
        if (str_contains($lowerName, 'logo') || str_contains($lowerName, 'hero-image')) {
            continue;
        }

        $galleryImages[] = $file;
    }
}

// Sort naturally so similarly named WhatsApp images appear in a sensible order.
natcasesort($galleryImages);
$galleryImages = array_values($galleryImages);
?>

<section class="py-5">
  <div class="container">
    <h1 class="fw-bold text-center">Media &amp; Gallery</h1>
    <p class="text-secondary text-center mt-2">Moments from our programmes and community events.</p>

    <?php if (!empty($galleryImages)): ?>
      <div class="row g-3 mt-4">
        <?php foreach ($galleryImages as $index => $image): ?>
          <div class="col-6 col-md-4 col-lg-3">
            <div class="gallery-card h-100">
              <img
                src="images/<?php echo rawurlencode($image); ?>"
                alt="Maretlagadi community gallery photo <?php echo $index + 1; ?>"
                class="w-100 rounded shadow-sm"
                style="height: 220px; object-fit: cover; display: block;"
                loading="lazy"
              >
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="alert alert-info text-center mt-4">
        No gallery images were found in the <strong>images</strong> folder.
      </div>
    <?php endif; ?>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
