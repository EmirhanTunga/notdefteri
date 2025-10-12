<?php
session_start();
if (!isset($_SESSION['username'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

require_once 'db.php';

header('Content-Type: application/json');

try {
    // Kullanıcı ID'sini al
    $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
    $stmt->execute([$_SESSION['username']]);
    $user = $stmt->fetch();
    
    if (!$user) {
        http_response_code(401);
        echo json_encode(['error' => 'Kullanıcı bulunamadı']);
        exit();
    }
    
    // Planları getir
    $stmt = $pdo->prepare('
        SELECT ap.*, 
               COUNT(aps.id) as total_steps,
               SUM(CASE WHEN aps.status = "completed" THEN 1 ELSE 0 END) as completed_steps
        FROM action_plans ap
        LEFT JOIN action_plan_steps aps ON ap.id = aps.plan_id
        WHERE ap.user_id = ?
        GROUP BY ap.id
        ORDER BY ap.created_at DESC
    ');
    $stmt->execute([$user['id']]);
    $plans = $stmt->fetchAll();
    
    echo json_encode(['plans' => $plans]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Planlar getirilirken hata: ' . $e->getMessage()]);
}
?>
