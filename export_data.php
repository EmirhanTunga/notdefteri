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
    $exportData = [
        'export_date' => date('Y-m-d H:i:s'),
        'user_info' => [
            'username' => $_SESSION['username'],
            'export_version' => '1.0'
        ],
        'data' => []
    ];
    
    // Export notes
    $stmt = $pdo->prepare('SELECT * FROM notes WHERE user_id = ? ORDER BY created_at DESC');
    $stmt->execute([$userId]);
    $exportData['data']['notes'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Export daily tasks
    $stmt = $pdo->prepare('SELECT * FROM daily_tasks WHERE user_id = ? ORDER BY created_at DESC');
    $stmt->execute([$userId]);
    $exportData['data']['daily_tasks'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Export weekly tasks
    $stmt = $pdo->prepare('SELECT * FROM weekly_tasks WHERE user_id = ? ORDER BY created_at DESC');
    $stmt->execute([$userId]);
    $exportData['data']['weekly_tasks'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Export public notes (only user's own)
    $stmt = $pdo->prepare('SELECT * FROM public_notes WHERE user_id = ? ORDER BY created_at DESC');
    $stmt->execute([$userId]);
    $exportData['data']['public_notes'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Add statistics
    $exportData['statistics'] = [
        'total_notes' => count($exportData['data']['notes']),
        'total_daily_tasks' => count($exportData['data']['daily_tasks']),
        'total_weekly_tasks' => count($exportData['data']['weekly_tasks']),
        'total_public_notes' => count($exportData['data']['public_notes'])
    ];
    
    echo json_encode([
        'success' => true,
        'data' => $exportData
    ]);
    
} catch (Exception $e) {
    error_log('Export data error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Veri dışa aktarma hatası']);
}
?>