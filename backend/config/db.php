<?php
// backend/config/db.php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

$host = 'localhost';
$db_name = 'smart_student_helpdesk';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;port=3307;dbname=$db_name", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // If database doesn't exist, we might want to try connecting without dbname to create it, 
    // but usually schema.sql handles creation. 
    // For now, fail gracefully or output JSON error.
    echo json_encode(["status" => "error", "message" => "Connection Error: " . $e->getMessage()]);
    exit();
}
?>
