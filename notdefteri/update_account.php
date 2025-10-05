<?php
session_start();
header('Content-Type: application/json');

// Kullanıcı giriş kontrolü
if (!isset($_SESSION['username'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Oturum bulunamadı']);
    exit();
}

require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Geçersiz istek']);
    exit();
}

$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (!$username || !$email) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Kullanıcı adı ve e-posta zorunludur']);
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
    echo json_encode(['success' => false, 'message' => 'Kullanıcı bulunamadı']);
    exit();
}

try {
    // E-posta validasyonu
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Geçersiz e-posta adresi']);
        exit();
    }
    
    // Kullanıcı adı kontrolü (başka kullanıcıya ait mi?)
    if ($username !== $_SESSION['username']) {
        $checkStmt = $pdo->prepare('SELECT id FROM users WHERE username = ? AND id != ?');
        $checkStmt->execute([$username, $userId]);
        
        if ($checkStmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Bu kullanıcı adı zaten kullanılıyor']);
            exit();
        }
    }
    
    // E-posta kontrolü (başka kullanıcıya ait mi?)
    $checkStmt = $pdo->prepare('SELECT id FROM users WHERE email = ? AND id != ?');
    $checkStmt->execute([$email, $userId]);
    
    if ($checkStmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Bu e-posta adresi zaten kullanılıyor']);
        exit();
    }
    
    // Şifre değişikliği varsa
    if (!empty($password)) {
        // Şifre en az 6 karakter olmalı
        if (strlen($password) < 6) {
            echo json_encode(['success' => false, 'message' => 'Şifre en az 6 karakter olmalıdır']);
            exit();
        }
        
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare('UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?');
        $stmt->execute([$username, $email, $hashedPassword, $userId]);
    } else {
        // Sadece kullanıcı adı ve e-posta güncelleme
        $stmt = $pdo->prepare('UPDATE users SET username = ?, email = ? WHERE id = ?');
        $stmt->execute([$username, $email, $userId]);
    }
    
    // Session'ı güncelle
    $_SESSION['username'] = $username;
    
    echo json_encode([
        'success' => true, 
        'message' => 'Profil başarıyla güncellendi'
    ]);
    
} catch (Exception $e) {
    error_log('Update account error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Veritabanı hatası: ' . $e->getMessage()]);
}
?>