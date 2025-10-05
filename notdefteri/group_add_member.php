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
    
    $groupId = intval($data['group_id'] ?? 0);
    $userId = intval($data['user_id'] ?? 0);
    
    if (!$groupId || !$userId) {
        http_response_code(400);
        echo json_encode(['error' => 'Grup ID ve kullanıcı ID gerekli']);
        exit();
    }
    
    try {
        // Mevcut kullanıcı ID'sini al
        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->execute([$_SESSION['username']]);
        $currentUser = $stmt->fetch();
        
        // Admin kontrolü
        $stmt = $pdo->prepare('SELECT role FROM group_members WHERE group_id = ? AND user_id = ?');
        $stmt->execute([$groupId, $currentUser['id']]);
        $member = $stmt->fetch();
        
        if (!$member || $member['role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['error' => 'Bu işlem için admin yetkisi gerekli']);
            exit();
        }
        
        // Grup üye sayısını kontrol et (maksimum 5)
        $stmt = $pdo->prepare('SELECT COUNT(*) as count FROM group_members WHERE group_id = ?');
        $stmt->execute([$groupId]);
        $memberCount = $stmt->fetch();
        
        if ($memberCount['count'] >= 5) {
            http_response_code(400);
            echo json_encode(['error' => 'Grup maksimum 5 üye alabilir']);
            exit();
        }
        
        // Kullanıcının var olduğunu kontrol et
        $stmt = $pdo->prepare('SELECT id FROM users WHERE id = ?');
        $stmt->execute([$userId]);
        $newUser = $stmt->fetch();
        
        if (!$newUser) {
            http_response_code(404);
            echo json_encode(['error' => 'Kullanıcı bulunamadı']);
            exit();
        }
        
        // Kullanıcıyı gruba ekle
        $stmt = $pdo->prepare('INSERT INTO group_members (group_id, user_id, role) VALUES (?, ?, ?)');
        $stmt->execute([$groupId, $userId, 'member']);
        
        echo json_encode([
            'success' => true,
            'message' => 'Kullanıcı gruba eklendi'
        ]);
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            http_response_code(400);
            echo json_encode(['error' => 'Kullanıcı zaten grupta']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Üye eklenirken hata: ' . $e->getMessage()]);
        }
    }
}
?>
