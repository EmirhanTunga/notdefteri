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
    
    $taskId = intval($data['task_id'] ?? 0);
    $status = $data['status'] ?? null;
    $assignedTo = isset($data['assigned_to']) ? intval($data['assigned_to']) : null;
    
    if (!$taskId) {
        http_response_code(400);
        echo json_encode(['error' => 'Görev ID gerekli']);
        exit();
    }
    
    try {
        // Kullanıcı ID'sini al
        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->execute([$_SESSION['username']]);
        $user = $stmt->fetch();
        
        // Görevin grup bilgisini al
        $stmt = $pdo->prepare('SELECT group_id FROM group_tasks WHERE id = ?');
        $stmt->execute([$taskId]);
        $task = $stmt->fetch();
        
        if (!$task) {
            http_response_code(404);
            echo json_encode(['error' => 'Görev bulunamadı']);
            exit();
        }
        
        // Kullanıcının grupta olduğunu kontrol et
        $stmt = $pdo->prepare('SELECT id FROM group_members WHERE group_id = ? AND user_id = ?');
        $stmt->execute([$task['group_id'], $user['id']]);
        if (!$stmt->fetch()) {
            http_response_code(403);
            echo json_encode(['error' => 'Bu grubun üyesi değilsiniz']);
            exit();
        }
        
        // Görevi güncelle
        $updates = [];
        $params = [];
        
        if ($status !== null) {
            $updates[] = 'status = ?';
            $params[] = $status;
        }
        
        if ($assignedTo !== null) {
            $updates[] = 'assigned_to = ?';
            $params[] = $assignedTo;
        }
        
        if (empty($updates)) {
            http_response_code(400);
            echo json_encode(['error' => 'Güncellenecek alan belirtilmedi']);
            exit();
        }
        
        $params[] = $taskId;
        $sql = 'UPDATE group_tasks SET ' . implode(', ', $updates) . ' WHERE id = ?';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        echo json_encode([
            'success' => true,
            'message' => 'Görev güncellendi'
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Görev güncellenirken hata: ' . $e->getMessage()]);
    }
}
?>
