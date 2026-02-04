<?php
// backend/admin/api.php
require_once '../config/session.php';
require_once '../config/db.php';

header('Content-Type: application/json');

// Check Admin Auth
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $action = $_GET['action'] ?? 'dashboard_stats';

    if ($action === 'dashboard_stats') {
        // Fetch counts
        $stats = [];
        $stats['total_complaints'] = $conn->query("SELECT COUNT(*) FROM complaints")->fetchColumn();
        $stats['pending'] = $conn->query("SELECT COUNT(*) FROM complaints WHERE status='pending'")->fetchColumn();
        $stats['resolved'] = $conn->query("SELECT COUNT(*) FROM complaints WHERE status='resolved'")->fetchColumn();
        $stats['users'] = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
        echo json_encode(['status' => 'success', 'data' => $stats]);
    }
    
    elseif ($action === 'get_departments') {
        $stmt = $conn->query("SELECT * FROM departments ORDER BY name ASC");
        echo json_encode(['status' => 'success', 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    }

    elseif ($action === 'get_users') {
        $stmt = $conn->query("SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC");
        echo json_encode(['status' => 'success', 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    }
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $action = $data['action'] ?? '';

    if ($action === 'add_department') {
        $name = $data['name'];
        if (!empty($name)) {
            $stmt = $conn->prepare("INSERT INTO departments (name) VALUES (?)");
            if($stmt->execute([$name])) {
                echo json_encode(['status' => 'success', 'message' => 'Department Added']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to add']);
            }
        }
    }
    
    elseif ($action === 'delete_department') {
        $id = $data['id'];
        if (!empty($id)) {
            $stmt = $conn->prepare("DELETE FROM departments WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['status' => 'success', 'message' => 'Department Deleted']);
        }
    }
    
    elseif ($action === 'delete_user') {
        $id = $data['id'];
        if (!empty($id)) {
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['status' => 'success', 'message' => 'User Deleted']);
        }
    }
}
?>
