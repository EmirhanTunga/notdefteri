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
    
    $requestId = intval($data['request_id'] ?? 0);
    $action = $data['action'] ?? ''; // 'accept' or 'reject'
    
    if (!$requestId || !in_array($action, ['accept', 'reject'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Geçersiz parametreler']);
        exit();
    }
    
    try {
        // Kullanıcı ID'sini al
        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->execute([$_SESSION['username']]);
        $user = $stmt->fetch();
        
        // İsteğin bu kullanıcıya ait olduğunu kontrol et
        $stmt = $pdo->prepare('SELECT * FROM friendships WHERE id = ? AND friend_id = ? AND status = "pending"');
        $stmt->execute([$requestId, $user['id']]);
        $request = $stmt->fetch();
        
        if (!$request) {
            http_response_code(404);
            echo json_encode(['error' => 'İstek bulunamadı']);
            exit();
        }
        
        // İsteği güncelle
        $newStatus = $action === 'accept' ? 'accepted' : 'rejected';
        $stmt = $pdo->prepare('UPDATE friendships SET status = ?, responded_at = NOW() WHERE id = ?');
        $stmt->execute([$newStatus, $requestId]);
        
        echo json_encode([
            'success' => true,
            'message' => $action === 'accept' ? 'Arkadaşlık kabul edildi' : 'Arkadaşlık reddedildi'
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'İşlem sırasında hata: ' . $e->getMessage()]);
    }
}
?>
