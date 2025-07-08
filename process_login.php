<?php
session_start();
require 'db.php';

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$action = $_POST['action'] ?? '';

if (!$username || !$password) {
    header('Location: login.php?error=Boş alan bırakmayın');
    exit();
}

if ($action === 'register') {
    // Kullanıcı var mı kontrol et
    $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        header('Location: login.php?error=Kullanıcı adı zaten var');
        exit();
    }
    // Kayıt
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
    $stmt->execute([$username, $hash]);
    $_SESSION['username'] = $username;
    header('Location: index.php');
    exit();
} elseif ($action === 'login') {
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if (!$user || !password_verify($password, $user['password'])) {
        header('Location: login.php?error=Hatalı kullanıcı adı veya şifre');
        exit();
    }
    $_SESSION['username'] = $username;
    header('Location: index.php');
    exit();
}
header('Location: login.php?error=Bilinmeyen istek');
exit(); 