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
    
    // Etiketleri getir
    $stmt = $pdo->prepare('
        SELECT t.*, 
               COUNT(nt.note_id) as note_count
        FROM tags t
        LEFT JOIN note_tags nt ON t.id = nt.tag_id
        WHERE t.user_id = ?
        GROUP BY t.id
        ORDER BY t.created_at DESC
    ');
    $stmt->execute([$user['id']]);
    $tags = $stmt->fetchAll();
    
    echo json_encode(['tags' => $tags]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Etiketler getirilirken hata: ' . $e->getMessage()]);
}
?>
