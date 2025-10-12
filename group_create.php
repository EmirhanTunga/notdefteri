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
    $description = trim($data['description'] ?? '');
    
    if (empty($name)) {
        http_response_code(400);
        echo json_encode(['error' => 'Grup adı gerekli']);
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
        
        // Grubu oluştur
        $stmt = $pdo->prepare('INSERT INTO `groups` (name, description, created_by) VALUES (?, ?, ?)');
        $stmt->execute([$name, $description, $user['id']]);
        $groupId = $pdo->lastInsertId();
        
        // Oluşturanı admin olarak ekle
        $stmt = $pdo->prepare('INSERT INTO group_members (group_id, user_id, role) VALUES (?, ?, ?)');
        $stmt->execute([$groupId, $user['id'], 'admin']);
        
        echo json_encode([
            'success' => true,
            'group_id' => $groupId,
            'message' => 'Grup başarıyla oluşturuldu'
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Grup oluşturulurken hata: ' . $e->getMessage()]);
    }
}
?>
