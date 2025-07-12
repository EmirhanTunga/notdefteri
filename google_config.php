<?php
// Google OAuth 2.0 Konfigürasyonu


// Google OAuth URL'leri
define('GOOGLE_AUTH_URL', 'https://accounts.google.com/o/oauth2/auth');
define('GOOGLE_TOKEN_URL', 'https://oauth2.googleapis.com/token');
define('GOOGLE_USERINFO_URL', 'https://www.googleapis.com/oauth2/v2/userinfo');

// Google ile giriş URL'ini oluştur
function getGoogleLoginUrl() {
    $params = [
        'client_id' => GOOGLE_CLIENT_ID,
        'redirect_uri' => GOOGLE_REDIRECT_URI,
        'scope' => 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile',
        'response_type' => 'code',
        'access_type' => 'offline',
        'prompt' => 'consent'
    ];
    
    return GOOGLE_AUTH_URL . '?' . http_build_query($params);
}
?> 