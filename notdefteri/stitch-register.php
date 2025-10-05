<?php
session_start();

// Eğer kullanıcı zaten giriş yapmışsa ana sayfaya yönlendir
if (isset($_SESSION['username'])) {
    header('Location: stitch-index.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once 'db.php';
    
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (!$username || !$email || !$phone || !$password || !$confirm_password) {
        $error = 'Tüm alanları doldurun';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Geçerli bir e-posta adresi girin';
    } elseif ($password !== $confirm_password) {
        $error = 'Şifreler eşleşmiyor';
    } elseif (strlen($password) < 6) {
        $error = 'Şifre en az 6 karakter olmalı';
    } else {
        // Kullanıcı adı var mı kontrol et
        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = 'Kullanıcı adı zaten var';
        } else {
            // E-posta var mı kontrol et
            $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = 'Bu e-posta adresi zaten kayıtlı';
            } else {
                // Kayıt
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('INSERT INTO users (username, email, phone, password) VALUES (?, ?, ?, ?)');
                if ($stmt->execute([$username, $email, $phone, $hash])) {
                    $_SESSION['username'] = $username;
                    header('Location: stitch-index.php');
                    exit();
                } else {
                    $error = 'Kayıt sırasında bir hata oluştu';
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Not Defteri - Kayıt Ol</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#137fec",
                        "background-light": "#f6f7f8",
                        "background-dark": "#101922",
                    },
                    fontFamily: {
                        "display": ["Inter"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-gray-800 dark:text-gray-200">
    <div class="flex items-center justify-center min-h-screen py-12">
        <div class="w-full max-w-md p-8 space-y-6 bg-white dark:bg-background-dark rounded-xl shadow-lg">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Not Defteri</h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Hesabınızı oluşturun ve başlayın.</p>
            </div>
            
            <?php if ($error): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="space-y-4">
                <div>
                    <label class="sr-only" for="username">Kullanıcı Adı</label>
                    <input 
                        autocomplete="username" 
                        class="w-full px-4 py-3 bg-background-light dark:bg-background-dark border border-gray-300 dark:border-gray-700 rounded-lg placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" 
                        id="username" 
                        name="username" 
                        placeholder="Kullanıcı adı" 
                        required 
                        type="text"
                        value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                    />
                </div>
                
                <div>
                    <label class="sr-only" for="email">E-posta</label>
                    <input 
                        autocomplete="email" 
                        class="w-full px-4 py-3 bg-background-light dark:bg-background-dark border border-gray-300 dark:border-gray-700 rounded-lg placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" 
                        id="email" 
                        name="email" 
                        placeholder="E-posta adresi" 
                        required 
                        type="email"
                        value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                    />
                </div>
                
                <div>
                    <label class="sr-only" for="phone">Telefon</label>
                    <input 
                        autocomplete="tel" 
                        class="w-full px-4 py-3 bg-background-light dark:bg-background-dark border border-gray-300 dark:border-gray-700 rounded-lg placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" 
                        id="phone" 
                        name="phone" 
                        placeholder="Telefon numarası" 
                        required 
                        type="tel"
                        value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>"
                    />
                </div>
                
                <div>
                    <label class="sr-only" for="password">Şifre</label>
                    <input 
                        autocomplete="new-password" 
                        class="w-full px-4 py-3 bg-background-light dark:bg-background-dark border border-gray-300 dark:border-gray-700 rounded-lg placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" 
                        id="password" 
                        name="password" 
                        placeholder="Şifre (en az 6 karakter)" 
                        required 
                        type="password"
                        minlength="6"
                    />
                </div>
                
                <div>
                    <label class="sr-only" for="confirm_password">Şifre Tekrar</label>
                    <input 
                        autocomplete="new-password" 
                        class="w-full px-4 py-3 bg-background-light dark:bg-background-dark border border-gray-300 dark:border-gray-700 rounded-lg placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" 
                        id="confirm_password" 
                        name="confirm_password" 
                        placeholder="Şifre tekrar" 
                        required 
                        type="password"
                        minlength="6"
                    />
                </div>
                
                
                
                <div>
                    <button 
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors" 
                        type="submit"
                    >
                        Hesap Oluştur
                    </button>
                </div>
            </form>
            
            
            
            <p class="text-sm text-center text-gray-500 dark:text-gray-400">
                Zaten hesabınız var mı?
                <a class="font-medium text-primary hover:underline" href="stitch-login.php">
                    Giriş yapın
                </a>
            </p>
        </div>
    </div>
</body>
</html>