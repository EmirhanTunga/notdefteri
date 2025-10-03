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

$field = $_POST['field'] ?? '';
$value = $_POST['value'] ?? '';

if (!$field || !$value) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Field and value are required']);
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
    if ($field === 'username') {
        // Check if username already exists
        $checkStmt = $pdo->prepare('SELECT id FROM users WHERE username = ? AND id != ?');
        $checkStmt->execute([$value, $userId]);
        
        if ($checkStmt->fetch()) {
            echo json_encode(['success' => false, 'error' => 'Bu kullanıcı adı zaten kullanılıyor']);
            exit();
        }
        
        // Update username
        $stmt = $pdo->prepare('UPDATE users SET username = ? WHERE id = ?');
        $stmt->execute([$value, $userId]);
        
        // Update session
        $_SESSION['username'] = $value;
        
    } elseif ($field === 'email') {
        // Validate email
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'error' => 'Geçersiz e-posta adresi']);
            exit();
        }
        
        // Check if email already exists
        $checkStmt = $pdo->prepare('SELECT id FROM users WHERE email = ? AND id != ?');
        $checkStmt->execute([$value, $userId]);
        
        if ($checkStmt->fetch()) {
            echo json_encode(['success' => false, 'error' => 'Bu e-posta adresi zaten kullanılıyor']);
            exit();
        }
        
        // Update email
        $stmt = $pdo->prepare('UPDATE users SET email = ? WHERE id = ?');
        $stmt->execute([$value, $userId]);
        
    } else {
        echo json_encode(['success' => false, 'error' => 'Geçersiz alan']);
        exit();
    }
    
    echo json_encode(['success' => true, 'message' => 'Güncelleme başarılı']);
    
} catch (Exception $e) {
    error_log('Update account error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Veritabanı hatası']);
}
?>