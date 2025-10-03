<?php
session_start();
header('Content-Type: application/json');

// Kullanıcı giriş kontrolü
if (!isset($_SESSION['username'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit();
}

$password = $_POST['password'] ?? '';

if (!$password) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Şifre gerekli']);
    exit();
}

$userId = $_SESSION['user_id'] ?? null;

// Get user ID if not in session
if (!$userId) {
    $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
    $stmt->execute([$_SESSION['username']]);
    $user = $stmt->fetch();
    $userId = $user['id'] ?? null;
    $_SESSION['user_id'] = $userId;
}

if (!$userId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'User not found']);
    exit();
}

try {
    // Get user password hash
    $stmt = $pdo->prepare('SELECT password FROM users WHERE id = ?');
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    if (!$user) {
        echo json_encode(['success' => false, 'error' => 'Kullanıcı bulunamadı']);
        exit();
    }
    
    // Verify password
    if (!password_verify($password, $user['password'])) {
        echo json_encode(['success' => false, 'error' => 'Şifre yanlış']);
        exit();
    }
    
    // Start transaction
    $pdo->beginTransaction();
    
    // Delete all user data (foreign key constraints will handle cascading)
    $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
    $stmt->execute([$userId]);
    
    // Commit transaction
    $pdo->commit();
    
    // Destroy session
    session_destroy();
    
    echo json_encode(['success' => true, 'message' => 'Hesap başarıyla silindi']);
    
} catch (Exception $e) {
    // Rollback transaction
    $pdo->rollBack();
    
    error_log('Delete account error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Hesap silme hatası']);
}
?>