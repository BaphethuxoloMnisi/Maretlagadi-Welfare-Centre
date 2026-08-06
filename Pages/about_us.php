<?php
include '../navigation-bar/navbar.php';

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>About Us | Maretlagadi Welfare Centre</title>

        <style>
            /* ===== About Hero ===== */
            .about-hero {
                background-image: url('../assets/images/about-hero-bg.jpg');
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                min-height: 40vh;
                display: flex;
                align-items: center;
                position: relative;
            }

            .about-overlay {
                width: 100%;
                background: rgba(0, 0, 0, 0.55);
                padding: 60px 0;
                color: #ffffff;
            }

            .about-overlay h1 {
                font-size: 2.4rem;
                font-weight: 700;
                margin-bottom: 15px;
            }

            .about-overlay p {
                font-size: 1.1rem;
                max-width: 650px;
                margin: 0 auto;
                color: #f1f1f1;
                line-height: 1.6;
            }

            /* ===== General Section Spacing ===== */
            section h2 {
                font-weight: 700;
            }

            section p {
                color: #444;
                line-height: 1.7;
            }

            /* ===== About Content ===== */
            .col-lg-6 h2 {
                font-size: 1.8rem;
            }

            /* ===== Cards (Vision / Mission / Values) ===== */
            .card {
                border: none;
                border-radius: 12px;
                overflow: hidden;
                transition: transform 0.2s ease;
            }

            .card:hover {
                transform: translateY(-4px);
            }

            .card-body h3 {
                font-weight: 700;
                margin-bottom: 12px;
            }

            .card-body h4 {
                font-weight: 700;
                margin-bottom: 10px;
                color: #1a1a1a;
            }

            .card-body p {
                font-size: 0.98rem;
            }

            /* ===== Impact Section ===== */
            .impact-section {
                background-color: #198754; /* bootstrap success green */
                color: #ffffff;
                padding: 60px 0;
            }

            .impact-section h2 {
                font-size: 2.4rem;
                font-weight: 700;
                margin-bottom: 5px;
            }

            .impact-section p {
                color: #eafff1;
                font-size: 1rem;
                margin-bottom: 0;
            }

            /* ===== CTA Section ===== */
            .cta-section {
                background-color: #f8f9fa;
                padding: 60px 0;
                text-align: center;
            }

            .cta-section h2 {
                font-size: 2rem;
                margin-bottom: 15px;
            }

            .cta-section p {
                max-width: 550px;
                margin: 0 auto;
                font-size: 1.05rem;
            }

            .cta-section .btn {
                border-radius: 30px;
                padding: 10px 32px;
                font-weight: 600;
            }

            /* ===== Responsive ===== */
            @media (max-width: 768px) {
                .about-hero {
                    min-height: 32vh;
                }

                .about-overlay h1 {
                    font-size: 1.8rem;
                }

                .impact-section h2 {
                    font-size: 1.9rem;
                }
            }
        </style>
    </head>

    <body>
    <!-- Hero Section -->
        <section class="about-hero">
            <br>
            <div class="about-overlay">
                <div class="container text-center">
                    <h1>About Maretlagadi Welfare Centre</h1>

                    <p>
                        Empowering communities through education, compassion,
                        skills development and sustainable community programmes.
                    </p>

                </div>
            </div>
        </section>

        <!-- About -->
         <section class="py-5">
            <div class="container">

                <div class="row align-items-center">

                    <div class="col-lg-6 mb-4">
                        <img src="../images/landing_page.jpg" 
                        class="img-fluid rounded shadow"
                        alt="About Image">

                    </div>

                    <div class="col-lg-6">
                        <h2 class="text-success mb-4">
                            About Our Organisation
                        </h2>

                        <p>
                            Maretlagadi Welfare Centre has been dedicated to improving
                            lives through community uplifment, education, poverty
                            alleviation and social development.
                        </p>

                        <p>
                            We work closely with families, youth and vulnerable
                            individuals by providing programmes that create hope,
                            opportunity and long-term sustainable development.
                        </p>

                        <a href="programs_projects.php"
                           class="btn btn-success mt-3">
                            Our programmes
                        </a>

                    </div>

                </div>

            </div>
         </section>

         <!-- Vision & Mission -->
          <section class="bg-light py-5">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card shadow h-100">
                                <div class="card-body">
                                    <h3 class="text-success">
                                        Our Vision
                                    </h3>

                                    <p>
                                        To build an inclusive community where every person has
                                        equal opportunitied and access to quality support services.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card shadow h-100">
                                <div class="card-body">
                                    <h3 class="text-success">
                                        Our Mission
                                    </h3>

                                    <p>
                                        To empower communities through education, skills
                                        development, welfare service and collaborative
                                        community initiatives.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
          </section>

          <!-- Values -->
           <section class="py-5">
                <div class="container">
                    <div class="text-center mb-5">
                        <h2 class="text-success">
                            Our Core Values
                        </h2>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="card text-center shadow">
                                <div class="card-body">
                                    <h4>
                                        Compassion
                                    </h4>

                                    <p>
                                        Serving every person with dignity and care.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card text-center shadow">
                                <div class="card-body">
                                    <h4>
                                        Integrity
                                    </h4>

                                    <p>
                                        Honesty and transparency in everything we do.
                                    </p>

                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card text-center shadow">
                                <div class="card-body">
                                    <h4> 
                                        Empowerment 
                                    </h4>

                                    <p>
                                        Creating opportunities for lasting change.
                                    </p>

                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card text-center shadow">
                                <div class="card-body">
                                    <h4> Respect </h4>

                                    <p>
                                        Valuing every individual regardless of background.
                                    </p>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
           </section>

           <section class="impact-section">
                <div class="container">
                    <div class="row text-center">

                        <div class="col-md-3">
                            <h2>500+</h2>
                            <p>Families Assisted</p>
                        </div>

                        <div class="col-md-3">
                            <h2>40+</h2>
                            <p>Volunteers</p>
                        </div>

                        <div class="col-md-3">
                            <h2>20+</h2>
                            <p>Community Programmes</p>
                        </div>

                        <div class="col-md-3">
                            <h2>10+</h2>
                            <p>Years of Service</p>
                        </div>
                    </div>
                </div>
           </section>
<br>
            <!-- CTA -->
             <section class="cta-section">
                <div class="container text-center">
                    <h2>
                        Together We Can Make a Difference
                    </h2>

                    <p>
                        Support our mission by becoming a volunteer or making
                        a donation today.
                    </p>
<br>
                    <a href="donation.php" class="btn btn-success">
                        Donate Now
                    </a>
                </div>
             </section>
<br>
             <?php include '../footer/footer.php'; ?>
    </body>
</html>