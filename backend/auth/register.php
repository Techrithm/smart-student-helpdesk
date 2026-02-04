<?php
// backend/auth/register.php
require_once '../config/session.php';
require_once '../config/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid Request Method']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
if (!$data && !empty($_POST)) {
    $data = $_POST;
}

$name = $data['name'] ?? '';
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';
$role = 'student'; // Only students register openly

if (empty($name) || empty($email) || empty($password)) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
    exit;
}

try {
    // Check if email exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Email already registered']);
        exit;
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$name, $email, $hashed_password, $role])) {
        echo json_encode(['status' => 'success', 'message' => 'Registration Successful']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Registration Failed']);
    }

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database Error: ' . $e->getMessage()]);
}
?>
