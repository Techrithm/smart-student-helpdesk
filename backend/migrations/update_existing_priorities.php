<?php
// backend/migrations/update_existing_priorities.php
require_once __DIR__ . '/../config/db.php';

// Function to auto-detect priority based on keywords
function detectPriority($subject, $description) {
    $text = strtolower($subject . ' ' . $description);
    
    // HIGH PRIORITY keywords
    $highKeywords = [
        'water', 'leak', 'leakage', 'flood', 'flooding',
        'fire', 'smoke', 'emergency', 'urgent', 'critical',
        'electricity', 'power', 'outage', 'blackout',
        'security', 'theft', 'stolen', 'break-in', 'unsafe',
        'accident', 'injury', 'medical', 'health', 'hazard',
        'broken', 'damage', 'dangerous', 'immediate',
        'hostel', 'dormitory', 'residence'
    ];
    
    // LOW PRIORITY keywords
    $lowKeywords = [
        'library', 'book', 'books', 'magazine', 'journal',
        'notes', 'syllabus', 'timetable', 'schedule',
        'suggestion', 'feedback', 'improvement', 'request',
        'question', 'inquiry', 'information', 'clarification',
        'update', 'outdated', 'old', 'condition'
    ];
    
    foreach ($highKeywords as $keyword) {
        if (strpos($text, $keyword) !== false) {
            return 'high';
        }
    }
    
    foreach ($lowKeywords as $keyword) {
        if (strpos($text, $keyword) !== false) {
            return 'low';
        }
    }
    
    return 'medium';
}

try {
    // Get all complaints
    $stmt = $conn->query("SELECT id, subject, description, priority FROM complaints");
    $complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $updateStmt = $conn->prepare("UPDATE complaints SET priority = ? WHERE id = ?");
    
    $updated = 0;
    foreach ($complaints as $c) {
        $newPriority = detectPriority($c['subject'], $c['description']);
        if ($newPriority !== $c['priority']) {
            $updateStmt->execute([$newPriority, $c['id']]);
            echo "Updated complaint #{$c['id']} '{$c['subject']}': {$c['priority']} -> {$newPriority}\n";
            $updated++;
        }
    }
    
    echo "\nDone! Updated $updated complaint(s).\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
