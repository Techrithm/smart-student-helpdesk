<?php
require_once __DIR__ . '/../config/db.php';
$conn->exec("UPDATE complaints SET priority = 'medium' WHERE id = 7");
echo "Complaint #7 updated to medium priority\n";
?>
