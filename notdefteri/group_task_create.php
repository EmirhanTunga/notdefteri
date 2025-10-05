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
    $title = trim($data['title'] ?? '');
    $description = trim($data['description'] ?? '');
    $assignedTo = !empty($data['assigned_to']) ? intval($data['assigned_to']) : null;
    $priority = $data['priority'] ?? 'medium';
    $dueDate = !empty($data['due_date']) ? $data['due_date'] : null;
    
    if (!$groupId || empty($title)) {
        http_response_code(400);
        echo json_encode(['error' => 'Grup ID ve görev başlığı gerekli']);
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
        
        // Görevi oluştur
        $stmt = $pdo->prepare('
            INSERT INTO group_tasks (group_id, title, description, assigned_to, created_by, priority, due_date)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ');
        $stmt->execute([$groupId, $title, $description, $assignedTo, $user['id'], $priority, $dueDate]);
        $taskId = $pdo->lastInsertId();
        
        echo json_encode([
            'success' => true,
            'task_id' => $taskId,
            'message' => 'Görev başarıyla oluşturuldu'
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Görev oluşturulurken hata: ' . $e->getMessage()]);
    }
}
?>
