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
    
    $stepId = intval($data['step_id'] ?? 0);
    $status = $data['status'] ?? '';
    
    if (!$stepId || !in_array($status, ['pending', 'in_progress', 'completed'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Geçersiz parametreler']);
        exit();
    }
    
    try {
        // Kullanıcı ID'sini al
        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->execute([$_SESSION['username']]);
        $user = $stmt->fetch();
        
        // Adımın kullanıcıya ait olduğunu kontrol et
        $stmt = $pdo->prepare('
            SELECT aps.* FROM action_plan_steps aps
            JOIN action_plans ap ON aps.plan_id = ap.id
            WHERE aps.id = ? AND ap.user_id = ?
        ');
        $stmt->execute([$stepId, $user['id']]);
        $step = $stmt->fetch();
        
        if (!$step) {
            http_response_code(403);
            echo json_encode(['error' => 'Bu adıma erişim yetkiniz yok']);
            exit();
        }
        
        // Durumu güncelle
        $completedAt = $status === 'completed' ? date('Y-m-d H:i:s') : null;
        $stmt = $pdo->prepare('UPDATE action_plan_steps SET status = ?, completed_at = ? WHERE id = ?');
        $stmt->execute([$status, $completedAt, $stepId]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Adım durumu güncellendi'
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Güncelleme sırasında hata: ' . $e->getMessage()]);
    }
}
?>
