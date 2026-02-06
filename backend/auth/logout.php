<?php
// backend/auth/logout.php
require_once '../config/session.php';
// Capture role before destroying session
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';

session_destroy();

// Redirect based on role
switch ($role) {
    case 'student':
        header('Location: ../../frontend/student/login.html');
        break;
    case 'staff':
        header('Location: ../../frontend/staff/login.html');
        break;
    case 'admin':
        header('Location: ../../frontend/admin/login.html');
        break;
    default:
        header('Location: ../../frontend/index.html');
        break;
}
exit;
?>
