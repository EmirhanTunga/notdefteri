<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['username'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

require_once 'db.php';

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
$type = $_GET['type'] ?? 'daily';

try {
    $tasks = [];
    
    switch($type) {
        case 'daily':
            $stmt = $pdo->prepare('SELECT * FROM daily_tasks WHERE user_id = ? ORDER BY created_at DESC');
            $stmt->execute([$userId]);
            $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
            
        case 'weekly':
            $stmt = $pdo->prepare('SELECT * FROM weekly_tasks WHERE user_id = ? ORDER BY created_at DESC');
            $stmt->execute([$userId]);
            $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
            
        case 'monthly':
            $stmt = $pdo->prepare('SELECT * FROM monthly_tasks WHERE user_id = ? ORDER BY created_at DESC');
            $stmt->execute([$userId]);
            $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
    }
    
    echo json_encode([
        'success' => true,
        'tasks' => $tasks,
        'count' => count($tasks)
    ]);
    
} catch (Exception $e) {
    error_log('Task get error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?>
