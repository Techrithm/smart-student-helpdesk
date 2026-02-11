<?php
// backend/auth/check_session.php
require_once '../config/session.php';

// Prevent caching of session validation results
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

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
