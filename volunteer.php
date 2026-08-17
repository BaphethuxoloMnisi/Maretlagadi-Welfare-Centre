<?php
require_once 'includes/db.php';
$pageTitle = "Volunteer";
$success = false; $error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $skills = trim($_POST['skills'] ?? '');
    $availability = $_POST['availability'] ?? 'both';

    if ($name === '' || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid name and email address.";
    } else {
        try {
            $pdo->beginTransaction();

            // Split name into first/surname (simple split; refine if needed)
            $parts = explode(' ', $name, 2);
            $firstName = $parts[0];
            $surname = $parts[1] ?? '';

            // Check if user already exists
            $stmt = $pdo->prepare("SELECT user_id FROM user WHERE email = ?");
            $stmt->execute([$email]);
            $existing = $stmt->fetch();

            if ($existing) {
                $userId = $existing['user_id'];
            } else {
                $tempPassword = password_hash(bin2hex(random_bytes(4)), PASSWORD_DEFAULT);
                $stmt = $pdo->prepare(
                    "INSERT INTO user (name, surname, email, password, role, phone) VALUES (?, ?, ?, ?, 'volunteer', ?)"
                );
                $stmt->execute([$firstName, $surname, $email, $tempPassword, $phone]);
                $userId = $pdo->lastInsertId();
            }

            // Insert into volunteer table (skip if already a volunteer)
            $stmt = $pdo->prepare("SELECT volunteer_id FROM volunteer WHERE user_id = ?");
            $stmt->execute([$userId]);
            if (!$stmt->fetch()) {
                $stmt = $pdo->prepare(
                    "INSERT INTO volunteer (user_id, skills, availability) VALUES (?, ?, ?)"
                );
                $stmt->execute([$userId, $skills, $availability]);
            }

            $pdo->commit();
            $success = true;
        } catch (PDOException $e) {
            $pdo->rollBack();
            error_log($e->getMessage());
            $error = "Something went wrong. Please try again.";
        }
    }
}
include 'includes/header.php';
?>

<section class="py-5">
  <div class="container" style="max-width:640px;">
    <h1 class="fw-bold text-center">Become a Volunteer</h1>
    <p class="text-secondary text-center mt-2">Join our team and help make a real difference in the community.</p>

    <?php if ($success): ?>
      <div class="alert alert-success mt-4">Thank you for registering! Our volunteer coordinator will be in touch shortly.</div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="alert alert-danger mt-4"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" class="mt-4">
      <div class="mb-3">
        <label class="form-label">Full Name</label>
        <input type="text" name="name" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Phone Number</label>
        <input type="tel" name="phone" class="form-control">
      </div>
      <div class="mb-3">
        <label class="form-label">Skills</label>
        <input type="text" name="skills" class="form-control" placeholder="e.g. Tutoring, First Aid, Admin">
      </div>
      <div class="mb-3">
        <label class="form-label">Availability</label>
        <select name="availability" class="form-select">
          <option value="weekday">Weekdays</option>
          <option value="weekend">Weekends</option>
          <option value="both">Both</option>
        </select>
      </div>
      <button type="submit" class="btn btn-brand w-100 rounded-pill py-2">Register as Volunteer</button>
    </form>
  </div>
</section>

<?php include 'includes/footer.php'; ?>