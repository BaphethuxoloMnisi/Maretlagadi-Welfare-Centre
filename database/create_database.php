<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "maretlagadi_welfare_centre";

// Create Connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " .$conn->connect_error);
} else {
    //echo "Database connected successfully";
}

// Create Users table
/*$sql = "CREATE TABLE IF NOT EXISTS users (
user_id INT AUTO_INCREMENT PRIMARY KEY,
name VARCHAR(100) NOT NULL,
surname VARCHAR(100) NOT NULL,
email VARCHAR(100) NOT NULL UNIQUE,
password VARCHAR(255) NOT NULL,
role ENUM('Admin', 'Volunteer', 'Donor', 'Member') NOT NULL,
phone VARCHAR(20),
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Users table created successfully.<br>";
} else {
    echo "Error creating Users table: " . $conn->error . "<br>";
}*/

// Create messages table
/*$sql = "CREATE TABLE messages (
message_id INT AUTO_INCREMENT PRIMARY KEY,
sender_id INT NOT NULL,
receiver_id INT NOT NULL,
message_text TEXT NOT NULL,
timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

FOREIGN KEY (sender_id) REFERENCES users(user_id),
FOREIGN KEY (receiver_id) REFERENCES users(user_id)
)";

if ($conn->query($sql) === TRUE) {
    echo "Messages table created successfully.<br>";
} else {
    echo "Error creating Messages table: " . $conn->error . "<br>";
}*/

// Create notifications table
/*$sql = "CREATE TABLE IF NOT EXISTS notifications (
notification_id INT AUTO_INCREMENT PRIMARY KEY,
user_id INT NOT NULL,
message TEXT NOT NULL,
status ENUM('Unread','Read') DEFAULT 'Unread',
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

FOREIGN KEY (user_id) REFERENCES users(user_id)
)";

if ($conn->query($sql) === TRUE) {
    echo "Notifications table created successfully.<br>";
} else {
    echo "Error creating Notifications table: " . $conn->error . "<br>";
}*/

// Create volunteers table
/*$sql = "CREATE TABLE IF NOT EXISTS volunteers (
    volunteer_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    skills TEXT,
    availability VARCHAR(100),
    total_hours DECIMAL(5,2) DEFAULT 0,

    FOREIGN KEY (user_id) REFERENCES users(user_id)
)";

if ($conn->query($sql) === TRUE) {
    echo "Volunteers table created successfully.<br>";
} else {
    echo "Error creating Volunteers table: " . $conn->error . "<br>";
}*/


// Create programs table
/*$sql = "CREATE TABLE IF NOT EXISTS programs (
    program_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    target_group VARCHAR(150)
)";

if ($conn->query($sql) === TRUE) {
    echo "Programs table created successfully.<br>";
} else {
    echo "Error creating Programs table: " . $conn->error . "<br>";
}*/


// Create events table
/*$sql = "CREATE TABLE IF NOT EXISTS events (
event_id INT AUTO_INCREMENT PRIMARY KEY,
program_id INT NOT NULL,
name VARCHAR(150) NOT NULL,
description TEXT,
date DATE,
location VARCHAR(255),

FOREIGN KEY (program_id) REFERENCES programs(program_id)
)";

if ($conn->query($sql) === TRUE) {
    echo "Events table created successfully.<br>";
} else {
    echo "Error creating Events table: " . $conn->error . "<br>";
}*/


// ==== Create volunteer shifts table ====
/*$sql = "CREATE TABLE IF NOT EXISTS volunteer_shifts (
    shift_id INT AUTO_INCREMENT PRIMARY KEY,
    volunteer_id INT NOT NULL,
    event_id INT NOT NULL,
    hours_worked DECIMAL(5,2),
    date DATE,

    FOREIGN KEY (volunteer_id) REFERENCES volunteers(volunteer_id),
    FOREIGN KEY (event_id) REFERENCES events(event_id)
)";

if ($conn->query($sql) === TRUE) {
    echo "Volunteer Shifts table created successfully.<br>";
} else {
    echo "Error creating Volunteer Shifts table: " . $conn->error . "<br>";
}*/

// ==== Create donation table ====
/*$sql = "CREATE TABLE IF NOT EXISTS donations (
    donation_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    date DATE,
    payment_ref VARCHAR(100),

    FOREIGN KEY (user_id) REFERENCES users(user_id)
)";

if ($conn->query($sql) === TRUE) {
    echo "Donations table created successfully.<br>";
} else {
    echo "Error creating Donations table: " . $conn->error . "<br>";
}*/

// Run these against your existing database (the one create_database.php connects to).
// Adjust ENGINE/CHARSET if the rest of your schema uses something specific.

$sql = "CREATE TABLE IF NOT EXISTS donations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(30),
    amount DECIMAL(10,2) NOT NULL,
    payment_status ENUM('pending','complete','failed','cancelled') DEFAULT 'pending',
    pf_payment_id VARCHAR(100) DEFAULT NULL,
    m_payment_id VARCHAR(100) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)"; //ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

$sql = "CREATE TABLE IF NOT EXISTS volunteers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    area_of_interest VARCHAR(100),
    availability VARCHAR(100),
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)"; //ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

$sql = "CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    subject VARCHAR(200),
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)"; // ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

$sql = "CREATE TABLE IF NOT EXISTS gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image_path VARCHAR(255) NOT NULL,
    caption VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)" // ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

//$conn->close();
?>