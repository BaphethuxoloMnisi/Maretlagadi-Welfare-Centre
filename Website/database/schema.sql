-- Maretlagadi Welfare Centre Database Schema
-- Matches ERD from SS1 (Section 11: Module Design, User Inputs, and Data Flows)

CREATE DATABASE IF NOT EXISTS maretlagadi_db;
USE maretlagadi_db;

-- USER: base table for all people who can log in
CREATE TABLE user (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    surname VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,       -- hashed with password_hash()
    role ENUM('admin', 'volunteer', 'public') NOT NULL DEFAULT 'public',
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ADMIN: extends user with admin-specific permissions
CREATE TABLE admin (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    permissions VARCHAR(255) DEFAULT 'standard',
    FOREIGN KEY (user_id) REFERENCES user(user_id) ON DELETE CASCADE
);

-- VOLUNTEER: extends user with volunteer-specific data
CREATE TABLE volunteer (
    volunteer_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    skills VARCHAR(255),
    availability ENUM('weekday', 'weekend', 'both') DEFAULT 'both',
    total_hours DECIMAL(6,2) DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES user(user_id) ON DELETE CASCADE
);

-- PROGRAM: organisational programmes
CREATE TABLE program (
    program_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    target_group VARCHAR(150)
);

-- EVENT: events belonging to a programme
CREATE TABLE event (
    event_id INT AUTO_INCREMENT PRIMARY KEY,
    program_id INT NOT NULL,
    description TEXT,
    date DATE NOT NULL,
    location VARCHAR(150),
    FOREIGN KEY (program_id) REFERENCES program(program_id) ON DELETE CASCADE
);

-- VOLUNTEER_SHIFT: links volunteers to events
CREATE TABLE volunteer_shift (
    shift_id INT AUTO_INCREMENT PRIMARY KEY,
    volunteer_id INT NOT NULL,
    event_id INT NOT NULL,
    hours_worked DECIMAL(5,2) DEFAULT 0,
    date DATE NOT NULL,
    status ENUM('pending', 'accepted', 'declined', 'completed') DEFAULT 'pending',
    FOREIGN KEY (volunteer_id) REFERENCES volunteer(volunteer_id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES event(event_id) ON DELETE CASCADE
);

-- DONATION: donation records (payment handled externally, this just logs it)
CREATE TABLE donation (
    donation_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,                      -- nullable: anonymous donations allowed
    amount DECIMAL(10,2) NOT NULL,
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    payment_ref VARCHAR(100),
    FOREIGN KEY (user_id) REFERENCES user(user_id) ON DELETE SET NULL
);

-- MESSAGE: contact form submissions / in-app chat
CREATE TABLE message (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NULL,
    receiver_id INT NULL,
    sender_name VARCHAR(100),              -- for public contact form (no account)
    sender_email VARCHAR(150),
    message_text TEXT NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES user(user_id) ON DELETE SET NULL,
    FOREIGN KEY (receiver_id) REFERENCES user(user_id) ON DELETE SET NULL
);

-- NOTIFICATION: alerts sent to users
CREATE TABLE notification (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message VARCHAR(255) NOT NULL,
    status ENUM('unread', 'read') DEFAULT 'unread',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user(user_id) ON DELETE CASCADE
);

-- Seed data for testing / demo
INSERT INTO program (name, description, target_group) VALUES
('Education Support', 'Tutoring and scholarships for students.', 'Children & Youth'),
('Skills Development', 'Vocational training for employment.', 'Adults'),
('Community Outreach', 'Food security and healthcare access.', 'Families');

-- Default admin account (password: Admin@123 — change after first login)
INSERT INTO user (name, surname, email, password, role) VALUES
('System', 'Admin', 'admin@maretlagadi.org', '$2y$10$examplehashreplaceonfirstrun', 'admin');