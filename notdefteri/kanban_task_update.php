<?php
session_start();
header('Content-Type: application/json');

// Kullanıcı giriş kontrolü
if (!isset($_SESSION['username'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

try {
    $username = $_SESSION['username'];
    $task_id = $_POST['id'] ?? null;
    $status = $_POST['status'] ?? null;
    
    if (!$task_id || !$status) {
        echo json_encode(['success' => false, 'message' => 'Gerekli parametreler eksik']);
        exit();
    }
    
    // Geçerli status kontrolü
    $validStatuses = ['todo', 'in_progress', 'completed'];
    if (!in_array($status, $validStatuses)) {
        echo json_encode(['success' => false, 'message' => 'Geçersiz durum']);
        exit();
    }
    
    // Kullanıcı id'sini bul
    $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit();
    }
    
    $user_id = $user['id'];
    
    // Görev durumunu güncelle
    $stmt = $pdo->prepare('UPDATE kanban_tasks SET status = ?, updated_at = NOW() WHERE id = ? AND user_id = ?');
    $result = $stmt->execute([$status, $task_id, $user_id]);
    
    if ($result && $stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Görev durumu güncellendi']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Görev bulunamadı veya güncellenemedi']);
    }
    
} catch (Exception $e) {
    error_log('Kanban task update error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Veritabanı hatası']);
}
?>

