<?php
session_start();
header('Content-Type: application/json');

// Kullanıcı giriş kontrolü
if (!isset($_SESSION['username'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

require_once 'db.php';

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Task ID required']);
    exit();
}

$taskId = (int)$_GET['id'];
$userId = $_SESSION['user_id'] ?? null;

// Get user ID if not in session
if (!$userId) {
    $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
    $stmt->execute([$_SESSION['username']]);
    $user = $stmt->fetch();
    $userId = $user['id'] ?? null;
    $_SESSION['user_id'] = $userId;
}

if (!$userId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'User not found']);
    exit();
}

try {
    // Check daily tasks first
    $stmt = $pdo->prepare('SELECT id FROM daily_tasks WHERE id = ? AND user_id = ?');
    $stmt->execute([$taskId, $userId]);
    
    if ($stmt->fetch()) {
        echo json_encode(['success' => true, 'type' => 'daily']);
        exit();
    }
    
    // Check weekly tasks
    $stmt = $pdo->prepare('SELECT id FROM weekly_tasks WHERE id = ? AND user_id = ?');
    $stmt->execute([$taskId, $userId]);
    
    if ($stmt->fetch()) {
        echo json_encode(['success' => true, 'type' => 'weekly']);
        exit();
    }
    
    // Task not found
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Task not found']);
    
} catch (Exception $e) {
    error_log('Get task type error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database error']);
}
?>