<?php
// backend/migrations/add_priority_column.php
require_once __DIR__ . '/../config/db.php';

try {
    // Check if column exists first
    $stmt = $conn->query("SHOW COLUMNS FROM complaints LIKE 'priority'");
    if ($stmt->rowCount() == 0) {
        $conn->exec("ALTER TABLE complaints ADD COLUMN priority ENUM('low', 'medium', 'high') DEFAULT 'medium' AFTER description");
        echo "Priority column added successfully.\n";
    } else {
        echo "Priority column already exists.\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
