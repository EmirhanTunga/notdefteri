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
    
    // Görevleri getir
    $stmt = $pdo->prepare('
        SELECT gt.*, 
               u1.username as created_by_username,
               u2.username as assigned_to_username
        FROM group_tasks gt
        LEFT JOIN users u1 ON gt.created_by = u1.id
        LEFT JOIN users u2 ON gt.assigned_to = u2.id
        WHERE gt.group_id = ?
        ORDER BY 
            CASE gt.status
                WHEN "todo" THEN 1
                WHEN "in_progress" THEN 2
                WHEN "completed" THEN 3
            END,
            CASE gt.priority
                WHEN "high" THEN 1
                WHEN "medium" THEN 2
                WHEN "low" THEN 3
            END,
            gt.due_date ASC
    ');
    $stmt->execute([$groupId]);
    $tasks = $stmt->fetchAll();
    
    echo json_encode(['tasks' => $tasks]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Görevler getirilirken hata: ' . $e->getMessage()]);
}
?>
