<?php
session_start();

// Eğer kullanıcı zaten giriş yapmışsa ana sayfaya yönlendir
if (isset($_SESSION['username'])) {
    header('Location: stitch-index.php');
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once 'db.php';
    
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (!$username || !$password) {
        $error = 'Kullanıcı adı ve şifre gerekli';
    } else {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if (!$user || !password_verify($password, $user['password'])) {
            $error = 'Hatalı kullanıcı adı veya şifre';
        } else {
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $user['id']; // User ID'yi de session'a ekle
            
            // Debug için
            error_log("User logged in: " . $username . " (ID: " . $user['id'] . ")");
            
            header('Location: stitch-index.php');
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Not Defteri - Giriş</title>
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
    <div class="flex items-center justify-center min-h-screen">
        <div class="w-full max-w-md p-8 space-y-6 bg-white dark:bg-background-dark rounded-xl shadow-lg">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Not Defteri</h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Hoş geldiniz! Lütfen giriş bilgilerinizi girin.</p>
            </div>
            
            <?php if ($error): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="space-y-6">
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
                    <label class="sr-only" for="password">Şifre</label>
                    <input 
                        autocomplete="current-password" 
                        class="w-full px-4 py-3 bg-background-light dark:bg-background-dark border border-gray-300 dark:border-gray-700 rounded-lg placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" 
                        id="password" 
                        name="password" 
                        placeholder="Şifre" 
                        required 
                        type="password"
                    />
                </div>
                
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input 
                            id="remember-me" 
                            name="remember-me" 
                            type="checkbox" 
                            class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded"
                        >
                        <label for="remember-me" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                            Beni hatırla
                        </label>
                    </div>
                    <div class="text-sm">
                        <a href="forgot_password.php" class="text-primary hover:underline">
                            Şifremi unuttum
                        </a>
                    </div>
                </div>
                
                <div>
                    <button 
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors" 
                        type="submit"
                    >
                        Giriş Yap
                    </button>
                </div>
            </form>
            
            
            
            <p class="text-sm text-center text-gray-500 dark:text-gray-400">
                Hesabınız yok mu?
                <a class="font-medium text-primary hover:underline" href="stitch-register.php">
                    Kayıt olun
                </a>
            </p>
        </div>
    </div>
</body>
</html>