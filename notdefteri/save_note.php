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
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $is_public = isset($_POST['is_public']) ? 1 : 0;
    $note_id = $_POST['id'] ?? null;
    
    // Kullanıcı id'sini bul
    $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit();
    }
    
    $user_id = $user['id'];
    
    if ($note_id) {
        // Not güncelleme
        $stmt = $pdo->prepare('UPDATE notes SET title = ?, content = ?, is_public = ?, updated_at = NOW() WHERE id = ? AND user_id = ?');
        $result = $stmt->execute([$title, $content, $is_public, $note_id, $user_id]);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Not güncellendi']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Not güncellenemedi']);
        }
    } else {
        // Yeni not ekleme
        if (empty($title) && empty($content)) {
            echo json_encode(['success' => false, 'message' => 'Başlık veya içerik gerekli']);
            exit();
        }
        
        $stmt = $pdo->prepare('INSERT INTO notes (user_id, title, content, is_public, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())');
        $result = $stmt->execute([$user_id, $title, $content, $is_public]);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Not eklendi', 'id' => $pdo->lastInsertId()]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Not eklenemedi']);
        }
    }
    
} catch (Exception $e) {
    error_log('Save note error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Veritabanı hatası']);
} 