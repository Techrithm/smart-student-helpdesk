<?php
// backend/auth/check_session.php
require_once '../config/session.php';
header('Content-Type: application/json');

if (isset($_SESSION['user_id'])) {
    echo json_encode([
        'status' => 'success',
        'logged_in' => true,
        'user' => [
            'id' => $_SESSION['user_id'],
            'role' => $_SESSION['role'],
            'name' => $_SESSION['name']
        ]
    ]);
} else {
    echo json_encode([
        'status' => 'success',
        'logged_in' => false
    ]);
}
?>
