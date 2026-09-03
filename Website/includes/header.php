<?php
// header.php - shared navigation for all public pages
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        <?php
        echo isset($pageTitle)
            ? $pageTitle . ' - Maretlagadi Welfare Centre'
            : 'Maretlagadi Welfare Centre';
        ?>
    </title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Google Font -->
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <!-- Website CSS -->
    <link rel="stylesheet" href="css/style.css">


    <!-- Header Logo Styling -->
    <style>

        /* =========================
           HEADER LOGO
           ========================= */

        .header-logo {
            height: 55px;
            width: auto;
            max-width: 180px;
            object-fit: contain;
            display: block;
        }


        /* =========================
           MOBILE LOGO
           ========================= */

        @media (max-width: 767px) {

            .header-logo {
                height: 45px;
                max-width: 150px;
            }

        }


        /* =========================
           NAVIGATION
           ========================= */

        .navbar {
            min-height: 70px;
        }

        .nav-link {
            font-weight: 500;
        }

    </style>

</head>


<body>


<!-- =========================
     NAVIGATION BAR
     ========================= -->

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">

    <div class="container">


        <!-- =========================
             LOGO
             ========================= -->

        <a class="navbar-brand" href="index.php">

            <!--
                CHANGE THIS IMAGE PATH
                TO YOUR LOGO IMAGE
            -->

            <img
                src="images\maretlagadi logo.jpeg"
                alt="Maretlagadi Welfare Centre Logo"
                class="header-logo"
            >

        </a>


        <!-- =========================
             MOBILE MENU BUTTON
             ========================= -->

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navMenu"
            aria-controls="navMenu"
            aria-expanded="false"
            aria-label="Toggle navigation"
        >

            <span class="navbar-toggler-icon"></span>

        </button>


        <!-- =========================
             NAVIGATION LINKS
             ========================= -->

        <div
            class="collapse navbar-collapse justify-content-end"
            id="navMenu"
        >

            <ul class="navbar-nav align-items-lg-center gap-lg-2">


                <!-- HOME -->

                <li class="nav-item">

                    <a
                        class="nav-link"
                        href="index.php"
                    >
                        Home
                    </a>

                </li>


                <!-- ABOUT -->

                <li class="nav-item">

                    <a
                        class="nav-link"
                        href="about.php"
                    >
                        About
                    </a>

                </li>


                <!-- PROGRAMMES -->

                <li class="nav-item">

                    <a
                        class="nav-link"
                        href="programmes.php"
                    >
                        Programmes
                    </a>

                </li>


                <!-- ANNOUNCEMENTS -->

                <li class="nav-item">

                    <a
                        class="nav-link"
                        href="announcements.php"
                    >
                        Announcements
                    </a>

                </li>


                <!-- VOLUNTEER -->

                <li class="nav-item">

                    <a
                        class="nav-link"
                        href="volunteer.php"
                    >
                        Volunteer
                    </a>

                </li>


                <!-- GALLERY -->

                <li class="nav-item">

                    <a
                        class="nav-link"
                        href="gallery.php"
                    >
                        Gallery
                    </a>

                </li>


                <!-- CONTACT -->

                <li class="nav-item">

                    <a
                        class="nav-link"
                        href="contact.php"
                    >
                        Contact
                    </a>

                </li>


                <!-- DONATE -->

                <li class="nav-item">

                    <a
                        class="btn btn-dark rounded-pill px-3 ms-lg-2"
                        href="donate.php"
                    >
                        Donate
                    </a>

                </li>


            </ul>

        </div>

    </div>

</nav>


<!-- =========================
     PAGE CONTENT STARTS HERE
     ========================= -->