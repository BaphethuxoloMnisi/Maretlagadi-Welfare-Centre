<?php
$pageTitle = "Home";
include 'includes/header.php';
?>

<style>
    /* =========================
       HERO SECTION
       ========================= */

    .hero {
        padding: 70px 0;
    }

    .hero-image-wrapper {
        width: 100%;
        max-width: 600px;
        margin: 0 auto;
        overflow: hidden;
        border-radius: 20px;
    }

    .hero-image {
        display: block;
        width: 100%;
        height: auto;
        max-width: 100%;
        border-radius: 20px;
        object-fit: cover;
        object-position: center;
    }


    /* =========================
       PROGRAMME CARDS
       ========================= */

    .programme-img {
        width: 100%;
        height: 220px;
        overflow: hidden;
        background: #e5e3df;
        border-radius: 12px 12px 0 0;
    }

    .programme-img img {
        width: 100%;
        height: 100%;
        display: block;
        object-fit: cover;
        object-position: center;
    }

    .programme-card {
        overflow: hidden;
        border-radius: 12px;
    }


    /* =========================
       ABOUT IMAGE
       ========================= */

    .about-image-wrapper {
        width: 100%;
        overflow: hidden;
        border-radius: 20px;
    }

    .about-image {
        width: 100%;
        height: auto;
        display: block;
        object-fit: cover;
        border-radius: 20px;
    }


    /* =========================
       TABLET
       ========================= */

    @media (max-width: 991px) {

        .hero {
            padding: 50px 0;
        }

        .hero-image-wrapper {
            max-width: 550px;
        }

        .programme-img {
            height: 200px;
        }
    }


    /* =========================
       MOBILE
       ========================= */

    @media (max-width: 767px) {

        .hero {
            padding: 40px 0;
        }

        .hero-image-wrapper {
            width: 100%;
            max-width: 100%;
        }

        .hero-image {
            width: 100%;
            height: auto;
        }

        .programme-img {
            height: 220px;
        }

        .hero h1 {
            font-size: 2rem;
        }
    }


    /* =========================
       SMALL PHONES
       ========================= */

    @media (max-width: 480px) {

        .hero {
            padding: 30px 0;
        }

        .hero h1 {
            font-size: 1.7rem;
        }

        .programme-img {
            height: 200px;
        }

        .hero-image-wrapper,
        .hero-image,
        .about-image-wrapper,
        .about-image {
            border-radius: 15px;
        }
    }
</style>


<!-- =========================
     HERO SECTION
     ========================= -->

<section class="hero">

    <div class="container">

        <div class="row align-items-center gy-5">

            <!-- TEXT -->
            <div class="col-lg-6">

                <h1>
                    Welcome to,
                    <span class="text-success">
                        Maretlagadi Welfare Centre
                    </span>
                </h1>

                <p class="text-secondary mt-3">
                    Maretlagadi Welfare Centre is dedicated to uplifting our
                    community through education, support programmes, and
                    sustainable development initiatives — with a focus on
                    children with disabilities and vulnerable individuals.
                </p>

                <div class="d-flex flex-wrap gap-3 mt-4">

                    <a href="donate.php"
                       class="btn btn-brand rounded-pill px-4 py-2">
                        Donate Now
                    </a>

                    <a href="volunteer.php"
                       class="btn btn-outline-dark rounded-pill px-4 py-2">
                        Volunteer
                    </a>

                </div>

            </div>


            <!-- HERO IMAGE -->
            <div class="col-lg-6">

                <div class="hero-image-wrapper">

                    <img
                        src="images/hero-image.jpeg"
                        alt="Maretlagadi Welfare Centre"
                        class="hero-image"
                    >

                </div>

            </div>

        </div>

    </div>

</section>


<!-- =========================
     PROGRAMMES
     ========================= -->

<section class="py-5">

    <div class="container">

        <h2 class="text-center fw-bold mb-5">
            Our Programmes
        </h2>

        <div class="row g-4">

            <?php

            $programmes = [

                [
                    "Education Support",
                    "Providing educational resources, tutoring, and scholarships to help students achieve their academic goals.",
                    "images/education.jpg"
                ],

                [
                    "Skills Development",
                    "Empowering community members with practical skills training and vocational education for sustainable employment.",
                    "images/skills.jpg"
                ],

                [
                    "Community Outreach",
                    "Connecting with families to provide support services, food security, and healthcare access initiatives.",
                    "images/outreach.jpg"
                ]

            ];

            foreach ($programmes as $p):
            ?>

            <div class="col-md-6 col-lg-4">

                <div class="programme-card h-100">

                    <!-- PROGRAMME IMAGE -->
                    <div class="programme-img">

                        <img
                            src="images\education.jpeg"
                            alt="<?php echo htmlspecialchars($p[0]); ?> - index.php:273"
                            loading="lazy"
                        >

                    </div>


                    <!-- PROGRAMME CONTENT -->
                    <div class="p-3">

                        <h5 class="fw-semibold">
                            <?php echo htmlspecialchars($p[0]); ?>
                        </h5>

                        <p class="text-secondary small">
                            <?php echo htmlspecialchars($p[1]); ?>
                        </p>

                        <a href="programmes.php"
                           class="btn btn-sm btn-outline-dark">
                            Learn More
                        </a>

                    </div>

                </div>

            </div>

            <?php endforeach; ?>

        </div>

    </div>

</section>


<!-- =========================
     ABOUT SECTION
     ========================= -->

<section class="py-5 bg-light">

    <div class="container">

        <div class="row align-items-center gy-4">

            <!-- ABOUT IMAGE -->
            <div class="col-lg-5">

                <div class="about-image-wrapper">

                    <img
                        src="images\maretlagadi logo.jpeg"
                        alt="About Maretlagadi Welfare Centre"
                        class="about-image"
                    >

                </div>

            </div>


            <!-- ABOUT TEXT -->
            <div class="col-lg-7">

                <h2 class="fw-bold">
                    About Our Organisation
                </h2>

                <p class="text-secondary mt-3">
                    Since our founding, Maretlagadi Welfare Centre has been
                    at the forefront of community development. We work
                    tirelessly to provide resources, education, and support
                    to those who need it most.
                </p>


                <div class="row mt-4 text-center text-md-start">

                    <div class="col-4">

                        <div class="stat-number">
                            500+
                        </div>

                        <div class="small text-secondary">
                            Beneficiaries
                        </div>

                    </div>


                    <div class="col-4">

                        <div class="stat-number">
                            30+
                        </div>

                        <div class="small text-secondary">
                            Volunteers
                        </div>

                    </div>


                    <div class="col-4">

                        <div class="stat-number">
                            15+
                        </div>

                        <div class="small text-secondary">
                            Programmes
                        </div>

                    </div>

                </div>


                <a href="about.php"
                   class="btn btn-brand rounded-pill px-4 mt-4">
                    Read More
                </a>

            </div>

        </div>

    </div>

</section>


<!-- =========================
     CALL TO ACTION
     ========================= -->

<section class="py-5">

    <div class="container">

        <div class="cta-band p-5 text-center">

            <h2 class="fw-bold">
                Make a Difference Today
            </h2>

            <p class="text-secondary text-light-emphasis mt-2 mb-4">
                Your contribution helps us continue our mission to empower
                and uplift our community. Every donation makes a lasting
                impact.
            </p>

            <a href="donate.php"
               class="btn btn-light rounded-pill px-4 py-2">
                Donate Now
            </a>

        </div>

    </div>

</section>


<?php include 'includes/footer.php'; ?>