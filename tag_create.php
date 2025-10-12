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
    
    $name = trim($data['name'] ?? '');
    $color = trim($data['color'] ?? '#4a90e2');
    
    if (empty($name)) {
        http_response_code(400);
        echo json_encode(['error' => 'Etiket adı gerekli']);
        exit();
    }
    
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
        
        // Etiketi oluştur
        $stmt = $pdo->prepare('INSERT INTO tags (user_id, name, color) VALUES (?, ?, ?)');
        $stmt->execute([$user['id'], $name, $color]);
        $tagId = $pdo->lastInsertId();
        
        echo json_encode([
            'success' => true,
            'tag_id' => $tagId,
            'message' => 'Etiket başarıyla oluşturuldu'
        ]);
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            http_response_code(400);
            echo json_encode(['error' => 'Bu etiket zaten mevcut']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Etiket oluşturulurken hata: ' . $e->getMessage()]);
        }
    }
}
?>
