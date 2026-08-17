<?php
$pageTitle = "Our Programmes";
include 'includes/header.php';
?>

<style>
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

    @media (max-width: 991px) {
        .programme-img {
            height: 200px;
        }
    }

    @media (max-width: 767px) {
        .programme-img {
            height: 220px;
        }
    }

    @media (max-width: 480px) {
        .programme-img {
            height: 200px;
        }
    }
</style>

<section class="py-5">
    <div class="container">

        <h1 class="fw-bold text-center">
            Our Programmes
        </h1>

        <p class="text-secondary text-center mx-auto mt-3"
           style="max-width:700px;">
            Explore the initiatives we run to support education, skills
            development, and outreach in our community.
        </p>

        <div class="row g-4 mt-4">

            <!-- Education Support -->
            <div class="col-md-6 col-lg-4">
                <div class="programme-card h-100">

                    <div class="programme-img">
                        <img
                            src="images\education.jpeg"
                            alt="Education Support"
                        >
                    </div>

                    <div class="p-3">
                        <span class="badge bg-light text-dark border mb-2">
                            Ongoing
                        </span>

                        <h5 class="fw-semibold">
                            Education Support
                        </h5>

                        <p class="text-secondary small">
                            Providing educational resources, tutoring, and
                            scholarships to help students achieve their
                            academic goals.
                        </p>

                        <a href="volunteer.php"
                           class="btn btn-sm btn-brand">
                            Get Involved
                        </a>
                    </div>

                </div>
            </div>


            <!-- Skills Development -->
            <div class="col-md-6 col-lg-4">
                <div class="programme-card h-100">

                    <div class="programme-img">
                        <img
                            src="images\skills.jpeg"
                            alt="Skills Development"
                        >
                    </div>

                    <div class="p-3">
                        <span class="badge bg-light text-dark border mb-2">
                            Weekly
                        </span>

                        <h5 class="fw-semibold">
                            Skills Development
                        </h5>

                        <p class="text-secondary small">
                            Empowering community members with practical
                            skills training and vocational education for
                            sustainable employment.
                        </p>

                        <a href="volunteer.php"
                           class="btn btn-sm btn-brand">
                            Get Involved
                        </a>
                    </div>

                </div>
            </div>


            <!-- Community Outreach -->
            <div class="col-md-6 col-lg-4">
                <div class="programme-card h-100">

                    <div class="programme-img">
                        <img
                            src="images\community.jpeg"
                            alt="Community Outreach"
                        >
                    </div>

                    <div class="p-3">
                        <span class="badge bg-light text-dark border mb-2">
                            Monthly
                        </span>

                        <h5 class="fw-semibold">
                            Community Outreach
                        </h5>

                        <p class="text-secondary small">
                            Connecting with families to provide support
                            services, food security, and healthcare access
                            initiatives.
                        </p>

                        <a href="volunteer.php"
                           class="btn btn-sm btn-brand">
                            Get Involved
                        </a>
                    </div>

                </div>
            </div>


            <!-- Youth Mentorship -->
            <div class="col-md-6 col-lg-4">
                <div class="programme-card h-100">

                    <div class="programme-img">
                        <img
                            src="images\youth.jpeg"
                            alt="Youth Mentorship"
                        >
                    </div>

                    <div class="p-3">
                        <span class="badge bg-light text-dark border mb-2">
                            Bi-weekly
                        </span>

                        <h5 class="fw-semibold">
                            Youth Mentorship
                        </h5>

                        <p class="text-secondary small">
                            Pairing young people with mentors who provide
                            guidance, encouragement, and life-skills
                            coaching.
                        </p>

                        <a href="volunteer.php"
                           class="btn btn-sm btn-brand">
                            Get Involved
                        </a>
                    </div>

                </div>
            </div>


            <!-- Elderly Care Support -->
            <div class="col-md-6 col-lg-4">
                <div class="programme-card h-100">

                    <div class="programme-img">
                        <img
                            src="images\elderly.jpeg"
                            alt="Elderly Care Support"
                        >
                    </div>

                    <div class="p-3">
                        <span class="badge bg-light text-dark border mb-2">
                            Weekly
                        </span>

                        <h5 class="fw-semibold">
                            Elderly Care Support
                        </h5>

                        <p class="text-secondary small">
                            Bringing care and comfort to elderly members
                            of the community through visits and assistance.
                        </p>

                        <a href="volunteer.php"
                           class="btn btn-sm btn-brand">
                            Get Involved
                        </a>
                    </div>

                </div>
            </div>


            <!-- Environmental Awareness -->
            <div class="col-md-6 col-lg-4">
                <div class="programme-card h-100">

                    <div class="programme-img">
                        <img
                            src="images\environmental.jpeg"
                            alt="Environmental Awareness"
                        >
                    </div>

                    <div class="p-3">
                        <span class="badge bg-light text-dark border mb-2">
                            Monthly
                        </span>

                        <h5 class="fw-semibold">
                            Environmental Awareness
                        </h5>

                        <p class="text-secondary small">
                            Promoting a cleaner and greener environment
                            through clean-up drives and education.
                        </p>

                        <a href="volunteer.php"
                           class="btn btn-sm btn-brand">
                            Get Involved
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>