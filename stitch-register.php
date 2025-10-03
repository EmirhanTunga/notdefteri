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
                
                <div class="flex items-center">
                    <input 
                        id="terms" 
                        name="terms" 
                        type="checkbox" 
                        class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded"
                        required
                    >
                    <label for="terms" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                        <a href="#" class="text-primary hover:underline">Kullanım şartlarını</a> kabul ediyorum
                    </label>
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
            
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300 dark:border-gray-700"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white dark:bg-background-dark text-gray-500 dark:text-gray-400">
                        Veya devam edin
                    </span>
                </div>
            </div>
            
            <div class="space-y-4">
                <a href="google_callback.php" class="w-full flex items-center justify-center py-3 px-4 border border-gray-300 dark:border-gray-700 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-background-dark hover:bg-background-light dark:hover:bg-primary/10 transition-colors">
                    <svg class="w-5 h-5 mr-2" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                        <path d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12s5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24s8.955,20,20,20s20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z" fill="#4285F4"></path>
                        <path d="M43.611,20.083L43.595,20L42,20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571l5.657,5.657C41.474,34.29,44,29.56,44,24C44,22.659,43.862,21.35,43.611,20.083z" fill="#34A853"></path>
                        <path d="M10.21,29.29C9.537,27.262,9.158,25.17,9.158,23s0.379-4.262,1.052-6.29l-5.657-5.657C3.593,14.659,3,18.7,3,23s0.593,8.341,2.553,11.947L10.21,29.29z" fill="#FBBC05"></path>
                        <path d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-5.657-5.657c-1.854,1.407-4.148,2.24-6.752,2.24c-4.404,0-8.158-2.535-9.886-6.08h-5.83v5.99C7.158,38.648,14.93,44,24,44z" fill="#EA4335"></path>
                        <path d="M3,3h42v42H3V3z" fill="none"></path>
                    </svg>
                    Google ile kayıt ol
                </a>
            </div>
            
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