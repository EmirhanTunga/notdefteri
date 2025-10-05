<?php
session_start();
if (!isset($_SESSION['username'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

require_once 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $tagId = intval($data['tag_id'] ?? 0);
    
    if (!$tagId) {
        http_response_code(400);
        echo json_encode(['error' => 'Etiket ID gerekli']);
        exit();
    }
    
    try {
        // Kullanıcı ID'sini al
        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->execute([$_SESSION['username']]);
        $user = $stmt->fetch();
        
        // Etiketin kullanıcıya ait olduğunu kontrol et
        $stmt = $pdo->prepare('SELECT id FROM tags WHERE id = ? AND user_id = ?');
        $stmt->execute([$tagId, $user['id']]);
        if (!$stmt->fetch()) {
            http_response_code(403);
            echo json_encode(['error' => 'Bu etikete erişim yetkiniz yok']);
            exit();
        }
        
        // Etiketi sil
        $stmt = $pdo->prepare('DELETE FROM tags WHERE id = ?');
        $stmt->execute([$tagId]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Etiket silindi'
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Etiket silinirken hata: ' . $e->getMessage()]);
    }
}
?>
