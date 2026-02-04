<?php
// backend/complaints/api.php
require_once '../config/session.php';
require_once '../config/db.php';

header('Content-Type: application/json');

// Function to auto-detect priority based on keywords
function detectPriority($subject, $description) {
    $text = strtolower($subject . ' ' . $description);
    
    // HIGH PRIORITY keywords - Urgent/Safety/Critical issues
    $highKeywords = [
        'water', 'leak', 'leakage', 'flood', 'flooding',
        'fire', 'smoke', 'emergency', 'urgent', 'critical',
        'electricity', 'power', 'outage', 'blackout',
        'security', 'theft', 'stolen', 'break-in', 'unsafe',
        'accident', 'injury', 'medical', 'health', 'hazard',
        'broken', 'damage', 'dangerous', 'immediate',
        'hostel', 'dormitory', 'residence'
    ];
    
    // MEDIUM PRIORITY keywords - Important but not urgent
    $mediumKeywords = [
        'fees', 'fee', 'payment', 'deducted', 'refund', 'account', 'accounts',
        'admission', 'certificate', 'document', 'exam', 'result', 'marks',
        'attendance', 'transport', 'bus', 'canteen', 'food'
    ];
    
    // LOW PRIORITY keywords - Minor/Non-urgent issues
    $lowKeywords = [
        'library', 'book', 'books', 'magazine', 'journal',
        'notes', 'syllabus', 'timetable', 'schedule',
        'suggestion', 'feedback', 'improvement', 'request',
        'question', 'inquiry', 'information', 'clarification',
        'outdated', 'old', 'condition'
    ];
    
    // Check for HIGH priority keywords first
    foreach ($highKeywords as $keyword) {
        if (strpos($text, $keyword) !== false) {
            return 'high';
        }
    }
    
    // Check for MEDIUM priority keywords
    foreach ($mediumKeywords as $keyword) {
        if (strpos($text, $keyword) !== false) {
            return 'medium';
        }
    }
    
    // Check for LOW priority keywords
    foreach ($lowKeywords as $keyword) {
        if (strpos($text, $keyword) !== false) {
            return 'low';
        }
    }
    
    // Default to MEDIUM if no keywords match
    return 'medium';
}

// Check Auth
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

$method = $_SERVER['REQUEST_METHOD'];

// Handle GET requests (List, Details)
if ($method === 'GET') {
    $action = $_GET['action'] ?? 'list';
    
    if ($action === 'list') {
        // List my complaints
        if ($role === 'student') {
            $stmt = $conn->prepare("
                SELECT c.*, d.name as department_name 
                FROM complaints c 
                JOIN departments d ON c.department_id = d.id 
                WHERE c.user_id = ? 
                ORDER BY c.created_at DESC
            ");
            $stmt->execute([$user_id]);
        } elseif ($role === 'staff') {
             $stmt = $conn->query("
                SELECT c.*, d.name as department_name, u.name as student_name 
                FROM complaints c 
                JOIN departments d ON c.department_id = d.id
                JOIN users u ON c.user_id = u.id
                ORDER BY FIELD(c.priority, 'high', 'medium', 'low'), c.created_at DESC
             ");
        } else {
            // Admin sees all
             $stmt = $conn->query("
                SELECT c.*, d.name as department_name, u.name as student_name 
                FROM complaints c 
                JOIN departments d ON c.department_id = d.id
                JOIN users u ON c.user_id = u.id
                ORDER BY FIELD(c.priority, 'high', 'medium', 'low'), c.created_at DESC
             ");
        }
        
        if (isset($stmt)) {
            $complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['status' => 'success', 'data' => $complaints]);
        }
        
    } elseif ($action === 'details') {
        $id = $_GET['id'] ?? 0;
        
        $stmt = $conn->prepare("
            SELECT c.*, d.name as department_name, u.name as student_name 
            FROM complaints c 
            JOIN departments d ON c.department_id = d.id 
            JOIN users u ON c.user_id = u.id
            WHERE c.id = ?
        ");
        $stmt->execute([$id]);
        $complaint = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$complaint) {
            echo json_encode(['status' => 'error', 'message' => 'Not Found']);
            exit;
        }
        
        // Security check: Students see only own
        if ($role === 'student' && $complaint['user_id'] != $user_id) {
             echo json_encode(['status' => 'error', 'message' => 'Access Denied']);
             exit;
        }
        
        // Fetch replies
        $stmt = $conn->prepare("
            SELECT r.*, u.name as user_name, u.role as user_role 
            FROM complaint_replies r 
            JOIN users u ON r.user_id = u.id 
            WHERE r.complaint_id = ? 
            ORDER BY r.created_at ASC
        ");
        $stmt->execute([$id]);
        $replies = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'status' => 'success', 
            'data' => [
                'complaint' => $complaint,
                'replies' => $replies
            ]
        ]);
    }
}

// Handle POST requests (Create, Reply, Status Update)
if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $action = $data['action'] ?? 'create';
    
    if ($action === 'create' && $role === 'student') {
        $dept_id = $data['department_id'];
        $subject = $data['subject'];
        $desc = $data['description'];
        $priority = $data['priority'] ?? null;
        
        // Auto-detect priority based on keywords if not explicitly set or if set to 'auto'
        if (empty($priority) || $priority === 'auto') {
            $priority = detectPriority($subject, $desc);
        }
        
        // Validate priority
        if (!in_array($priority, ['low', 'medium', 'high'])) {
            $priority = 'medium';
        }
        
        if (empty($dept_id) || empty($subject) || empty($desc)) {
            echo json_encode(['status' => 'error', 'message' => 'Fields required']);
            exit;
        }
        
        $stmt = $conn->prepare("INSERT INTO complaints (user_id, department_id, subject, description, priority) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$user_id, $dept_id, $subject, $desc, $priority])) {
            echo json_encode(['status' => 'success', 'message' => 'Complaint Raised', 'priority' => $priority]);
        } else {
             echo json_encode(['status' => 'error', 'message' => 'Failed to raise complaint']);
        }
    }
    
    elseif ($action === 'reply') {
        $complaint_id = $data['complaint_id'];
        $message = $data['message'];
        
        if (empty($complaint_id) || empty($message)) {
             echo json_encode(['status' => 'error', 'message' => 'Message required']);
             exit;
        }
        
        // Add reply
        $stmt = $conn->prepare("INSERT INTO complaint_replies (complaint_id, user_id, message) VALUES (?, ?, ?)");
        $stmt->execute([$complaint_id, $user_id, $message]);
        
        echo json_encode(['status' => 'success', 'message' => 'Reply added']);
    }
    
    elseif ($action === 'update_status' && ($role === 'staff' || $role === 'admin')) {
        $complaint_id = $data['complaint_id'];
        $new_status = $data['status'];
        
        $stmt = $conn->prepare("UPDATE complaints SET status = ? WHERE id = ?");
        if ($stmt->execute([$new_status, $complaint_id])) {
            
            // Log history
            $stmt = $conn->prepare("INSERT INTO status_history (complaint_id, status, changed_by) VALUES (?, ?, ?)");
            $stmt->execute([$complaint_id, $new_status, $user_id]);
            
            echo json_encode(['status' => 'success', 'message' => 'Status Updated']);
        } else {
             echo json_encode(['status' => 'error', 'message' => 'Failed to update']);
        }
    }
}
?>
