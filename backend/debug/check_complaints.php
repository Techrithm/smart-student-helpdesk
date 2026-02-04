<?php
// backend/debug/check_complaints.php
require_once __DIR__ . '/../config/db.php';
header('Content-Type: application/json');

try {
    $stmt = $conn->query('SELECT id, subject, priority, status FROM complaints LIMIT 5');
    $complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['complaints' => $complaints], JSON_PRETTY_PRINT);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
