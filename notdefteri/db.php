<?php
$host = 'localhost';
$db   = 'notdefteri';
$user = 'root'; // Kendi kullanıcı adını yaz
$pass = 'rootroot';     // Kendi şifreni yaz
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die('Veritabanı bağlantı hatası: ' . $e->getMessage());
}

// Mesajlaşma için tablo kontrolü (ilk yüklemede hata olmasın diye)
try {
    $pdo->query('SELECT 1 FROM messages LIMIT 1');
} catch (Exception $e) {
    die('Lütfen önce aşağıdaki SQL ile messages tablosunu oluşturun:<br><pre>CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message TEXT NOT NULL,
    sent_at DATETIME DEFAULT CURRENT_TIMESTAMP
);</pre>');
} 