<?php
include '../navigation-bar/navbar.php';
include '../database/create_database.php'; // Database connection

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // User Conventions
    
    $full_name = trim($_POST['full_name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $subject   = trim($_POST['subject'] ?? '');
    $message   = trim($_POST['message'] ?? '');

    if ($full_name === '') $errors[] = 'Please enter your full name.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email address.';
    if ($message === '') $errors[] = 'Please enter a message.';

    if (empty($errors)) {
        $stmt = mysqli_prepare($conn, "INSERT INTO contact_messages (full_name, email, subject, message, created_at) VALUES (?, ?, ?, ?, NOW())");
        mysqli_stmt_bind_param($stmt, 'ssss', $full_name, $email, $subject, $message);
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
    <title>Contact Us | Maretlagadi Welfare Centre</title>

    <style>
        .contact-hero {
            background-image: url('../assets/images/contact-hero-bg.jpg');
            background-size: cover;
            background-position: center;
            min-height: 35vh;
            display: flex;
            align-items: center;
        }

        .contact-overlay {
            width: 100%;
            background: rgba(0, 0, 0, 0.6);
            padding: 55px 0;
            color: #fff;
            text-align: center;
        }

        .contact-overlay h1 {
            font-size: 2.4rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .contact-card {
            border: none;
            border-radius: 14px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.08);
        }

        .contact-info-item {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 22px;
        }

        .contact-info-icon {
            flex-shrink: 0;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background-color: #198754;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }

        .contact-info-item h5 {
            font-weight: 700;
            margin-bottom: 2px;
        }

        .contact-info-item p {
            color: #555;
            margin: 0;
        }

        .btn-contact {
            border-radius: 30px;
            padding: 12px;
            font-weight: 600;
        }

        .map-wrap iframe {
            width: 100%;
            height: 300px;
            border: 0;
            border-radius: 12px;
        }
    </style>
</head>

<body>

<section class="contact-hero">
    <div class="contact-overlay">
        <div class="container">
            <h1>Get In Touch</h1>
            <p>We'd love to hear from you. Reach out with any questions or enquiries.</p>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row">

            <div class="col-lg-5 mb-4">

                <h2 class="text-success mb-4">Contact Information</h2>

                <div class="contact-info-item">
                    <div class="contact-info-icon"></div>
                    <div>
                        <h5>Address</h5>
                        <p>123 Community Road, Maretlagadi, South Africa</p>
                    </div>
                </div>

                <div class="contact-info-item">
                    <div class="contact-info-icon"></div>
                    <div>
                        <h5>Phone</h5>
                        <p>+27 00 000 0000</p>
                    </div>
                </div>

                <div class="contact-info-item">
                    <div class="contact-info-icon"></div>
                    <div>
                        <h5>Email</h5>
                        <p>info@maretlagadiwelfare.org</p>
                    </div>
                </div>

                <div class="contact-info-item">
                    <div class="contact-info-icon"></div>
                    <div>
                        <h5>Office Hours</h5>
                        <p>Mon - Fri: 08:00 - 16:00</p>
                    </div>
                </div>

                <div class="map-wrap mt-4">
                    <iframe
                        src="https://www.google.com/maps?q=South%20Africa&output=embed"
                        allowfullscreen loading="lazy"></iframe>
                </div>

            </div>

            <div class="col-lg-7 mb-4">
                <div class="card contact-card">
                    <div class="card-body p-4">

                        <h3 class="text-success mb-4">Send Us a Message</h3>

                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                Thank you for reaching out! We'll get back to you shortly.
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

                        <form method="POST" action="contact.php">

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

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Subject</label>
                                <input type="text" class="form-control" name="subject"
                                    value="<?php echo isset($_POST['subject']) ? htmlspecialchars($_POST['subject']) : ''; ?>">
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Message</label>
                                <textarea class="form-control" name="message" rows="5" required><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                            </div>

                            <button type="submit" class="btn btn-success btn-contact w-100">
                                Send Message
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