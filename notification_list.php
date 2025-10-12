<?php
session_start();
if (!isset($_SESSION['username'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

require_once 'db.php';

header('Content-Type: application/json');

try {
    // Kullanıcı ID'sini al
    $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
    $stmt->execute([$_SESSION['username']]);
    $user = $stmt->fetch();
    
    if (!$user) {
        http_response_code(401);
        echo json_encode(['error' => 'Kullanıcı bulunamadı']);
        exit();
    }
    
    // Bildirimleri getir
    $stmt = $pdo->prepare('
        SELECT * FROM notifications 
        WHERE user_id = ? 
        ORDER BY created_at DESC 
        LIMIT 50
    ');
    $stmt->execute([$user['id']]);
    $notifications = $stmt->fetchAll();
    
    // Okunmamış bildirim sayısı
    $stmt = $pdo->prepare('SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = FALSE');
    $stmt->execute([$user['id']]);
    $unreadCount = $stmt->fetch()['count'];
    
    echo json_encode([
        'notifications' => $notifications,
        'unread_count' => $unreadCount
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Bildirimler getirilirken hata: ' . $e->getMessage()]);
}
?>
