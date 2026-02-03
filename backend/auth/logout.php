<?php
// backend/auth/logout.php
session_start();
session_destroy();
header('Location: ../../frontend/index.html');
exit;
?>
