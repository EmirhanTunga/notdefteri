<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: stitch-login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Sayfası</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: #137fec;
            color: white;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Test Sayfası Başarılı!</h1>
        </div>
        
        <div class="success">
            Giriş başarılı! Kullanıcı: <?php echo htmlspecialchars($_SESSION['username']); ?>
        </div>
        
        <h2>Sistem Durumu:</h2>
        <ul>
            <li>✅ PHP çalışıyor</li>
            <li>✅ Session aktif</li>
            <li>✅ Kullanıcı giriş yapmış</li>
        </ul>
        
        <p><a href="stitch-index.php" style="background: #137fec; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Ana Sayfaya Git</a></p>
        <p><a href="logout.php" style="background: #dc3545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Çıkış Yap</a></p>
    </div>
</body>
</html>