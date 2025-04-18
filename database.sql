-- Create the database
CREATE DATABASE greenconnect;
USE greenconnect;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    user_type ENUM('user', 'organization', 'community_leader') NOT NULL DEFAULT 'user',
    org_name VARCHAR(100) DEFAULT NULL,
    community_name VARCHAR(100) DEFAULT NULL,
    location VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create events table
CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    organizer_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    contact_email VARCHAR(100),
    contact_phone VARCHAR(20),
    start_datetime DATETIME NOT NULL,
    end_datetime DATETIME,
    address VARCHAR(255) NOT NULL,
    city VARCHAR(100) NOT NULL,
    district VARCHAR(100) NOT NULL,
    state VARCHAR(100) NOT NULL,
    tree_types TEXT NOT NULL,
    tree_count INT NOT NULL,
    needs_volunteers BOOLEAN DEFAULT FALSE,
    volunteer_count INT,
    description TEXT NOT NULL,
    event_banner_url VARCHAR(500),
    funding_source ENUM('self-funded', 'ngo-backed', 'govt-backed', 'sponsored') NOT NULL,
    partner_organizations TEXT,
    event_type ENUM('school-drive', 'community-planting', 'csr', 'urban-forestry', 'other') NOT NULL,
    expected_impact TEXT,
    has_permission BOOLEAN NOT NULL DEFAULT FALSE,
    recurrence_type ENUM('one-time', 'weekly', 'monthly') NOT NULL DEFAULT 'one-time',
    status ENUM('pending', 'approved', 'rejected', 'completed') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (organizer_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE event_participants (
    id INT PRIMARY KEY AUTO_INCREMENT,
    event_id INT NOT NULL,
    user_id INT NOT NULL,
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    UNIQUE KEY unique_participant (event_id, user_id)
);

CREATE TABLE contact_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('new', 'read', 'replied') DEFAULT 'new'
); 