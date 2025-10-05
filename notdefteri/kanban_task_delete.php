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
    
    if (!$task_id) {
        echo json_encode(['success' => false, 'message' => 'Görev ID gerekli']);
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
    
    // Görevi sil
    $stmt = $pdo->prepare('DELETE FROM kanban_tasks WHERE id = ? AND user_id = ?');
    $result = $stmt->execute([$task_id, $user_id]);
    
    if ($result && $stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Görev silindi']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Görev bulunamadı veya silinemedi']);
    }
    
} catch (Exception $e) {
    error_log('Kanban task delete error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Veritabanı hatası']);
}
?>

