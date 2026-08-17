<?php $pageTitle = "Home"; include 'includes/header.php'; ?>

<section class="hero">
  <div class="container">
    <div class="row align-items-center gy-4">
      <div class="col-lg-6">
        <h1>Welcome to, <span class="text-success">Maretlagadi Welfare Centre</span></h1>
        <p class="text-secondary mt-3">Maretlagadi Welfare Centre is dedicated to uplifting our community
          through education, support programmes, and sustainable development initiatives — with a focus
          on children with disabilities and vulnerable individuals.</p>
        <div class="d-flex gap-3 mt-4">
          <a href="donate.php" class="btn btn-brand rounded-pill px-4 py-2">Donate Now</a>
          <a href="volunteer.php" class="btn btn-outline-dark rounded-pill px-4 py-2">Volunteer</a>
        </div>
      </div>
      <div class="col-lg-6" style="">
        <div class="hero-img-placeholder"><img src="images/hero-image.jpeg" alt="Hero Image" width="550px"; height="500px"></div>
      </div>
    </div>
  </div>
</section>

<section class="py-5">
  <div class="container">
    <h2 class="text-center fw-bold mb-5">Our Programmes</h2>
    <div class="row g-4">
      <?php
      $programmes = [
        ["Education Support", "Providing educational resources, tutoring, and scholarships to help students achieve their academic goals."],
        ["Skills Development", "Empowering community members with practical skills training and vocational education for sustainable employment."],
        ["Community Outreach", "Connecting with families to provide support services, food security, and healthcare access initiatives."]
      ];
      foreach ($programmes as $p): ?>
      <div class="col-md-4">
        <div class="programme-card h-100">
          <div class="programme-img">IMAGE</div>
          <div class="p-3">
            <h5 class="fwsemibold - index.php:38"><?php echo $p[0]; ?></h5>
            <p class="textsecondary small - index.php:39"><?php echo $p[1]; ?></p>
            <a href="programmes.php" class="btn btn-sm btn-outline-dark">Learn More</a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="py-5 bg-light">
  <div class="container">
    <div class="row align-items-center gy-4">
      <div class="col-lg-5">
        <div class="hero-img-placeholder" style="min-height:260px;">ABOUT IMAGE</div>
      </div>
      <div class="col-lg-7">
        <h2 class="fw-bold">About Our Organisation</h2>
        <p class="text-secondary mt-3">Since our founding, Maretlagadi Welfare Centre has been at the forefront
          of community development. We work tirelessly to provide resources, education, and support to those
          who need it most.</p>
        <div class="row mt-4 text-center text-md-start">
          <div class="col-4"><div class="stat-number">500+</div><div class="small text-secondary">Beneficiaries</div></div>
          <div class="col-4"><div class="stat-number">30+</div><div class="small text-secondary">Volunteers</div></div>
          <div class="col-4"><div class="stat-number">15+</div><div class="small text-secondary">Programmes</div></div>
        </div>
        <a href="about.php" class="btn btn-brand rounded-pill px-4 mt-4">Read More</a>
      </div>
    </div>
  </div>
</section>

<section class="py-5">
  <div class="container">
    <div class="cta-band p-5 text-center">
      <h2 class="fw-bold">Make a Difference Today</h2>
      <p class="text-secondary text-light-emphasis mt-2 mb-4">Your contribution helps us continue our mission to
        empower and uplift our community. Every donation makes a lasting impact.</p>
      <a href="donate.php" class="btn btn-light rounded-pill px-4 py-2">Donate Now</a>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>