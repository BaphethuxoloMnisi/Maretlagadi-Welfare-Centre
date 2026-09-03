<?php $pageTitle = "Gallery"; include 'includes/header.php'; ?>

<section class="py-5">
  <div class="container">
    <h1 class="fw-bold text-center">Media & Gallery</h1>
    <p class="text-secondary text-center mt-2">Moments from our programmes and community events.</p>

    <div class="row g-3 mt-4">
      <?php for ($i = 1; $i <= 8; $i++): ?>
      <div class="col-6 col-md-3">
        <div class="hero-img-placeholder" style="min-height:160px;">PHOTO <?php echo $i; ?></div>
      </div>
      <?php endfor; ?>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>