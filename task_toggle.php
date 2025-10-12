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
$type = $input['type'] ?? '';
$taskId = intval($input['task_id'] ?? 0);
$isDone = $input['is_done'] ?? false;

if (empty($type) || $taskId <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid parameters']);
    exit();
}

try {
    $tableName = '';
    switch($type) {
        case 'daily':
            $tableName = 'daily_tasks';
            break;
        case 'weekly':
            $tableName = 'weekly_tasks';
            break;
        case 'monthly':
            $tableName = 'monthly_tasks';
            break;
        default:
            echo json_encode(['success' => false, 'error' => 'Invalid task type']);
            exit();
    }
    
    // Görevin kullanıcıya ait olduğunu kontrol et
    $stmt = $pdo->prepare("SELECT id FROM {$tableName} WHERE id = ? AND user_id = ?");
    $stmt->execute([$taskId, $userId]);
    $task = $stmt->fetch();
    
    if (!$task) {
        echo json_encode(['success' => false, 'error' => 'Task not found or access denied']);
        exit();
    }
    
    // Görev durumunu güncelle
    $stmt = $pdo->prepare("UPDATE {$tableName} SET is_done = ?, updated_at = NOW() WHERE id = ? AND user_id = ?");
    $result = $stmt->execute([$isDone ? 1 : 0, $taskId, $userId]);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => $isDone ? 'Görev tamamlandı!' : 'Görev tamamlanmadı olarak işaretlendi!'
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Görev durumu güncellenemedi']);
    }
    
} catch (Exception $e) {
    error_log('Task toggle error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?>