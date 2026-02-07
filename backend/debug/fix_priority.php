<?php
// backend/debug/fix_priority.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config/db.php';

try {
    echo "Starting Priority Fix...<br>";

    // Check if column exists first
    $stmt = $conn->query("DESCRIBE complaints priority");
    $exists = $stmt->fetch();

    if (!$exists) {
        // ADD
        $sql = "ALTER TABLE complaints ADD COLUMN priority ENUM('low', 'medium', 'high') DEFAULT 'medium' AFTER description";
        $conn->exec($sql);
        echo "Added 'priority' column successfully.<br>";
    } else {
        echo "'priority' column already exists.<br>";
        
        // Ensure it is ENUM
        $sql = "ALTER TABLE complaints MODIFY COLUMN priority ENUM('low', 'medium', 'high') DEFAULT 'medium'";
        $conn->exec($sql);
        echo "Ensured 'priority' is ENUM.<br>";
        
        // Fix values
        $sql = "UPDATE complaints SET priority = 'medium' WHERE priority NOT IN ('low', 'medium', 'high')";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        echo "Updated invalid priority values.<br>";
    }

    echo "Fix Complete.";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
