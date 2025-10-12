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
    
    // Kullanıcının üye olduğu grupları getir
    $stmt = $pdo->prepare('
        SELECT g.*, gm.role, u.username as created_by_username,
               (SELECT COUNT(*) FROM group_members WHERE group_id = g.id) as member_count,
               (SELECT COUNT(*) FROM group_tasks WHERE group_id = g.id) as task_count
        FROM `groups` g
        JOIN group_members gm ON g.id = gm.group_id
        JOIN users u ON g.created_by = u.id
        WHERE gm.user_id = ?
        ORDER BY g.updated_at DESC
    ');
    $stmt->execute([$user['id']]);
    $groups = $stmt->fetchAll();
    
    echo json_encode(['groups' => $groups]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Gruplar getirilirken hata: ' . $e->getMessage()]);
}
?>
