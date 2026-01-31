<?php
// backend/test_runner.php

$baseUrl = "http://localhost/smart-student-helpdesk/backend";
$cookieJar = tempnam(sys_get_temp_dir(), 'cookie');

function request($url, $data = [], $cookieJar) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieJar);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieJar);
    
    if (!empty($data)) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    }

    $response = curl_exec($ch);
    if (curl_errno($ch)) die("Curl Error: " . curl_error($ch));
    curl_close($ch);
    return json_decode($response, true);
}

echo "<h2>Starting Automated Test Flow</h2>";

// ==========================================
// 1. STUDENT FLOW
// ==========================================
echo "<h3>1. Student Flow</h3>";

// Login
echo "Logging in as Student... ";
$res = request("$baseUrl/auth/login.php", ['email'=>'student@helpdesk.com', 'password'=>'student123', 'role'=>'student'], $cookieJar);
if ($res['status'] == 'success') echo "<span style='color:green'>OK</span><br>";
else die("<span style='color:red'>Failed: " . $res['message'] . "</span>");

// Raise Complaint
echo "Raising Complaint... ";
$complaintData = [
    'action' => 'create',
    'department_id' => 1, // Assumed IT Dept exists with ID 1
    'subject' => 'Automated Test Ticket',
    'description' => 'This is a test complaint from the automation script.'
];
$res = request("$baseUrl/complaints/api.php", $complaintData, $cookieJar);
if ($res['status'] == 'success') echo "<span style='color:green'>OK</span><br>";
else die("<span style='color:red'>Failed: " . $res['message'] . "</span>");

// Verify List
echo "Verifying Complaint in List... ";
$res = request("$baseUrl/complaints/api.php?action=list", [], $cookieJar);
$complaintId = 0;
if ($res['status'] == 'success' && count($res['data']) > 0) {
    $complaintId = $res['data'][0]['id'];
    echo "<span style='color:green'>Found (ID: $complaintId)</span><br>";
} else {
    die("<span style='color:red'>Failed: Not found in list</span>");
}

// ==========================================
// 2. STAFF FLOW
// ==========================================
echo "<h3>2. Staff Flow</h3>";
// Clear Cookie to Logout
file_put_contents($cookieJar, ''); 

// Login Staff
echo "Logging in as Staff... ";
$res = request("$baseUrl/auth/login.php", ['email'=>'itstaff@helpdesk.com', 'password'=>'staff123', 'role'=>'staff'], $cookieJar);
if ($res['status'] == 'success') echo "<span style='color:green'>OK</span><br>";
else die("<span style='color:red'>Failed: " . $res['message'] . "</span>");

// Reply
echo "Replying to Ticket... ";
$replyData = [
    'action' => 'reply',
    'complaint_id' => $complaintId,
    'message' => 'Issue received. Working on it (Automated).'
];
$res = request("$baseUrl/complaints/api.php", $replyData, $cookieJar);
if ($res['status'] == 'success') echo "<span style='color:green'>OK</span><br>";
else echo "<span style='color:red'>Failed: " . $res['message'] . "</span><br>";

// Resolve
echo "Updating Status to Resolved... ";
$statusData = [
    'action' => 'update_status',
    'complaint_id' => $complaintId,
    'status' => 'resolved'
];
$res = request("$baseUrl/complaints/api.php", $statusData, $cookieJar);
if ($res['status'] == 'success') echo "<span style='color:green'>OK</span><br>";
else echo "<span style='color:red'>Failed: " . $res['message'] . "</span><br>";


// ==========================================
// 3. ADMIN FLOW
// ==========================================
echo "<h3>3. Admin Flow</h3>";
file_put_contents($cookieJar, ''); 

// Login Admin
echo "Logging in as Admin... ";
$res = request("$baseUrl/auth/login.php", ['email'=>'admin@helpdesk.com', 'password'=>'admin123', 'role'=>'admin'], $cookieJar);
if ($res['status'] == 'success') echo "<span style='color:green'>OK</span><br>";
else die("<span style='color:red'>Failed: " . $res['message'] . "</span>");

// Verify
echo "Verifying Status... ";
$res = request("$baseUrl/complaints/api.php?action=details&id=$complaintId", [], $cookieJar);

if ($res['status'] == 'success') {
    $c = $res['data']['complaint'];
    $replies = $res['data']['replies'];
    
    if ($c['status'] === 'resolved') echo "Status: <span style='color:green'>Resolved</span><br>";
    else echo "Status: <span style='color:red'>" . $c['status'] . "</span><br>";
    
    if (count($replies) > 0) echo "Replies: <span style='color:green'>Found " . count($replies) . "</span><br>";
    else echo "Replies: <span style='color:red'>None</span><br>";
} else {
    echo "<span style='color:red'>Failed to fetch details</span>";
}

echo "<h3>Test Completed</h3>";
unlink($cookieJar);
?>
