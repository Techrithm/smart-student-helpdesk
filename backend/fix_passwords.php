<?php
// backend/fix_passwords.php
require_once 'config/db.php';

try {
    echo "<h1>Fixing Passwords...</h1>";

    $users = [
        ['email' => 'admin@helpdesk.com', 'pass' => 'admin123'],
        ['email' => 'itstaff@helpdesk.com', 'pass' => 'staff123'],
        ['email' => 'student@helpdesk.com', 'pass' => 'student123']
    ];

    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");

    foreach ($users as $u) {
        $hash = password_hash($u['pass'], PASSWORD_DEFAULT);
        $stmt->execute([$hash, $u['email']]);
        if ($stmt->rowCount() > 0) {
            echo "Updated password for <b>{$u['email']}</b> to: <b>{$u['pass']}</b><br>";
        } else {
             // If rowCount is 0, it means email not found OR password was already same.
             // Let's check if user exists.
             $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
             $check->execute([$u['email']]);
             if($check->fetch()) {
                 echo "Password for <b>{$u['email']}</b> is already correct/active.<br>";
             } else {
                 echo "User <b>{$u['email']}</b> NOT FOUND. Please run setup_database.php first.<br>";
             }
        }
    }

    echo "<h3><a href='../frontend/student/login.html'>Go to Login</a></h3>";

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
