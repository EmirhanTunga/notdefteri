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
    
    // Arkadaşları getir (kabul edilmiş istekler)
    $stmt = $pdo->prepare('
        SELECT u.id, u.username, u.email, f.requested_at
        FROM friendships f
        JOIN users u ON (
            CASE 
                WHEN f.user_id = ? THEN u.id = f.friend_id
                WHEN f.friend_id = ? THEN u.id = f.user_id
            END
        )
        WHERE (f.user_id = ? OR f.friend_id = ?)
          AND f.status = "accepted"
        ORDER BY u.username ASC
    ');
    $stmt->execute([$user['id'], $user['id'], $user['id'], $user['id']]);
    $friends = $stmt->fetchAll();
    
    // Gelen istekler
    $stmt = $pdo->prepare('
        SELECT u.id, u.username, u.email, f.id as request_id, f.requested_at
        FROM friendships f
        JOIN users u ON f.user_id = u.id
        WHERE f.friend_id = ? AND f.status = "pending"
        ORDER BY f.requested_at DESC
    ');
    $stmt->execute([$user['id']]);
    $requests = $stmt->fetchAll();
    
    // Gönderilen istekler
    $stmt = $pdo->prepare('
        SELECT u.id, u.username, u.email, f.requested_at
        FROM friendships f
        JOIN users u ON f.friend_id = u.id
        WHERE f.user_id = ? AND f.status = "pending"
        ORDER BY f.requested_at DESC
    ');
    $stmt->execute([$user['id']]);
    $sent = $stmt->fetchAll();
    
    echo json_encode([
        'friends' => $friends,
        'requests' => $requests,
        'sent' => $sent
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Arkadaşlar getirilirken hata: ' . $e->getMessage()]);
}
?>
