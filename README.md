# Maretlagadi-Welfare-Centre
Maretlagadi Welfare centre is a community-focused platform developed to support the activities and services of Maretlagadi Welfare Centre. The project consists of a web-based platform and an Android mobile application. The system is designed to provide information about the organisation and its programmes while also supporting activities such as volunteering, donations, announcements and communication.

# Project Overview
The Maretlagadi Welfare Centre project is divided into two main components:
1. Website - A PHP-based web application that provides the public-facing website and administrative functionality.
2. Mobile Application - An Android application developed using Kotlin and Jetpack Compose.

Keeping the two applications in separate directories makes the repository easier to maintain and allows the web and mobile development teams to work independently without the project files conflicting with one another.

# Repository Structure
Maretlagadi-Welfare-Centre/ 
│ ├── Website/ 
│   ├── admin/ 
│   ├── css/ 
│   ├── database/ 
│   ├── images/ 
│   ├── includes/ 
│   ├── js/ 
│   ├── maretlagadi/ 
│   ├── uploads/ 
│   ├── about.php 
│   ├── announcement.php 
│   ├── announcements.php 
│   ├── contact.php 
│   ├── donate.php 
│   ├── donate-success.php 
│   ├── exported_database.sql 
│   ├── gallery.php 
│   ├── index.php 
│   ├── programmes.php 
│   ├── verify_donation.php 
│   └── volunteer.php 
|
│ ├── Mobile-App/ 
│   └── MaretlagadiWelfareCentre/ 
│        ├── app/ 
│        ├── gradle/ 
│        ├── build.gradle.kts 
│        ├── gradle.properties 
│        ├── gradlew 
│        ├── gradlew.bat 
│        └── settings.gradle.kts 
│ └── README.md

# Website
The website is located in the Website directory.

# Main Features

The website includes functionality for:

- Viewing information about Maretlagadi Welfare Centre
- Viewing programmes and community initiatives
- Viewing announcements
- Viewing the organisation's gallery
- Contacting the organisation
- Volunteering
- Making donations
- Donation verification
- Administrative management
- Managing announcements
- Managing donations
- Managing volunteers
- Managing messages
- Technologies

The website was developed using:

- PHP
- MySQL
- HTML5
- CSS3
- JavaScript
- Bootstrap
- Running the Website Locally

The website can be run using a local PHP development environment such as WAMP or XAMPP.

- Install and start Apache and MySQL.
- Place the Website directory inside the web server's document root.
- Create the required database using the provided SQL file:
- Website/exported_database.sql
- Configure the database connection in:
- Website/includes/db.php
- Start Apache and MySQL.
- Open the website through your local server.

For example:

https://maretlagadi-welfare.site.je/index.php

Deployed Website Link:


# Mobile Application

The Android application is located in:

Mobile-App/MaretlagadiWelfareCentre/

The application is developed using Android Studio, Kotlin and Jetpack Compose.

# Technologies
- Kotlin
- Jetpack Compose
- Android SDK
- Gradle
- Android Studio
- Opening the Mobile Application
- Install Android Studio.
- Clone the repository.
- Open Android Studio.
- Select Open.
- Navigate to:
- Mobile-App/MaretlagadiWelfareCentre/
- Allow Android Studio to synchronise the Gradle project.
- Select an Android emulator or connected Android device.
- Build and run the application.

The Android project contains its own Gradle configuration and should be opened from the MaretlagadiWelfareCentre directory.

#Database

The website uses a MySQL database.

The database export is available at:

Website/exported_database.sql

The database connection configuration is located in:

Website/includes/db.php

Developers working on the website should configure their local database credentials in accordance with their local WAMP or XAMPP environment.

Development Guidelines

To keep the project organised, changes should be made within the appropriate project directory.

Website changes

Website-related files should be placed inside:

Website/
Mobile application changes

Android-related files should be placed inside:

Mobile-App/MaretlagadiWelfareCentre/

Avoid placing generated build files, IDE-specific files, or local configuration files into the repository unless they are required by the project.

Before committing changes, developers should check their Git status:

git status

It is recommended to use descriptive commit messages that clearly explain what was changed.

For example:

git add .
git commit -m "Update volunteer registration"
git push origin main
Git Workflow

The project uses Git and GitHub for version control.

The main branch is:

main

Before starting new work, developers should pull the latest changes:

git pull origin main

After completing their changes:

git add .
git commit -m "Describe your changes"
git push origin main

When working on larger features, contributors should consider creating a separate branch before making changes.

# Contributors

This project was developed collaboratively by the members of the Maretlagadi Welfare Centre development team.

GitHub contributors include:

- Baphethuxolo Mnisi
- Vutomii
- Mashaokhomotso
- Com3ly

Individual contributions can be viewed through the repository's Git history and contributor information.

# Project Status

The repository currently contains both the website and Android mobile application.

The project structure separates the two applications into their respective directories to simplify development, maintenance and version control.

Further development may include additional features, improvements to the user interface, security enhancements, database improvements and integration between the web and mobile platforms.

# License

This project was developed as part of an academic/group project for Maretlagadi Welfare Centre.

Unless otherwise specified by the project team, the source code and associated project materials should not be redistributed or used outside the intended project purposes without permission from the project owners.
