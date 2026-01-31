<?php
// backend/common/departments.php
require_once '../config/db.php';
header('Content-Type: application/json');

try {
    $stmt = $conn->query("SELECT * FROM departments ORDER BY name ASC");
    $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['status' => 'success', 'data' => $departments]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
