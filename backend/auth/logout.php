<?php
// backend/auth/logout.php
require_once '../config/session.php';
session_destroy();
header('Location: ../../frontend/index.html');
exit;
?>
