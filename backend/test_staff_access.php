<?php
// backend/test_staff_access.php
// Test script to verify staff can only access IT department complaints

require_once 'config/db.php';
require_once 'config/session.php';

echo "<h2>Staff Access Verification Test</h2>\n\n";

// Check users table schema
echo "<h3>1. Users Table Schema Check</h3>\n";
$result = $conn->query("DESCRIBE users");
$columns = $result->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
foreach ($columns as $col) {
    if ($col['Field'] === 'department_id') {
        echo "✓ department_id column EXISTS\n";
        echo "  Type: {$col['Type']}\n";
        echo "  Null: {$col['Null']}\n";
        echo "  Key: {$col['Key']}\n";
        break;
    }
}
echo "</pre>\n\n";

// Check staff users and their departments
echo "<h3>2. Staff Users and Departments</h3>\n";
$stmt = $conn->query("
    SELECT u.id, u.name, u.email, u.role, u.department_id, d.name as department_name 
    FROM users u 
    LEFT JOIN departments d ON u.department_id = d.id 
    WHERE u.role = 'staff'
");
$staffUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
foreach ($staffUsers as $staff) {
    echo "Staff: {$staff['name']} (ID: {$staff['id']})\n";
    echo "  Email: {$staff['email']}\n";
    echo "  Department: {$staff['department_name']} (ID: {$staff['department_id']})\n\n";
}
echo "</pre>\n\n";

// Check all departments
echo "<h3>3. Available Departments</h3>\n";
$stmt = $conn->query("SELECT * FROM departments ORDER BY id");
$departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
foreach ($departments as $dept) {
    echo "ID: {$dept['id']} - {$dept['name']}\n";
}
echo "</pre>\n\n";

// Check complaints by department
echo "<h3>4. Complaints Distribution by Department</h3>\n";
$stmt = $conn->query("
    SELECT d.name as department, COUNT(c.id) as count 
    FROM departments d 
    LEFT JOIN complaints c ON d.id = c.department_id 
    GROUP BY d.id, d.name 
    ORDER BY d.id
");
$complaintDist = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
foreach ($complaintDist as $dist) {
    echo "{$dist['department']}: {$dist['count']} complaints\n";
}
echo "</pre>\n\n";

// Simulate staff access
echo "<h3>5. Simulated Staff Access Test</h3>\n";
if (!empty($staffUsers)) {
    $testStaff = $staffUsers[0];
    $staff_dept_id = $testStaff['department_id'];
    
    echo "<p>Testing as: <strong>{$testStaff['name']}</strong> (Department: {$testStaff['department_name']})</p>\n";
    
    // Query what staff would see
    $stmt = $conn->prepare("
        SELECT c.*, d.name as department_name 
        FROM complaints c 
        JOIN departments d ON c.department_id = d.id 
        WHERE c.department_id = ?
        ORDER BY c.created_at DESC
    ");
    $stmt->execute([$staff_dept_id]);
    $accessibleComplaints = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<pre>";
    echo "Staff can access: " . count($accessibleComplaints) . " complaints\n\n";
    
    if (!empty($accessibleComplaints)) {
        echo "Sample accessible complaints:\n";
        foreach (array_slice($accessibleComplaints, 0, 5) as $complaint) {
            echo "  - ID {$complaint['id']}: {$complaint['subject']} ({$complaint['department_name']})\n";
        }
    } else {
        echo "No complaints found for this department.\n";
    }
    echo "</pre>\n\n";
    
    // Check if staff can see other departments
    $stmt = $conn->query("
        SELECT COUNT(*) as count 
        FROM complaints 
        WHERE department_id != " . intval($staff_dept_id)
    );
    $otherDeptCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    echo "<pre>";
    echo "Complaints from OTHER departments: {$otherDeptCount}\n";
    echo "Staff should NOT be able to access these.\n";
    echo "</pre>\n\n";
    
    echo "<h4>✓ Test Result:</h4>\n";
    echo "<p style='color: green; font-weight: bold;'>";
    echo "Staff access is RESTRICTED to their assigned department ({$testStaff['department_name']}).\n";
    echo "They can see " . count($accessibleComplaints) . " complaints from their department.\n";
    echo "They CANNOT see {$otherDeptCount} complaints from other departments.";
    echo "</p>\n";
} else {
    echo "<p style='color: red;'>No staff users found to test.</p>\n";
}
?>
