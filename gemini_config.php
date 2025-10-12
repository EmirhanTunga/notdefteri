<?php
/**
 * Gemini AI Yapılandırması
 * 
 * Gemini API anahtarınızı buraya ekleyin.
 * API anahtarı almak için: https://makersuite.google.com/app/apikey
 */

// Gemini API Anahtarı


// Gemini API Endpoint
// Farklı modeller deneyeceğiz
define('GEMINI_API_ENDPOINTS', [
    'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent',
    'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent',
    'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-pro:generateContent',
]);
define('GEMINI_API_URL', GEMINI_API_ENDPOINTS[0]); // Varsayılan olarak gemini-pro

// API anahtarı kontrolü
function isGeminiConfigured() {
    return GEMINI_API_KEY !== 'YOUR_GEMINI_API_KEY_HERE' && !empty(GEMINI_API_KEY);
}

// Mevcut modelleri listele
function getAvailableModels() {
    $ch = curl_init('https://generativelanguage.googleapis.com/v1beta/models?key=' . GEMINI_API_KEY);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        $data = json_decode($response, true);
        if (isset($data['models'])) {
            $generateContentModels = [];
            foreach ($data['models'] as $model) {
                // Sadece generateContent destekleyen modelleri al
                if (isset($model['supportedGenerationMethods']) && 
                    in_array('generateContent', $model['supportedGenerationMethods'])) {
                    $generateContentModels[] = $model['name'];
                }
            }
            return $generateContentModels;
        }
    }
    
    return [];
}

// Gemini API çağrısı (otomatik model deneme)
function callGeminiAPI($prompt, $conversationHistory = []) {
    // Execution time limitini artır
    set_time_limit(120); // 2 dakika
    
    if (!isGeminiConfigured()) {
        return [
            'success' => false,
            'error' => 'Gemini API anahtarı yapılandırılmamış. Lütfen gemini_config.php dosyasını düzenleyin.'
        ];
    }

    // Önce mevcut modelleri al
    $availableModels = getAvailableModels();
    
    if (empty($availableModels)) {
        // Model listesi alınamadıysa, sabit endpoint'leri dene
        $availableModels = [
            'models/gemini-pro',
            'models/gemini-1.5-flash',
            'models/gemini-1.5-pro'
        ];
    }

    // Her mevcut modeli dene
    $lastError = '';
    foreach ($availableModels as $modelName) {
        // Model adından endpoint oluştur
        $endpoint = 'https://generativelanguage.googleapis.com/v1beta/' . $modelName . ':generateContent';
        
        $result = tryGeminiEndpoint($endpoint, $prompt, $conversationHistory);
        if ($result['success']) {
            return $result;
        }
        $lastError = $result['error'];
    }

    // Hiçbir model çalışmadı
    return [
        'success' => false,
        'error' => 'Tüm modeller denendi ancak başarısız oldu. Son hata: ' . $lastError . "\n\nMevcut modeller: " . implode(', ', $availableModels)
    ];
}

// Tek bir endpoint'i dene
function tryGeminiEndpoint($endpoint, $prompt, $conversationHistory = []) {
    // Konuşma geçmişini formatla
    $contents = [];
    foreach ($conversationHistory as $msg) {
        $contents[] = [
            'role' => $msg['role'] === 'user' ? 'user' : 'model',
            'parts' => [['text' => $msg['content']]]
        ];
    }
    
    // Mevcut prompt'u ekle
    $contents[] = [
        'role' => 'user',
        'parts' => [['text' => $prompt]]
    ];

    $data = [
        'contents' => $contents,
        'generationConfig' => [
            'temperature' => 0.7,
            'topK' => 40,
            'topP' => 0.95,
            'maxOutputTokens' => 2048,
        ],
        'safetySettings' => [
            [
                'category' => 'HARM_CATEGORY_HARASSMENT',
                'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
            ],
            [
                'category' => 'HARM_CATEGORY_HATE_SPEECH',
                'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
            ],
            [
                'category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT',
                'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
            ],
            [
                'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
                'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
            ]
        ]
    ];

    $ch = curl_init($endpoint . '?key=' . GEMINI_API_KEY);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60); // 60 saniye
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // Bağlantı timeout'u

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        return [
            'success' => false,
            'error' => 'Bağlantı hatası: ' . $curlError
        ];
    }

    if ($httpCode !== 200) {
        $errorDetail = json_decode($response, true);
        $errorMessage = 'HTTP ' . $httpCode;
        
        if (isset($errorDetail['error']['message'])) {
            $errorMessage .= ': ' . $errorDetail['error']['message'];
        }
        
        return [
            'success' => false,
            'error' => $errorMessage
        ];
    }

    $result = json_decode($response, true);
    
    if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
        return [
            'success' => true,
            'response' => $result['candidates'][0]['content']['parts'][0]['text']
        ];
    }

    return [
        'success' => false,
        'error' => 'Beklenmeyen API yanıtı'
    ];
}
