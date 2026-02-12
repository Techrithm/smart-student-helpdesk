<?php
// backend/migrations/add_department_to_users.php
require_once '../config/db.php';

try {
    // Add department_id column to users table
    $conn->exec("
        ALTER TABLE users 
        ADD COLUMN department_id INT DEFAULT NULL,
        ADD FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL
    ");
    echo "✓ Added department_id column to users table\n";
    
    // Get IT department ID (assuming it's named 'IT Department')
    $stmt = $conn->query("SELECT id FROM departments WHERE name LIKE '%IT%' LIMIT 1");
    $itDept = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($itDept) {
        $itDeptId = $itDept['id'];
        
        // Assign all existing staff users to IT department
        $stmt = $conn->prepare("UPDATE users SET department_id = ? WHERE role = 'staff'");
        $stmt->execute([$itDeptId]);
        $count = $stmt->rowCount();
        
        echo "✓ Updated {$count} staff user(s) to IT department (ID: {$itDeptId})\n";
    } else {
        echo "⚠ Warning: IT department not found. Please manually assign staff to departments.\n";
    }
    
    echo "\n✓ Migration completed successfully!\n";
    
} catch (PDOException $e) {
    echo "✗ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
?>
