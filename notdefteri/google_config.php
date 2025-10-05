<?php
// Google OAuth 2.0 Konfigürasyonu
if (!defined('GOOGLE_CLIENT_ID')) {
    define('GOOGLE_CLIENT_ID', );
}
if (!defined('GOOGLE_CLIENT_SECRET')) {
    define('GOOGLE_CLIENT_SECRET', '');
}
if (!defined('GOOGLE_REDIRECT_URI')) {
    define('GOOGLE_REDIRECT_URI', 'http://localhost:8000/notdefteri/google_callback.php');
}

// Google OAuth URL'leri
if (!defined('GOOGLE_AUTH_URL')) {
    define('GOOGLE_AUTH_URL', 'https://accounts.google.com/o/oauth2/auth');
}
if (!defined('GOOGLE_TOKEN_URL')) {
    define('GOOGLE_TOKEN_URL', 'https://oauth2.googleapis.com/token');
}
if (!defined('GOOGLE_USERINFO_URL')) {
    define('GOOGLE_USERINFO_URL', 'https://www.googleapis.com/oauth2/v2/userinfo');
}

// Google ile giriş URL'ini oluştur
function getGoogleLoginUrl() {
    if (!defined('GOOGLE_CLIENT_ID') || !defined('GOOGLE_REDIRECT_URI')) {
        return false;
    }
    
    $params = [
        'client_id' => GOOGLE_CLIENT_ID,
        'redirect_uri' => GOOGLE_REDIRECT_URI,
        'scope' => 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile openid',
        'response_type' => 'code',
        'access_type' => 'offline',
        'prompt' => 'consent'
    ];
    
    return GOOGLE_AUTH_URL . '?' . http_build_query($params);
}
?> 