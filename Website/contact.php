<?php
require_once 'includes/db.php';
$pageTitle = "Contact Us";
$sent = false; $error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $msg = trim($_POST['message'] ?? '');

    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $msg === '') {
        $error = "Please fill in all fields with a valid email address.";
    } else {
        $stmt = $pdo->prepare(
            "INSERT INTO message (sender_name, sender_email, message_text) VALUES (?, ?, ?)"
        );
        $stmt->execute([$name, $email, $msg]);
        $sent = true;
    }
}
include 'includes/header.php';
?>

<section class="py-5">
  <div class="container">
    <div class="row gy-5">
      <div class="col-lg-6">
        <h1 class="fw-bold">Contact Us</h1>
        <p class="text-secondary mt-2">Have a question or want to get involved? Reach out to us directly.</p>
        <ul class="list-unstyled mt-4">
          <li class="mb-2"><strong>Email:</strong> info@maretlagadi.org</li>
          <li class="mb-2"><strong>Phone:</strong> +27 (0)12 345 6789</li>
          <li class="mb-2"><strong>Address:</strong> Centurion, Gauteng, South Africa</li>
        </ul>
      </div>
      <div class="col-lg-6">
        <?php if ($sent): ?>
          <div class="alert alert-success">Thanks for reaching out — we'll respond within 2 business days.</div>
        <?php endif; ?>
        <?php if ($error): ?>
          <div class="alert alertdanger - contact.php:41"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST">
          <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Message</label>
            <textarea name="message" rows="4" class="form-control" required></textarea>
          </div>
          <button type="submit" class="btn btn-brand rounded-pill px-4 py-2">Send Message</button>
        </form>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>