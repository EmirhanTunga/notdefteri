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

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['query'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit();
}

$query = trim($_POST['query']);
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
    $results = [
        'notes' => [],
        'tasks' => []
    ];
    
    // Search in notes
    $noteStmt = $pdo->prepare('
        SELECT id, note, created_at, tags, is_favorite 
        FROM notes 
        WHERE user_id = ? AND (note LIKE ? OR tags LIKE ?) 
        ORDER BY created_at DESC 
        LIMIT 10
    ');
    $searchTerm = '%' . $query . '%';
    $noteStmt->execute([$userId, $searchTerm, $searchTerm]);
    $results['notes'] = $noteStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Search in daily tasks
    $dailyTaskStmt = $pdo->prepare('
        SELECT id, task, created_at, is_done, is_favorite, "daily" as type
        FROM daily_tasks 
        WHERE user_id = ? AND task LIKE ? 
        ORDER BY created_at DESC 
        LIMIT 5
    ');
    $dailyTaskStmt->execute([$userId, $searchTerm]);
    $dailyTasks = $dailyTaskStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Search in weekly tasks
    $weeklyTaskStmt = $pdo->prepare('
        SELECT id, task, created_at, is_done, is_favorite, week_start, "weekly" as type
        FROM weekly_tasks 
        WHERE user_id = ? AND task LIKE ? 
        ORDER BY created_at DESC 
        LIMIT 5
    ');
    $weeklyTaskStmt->execute([$userId, $searchTerm]);
    $weeklyTasks = $weeklyTaskStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Combine and sort tasks
    $allTasks = array_merge($dailyTasks, $weeklyTasks);
    usort($allTasks, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
    
    $results['tasks'] = array_slice($allTasks, 0, 10);
    
    echo json_encode([
        'success' => true,
        'data' => $results,
        'query' => $query
    ]);
    
} catch (Exception $e) {
    error_log('Search error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Search failed']);
}
?>