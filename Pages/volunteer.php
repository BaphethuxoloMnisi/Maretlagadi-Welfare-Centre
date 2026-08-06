<?php
include '../navigation-bar/navbar.php';
include '../database/create_database.php'; // Database connection

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $full_name = trim($_POST['full_name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $area      = trim($_POST['area_of_interest'] ?? '');
    $availability = trim($_POST['availability'] ?? '');
    $message   = trim($_POST['message'] ?? '');

    if ($full_name === '') $errors[] = 'Please enter your full name.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email address.';
    if ($phone === '') $errors[] = 'Please enter a contact number.';
    if ($area === '') $errors[] = 'Please select an area of interest.';

    if (empty($errors)) {
        $stmt = mysqli_prepare($conn, "INSERT INTO volunteers (full_name, email, phone, area_of_interest, availability, message, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        mysqli_stmt_bind_param($stmt, 'ssssss', $full_name, $email, $phone, $area, $availability, $message);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $success = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Volunteer | Maretlagadi Welfare Centre</title>

    <style>
        .volunteer-hero {
            background-image: url('../assets/images/volunteer-hero-bg.jpg');
            background-size: cover;
            background-position: center;
            min-height: 40vh;
            display: flex;
            align-items: center;
        }

        .volunteer-overlay {
            width: 100%;
            background: rgba(0, 0, 0, 0.6);
            padding: 60px 0;
            color: #fff;
            text-align: center;
        }

        .volunteer-overlay h1 {
            font-size: 2.4rem;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .volunteer-overlay p {
            max-width: 600px;
            margin: 0 auto;
            font-size: 1.1rem;
            color: #f1f1f1;
        }

        .volunteer-card {
            border: none;
            border-radius: 14px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.08);
        }

        .why-volunteer-item {
            display: flex;
            gap: 14px;
            margin-bottom: 22px;
        }

        .why-volunteer-icon {
            flex-shrink: 0;
            width: 46px;
            height: 46px;
            border-radius: 50%;
            background-color: #198754;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }

        .why-volunteer-item h5 {
            font-weight: 700;
            margin-bottom: 4px;
        }

        .why-volunteer-item p {
            color: #555;
            margin: 0;
        }

        .btn-volunteer {
            border-radius: 30px;
            padding: 12px;
            font-weight: 600;
        }
    </style>
</head>

<body>

<section class="volunteer-hero">
    <div class="volunteer-overlay">
        <div class="container">
            <h1>Become a Volunteer</h1>
            <p>
                Give your time and skills to help us uplift families and
                individuals in our community.
            </p>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row">

            <div class="col-lg-5 mb-4">
                <h2 class="text-success mb-4">Why Volunteer With Us?</h2>

                <div class="why-volunteer-item">
                    <div class="why-volunteer-icon">1</div>
                    <div>
                        <h5>Make a Real Impact</h5>
                        <p>Your time directly supports families and individuals who need it most.</p>
                    </div>
                </div>

                <div class="why-volunteer-item">
                    <div class="why-volunteer-icon">2</div>
                    <div>
                        <h5>Build New Skills</h5>
                        <p>Gain hands-on experience in community development and social work.</p>
                    </div>
                </div>

                <div class="why-volunteer-item">
                    <div class="why-volunteer-icon">3</div>
                    <div>
                        <h5>Join a Community</h5>
                        <p>Meet like-minded people who care about creating lasting change.</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-7 mb-4">
                <div class="card volunteer-card">
                    <div class="card-body p-4">

                        <h3 class="text-success mb-4">Volunteer Application</h3>

                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                Thank you for applying! Our team will be in touch with you soon.
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="volunteer.php">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Full Name</label>
                                    <input type="text" class="form-control" name="full_name" required
                                        value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Email Address</label>
                                    <input type="email" class="form-control" name="email" required
                                        value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Phone Number</label>
                                    <input type="text" class="form-control" name="phone" required
                                        value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Area of Interest</label>
                                    <select class="form-select" name="area_of_interest" required>
                                        <option value="">Select an area</option>
                                        <option value="Education Support">Education Support</option>
                                        <option value="Skills Development">Skills Development</option>
                                        <option value="Community Outreach">Community Outreach</option>
                                        <option value="Youth Empowerment">Youth Empowerment</option>
                                        <option value="Events & Fundraising">Events & Fundraising</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Availability</label>
                                <select class="form-select" name="availability">
                                    <option value="">Select availability</option>
                                    <option value="Weekdays">Weekdays</option>
                                    <option value="Weekends">Weekends</option>
                                    <option value="Evenings">Evenings</option>
                                    <option value="Flexible">Flexible</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Tell us about yourself (optional)</label>
                                <textarea class="form-control" name="message" rows="4"><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                            </div>

                            <button type="submit" class="btn btn-success btn-volunteer w-100">
                                Submit Application
                            </button>

                        </form>

                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<?php include '../footer/footer.php'; ?>

</body>
</html>