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
    
    $notificationId = intval($data['notification_id'] ?? 0);
    
    try {
        // Kullanıcı ID'sini al
        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->execute([$_SESSION['username']]);
        $user = $stmt->fetch();
        
        if ($notificationId) {
            // Tek bildirimi okundu işaretle
            $stmt = $pdo->prepare('UPDATE notifications SET is_read = TRUE WHERE id = ? AND user_id = ?');
            $stmt->execute([$notificationId, $user['id']]);
        } else {
            // Tüm bildirimleri okundu işaretle
            $stmt = $pdo->prepare('UPDATE notifications SET is_read = TRUE WHERE user_id = ?');
            $stmt->execute([$user['id']]);
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Bildirim(ler) okundu olarak işaretlendi'
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'İşlem sırasında hata: ' . $e->getMessage()]);
    }
}
?>
