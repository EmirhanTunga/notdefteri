<?php
session_start();
require_once 'db.php';
require_once 'gemini_config.php';

header('Content-Type: application/json');

// Kullanıcı oturum kontrolü
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Oturum bulunamadı']);
    exit;
}

$user_id = $_SESSION['user_id'];

// POST verilerini al
$input = json_decode(file_get_contents('php://input'), true);
$message = $input['message'] ?? '';
$conversationHistory = $input['history'] ?? [];
$context = $input['context'] ?? 'general'; // general, planner, notes, tasks

if (empty($message)) {
    echo json_encode(['success' => false, 'error' => 'Mesaj boş olamaz']);
    exit;
}

// Gemini API yapılandırma kontrolü
if (!isGeminiConfigured()) {
    echo json_encode([
        'success' => false, 
        'error' => 'Gemini API yapılandırılmamış. Lütfen yöneticinizle iletişime geçin.'
    ]);
    exit;
}

// Kullanıcı bilgilerini al (kişiselleştirme için)
$stmt = $pdo->prepare('SELECT username, email FROM users WHERE id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Bağlam bazlı sistem promptu oluştur
$systemPrompt = "Sen NotDefteri uygulamasının yapay zeka asistanısın. Kullanıcı adı: {$user['username']}. ";

switch ($context) {
    case 'planner':
        $systemPrompt .= "Kullanıcının plan yapmasına, projelerini organize etmesine ve adım adım eylem planları oluşturmasına yardımcı ol. Planları net, uygulanabilir adımlara böl. Her adım için tahmini süre ve öncelik öner.";
        break;
    case 'notes':
        $systemPrompt .= "Kullanıcının notlarını düzenlemesine, özetlemesine ve daha iyi organize etmesine yardımcı ol. Not alma teknikleri ve yapılandırma önerileri sun.";
        break;
    case 'tasks':
        $systemPrompt .= "Kullanıcının görevlerini önceliklendirmesine, zaman yönetimi yapmasına ve üretkenliğini artırmasına yardımcı ol. Görev takibi ve tamamlama stratejileri öner.";
        break;
    case 'groups':
        $systemPrompt .= "Kullanıcının grup çalışmalarını organize etmesine, ekip işbirliğini geliştirmesine ve görev dağılımı yapmasına yardımcı ol.";
        break;
    default:
        $systemPrompt .= "Kullanıcının not alma, görev yönetimi, planlama ve organizasyon konularında yardımcı ol. Sorularına net, yapıcı ve uygulanabilir cevaplar ver. Türkçe konuş.";
}

// Sistem promptunu konuşma geçmişine ekle
$fullHistory = [
    ['role' => 'system', 'content' => $systemPrompt]
];
$fullHistory = array_merge($fullHistory, $conversationHistory);

// Gemini API'yi çağır
$result = callGeminiAPI($message, $fullHistory);

if ($result['success']) {
    // Konuşmayı veritabanına kaydet (isteğe bağlı)
    try {
        $stmt = $pdo->prepare('INSERT INTO gemini_conversations (user_id, context, user_message, ai_response, created_at) VALUES (?, ?, ?, ?, NOW())');
        $stmt->execute([$user_id, $context, $message, $result['response']]);
    } catch (Exception $e) {
        // Tablo yoksa sessizce devam et
        error_log('Gemini conversation save error: ' . $e->getMessage());
    }

    echo json_encode([
        'success' => true,
        'response' => $result['response'],
        'timestamp' => date('Y-m-d H:i:s')
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => $result['error'] ?? 'Bilinmeyen hata'
    ]);
}
