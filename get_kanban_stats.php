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
    
    // 1. GÜNLÜK İSTATİSTİKLER (Son 30 gün)
    $dailyStats = [];
    $stmt = $pdo->prepare('
        SELECT 
            DATE(updated_at) as date,
            COUNT(*) as completed_count
        FROM kanban_tasks 
        WHERE user_id = ? 
        AND status = "completed" 
        AND updated_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
        GROUP BY DATE(updated_at)
        ORDER BY date DESC
    ');
    $stmt->execute([$user_id]);
    $dailyStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 2. HAFTALIK İSTATİSTİKLER (Son 12 hafta)
    $weeklyStats = [];
    $stmt = $pdo->prepare('
        SELECT 
            YEARWEEK(updated_at, 1) as week,
            COUNT(*) as completed_count,
            MIN(updated_at) as week_start
        FROM kanban_tasks 
        WHERE user_id = ? 
        AND status = "completed" 
        AND updated_at >= DATE_SUB(CURDATE(), INTERVAL 12 WEEK)
        GROUP BY YEARWEEK(updated_at, 1)
        ORDER BY week DESC
    ');
    $stmt->execute([$user_id]);
    $weeklyStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 3. AYLIK İSTATİSTİKLER (Son 12 ay)
    $monthlyStats = [];
    $stmt = $pdo->prepare('
        SELECT 
            DATE_FORMAT(updated_at, "%Y-%m") as month,
            COUNT(*) as completed_count,
            DATE_FORMAT(updated_at, "%M %Y") as month_name
        FROM kanban_tasks 
        WHERE user_id = ? 
        AND status = "completed" 
        AND updated_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
        GROUP BY DATE_FORMAT(updated_at, "%Y-%m")
        ORDER BY month DESC
    ');
    $stmt->execute([$user_id]);
    $monthlyStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 4. ÖNCELİK DAĞILIMI
    $priorityDistribution = [];
    $stmt = $pdo->prepare('
        SELECT 
            priority,
            COUNT(*) as count,
            SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_count
        FROM kanban_tasks 
        WHERE user_id = ?
        GROUP BY priority
    ');
    $stmt->execute([$user_id]);
    $priorityDistribution = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 5. VERİMLİLİK SKORLARI
    $productivity = [];
    
    // Bu hafta tamamlanan görevler
    $stmt = $pdo->prepare('
        SELECT COUNT(*) as this_week_completed
        FROM kanban_tasks 
        WHERE user_id = ? 
        AND status = "completed" 
        AND updated_at >= DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY)
    ');
    $stmt->execute([$user_id]);
    $thisWeekCompleted = $stmt->fetchColumn();
    
    // Geçen hafta tamamlanan görevler
    $stmt = $pdo->prepare('
        SELECT COUNT(*) as last_week_completed
        FROM kanban_tasks 
        WHERE user_id = ? 
        AND status = "completed" 
        AND updated_at >= DATE_SUB(CURDATE(), INTERVAL (WEEKDAY(CURDATE()) + 7) DAY)
        AND updated_at < DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY)
    ');
    $stmt->execute([$user_id]);
    $lastWeekCompleted = $stmt->fetchColumn();
    
    // Ortalama tamamlanma süresi (gün)
    $stmt = $pdo->prepare('
        SELECT AVG(DATEDIFF(updated_at, created_at)) as avg_completion_days
        FROM kanban_tasks 
        WHERE user_id = ? 
        AND status = "completed"
        AND updated_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    ');
    $stmt->execute([$user_id]);
    $avgCompletionDays = round($stmt->fetchColumn() ?? 0, 1);
    
    // Aktif görev sayısı
    $stmt = $pdo->prepare('
        SELECT COUNT(*) as active_tasks
        FROM kanban_tasks 
        WHERE user_id = ? 
        AND status != "completed"
    ');
    $stmt->execute([$user_id]);
    $activeTasks = $stmt->fetchColumn();
    
    // Toplam tamamlanan görevler
    $stmt = $pdo->prepare('
        SELECT COUNT(*) as total_completed
        FROM kanban_tasks 
        WHERE user_id = ? 
        AND status = "completed"
    ');
    $stmt->execute([$user_id]);
    $totalCompleted = $stmt->fetchColumn();
    
    // Gecikmis görevler
    $stmt = $pdo->prepare('
        SELECT COUNT(*) as overdue_tasks
        FROM kanban_tasks 
        WHERE user_id = ? 
        AND status != "completed"
        AND due_date < CURDATE()
        AND due_date IS NOT NULL
    ');
    $stmt->execute([$user_id]);
    $overdueTasks = $stmt->fetchColumn();
    
    // Verimlilik skoru hesapla (0-100)
    $productivityScore = 0;
    if ($thisWeekCompleted > 0 || $lastWeekCompleted > 0) {
        $weeklyImprovement = $lastWeekCompleted > 0 ? 
            (($thisWeekCompleted - $lastWeekCompleted) / $lastWeekCompleted) * 100 : 100;
        $productivityScore = min(100, max(0, 50 + $weeklyImprovement));
    }
    
    $productivity = [
        'this_week_completed' => $thisWeekCompleted,
        'last_week_completed' => $lastWeekCompleted,
        'weekly_improvement' => $weeklyImprovement ?? 0,
        'productivity_score' => round($productivityScore, 1),
        'avg_completion_days' => $avgCompletionDays,
        'active_tasks' => $activeTasks,
        'total_completed' => $totalCompleted,
        'overdue_tasks' => $overdueTasks
    ];
    
    // 6. MEVCUT HEDEFLER
    $currentGoals = [];
    $stmt = $pdo->prepare('
        SELECT 
            goal_type,
            target_count,
            period_start,
            period_end,
            (SELECT COUNT(*) FROM kanban_tasks 
             WHERE user_id = ? 
             AND status = "completed" 
             AND DATE(updated_at) BETWEEN period_start AND period_end) as current_progress
        FROM kanban_goals 
        WHERE user_id = ? 
        AND is_active = 1 
        AND period_end >= CURDATE()
        ORDER BY period_start DESC
    ');
    $stmt->execute([$user_id, $user_id]);
    $currentGoals = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Response
    echo json_encode([
        'success' => true,
        'stats' => [
            'daily' => $dailyStats,
            'weekly' => $weeklyStats,
            'monthly' => $monthlyStats,
            'priority_distribution' => $priorityDistribution,
            'productivity' => $productivity,
            'current_goals' => $currentGoals
        ]
    ]);
    
} catch (Exception $e) {
    error_log('Get kanban stats error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Veritabanı hatası']);
}
?>