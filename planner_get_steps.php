<?php
session_start();
if (!isset($_SESSION['username'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

require_once 'db.php';

header('Content-Type: application/json');

$planId = intval($_GET['plan_id'] ?? 0);

if (!$planId) {
    http_response_code(400);
    echo json_encode(['error' => 'Plan ID gerekli']);
    exit();
}

try {
    // Kullanıcı ID'sini al
    $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
    $stmt->execute([$_SESSION['username']]);
    $user = $stmt->fetch();
    
    // Planın kullanıcıya ait olduğunu kontrol et
    $stmt = $pdo->prepare('SELECT * FROM action_plans WHERE id = ? AND user_id = ?');
    $stmt->execute([$planId, $user['id']]);
    $plan = $stmt->fetch();
    
    if (!$plan) {
        http_response_code(403);
        echo json_encode(['error' => 'Bu plana erişim yetkiniz yok']);
        exit();
    }
    
    // Adımları getir
    $stmt = $pdo->prepare('
        SELECT * FROM action_plan_steps 
        WHERE plan_id = ? 
        ORDER BY step_number ASC
    ');
    $stmt->execute([$planId]);
    $steps = $stmt->fetchAll();
    
    echo json_encode([
        'plan' => $plan,
        'steps' => $steps
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Adımlar getirilirken hata: ' . $e->getMessage()]);
}
?>
