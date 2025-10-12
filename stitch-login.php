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
                    animation: {
                        'fade-in': 'fadeIn 0.8s ease-out',
                        'slide-up': 'slideUp 0.6s ease-out',
                        'float': 'float 3s ease-in-out infinite',
                        'pulse-slow': 'pulse 3s ease-in-out infinite',
                        'gradient': 'gradient 8s ease infinite',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' }
                        },
                        slideUp: {
                            '0%': { opacity: '0', transform: 'translateY(30px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' }
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-10px)' }
                        },
                        gradient: {
                            '0%, 100%': { backgroundPosition: '0% 50%' },
                            '50%': { backgroundPosition: '100% 50%' }
                        }
                    }
                },
            },
        }
    </script>
    <style>
        .gradient-bg {
            background: linear-gradient(-45deg, #667eea, #764ba2, #f093fb, #f5576c, #4facfe, #00f2fe);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
        }
        
        .glass-effect {
            backdrop-filter: blur(20px);
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid rgba(59, 130, 246, 0.3);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
        }
        
        .dark .glass-effect {
            background: rgba(15, 23, 42, 0.9);
            border: 1px solid rgba(99, 102, 241, 0.4);
        }
        
        .floating-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            pointer-events: none;
        }
        
        .shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: float 6s ease-in-out infinite;
        }
        
        .shape:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }
        
        .shape:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 60%;
            right: 10%;
            animation-delay: 2s;
        }
        
        .shape:nth-child(3) {
            width: 60px;
            height: 60px;
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }
        
        .shape:nth-child(4) {
            width: 100px;
            height: 100px;
            top: 30%;
            right: 30%;
            animation-delay: 1s;
        }
        
        .shape:nth-child(5) {
            width: 40px;
            height: 40px;
            bottom: 40%;
            right: 20%;
            animation-delay: 3s;
        }
        
        .login-container {
            animation: slideUp 0.8s ease-out;
        }
        
        .form-group {
            animation: fadeIn 0.6s ease-out;
        }
        
        .form-group:nth-child(1) { animation-delay: 0.1s; }
        .form-group:nth-child(2) { animation-delay: 0.2s; }
        .form-group:nth-child(3) { animation-delay: 0.3s; }
        .form-group:nth-child(4) { animation-delay: 0.4s; }
        
        .input-focus {
            transition: all 0.3s ease;
            color: white !important;
        }
        
        .input-focus::placeholder {
            color: rgba(191, 219, 254, 0.7) !important;
        }
        
        .input-focus:-webkit-autofill,
        .input-focus:-webkit-autofill:hover,
        .input-focus:-webkit-autofill:focus,
        .input-focus:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 30px rgba(51, 65, 85, 0.5) inset !important;
            -webkit-text-fill-color: white !important;
        }
        
        .input-focus:focus {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
        }
        
        .btn-hover {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.4);
        }
        
        .btn-hover::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn-hover:hover::before {
            left: 100%;
        }
        
        .logo-animation {
            animation: float 3s ease-in-out infinite;
        }
        
        @media (prefers-reduced-motion: reduce) {
            .gradient-bg,
            .shape,
            .login-container,
            .form-group,
            .logo-animation {
                animation: none;
            }
        }
    </style>
</head>
<body class="gradient-bg font-display text-white min-h-screen relative">
    <!-- Floating Shapes Background -->
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>
    
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="login-container w-full max-w-md p-8 space-y-6 glass-effect rounded-2xl shadow-2xl">
            <div class="text-center">
                <div class="logo-animation mb-4">
                    <div class="w-16 h-16 mx-auto bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center backdrop-blur-sm shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
                <h1 class="text-3xl font-bold text-white mb-2">Not Defteri</h1>
                <p class="text-sm text-white/80">Hoş geldiniz! Lütfen giriş bilgilerinizi girin.</p>
            </div>
            
            <?php if ($error): ?>
                <div class="form-group bg-red-500/20 border border-red-500/30 text-red-100 px-4 py-3 rounded-lg backdrop-blur-sm">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="space-y-6">
                <div class="form-group">
                    <label class="sr-only" for="username">Kullanıcı Adı</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <input 
                            autocomplete="username" 
                            class="input-focus w-full pl-10 pr-4 py-3 bg-slate-700/50 border border-blue-400/30 rounded-lg placeholder-blue-200/70 text-white focus:outline-none focus:ring-2 focus:ring-blue-400/50 focus:border-blue-400/50 backdrop-blur-sm" 
                            id="username" 
                            name="username" 
                            placeholder="Kullanıcı adı" 
                            required 
                            type="text"
                            value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                            style="color: white !important;"
                        />
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="sr-only" for="password">Şifre</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <input 
                            autocomplete="current-password" 
                            class="input-focus w-full pl-10 pr-4 py-3 bg-slate-700/50 border border-blue-400/30 rounded-lg placeholder-blue-200/70 text-white focus:outline-none focus:ring-2 focus:ring-blue-400/50 focus:border-blue-400/50 backdrop-blur-sm" 
                            id="password" 
                            name="password" 
                            placeholder="Şifre" 
                            required 
                            type="password"
                            style="color: white !important;"
                        />
                    </div>
                </div>
                
                <div class="form-group flex items-center justify-between">
                    <div class="flex items-center">
                        <input 
                            id="remember-me" 
                            name="remember-me" 
                            type="checkbox" 
                            class="h-4 w-4 text-blue-600 focus:ring-blue-400/50 border-blue-400/50 rounded bg-slate-700/50"
                        >
                        <label for="remember-me" class="ml-2 block text-sm text-white/90">
                            Beni hatırla
                        </label>
                    </div>
                    <div class="text-sm">
                        <a href="forgot_password.php" class="text-white/80 hover:text-white hover:underline transition-colors">
                            Şifremi unuttum
                        </a>
                    </div>
                </div>
                
                <div class="form-group">
                    <button 
                        class="btn-hover w-full flex justify-center py-3 px-4 border border-transparent rounded-lg text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-400/50 backdrop-blur-sm" 
                        type="submit"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                        </svg>
                        Giriş Yap
                    </button>
                </div>
            </form>
            
            <div class="form-group text-center">
                <p class="text-sm text-white/80">
                    Hesabınız yok mu?
                    <a class="font-medium text-white hover:text-white/80 hover:underline transition-colors" href="stitch-register.php">
                        Kayıt olun
                    </a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>