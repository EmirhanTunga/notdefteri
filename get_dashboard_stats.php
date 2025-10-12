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
    $stats = [];
    
    // Total notes count
    $stmt = $pdo->prepare('SELECT COUNT(*) as count FROM notes WHERE user_id = ?');
    $stmt->execute([$userId]);
    $stats['totalNotes'] = (int)$stmt->fetchColumn();
    
    // Completed tasks count (daily + weekly)
    $stmt = $pdo->prepare('
        SELECT 
            (SELECT COUNT(*) FROM daily_tasks WHERE user_id = ? AND is_done = 1) +
            (SELECT COUNT(*) FROM weekly_tasks WHERE user_id = ? AND is_done = 1) as count
    ');
    $stmt->execute([$userId, $userId]);
    $stats['completedTasks'] = (int)$stmt->fetchColumn();
    
    // Favorite notes count
    $stmt = $pdo->prepare('SELECT COUNT(*) as count FROM notes WHERE user_id = ? AND is_favorite = 1');
    $stmt->execute([$userId]);
    $stats['favoriteNotes'] = (int)$stmt->fetchColumn();
    
    // Pending tasks count (daily + weekly)
    $stmt = $pdo->prepare('
        SELECT 
            (SELECT COUNT(*) FROM daily_tasks WHERE user_id = ? AND is_done = 0) +
            (SELECT COUNT(*) FROM weekly_tasks WHERE user_id = ? AND is_done = 0) as count
    ');
    $stmt->execute([$userId, $userId]);
    $stats['pendingTasks'] = (int)$stmt->fetchColumn();
    
    echo json_encode([
        'success' => true,
        'stats' => [
            'total_notes' => $stats['totalNotes'],
            'daily_tasks' => $stats['pendingTasks'],
            'weekly_tasks' => $stats['completedTasks'],
            'friends_count' => 0 // Placeholder for now
        ]
    ]);
    
} catch (Exception $e) {
    error_log('Dashboard stats error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database error']);
}
?>