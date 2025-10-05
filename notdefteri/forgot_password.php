<?php
session_start();

require_once 'db.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier'] ?? ''); // email or phone
    if (!$identifier) {
        $error = 'Lütfen e-posta adresinizi veya telefon numaranızı girin';
    } else {
        // Kullanıcıyı e-posta veya telefon ile bul
        $stmt = $pdo->prepare('SELECT id, username, email, phone FROM users WHERE email = ? OR phone = ? LIMIT 1');
        $stmt->execute([$identifier, $identifier]);
        $user = $stmt->fetch();

        if (!$user) {
            $error = 'Bu bilgilere sahip kullanıcı bulunamadı';
        } else {
            // Geçici şifre üret ve e-postaya gönder (SMS opsiyonel)
            $tempPassword = substr(bin2hex(random_bytes(6)), 0, 10);
            $hash = password_hash($tempPassword, PASSWORD_DEFAULT);

            $update = $pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
            $update->execute([$hash, $user['id']]);

            // Basit mail - PHP mail() kullanılabilir; prod ortamda SMTP önerilir
            if (!empty($user['email'])) {
                $to = $user['email'];
                $subject = 'Not Defteri - Geçici Şifre';
                $body = "Merhaba {$user['username']},\n\nGeçici şifreniz: {$tempPassword}\nLütfen giriş yaptıktan sonra şifrenizi değiştirin.";
                @mail($to, $subject, $body);
            }

            // SMS entegrasyonu için burada bir SMS servisi (Twilio vb.) çağrılabilir
            // if (!empty($user['phone'])) { ... }

            $message = 'Geçici şifre oluşturuldu. E-posta adresinize gönderildi.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Şifremi Unuttum</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-gray-800 dark:text-gray-200">
    <div class="flex items-center justify-center min-h-screen">
        <div class="w-full max-w-md p-8 space-y-6 bg-white dark:bg-background-dark rounded-xl shadow-lg">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Şifremi Unuttum</h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">E‑posta veya telefon ile geçici şifre oluşturun.</p>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($message): ?>
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <div>
                    <label class="sr-only" for="identifier">E‑posta veya Telefon</label>
                    <input 
                        autocomplete="username" 
                        class="w-full px-4 py-3 bg-background-light dark:bg-background-dark border border-gray-300 dark:border-gray-700 rounded-lg placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" 
                        id="identifier" 
                        name="identifier" 
                        placeholder="E‑posta adresi veya telefon" 
                        required 
                        type="text"
                    />
                </div>

                <div>
                    <button 
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors" 
                        type="submit"
                    >
                        Geçici Şifre Gönder
                    </button>
                </div>
            </form>

            <p class="text-sm text-center text-gray-500 dark:text-gray-400">
                <a class="font-medium text-primary hover:underline" href="stitch-login.php">Giriş sayfasına dön</a>
            </p>
        </div>
    </div>
</body>
</html>


