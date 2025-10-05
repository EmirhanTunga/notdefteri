<?php
// Google kullanıcılarının şifre değiştirmesini engelle
function isGoogleUser($pdo, $username) {
    $stmt = $pdo->prepare('SELECT google_id FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    return $user && $user['google_id'];
}

// Google kullanıcıları için uyarı mesajı
function getGoogleUserWarning() {
    return '<div style="background: #fff3cd; border: 1px solid #ffeaa7; color: #856404; padding: 10px; border-radius: 8px; margin: 10px 0; text-align: center;">
        <strong>Google ile giriş yaptınız!</strong><br>
        Şifrenizi değiştirmek için Google hesabınızın ayarlarını kullanın.
    </div>';
}
?> 