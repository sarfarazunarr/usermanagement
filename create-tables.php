<?php
require 'config.php';

try {
    // Create users table
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        verified BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Create otps table
    $pdo->exec("CREATE TABLE IF NOT EXISTS otps (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        otp VARCHAR(6) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        expires_at TIMESTAMP NULL,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    // Drop the trigger if it already exists
    $pdo->exec("DROP TRIGGER IF EXISTS before_otps_insert");

    // Create a trigger to set expires_at
    $pdo->exec("CREATE TRIGGER before_otps_insert
        BEFORE INSERT ON otps
        FOR EACH ROW
        SET NEW.expires_at = DATE_ADD(NEW.created_at, INTERVAL 1 HOUR)");

    echo "Tables and trigger created successfully.";
} catch (PDOException $e) {
    echo "Error creating tables: " . $e->getMessage();
}
?>
