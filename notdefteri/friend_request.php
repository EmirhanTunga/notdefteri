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
    
    $friendUsername = trim($data['username'] ?? '');
    
    if (empty($friendUsername)) {
        http_response_code(400);
        echo json_encode(['error' => 'Kullanıcı adı gerekli']);
        exit();
    }
    
    try {
        // Mevcut kullanıcı ID'sini al
        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->execute([$_SESSION['username']]);
        $currentUser = $stmt->fetch();
        
        // Arkadaş olacak kullanıcıyı bul
        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->execute([$friendUsername]);
        $friendUser = $stmt->fetch();
        
        if (!$friendUser) {
            http_response_code(404);
            echo json_encode(['error' => 'Kullanıcı bulunamadı']);
            exit();
        }
        
        if ($currentUser['id'] === $friendUser['id']) {
            http_response_code(400);
            echo json_encode(['error' => 'Kendinize arkadaşlık isteği gönderemezsiniz']);
            exit();
        }
        
        // Zaten arkadaş mı veya bekleyen istek var mı kontrol et
        $stmt = $pdo->prepare('
            SELECT * FROM friendships 
            WHERE (user_id = ? AND friend_id = ?) 
               OR (user_id = ? AND friend_id = ?)
        ');
        $stmt->execute([$currentUser['id'], $friendUser['id'], $friendUser['id'], $currentUser['id']]);
        $existing = $stmt->fetch();
        
        if ($existing) {
            if ($existing['status'] === 'accepted') {
                http_response_code(400);
                echo json_encode(['error' => 'Zaten arkadaşsınız']);
                exit();
            } elseif ($existing['status'] === 'pending') {
                http_response_code(400);
                echo json_encode(['error' => 'Bekleyen bir istek var']);
                exit();
            }
        }
        
        // Arkadaşlık isteği gönder
        $stmt = $pdo->prepare('INSERT INTO friendships (user_id, friend_id, status) VALUES (?, ?, ?)');
        $stmt->execute([$currentUser['id'], $friendUser['id'], 'pending']);
        
        echo json_encode([
            'success' => true,
            'message' => 'Arkadaşlık isteği gönderildi'
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'İstek gönderilirken hata: ' . $e->getMessage()]);
    }
}
?>
