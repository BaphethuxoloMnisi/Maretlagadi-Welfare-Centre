<?php $pageTitle = "Our Programmes"; include 'includes/header.php'; ?>

<section class="py-5">
  <div class="container">
    <h1 class="fw-bold text-center">Our Programmes</h1>
    <p class="text-secondary text-center mx-auto mt-3" style="max-width:700px;">
      Explore the initiatives we run to support education, skills development, and outreach in our community.
    </p>

    <div class="row g-4 mt-4">
      <?php
      $programmes = [
        ["Education Support", "Providing educational resources, tutoring, and scholarships to help students achieve their academic goals.", "Ongoing"],
        ["Skills Development", "Empowering community members with practical skills training and vocational education for sustainable employment.", "Weekly"],
        ["Community Outreach", "Connecting with families to provide support services, food security, and healthcare access initiatives.", "Monthly"],
        ["Youth Mentorship", "Pairing young people with mentors who provide guidance, encouragement, and life-skills coaching.", "Bi-weekly"],
        ["Elderly Care Support", "Bringing care and comfort to elderly members of the community through visits and assistance.", "Weekly"],
        ["Environmental Awareness", "Promoting a cleaner and greener environment through clean-up drives and education.", "Monthly"]
      ];
      foreach ($programmes as $p): ?>
      <div class="col-md-4">
        <div class="programme-card h-100">
          <div class="programme-img">IMAGE</div>
          <div class="p-3">
            <span class="badge bg-light text-dark border mb-2"><?php echo $p[2]; ?></span>
            <h5 class="fw-semibold"><?php echo $p[0]; ?></h5>
            <p class="text-secondary small"><?php echo $p[1]; ?></p>
            <a href="volunteer.php" class="btn btn-sm btn-brand">Get Involved</a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>