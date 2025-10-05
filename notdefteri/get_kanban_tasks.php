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

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

try {
    $username = $_SESSION['username'];
    
    // Kullanıcı id'sini bul
    $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit();
    }
    
    $user_id = $user['id'];
    
    // Kanban tasks tablosunu kontrol et ve yoksa oluştur
    $checkTableSQL = "SHOW TABLES LIKE 'kanban_tasks'";
    $result = $pdo->query($checkTableSQL);
    
    if ($result->rowCount() == 0) {
        // Tablo yoksa oluştur
        $createTableSQL = "
            CREATE TABLE kanban_tasks (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                title VARCHAR(255) NOT NULL,
                description TEXT,
                priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
                status ENUM('todo', 'in_progress', 'completed') DEFAULT 'todo',
                due_date DATE NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )
        ";
        $pdo->exec($createTableSQL);
    }
    
    // Görevleri getir
    $stmt = $pdo->prepare('SELECT * FROM kanban_tasks WHERE user_id = ? ORDER BY created_at DESC');
    $stmt->execute([$user_id]);
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Debug log
    error_log('User ID: ' . $user_id);
    error_log('Tasks found: ' . count($tasks));
    error_log('Tasks data: ' . json_encode($tasks));
    
    echo json_encode(['success' => true, 'tasks' => $tasks]);
    
} catch (Exception $e) {
    error_log('Get kanban tasks error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Veritabanı hatası']);
}
?>
