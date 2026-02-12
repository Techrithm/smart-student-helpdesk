&lt;?php
// backend/reset_auto_increment.php
// This script resets the AUTO_INCREMENT value for specified tables

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'smart_student_helpdesk';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo-&gt;setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "&lt;h2&gt;Reset Auto-Increment IDs&lt;/h2&gt;";
    echo "&lt;p&gt;This will reset the AUTO_INCREMENT counter to 1 for selected tables.&lt;/p&gt;";
    echo "&lt;p&gt;&lt;strong&gt;WARNING:&lt;/strong&gt; Make sure to DELETE all records first, or this may cause ID conflicts!&lt;/p&gt;";
    
    // List of tables to reset (you can comment out tables you don't want to reset)
    $tables = [
        'users',
        'complaints',
        'complaint_replies',
        'departments',
        'status_history'
    ];

    echo "&lt;hr&gt;";

    foreach ($tables as $table) {
        // First, check if table has any records
        $stmt = $pdo-&gt;query("SELECT COUNT(*) as count FROM $table");
        $count = $stmt-&gt;fetch(PDO::FETCH_ASSOC)['count'];
        
        echo "&lt;div style='margin: 10px 0; padding: 10px; background: #f0f0f0; border-left: 4px solid #007bff;'&gt;";
        echo "&lt;strong&gt;Table: $table&lt;/strong&gt;&lt;br&gt;";
        echo "Current record count: $count&lt;br&gt;";
        
        if ($count &gt; 0) {
            echo "&lt;span style='color: orange;'&gt;⚠️ Table has $count records. Resetting AUTO_INCREMENT may cause issues if you add new records.&lt;/span&gt;&lt;br&gt;";
            echo "&lt;span style='color: red;'&gt;Skipping this table. Please delete all records first if you want to reset it.&lt;/span&gt;";
        } else {
            // Reset AUTO_INCREMENT to 1
            $pdo-&gt;exec("ALTER TABLE $table AUTO_INCREMENT = 1");
            echo "&lt;span style='color: green;'&gt;✓ AUTO_INCREMENT reset to 1&lt;/span&gt;";
        }
        
        echo "&lt;/div&gt;";
    }

    echo "&lt;hr&gt;";
    echo "&lt;p&gt;&lt;strong&gt;Done!&lt;/strong&gt;&lt;/p&gt;";
    echo "&lt;p&gt;&lt;a href='../index.php'&gt;Return to Home&lt;/a&gt;&lt;/p&gt;";

} catch (PDOException $e) {
    die("&lt;div style='color: red; padding: 20px; background: #ffe6e6; border: 1px solid red;'&gt;" .
        "&lt;strong&gt;Error:&lt;/strong&gt; " . $e-&gt;getMessage() . 
        "&lt;/div&gt;");
}
?&gt;
