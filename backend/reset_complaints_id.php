&lt;?php
// backend/reset_complaints_id.php
// This script deletes all complaints and resets the ID to start from 1

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'smart_student_helpdesk';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo-&gt;setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "&lt;h2&gt;Reset Complaints Table ID&lt;/h2&gt;";
    
    // First, delete all complaint-related records (due to foreign keys)
    echo "&lt;p&gt;Deleting all complaint replies...&lt;/p&gt;";
    $pdo-&gt;exec("DELETE FROM complaint_replies");
    echo "&lt;p style='color: green;'&gt;✓ Complaint replies deleted&lt;/p&gt;";
    
    echo "&lt;p&gt;Deleting all status history...&lt;/p&gt;";
    $pdo-&gt;exec("DELETE FROM status_history");
    echo "&lt;p style='color: green;'&gt;✓ Status history deleted&lt;/p&gt;";
    
    echo "&lt;p&gt;Deleting all complaints...&lt;/p&gt;";
    $pdo-&gt;exec("DELETE FROM complaints");
    echo "&lt;p style='color: green;'&gt;✓ All complaints deleted&lt;/p&gt;";
    
    // Reset AUTO_INCREMENT to 1
    echo "&lt;p&gt;Resetting AUTO_INCREMENT to 1...&lt;/p&gt;";
    $pdo-&gt;exec("ALTER TABLE complaints AUTO_INCREMENT = 1");
    $pdo-&gt;exec("ALTER TABLE complaint_replies AUTO_INCREMENT = 1");
    $pdo-&gt;exec("ALTER TABLE status_history AUTO_INCREMENT = 1");
    
    echo "&lt;h3 style='color: green;'&gt;✓ SUCCESS!&lt;/h3&gt;";
    echo "&lt;p&gt;The complaints table has been reset. New complaints will start with ID #1.&lt;/p&gt;";
    echo "&lt;p&gt;&lt;a href='../index.php'&gt;Return to Home&lt;/a&gt;&lt;/p&gt;";

} catch (PDOException $e) {
    die("&lt;div style='color: red; padding: 20px; background: #ffe6e6; border: 1px solid red;'&gt;" .
        "&lt;strong&gt;Error:&lt;/strong&gt; " . $e-&gt;getMessage() . 
        "&lt;/div&gt;");
}
?&gt;
