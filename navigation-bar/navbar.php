<?php 



?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Martlagadi Welfare Centre</title>

        <!-- Bootstrap CSS -->
         <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

         <!-- External stylesheet -->
          <link href="../css/style.css" rel="stylesheet">
    </head>

    <body>
<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container">

        <!-- Left Logo -->
        <a href="#" class="navbar-brand">
            <img src="../images/logo.png"
                 alt="Organisation Logo"
                 width="50"
                 height="50"
                 class="d-inline-block align-text-top">
        </a>

        <!-- Mobile Toggler -->
        <button class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarNav"
                aria-controls="navbarNav"
                aria-expanded="false"
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigation -->
        <div class="collapse navbar-collapse" id="navbarNav">

            <!-- Empty space to balance the logo -->
            <div class="flex-fill"></div>

            <!-- Center Navigation Links -->
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link" href="../Pages/index.php">Home</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="../Pages/about_us.php">About</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="../Pages/programs_projects.php">Programmes</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="../Pages/volunteer.php">Volunteer</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="../Pages/contact.php">Contact</a>
                </li>
            </ul>

            <!-- Right Donate Button -->
            <div class="flex-fill d-flex justify-content-end">
                <a href="../Pages/donation.php">
                    <button class="donate-btn">
                        Donate Now
                    </button>
                </a>
            </div>

        </div>

    </div>
</nav>

        <!-- Bootstrap JS -->
         <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
