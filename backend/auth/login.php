<?php
// backend/auth/login.php
require_once '../config/session.php';
require_once '../config/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid Request Method']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
// If not JSON, check $_POST
if (!$data && !empty($_POST)) {
    $data = $_POST;
}

$email = $data['email'] ?? '';
$password = $data['password'] ?? '';
$role = $data['role'] ?? ''; // Optional: enforce role check if needed, or just login user and return role

if (empty($email) || empty($password)) {
    echo json_encode(['status' => 'error', 'message' => 'Email and Password required']);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Check if role matches if provided
        if (!empty($role) && $user['role'] !== $role) {
             echo json_encode(['status' => 'error', 'message' => 'Access Denied for this role']);
             exit;
        }

        // Set Session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];

        echo json_encode([
            'status' => 'success',
            'message' => 'Login Successful',
            'user' => [
                'id' => $user['id'],
                'name' => $user['name'],
                'role' => $user['role']
            ]
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid Credentials']);
    }

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database Error: ' . $e->getMessage()]);
}
?>
