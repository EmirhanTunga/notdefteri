<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['username'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit();
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid JSON input']);
    exit();
}

$username = $_SESSION['username'];
$stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
$stmt->execute([$username]);
$user = $stmt->fetch();

if (!$user) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'User not found']);
    exit();
}

$userId = $user['id'];
$title = trim($input['title'] ?? '');
$description = trim($input['description'] ?? '');
$priority = $input['priority'] ?? 'medium';
$duration = intval($input['duration'] ?? 60);
$dueDate = $input['due_date'] ?? null;
$type = $input['type'] ?? 'daily';

// Validation
if (empty($title)) {
    echo json_encode(['success' => false, 'error' => 'Görev başlığı boş olamaz']);
    exit();
}

if (!in_array($priority, ['low', 'medium', 'high'])) {
    $priority = 'medium';
}

if (!in_array($type, ['daily', 'weekly', 'monthly'])) {
    $type = 'daily';
}

try {
    // Parse due date
    $dueDateTime = null;
    if ($dueDate) {
        $dueDateTime = new DateTime($dueDate);
    } else {
        // Auto-calculate due date based on type
        $dueDateTime = new DateTime();
        switch($type) {
            case 'daily':
                $dueDateTime->add(new DateInterval('P1D'));
                $dueDateTime->setTime(18, 0, 0);
                break;
            case 'weekly':
                $dueDateTime->add(new DateInterval('P7D'));
                $dueDateTime->setTime(18, 0, 0);
                break;
            case 'monthly':
                $dueDateTime->add(new DateInterval('P1M'));
                $dueDateTime->setTime(18, 0, 0);
                break;
        }
    }

    // Insert task based on type
    switch($type) {
        case 'daily':
            $stmt = $pdo->prepare('INSERT INTO daily_tasks (user_id, task, description, priority, duration_minutes, due_date, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())');
            $stmt->execute([$userId, $title, $description, $priority, $duration, $dueDateTime->format('Y-m-d H:i:s')]);
            break;
            
        case 'weekly':
            $stmt = $pdo->prepare('INSERT INTO weekly_tasks (user_id, task, description, priority, duration_minutes, due_date, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())');
            $stmt->execute([$userId, $title, $description, $priority, $duration, $dueDateTime->format('Y-m-d H:i:s')]);
            break;
            
        case 'monthly':
            $stmt = $pdo->prepare('INSERT INTO monthly_tasks (user_id, task, description, priority, duration_minutes, due_date, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())');
            $stmt->execute([$userId, $title, $description, $priority, $duration, $dueDateTime->format('Y-m-d H:i:s')]);
            break;
    }

    echo json_encode([
        'success' => true, 
        'message' => ucfirst($type) . ' görev başarıyla eklendi!',
        'task' => [
            'title' => $title,
            'description' => $description,
            'priority' => $priority,
            'duration' => $duration,
            'due_date' => $dueDateTime->format('Y-m-d H:i:s'),
            'type' => $type
        ]
    ]);

} catch (Exception $e) {
    error_log('Task add error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Veritabanı hatası: ' . $e->getMessage()]);
}
?> 