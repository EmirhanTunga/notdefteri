<?php
session_start();
require_once 'db.php';

echo "<h2>Debug Bilgileri</h2>";

echo "<h3>Session Bilgileri:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h3>GET Parametreleri:</h3>";
echo "<pre>";
print_r($_GET);
echo "</pre>";

echo "<h3>POST Parametreleri:</h3>";
echo "<pre>";
print_r($_POST);
echo "</pre>";

if (isset($_SESSION['username'])) {
    echo "<h3>Kullanıcı Sorgusu:</h3>";
    try {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute([$_SESSION['username']]);
        $user = $stmt->fetch();
        echo "<pre>";
        print_r($user);
        echo "</pre>";
    } catch (Exception $e) {
        echo "Veritabanı hatası: " . $e->getMessage();
    }
}

echo "<h3>Veritabanı Bağlantısı:</h3>";
try {
    $stmt = $pdo->query("SELECT 1");
    echo "✅ Veritabanı bağlantısı başarılı";
} catch (Exception $e) {
    echo "❌ Veritabanı bağlantı hatası: " . $e->getMessage();
}

echo "<h3>Tablolar:</h3>";
try {
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "<pre>";
    print_r($tables);
    echo "</pre>";
} catch (Exception $e) {
    echo "Tablo listesi alınamadı: " . $e->getMessage();
}
?>