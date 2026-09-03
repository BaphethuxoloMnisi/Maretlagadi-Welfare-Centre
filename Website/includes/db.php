<?php
// db.php - PDO database connection (Section 2.5 Project Structure / 2.6 Security)

$host = 'localhost';
$dbname = 'maretlagadi_db';
$dbuser = 'root';       // change for production
$dbpass = '';           // change for production

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $dbuser,
        $dbpass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,   // throw exceptions on error
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,            // real prepared statements -> prevents SQL injection
        ]
    );
} catch (PDOException $e) {
    // Never expose raw DB errors to users (Section 2.6 Security Considerations)
    error_log($e->getMessage());
    die("We're experiencing a technical issue. Please try again later.");
}