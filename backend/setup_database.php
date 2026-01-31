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

    // Seed Departments
    $departments = ['IT Department', 'Library', 'Hostel', 'Administration', 'Accounts'];
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM departments WHERE name = ?");
    $insertDept = $pdo->prepare("INSERT INTO departments (name) VALUES (?)");
    
    foreach ($departments as $dept) {
        $stmt->execute([$dept]);
        if ($stmt->fetchColumn() == 0) {
            $insertDept->execute([$dept]);
            echo "Department '$dept' created.<br>";
        }
    }

    // Seed Users
    $checkUser = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $insertUser = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");

    $users = [
        ['System Admin', 'admin@helpdesk.com', 'admin123', 'admin'],
        ['IT Staff', 'itstaff@helpdesk.com', 'staff123', 'staff'],
        ['John Student', 'student@helpdesk.com', 'student123', 'student']
    ];

    foreach ($users as $u) {
        $checkUser->execute([$u[1]]);
        if ($checkUser->fetchColumn() == 0) {
            $pwd = password_hash($u[2], PASSWORD_DEFAULT);
            $insertUser->execute([$u[0], $u[1], $pwd, $u[3]]);
            echo "User '{$u[0]}' ({$u[3]}) created.<br>";
        } else {
            echo "User '{$u[0]}' already exists. [Skipped]<br>";
        }
    }

    echo "Setup Complete! You can now use the application.";

} catch (PDOException $e) {
    die("Setup Error: " . $e->getMessage());
}
?>
