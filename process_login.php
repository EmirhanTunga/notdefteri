<?php
session_start();
require 'db.php';

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$action = $_POST['action'] ?? '';
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');

if ($action === 'register') {
    // Kayıt işlemi için tüm alanlar gerekli
    if (!$username || !$password || !$email || !$phone) {
        header('Location: login.php?error=Tüm alanları doldurun');
        exit();
    }
    
    // E-posta formatı kontrolü
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: login.php?error=Geçerli bir e-posta adresi girin');
        exit();
    }
    
    // Kullanıcı adı var mı kontrol et
    $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        header('Location: login.php?error=Kullanıcı adı zaten var');
        exit();
    }
    
    // E-posta var mı kontrol et
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        header('Location: login.php?error=Bu e-posta adresi zaten kayıtlı');
        exit();
    }
    
    // Kayıt
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO users (username, email, phone, password) VALUES (?, ?, ?, ?)');
    $stmt->execute([$username, $email, $phone, $hash]);
    $_SESSION['username'] = $username;
    header('Location: stitch-index.php');
    exit();
} elseif ($action === 'login') {
    // Giriş işlemi için sadece kullanıcı adı ve şifre gerekli
    if (!$username || !$password) {
        header('Location: login.php?error=Kullanıcı adı ve şifre gerekli');
        exit();
    }
    
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if (!$user || !password_verify($password, $user['password'])) {
        header('Location: login.php?error=Hatalı kullanıcı adı veya şifre');
        exit();
    }
    $_SESSION['username'] = $username;
    header('Location: stitch-index.php');
    exit();
}
header('Location: login.php?error=Bilinmeyen istek');
exit(); 