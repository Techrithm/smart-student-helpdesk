<?php
// backend/auth/logout.php
require_once '../config/session.php';

// Prevent caching
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Capture role before destroying session
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';

// Clear session data
$_SESSION = array();

// Delete the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destroy the session
session_destroy();

// Redirect to dashboard
header('Location: ../../frontend/index.html');
exit;
?>
