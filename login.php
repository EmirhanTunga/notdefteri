<?php
session_start();
if (isset($_SESSION['username'])) {
    header('Location: stitch-index.php');
    exit();
}
// Yeni Stitch login sayfasına yönlendir
header('Location: stitch-login.php');
exit();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Not Defterim - Giriş</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Quicksand:wght@400;600&display=swap" rel="stylesheet">
    <style>
    html, body {
        height: 100vh;
        margin: 0;
        padding: 0;
    }
    body {
        background: linear-gradient(120deg, #f6d365 0%, #fda085 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        width: 100vw;
    }
    .login-container {
        background: rgba(255,255,255,0.97);
        border-radius: 28px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.13);
        padding: 30px 24px 26px 24px;
        width: 370px;
        max-width: 95vw;
        display: flex;
        flex-direction: column;
        align-items: center;
        animation: popin 0.7s cubic-bezier(.68,-0.55,.27,1.55);
        position: relative;
        margin: 0 auto;
        box-sizing: border-box;
    }
    .login-container h1 {
        font-family: 'Pacifico', cursive;
        font-size: 2.7em;
        color: #f76d6d;
        margin-bottom: 18px;
        letter-spacing: 1.5px;
        text-shadow: 0 2px 8px #fda08533;
        animation: floatTitle 2.5s infinite alternate;
    }
    @keyframes floatTitle {
        0% { transform: scale(1) translateY(0); }
        100% { transform: scale(1.04) translateY(-6px); }
    }
    .login-form {
        width: 100%;
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-bottom: 10px;
    }
    .login-form input {
        width: 100%;
        padding: 11px 12px;
        border-radius: 12px;
        border: 1.5px solid #fda085;
        font-size: 1.08em;
        font-family: 'Quicksand', sans-serif;
        background: #fff7e6;
        color: #d35400;
        transition: box-shadow 0.2s, border 0.2s;
        margin-bottom: 2px;
        box-sizing: border-box;
    }
    .login-form input:focus {
        box-shadow: 0 0 0 2px #f6d365;
        border: 2px solid #fda085;
        outline: none;
        background: #fffbe6;
    }
    .login-form button {
        width: 100%;
        padding: 12px;
        border-radius: 12px;
        border: none;
        font-size: 1.1em;
        font-family: 'Quicksand', sans-serif;
        font-weight: bold;
        background: linear-gradient(90deg, #fda085, #f6d365);
        color: #fff;
        margin-top: 4px;
        cursor: pointer;
        transition: background 0.2s, transform 0.1s;
        box-shadow: 0 2px 8px #fda08522;
    }
    .login-form button:hover {
        background: linear-gradient(90deg, #f6d365, #fda085);
        transform: scale(1.04);
    }
    .error {
        color: #fff;
        background: #f76d6d;
        padding: 10px 14px;
        border-radius: 10px;
        margin-top: 12px;
        text-align: center;
        font-size: 1.08em;
        animation: shake 0.3s;
    }
    @keyframes shake {
        0% { transform: translateX(-5px); }
        25% { transform: translateX(5px); }
        50% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
        100% { transform: translateX(0); }
    }
    .login-footer {
        margin-top: 32px;
        color: #f76d6d;
        font-family: 'Pacifico', cursive;
        font-size: 1.25em;
        text-align: center;
        letter-spacing: 1px;
        text-shadow: 0 2px 8px #fda08533;
        opacity: 0.85;
        user-select: none;
    }
    .register-hint {
        margin-top: 8px;
        font-size: 0.98em;
        color: #bdbdbd;
        font-family: 'Quicksand', cursive, sans-serif;
        text-align: center;
        letter-spacing: 0.2px;
        opacity: 0.85;
        user-select: none;
    }
    
    
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Not Defterim</h1>
        
        <div id="loginForm" class="login-form">
            <h3 style="text-align: center; color: #f76d6d; margin-bottom: 15px;">Giriş Yap</h3>
            <form method="POST" action="process_login.php">
                <input type="text" name="username" placeholder="Kullanıcı Adı" required autocomplete="username">
                <input type="password" name="password" placeholder="Şifre" required autocomplete="current-password">
                <button type="submit" name="action" value="login">Giriş Yap</button>
            </form>
            <div style="text-align: center; margin-top: 15px;">
                <button type="button" onclick="showRegisterForm()" style="background: none; border: none; color: #fda085; text-decoration: underline; cursor: pointer; font-size: 0.9em;">Hesabın yok mu? Kayıt ol</button>
            </div>
            
        </div>
        
        <div id="registerForm" class="login-form" style="display: none;">
            <h3 style="text-align: center; color: #f76d6d; margin-bottom: 15px;">Kayıt Ol</h3>
            <form method="POST" action="process_login.php">
                <input type="text" name="username" placeholder="Kullanıcı Adı" required autocomplete="username">
                <input type="email" name="email" placeholder="E-posta Adresi" required autocomplete="email">
                <input type="tel" name="phone" placeholder="Telefon Numarası" required autocomplete="tel">
                <input type="password" name="password" placeholder="Şifre" required autocomplete="new-password">
                <button type="submit" name="action" value="register">Kayıt Ol</button>
            </form>
            <div style="text-align: center; margin-top: 15px;">
                <button type="button" onclick="showLoginForm()" style="background: none; border: none; color: #fda085; text-decoration: underline; cursor: pointer; font-size: 0.9em;">Zaten hesabın var mı? Giriş yap</button>
            </div>
        </div>
        <?php if (isset($_GET['error'])): ?>
            <div class="error"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>
    </div>
    
    <script>
        function showRegisterForm() {
            document.getElementById('loginForm').style.display = 'none';
            document.getElementById('registerForm').style.display = 'block';
        }
        
        function showLoginForm() {
            document.getElementById('registerForm').style.display = 'none';
            document.getElementById('loginForm').style.display = 'block';
        }
    </script>
</body>
</html> 