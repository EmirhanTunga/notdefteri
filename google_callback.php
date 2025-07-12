<?php
// Session başlatma kontrolü
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require 'db.php';
require 'google_config.php';

// Hata ayıklama için
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_GET['code'])) {
    $code = $_GET['code'];
    
    // Access token al
    $token_data = [
        'client_id' => GOOGLE_CLIENT_ID,
        'client_secret' => GOOGLE_CLIENT_SECRET,
        'code' => $code,
        'grant_type' => 'authorization_code',
        'redirect_uri' => GOOGLE_REDIRECT_URI
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, GOOGLE_TOKEN_URL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($token_data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // SSL sertifika kontrolünü devre dışı bırak
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Host kontrolünü devre dışı bırak
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded'
    ]);
    
    $response = curl_exec($ch);
    $curl_error = curl_error($ch);
    curl_close($ch);
    
    if ($curl_error) {
        error_log("Google OAuth CURL Error: " . $curl_error);
        header('Location: login.php?error=CURL Hatası: ' . urlencode($curl_error));
        exit();
    }
    
    $token_info = json_decode($response, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("Google OAuth Token Response Error: " . $response);
        header('Location: login.php?error=Token yanıtı çözülemedi: ' . urlencode($response));
        exit();
    }
    
    if (isset($token_info['access_token'])) {
        // Kullanıcı bilgilerini al
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, GOOGLE_USERINFO_URL . '?access_token=' . $token_info['access_token']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // SSL sertifika kontrolünü devre dışı bırak
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Host kontrolünü devre dışı bırak
        
        $user_response = curl_exec($ch);
        $user_curl_error = curl_error($ch);
        curl_close($ch);
        
        if ($user_curl_error) {
            header('Location: login.php?error=Kullanıcı bilgileri alınamadı: ' . urlencode($user_curl_error));
            exit();
        }
        
        $user_info = json_decode($user_response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            header('Location: login.php?error=Kullanıcı bilgileri çözülemedi: ' . urlencode($user_response));
            exit();
        }
        
        if (isset($user_info['email'])) {
            $google_email = $user_info['email'];
            $google_name = $user_info['name'] ?? '';
            $google_picture = $user_info['picture'] ?? '';
            
            // Kullanıcı veritabanında var mı kontrol et
            $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
            $stmt->execute([$google_email]);
            $user = $stmt->fetch();
            
            if ($user) {
                // Kullanıcı zaten var, giriş yap
                $_SESSION['username'] = $user['username'];
                $_SESSION['google_user'] = true;
                error_log("Google OAuth Login Success: " . $user['username']);
                header('Location: index.php');
                exit();
            } else {
                // Yeni kullanıcı oluştur
                $username = $google_name ?: explode('@', $google_email)[0];
                
                // Kullanıcı adı benzersiz mi kontrol et
                $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
                $stmt->execute([$username]);
                if ($stmt->fetch()) {
                    // Kullanıcı adı varsa sayı ekle
                    $counter = 1;
                    $original_username = $username;
                    while (true) {
                        $username = $original_username . $counter;
                        $stmt->execute([$username]);
                        if (!$stmt->fetch()) break;
                        $counter++;
                    }
                }
                
                // Yeni kullanıcıyı kaydet
                $stmt = $pdo->prepare('INSERT INTO users (username, email, password, google_id, google_picture) VALUES (?, ?, ?, ?, ?)');
                $google_id = $user_info['id'] ?? '';
                $dummy_password = password_hash(uniqid(), PASSWORD_DEFAULT); // Google kullanıcıları için dummy şifre
                $stmt->execute([$username, $google_email, $dummy_password, $google_id, $google_picture]);
                
                $_SESSION['username'] = $username;
                $_SESSION['google_user'] = true;
                error_log("Google OAuth New User Created: " . $username);
                header('Location: index.php');
                exit();
            }
        } else {
            header('Location: login.php?error=Google bilgileri alınamadı');
            exit();
        }
    } else {
        header('Location: login.php?error=Google token alınamadı');
        exit();
    }
} else {
    header('Location: login.php?error=Google giriş hatası');
    exit();
}
?> 