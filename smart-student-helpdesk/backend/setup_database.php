<?php
// backend/setup_database.php

$host = 'localhost';
$username = 'root';
$password = '';

try {
    // Connect without DB
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connected to MySQL server successfully.<br>";

    // Create DB
    $pdo->exec("CREATE DATABASE IF NOT EXISTS smart_student_helpdesk");
    echo "Database 'smart_student_helpdesk' checked/created.<br>";

    $pdo->exec("USE smart_student_helpdesk");

    // Read Schema
    $sql = file_get_contents(__DIR__ . '/../database/schema.sql');
    
    // Execute Schema - split by ; to handle multiple statements if PDO doesn't like batch
    // Actually PDO exec might handle multiple queries if configured, but safe way is split
    // However, schema.sql has simple structure. passing it usually works if driver allows.
    // Let's try executing full block.
    $pdo->exec($sql);
    echo "Tables created successfully.<br>";

    // Seed Users if not exist
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute(['admin@helpdesk.com']);
    if ($stmt->fetchColumn() == 0) {
        $pwd = password_hash('admin123', PASSWORD_DEFAULT);
        $pdo->exec("INSERT INTO users (name, email, password, role) VALUES ('System Admin', 'admin@helpdesk.com', '$pwd', 'admin')");
        echo "Admin user created (admin@helpdesk.com / admin123)<br>";
    }

    $stmt->execute(['itstaff@helpdesk.com']);
    if ($stmt->fetchColumn() == 0) {
        $pwd = password_hash('staff123', PASSWORD_DEFAULT);
        $pdo->exec("INSERT INTO users (name, email, password, role) VALUES ('IT Staff', 'itstaff@helpdesk.com', '$pwd', 'staff')");
        echo "Staff user created (itstaff@helpdesk.com / staff123)<br>";
    }

    $stmt->execute(['student@helpdesk.com']);
    if ($stmt->fetchColumn() == 0) {
        $pwd = password_hash('student123', PASSWORD_DEFAULT);
        $pdo->exec("INSERT INTO users (name, email, password, role) VALUES ('John Student', 'student@helpdesk.com', '$pwd', 'student')");
        echo "Student user created (student@helpdesk.com / student123)<br>";
    }

    echo "Setup Complete! You can now use the application.";

} catch (PDOException $e) {
    die("DB Setup Error: " . $e->getMessage());
}
?>
