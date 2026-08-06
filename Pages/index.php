<?php
include '../navigation-bar/navbar.php';
include '../database/create_database.php'; // Database connection
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Martlagadi Welfare Centre</title>

        <style>
            /* ===== Hero Section ===== */
            .hero {
                background-image: url('../assets/images/hero-bg.jpg');
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                min-height: 65vh;
                display: flex;
                align-items: center;
                position: relative;
            }

            .hero-overlay {
                width: 100%;
                background: rgba(0, 0, 0, 0.55);
                padding: 80px 0;
            }

            .hero-content {
                max-width: 650px;
                color: #ffffff;
            }

            .hero-content h1 {
                font-size: 2.6rem;
                font-weight: 700;
                margin-bottom: 20px;
                line-height: 1.2;
            }

            .hero-content p {
                font-size: 1.15rem;
                line-height: 1.6;
                color: #f1f1f1;
            }

            .hero-content .btn {
                border-radius: 30px;
                padding: 10px 28px;
                font-weight: 600;
            }

            /* ===== Vision / Mission / Announcements Section ===== */
            .card {
                border: none;
                border-radius: 12px;
                overflow: hidden;
            }

            .card-body h2 {
                font-size: 1.5rem;
                font-weight: 700;
                margin-bottom: 12px;
            }

            .card-body p {
                color: #444;
                line-height: 1.7;
                font-size: 1rem;
            }

            .card-body hr {
                margin: 25px 0;
                border-top: 1px solid #e0e0e0;
            }

            .card-header h3 {
                font-size: 1.25rem;
                font-weight: 600;
            }

            /* Announcements list */
            .card-body h5 {
                font-weight: 600;
                color: #1a1a1a;
                margin-bottom: 4px;
            }

            .card-body small.text-muted {
                font-size: 0.85rem;
            }

            .card-body .mb-4:last-child hr {
                display: none;
            }

            @media (max-width: 768px) {
                .hero {
                    min-height: 50vh;
                }

                .hero-content h1 {
                    font-size: 1.9rem;
                }

                .hero-content p {
                    font-size: 1rem;
                }
            }
        </style>
    </head>

    <body>
        <!-- Hero Section -->
         <section class="hero">
            <div class="hero-overlay">
                <div class="container">

                    <div class="hero-content">
                        <h1>Welcome to Maretlagadi Welfare Centre</h1>

                        <p>
                            Dedicated to improving lives through compassion,
                            empowerment, education, and community development.
                        </p>

                        <a href="about.php" class="btn btn-success btn-lg mt-3">
                            Learn More
                        </a>
                    </div>

                </div>
            </div>
         </section>

         <!-- Vision | Mission | Announcements -->
          <section class="py-5">
    <div class="container">

        <div class="row">

            <!-- Left Column -->
            <div class="col-lg-7">

                <div class="card shadow mb-4">
                    <div class="card-body">

                        <h2 class="text-success">Our Vision</h2>

                        <p>
                            To build a caring, inclusive and empowered community
                            where every individual has access to opportunities,
                            support and hope for a better future.
                        </p>

                        <hr>

                        <h2 class="text-success">Our Mission</h2>

                        <p>
                            To provide quality welfare services, educational
                            support, skills development and community programmes
                            that uplift vulnerable individuals and families.
                        </p>

                    </div>
                </div>

            </div>

            <!-- Right Column -->
            <div class="col-lg-5">

                <div class="card shadow">

                    <div class="card-header bg-success text-white">
                        <h3 class="mb-0">Latest Announcements</h3>
                    </div>

                    <div class="card-body">
                        <?php

$query = "SELECT * FROM announcements ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {

    while ($row = mysqli_fetch_assoc($result)) {
?>

        <div class="mb-4">

            <h5><?php echo htmlspecialchars($row['title']); ?></h5>

            <small class="text-muted">
                <?php echo date("d M Y", strtotime($row['created_at'])); ?>
            </small>

            <p class="mt-2">
                <?php echo nl2br(htmlspecialchars($row['message'])); ?>
            </p>

            <hr>

        </div>

<?php
    }

} else {

    echo "<p class='text-muted'>No announcements available at the moment.</p>";

}

?>

                    </div>

                </div>

            </div>

        </div>

    </div>
</section>

<?php
include '../footer/footer.php';

?>
    </body>
</html>