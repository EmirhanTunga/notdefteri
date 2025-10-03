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
    $note_id = $_POST['id'] ?? null;
    $is_favorite = (int)($_POST['is_favorite'] ?? 0);
    
    if (!$note_id) {
        echo json_encode(['success' => false, 'message' => 'Note ID required']);
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
    
    // Notun kullanıcıya ait olduğunu kontrol et
    $stmt = $pdo->prepare('SELECT id FROM notes WHERE id = ? AND user_id = ?');
    $stmt->execute([$note_id, $user_id]);
    $note = $stmt->fetch();
    
    if (!$note) {
        echo json_encode(['success' => false, 'message' => 'Note not found']);
        exit();
    }
    
    // Favori durumunu güncelle
    $stmt = $pdo->prepare('UPDATE notes SET is_favorite = ? WHERE id = ? AND user_id = ?');
    $result = $stmt->execute([$is_favorite, $note_id, $user_id]);
    
    if ($result) {
        $message = $is_favorite ? 'Not favorilere eklendi' : 'Not favorilerden çıkarıldı';
        echo json_encode(['success' => true, 'message' => $message]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Favori durumu güncellenemedi']);
    }
    
} catch (Exception $e) {
    error_log('Toggle favorite error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Veritabanı hatası']);
}
?>