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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit();
}

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
    // Start transaction
    $pdo->beginTransaction();
    
    // Delete all user data
    $tables = ['notes', 'daily_tasks', 'weekly_tasks', 'public_notes'];
    
    foreach ($tables as $table) {
        $stmt = $pdo->prepare("DELETE FROM {$table} WHERE user_id = ?");
        $stmt->execute([$userId]);
    }
    
    // Also delete related data
    $stmt = $pdo->prepare('DELETE FROM public_likes WHERE user_id = ?');
    $stmt->execute([$userId]);
    
    $stmt = $pdo->prepare('DELETE FROM public_comments WHERE user_id = ?');
    $stmt->execute([$userId]);
    
    $stmt = $pdo->prepare('DELETE FROM friends WHERE user_id = ? OR friend_id = ?');
    $stmt->execute([$userId, $userId]);
    
    $stmt = $pdo->prepare('DELETE FROM messages WHERE sender_id = ? OR receiver_id = ?');
    $stmt->execute([$userId, $userId]);
    
    // Commit transaction
    $pdo->commit();
    
    echo json_encode(['success' => true, 'message' => 'Tüm veriler başarıyla silindi']);
    
} catch (Exception $e) {
    // Rollback transaction
    $pdo->rollBack();
    
    error_log('Delete all data error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Veri silme hatası']);
}
?>