<?php
session_start();
if (!isset($_SESSION['username'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

require_once 'db.php';

header('Content-Type: application/json');

$groupId = intval($_GET['group_id'] ?? 0);

if (!$groupId) {
    http_response_code(400);
    echo json_encode(['error' => 'Grup ID gerekli']);
    exit();
}

try {
    // Kullanıcı ID'sini al
    $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
    $stmt->execute([$_SESSION['username']]);
    $user = $stmt->fetch();
    
    // Kullanıcının grupta olduğunu kontrol et
    $stmt = $pdo->prepare('SELECT id FROM group_members WHERE group_id = ? AND user_id = ?');
    $stmt->execute([$groupId, $user['id']]);
    if (!$stmt->fetch()) {
        http_response_code(403);
        echo json_encode(['error' => 'Bu grubun üyesi değilsiniz']);
        exit();
    }
    
    // Grup üyelerini getir
    $stmt = $pdo->prepare('
        SELECT u.id, u.username, u.email, gm.role, gm.joined_at
        FROM group_members gm
        JOIN users u ON gm.user_id = u.id
        WHERE gm.group_id = ?
        ORDER BY gm.role DESC, gm.joined_at ASC
    ');
    $stmt->execute([$groupId]);
    $members = $stmt->fetchAll();
    
    echo json_encode(['members' => $members]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Üyeler getirilirken hata: ' . $e->getMessage()]);
}
?>
