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
    
    $description = trim($data['description'] ?? '');
    
    if (empty($description)) {
        http_response_code(400);
        echo json_encode(['error' => 'Açıklama gerekli']);
        exit();
    }
    
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
        
        // Basit AI: Açıklamayı adımlara böl
        $steps = generateActionSteps($description);
        
        // Plan oluştur
        $title = mb_substr($description, 0, 100);
        $stmt = $pdo->prepare('INSERT INTO action_plans (user_id, title, description) VALUES (?, ?, ?)');
        $stmt->execute([$user['id'], $title, $description]);
        $planId = $pdo->lastInsertId();
        
        // Adımları ekle
        $stepNumber = 1;
        foreach ($steps as $step) {
            $stmt = $pdo->prepare('
                INSERT INTO action_plan_steps (plan_id, step_number, title, description) 
                VALUES (?, ?, ?, ?)
            ');
            $stmt->execute([$planId, $stepNumber, $step['title'], $step['description']]);
            $stepNumber++;
        }
        
        echo json_encode([
            'success' => true,
            'plan_id' => $planId,
            'steps_count' => count($steps),
            'message' => 'Aksiyon planı oluşturuldu'
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Plan oluşturulurken hata: ' . $e->getMessage()]);
    }
}

// Basit aksiyon adımı üreteci
function generateActionSteps($description) {
    $steps = [];
    
    // Anahtar kelimelere göre adımlar oluştur
    $keywords = [
        'proje' => ['Proje Planlaması', 'Kaynak Belirleme', 'Uygulama', 'Test ve Değerlendirme'],
        'öğren' => ['Araştırma Yap', 'Kaynak Topla', 'Çalışma Planı Oluştur', 'Pratik Yap', 'Değerlendirme'],
        'web' => ['Tasarım Oluştur', 'Frontend Geliştirme', 'Backend Geliştirme', 'Test', 'Yayınlama'],
        'yazı' => ['Konu Araştırması', 'Taslak Oluştur', 'İçerik Yazımı', 'Düzenleme', 'Yayınlama'],
        'toplantı' => ['Gündem Hazırla', 'Katılımcıları Belirle', 'Toplantı Yap', 'Notları Paylaş'],
        'rapor' => ['Veri Toplama', 'Analiz', 'Taslak Yazımı', 'Gözden Geçirme', 'Sunuma Hazırlama']
    ];
    
    $descLower = mb_strtolower($description);
    $matched = false;
    
    foreach ($keywords as $keyword => $stepTitles) {
        if (strpos($descLower, $keyword) !== false) {
            foreach ($stepTitles as $index => $title) {
                $steps[] = [
                    'title' => $title,
                    'description' => 'Adım ' . ($index + 1) . ': ' . $title
                ];
            }
            $matched = true;
            break;
        }
    }
    
    // Eğer eşleşme yoksa genel adımlar oluştur
    if (!$matched) {
        $generalSteps = [
            ['title' => 'Planlama ve Hazırlık', 'description' => 'Gerekli kaynakları ve araçları belirle'],
            ['title' => 'Araştırma', 'description' => 'Konu hakkında detaylı araştırma yap'],
            ['title' => 'Uygulama', 'description' => 'Planı hayata geçirmeye başla'],
            ['title' => 'Gözden Geçirme', 'description' => 'İlerlemeyi kontrol et ve gerekli düzeltmeleri yap'],
            ['title' => 'Tamamlama', 'description' => 'Son kontrolleri yap ve tamamla']
        ];
        $steps = $generalSteps;
    }
    
    return $steps;
}
?>
