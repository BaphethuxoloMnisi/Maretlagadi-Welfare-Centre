<?php
include '../navigation-bar/navbar.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Programmes & Projects | Maretlagadi Welfare Centre</title>

    <style>
        /* ===== Hero ===== */
        .programmes-hero {
            background-image: url('../assets/images/programmes-hero-bg.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 45vh;
            display: flex;
            align-items: center;
            position: relative;
        }

        .hero-overlay {
            width: 100%;
            background: rgba(0, 0, 0, 0.6);
            padding: 80px 0;
            color: #fff;
        }

        .hero-overlay h1 {
            font-size: 2.6rem;
            font-weight: 700;
            margin-bottom: 18px;
        }

        .hero-overlay p {
            max-width: 650px;
            margin: 0 auto;
            font-size: 1.1rem;
            line-height: 1.6;
            color: #f1f1f1;
        }

        /* ===== Section Intro ===== */
        .section-intro h2 {
            font-weight: 700;
            position: relative;
            display: inline-block;
            padding-bottom: 14px;
        }

        .section-intro h2::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 70px;
            height: 3px;
            background-color: #198754;
        }

        .section-intro p.lead {
            max-width: 700px;
            margin: 20px auto 0;
            color: #555;
            font-size: 1.1rem;
        }

        /* ===== Programme Cards ===== */
        .programme-card {
            border: none;
            border-radius: 14px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.08);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .programme-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.14);
        }

        .programme-img-wrap {
            position: relative;
            height: 220px;
            overflow: hidden;
        }

        .programme-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.35s ease;
        }

        .programme-card:hover .programme-img-wrap img {
            transform: scale(1.06);
        }

        .programme-icon-badge {
            position: absolute;
            top: 16px;
            left: 16px;
            background-color: #198754;
            color: #fff;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
            box-shadow: 0 3px 8px rgba(0,0,0,0.25);
        }

        .programme-card .card-body {
            padding: 24px 26px 26px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .programme-card h3 {
            font-size: 1.3rem;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 10px;
        }

        .programme-card p {
            color: #555;
            line-height: 1.6;
            flex-grow: 1;
        }

        .programme-card .btn {
            align-self: flex-start;
            border-radius: 30px;
            padding: 8px 22px;
            font-weight: 600;
            margin-top: 12px;
        }

        /* ===== Impact Section ===== */
        .impact-section {
            background-color: #198754;
            color: #fff;
            padding: 70px 0;
        }

        .impact-section h2 {
            font-weight: 700;
            margin-bottom: 45px;
        }

        .impact-stat h1 {
            font-size: 2.8rem;
            font-weight: 800;
            margin-bottom: 6px;
        }

        .impact-stat p {
            color: #eafff1;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.9rem;
            margin: 0;
        }

        /* ===== Get Involved ===== */
        .get-involved {
            padding: 70px 0;
            background-color: #f8f9fa;
        }

        .get-involved h2 {
            font-weight: 700;
            margin-bottom: 15px;
        }

        .get-involved p {
            max-width: 600px;
            margin: 0 auto 30px;
            color: #555;
            font-size: 1.05rem;
        }

        .get-involved .btn {
            border-radius: 30px;
            padding: 10px 28px;
            font-weight: 600;
            margin: 0 6px 10px;
        }

        /* ===== Responsive ===== */
        @media (max-width: 768px) {
            .programmes-hero {
                min-height: 35vh;
            }

            .hero-overlay h1 {
                font-size: 1.9rem;
            }

            .impact-stat h1 {
                font-size: 2.2rem;
            }
        }
    </style>

</head>

<body>

<!-- Hero -->

<section class="programmes-hero">

    <div class="hero-overlay">

        <div class="container text-center">

            <h1>Our Programmes & Projects</h1>

            <p>
                Empowering individuals, strengthening families,
                and building sustainable communities through
                meaningful initiatives.
            </p>

        </div>

    </div>

</section>

<!-- Introduction -->

<section class="py-5">

<div class="container">

<div class="section-intro text-center">

<h2 class="text-success">
Making a Difference Every Day
</h2>

<p class="lead">
Our programmes focus on education, skills development,
community support and youth empowerment. Every initiative
is designed to improve lives and create long-term change.
</p>

</div>

</div>

</section>

<!-- Programmes -->

<section class="pb-5">

<div class="container">

<div class="row g-4">

<!-- Programme -->

<div class="col-lg-6 col-xl-3">

<div class="programme-card">

<div class="programme-img-wrap">
    <span class="programme-icon-badge">1</span>
    <img src="../images/landing_page.jpg" alt="Education Support">
</div>

<div class="card-body">

<h3>Education Support</h3>

<p>
Providing tutoring, school resources, mentorship,
career guidance and educational assistance for
children and young people.
</p>

<a href="#" class="btn btn-success">
Learn More
</a>

</div>

</div>

</div>

<!-- Programme -->

<div class="col-lg-6 col-xl-3">

<div class="programme-card">

<div class="programme-img-wrap">
    <span class="programme-icon-badge">2</span>
    <img src="../images/landing_page.jpg" alt="Skills Development">
</div>

<div class="card-body">

<h3>Skills Development</h3>

<p>
Practical vocational training, entrepreneurship,
computer literacy and workplace readiness
programmes.
</p>

<a href="#" class="btn btn-success">
Learn More
</a>

</div>

</div>

</div>

<!-- Programme -->

<div class="col-lg-6 col-xl-3">

<div class="programme-card">

<div class="programme-img-wrap">
    <span class="programme-icon-badge">3</span>
    <img src="../images/landing_page.jpg" alt="Community Outreach">
</div>

<div class="card-body">

<h3>Community Outreach</h3>

<p>
Food parcels, clothing drives, awareness campaigns
and support services for vulnerable families.
</p>

<a href="#" class="btn btn-success">
Learn More
</a>

</div>

</div>

</div>

<!-- Programme -->

<div class="col-lg-6 col-xl-3">

<div class="programme-card">

<div class="programme-img-wrap">
    <span class="programme-icon-badge">4</span>
    <img src="../images/landing_page.jpg" alt="Youth Empowerment">
</div>

<div class="card-body">

<h3>Youth Empowerment</h3>

<p>
Leadership workshops, life skills,
career coaching and community engagement
for young people.
</p>

<a href="#" class="btn btn-success">
Learn More
</a>

</div>

</div>

</div>

</div>

</div>

</section>

<!-- Impact -->

<section class="impact-section">

<div class="container">

<div class="text-center">

<h2>Our Impact</h2>

</div>

<div class="row text-center">

<div class="col-md-3 impact-stat">

<h1>500+</h1>

<p>Families Supported</p>

</div>

<div class="col-md-3 impact-stat">

<h1>120+</h1>

<p>Youth Empowered</p>

</div>

<div class="col-md-3 impact-stat">

<h1>35+</h1>

<p>Community Events</p>

</div>

<div class="col-md-3 impact-stat">

<h1>50+</h1>

<p>Active Volunteers</p>

</div>

</div>

</div>

</section>

<!-- Get Involved -->

<section class="get-involved">

<div class="container">

<div class="text-center">

<h2>
Become Part of the Change
</h2>

<p>
Together we can transform lives through volunteering,
partnerships and donations.
</p>

<a href="volunteer.php"
class="btn btn-success">
Volunteer
</a>

<a href="donation.php"
class="btn btn-outline-success">
Donate
</a>

<a href="contact.php"
class="btn btn-dark">
Partner With Us
</a>

</div>

</div>

</section>

<?php include '../footer/footer.php'; ?>

</body>
</html>