<?php
// Oturum başlat
session_start();
// Kullanıcı giriş kontrolü
if (!isset($_SESSION['username'])) {
    header('Location: stitch-login.php');
    exit();
}
// Kullanıcı bilgilerini çek
require_once 'db.php';


try {
    $stmt = $pdo->prepare('SELECT username, email FROM users WHERE username = ?');
    $stmt->execute([$_SESSION['username']]);
    $user = $stmt->fetch();
    
    if (!$user) {
        // Kullanıcı bulunamadı, oturumu sonlandır
        session_destroy();
        header('Location: stitch-login.php');
        exit();
    }
} catch (Exception $e) {
    // Veritabanı hatası
    error_log('Database error in stitch-index.php: ' . $e->getMessage());
    session_destroy();
    header('Location: stitch-login.php?error=database');
    exit();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Not Defteri - Stitch Design</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#4a90e2",
                        "primary-accent": "#50e3c2",
                        "background-light": "#f7f9fc",
                        "background-dark": "#1a1d21",
                        "foreground-light": "#0d141b",
                        "foreground-dark": "#ffffff",
                        "subtle-light": "#6b7280",
                        "subtle-dark": "#9ca3af",
                        "border-light": "#e5e7eb",
                        "border-dark": "#374151",
                        "card-light": "#ffffff",
                        "card-dark": "#252a31",
                        "accent-light": "#e0f2fe",
                        "accent-dark": "#374151"
                    },
                    fontFamily: {
                        "display": ["Inter", "sans-serif"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.5rem",
                        "lg": "0.75rem",
                        "xl": "1rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
    <style>
        .icon {
            width: 24px;
            height: 24px;
            fill: currentColor;
        }
        .icon-sm {
            width: 20px;
            height: 20px;
            fill: currentColor;
        }
        .icon-xs {
            width: 16px;
            height: 16px;
            fill: currentColor;
        }
        
        /* Smooth Background Gradients */
        .smooth-bg-light {
            background: linear-gradient(135deg, 
                #f7f9fc 0%, 
                #e8f2ff 25%, 
                #f0f8ff 50%, 
                #e6f3ff 75%, 
                #f7f9fc 100%);
            background-size: 400% 400%;
            animation: gradientShift 20s ease infinite;
        }
        
        .smooth-bg-dark {
            background: linear-gradient(135deg, 
                #0a0e13 0%, 
                #1a1d21 15%, 
                #1e2329 25%, 
                #252a31 35%, 
                #2a2f36 50%, 
                #1e2329 65%, 
                #1a1d21 80%, 
                #0a0e13 100%);
            background-size: 400% 400%;
            animation: gradientShift 20s ease infinite;
        }
        
        /* Dark mode için ekstra renkli gradient */
        .dark .smooth-bg-dark {
            background: linear-gradient(135deg, 
                #0a0e13 0%, 
                #1a1d21 8%, 
                #1e2329 16%, 
                #252a31 24%, 
                #2a2f36 32%, 
                #1e2329 40%, 
                #1a1d21 48%, 
                #0a0e13 56%, 
                #1a1d21 64%, 
                #0a0e13 72%, 
                #1a1d21 80%, 
                #0a0e13 88%, 
                #1a1d21 96%, 
                #0a0e13 100%);
            background-size: 400% 400%;
            animation: gradientShift 20s ease infinite;
        }
        
        /* Koyu tema için alternatif renkli gradient */
        .dark .smooth-bg-dark::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, 
                rgba(74, 144, 226, 0.03) 0%, 
                rgba(80, 227, 194, 0.02) 25%, 
                rgba(74, 144, 226, 0.03) 50%, 
                rgba(80, 227, 194, 0.02) 75%, 
                rgba(74, 144, 226, 0.03) 100%);
            background-size: 200% 200%;
            animation: gradientShift 15s ease infinite;
            pointer-events: none;
            z-index: -2;
        }
        
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        /* Floating Elements */
        .floating-elements {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
            overflow: hidden;
        }
        
        .floating-element {
            position: absolute;
            border-radius: 50%;
            opacity: 0.1;
            animation: float 15s ease-in-out infinite;
        }
        
        .floating-element:nth-child(1) {
            width: 200px;
            height: 200px;
            background: linear-gradient(45deg, #4a90e2, #50e3c2);
            top: 10%;
            left: 10%;
            animation-delay: 0s;
            box-shadow: 0 0 40px rgba(74, 144, 226, 0.2);
        }
        
        .floating-element:nth-child(2) {
            width: 150px;
            height: 150px;
            background: linear-gradient(45deg, #50e3c2, #4a90e2);
            top: 60%;
            right: 15%;
            animation-delay: 5s;
            box-shadow: 0 0 40px rgba(80, 227, 194, 0.2);
        }
        
        .floating-element:nth-child(3) {
            width: 100px;
            height: 100px;
            background: linear-gradient(45deg, #4a90e2, #50e3c2);
            bottom: 20%;
            left: 20%;
            animation-delay: 10s;
            box-shadow: 0 0 40px rgba(74, 144, 226, 0.2);
        }
        
        .floating-element:nth-child(4) {
            width: 120px;
            height: 120px;
            background: linear-gradient(45deg, #50e3c2, #4a90e2);
            top: 30%;
            right: 30%;
            animation-delay: 7s;
            box-shadow: 0 0 40px rgba(80, 227, 194, 0.2);
        }
        
        .floating-element:nth-child(5) {
            width: 80px;
            height: 80px;
            background: linear-gradient(45deg, #4a90e2, #50e3c2);
            bottom: 40%;
            right: 20%;
            animation-delay: 12s;
            box-shadow: 0 0 40px rgba(74, 144, 226, 0.2);
        }
        
        @keyframes float {
            0%, 100% { 
                transform: translateY(0px) rotate(0deg); 
            }
            25% { 
                transform: translateY(-20px) rotate(90deg); 
            }
            50% { 
                transform: translateY(-10px) rotate(180deg); 
            }
            75% { 
                transform: translateY(-30px) rotate(270deg); 
            }
        }
        
        /* Dark mode floating elements */
        .dark .floating-element:nth-child(1) {
            background: linear-gradient(45deg, #4a90e2, #50e3c2);
            opacity: 0.15;
            box-shadow: 0 0 30px rgba(74, 144, 226, 0.3);
        }
        
        .dark .floating-element:nth-child(2) {
            background: linear-gradient(45deg, #50e3c2, #4a90e2);
            opacity: 0.15;
            box-shadow: 0 0 30px rgba(80, 227, 194, 0.3);
        }
        
        .dark .floating-element:nth-child(3) {
            background: linear-gradient(45deg, #4a90e2, #50e3c2);
            opacity: 0.15;
            box-shadow: 0 0 30px rgba(74, 144, 226, 0.3);
        }
        
        .dark .floating-element:nth-child(4) {
            background: linear-gradient(45deg, #50e3c2, #4a90e2);
            opacity: 0.15;
            box-shadow: 0 0 30px rgba(80, 227, 194, 0.3);
        }
        
        .dark .floating-element:nth-child(5) {
            background: linear-gradient(45deg, #4a90e2, #50e3c2);
            opacity: 0.15;
            box-shadow: 0 0 30px rgba(74, 144, 226, 0.3);
        }
        
        /* Koyu tema için doğru CSS selector - birden fazla selector */
        .dark .smooth-bg-dark,
        html.dark .smooth-bg-dark,
        body.dark .smooth-bg-dark,
        .dark.smooth-bg-dark {
            background: linear-gradient(135deg, 
                #0a0e13 0%, 
                #1a1d21 12%, 
                #1e2329 24%, 
                #252a31 36%, 
                #2a2f36 48%, 
                #1e2329 60%, 
                #1a1d21 72%, 
                #0a0e13 84%, 
                #1a1d21 96%, 
                #0a0e13 100%) !important;
            background-size: 400% 400% !important;
            animation: gradientShift 20s ease infinite !important;
        }
        
        /* Koyu tema için renkli overlay */
        .dark .smooth-bg-dark::before,
        html.dark .smooth-bg-dark::before,
        body.dark .smooth-bg-dark::before,
        .dark.smooth-bg-dark::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, 
                rgba(74, 144, 226, 0.03) 0%, 
                rgba(80, 227, 194, 0.02) 25%, 
                rgba(74, 144, 226, 0.03) 50%, 
                rgba(80, 227, 194, 0.02) 75%, 
                rgba(74, 144, 226, 0.03) 100%);
            background-size: 200% 200%;
            animation: gradientShift 15s ease infinite;
            pointer-events: none;
            z-index: -2;
        }
        
        /* Reduced motion support */
        @media (prefers-reduced-motion: reduce) {
            .smooth-bg-light,
            .smooth-bg-dark,
            .floating-element {
                animation: none;
            }
        }
    </style>
</head>
<body class="smooth-bg-light dark:smooth-bg-dark font-display text-foreground-light dark:text-foreground-dark">
    <!-- Floating Background Elements -->
    <div class="floating-elements">
        <div class="floating-element"></div>
        <div class="floating-element"></div>
        <div class="floating-element"></div>
        <div class="floating-element"></div>
        <div class="floating-element"></div>
    </div>
    
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-card-light/80 dark:bg-card-dark/80 backdrop-blur-sm border-r border-border-light/50 dark:border-border-dark/50 flex flex-col">
            <div class="p-4 border-b border-border-light dark:border-border-dark">
                <h1 class="text-lg font-bold">Not Defteri</h1>
                <p class="text-sm text-subtle-light dark:text-subtle-dark">Tüm notlar</p>
            </div>
            
            <nav class="flex-1 px-2 py-4 space-y-1">
                <a class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-subtle-light dark:text-subtle-dark hover:text-foreground-light dark:hover:text-foreground-dark hover:bg-accent-light dark:hover:bg-accent-dark transition-all duration-200 relative" href="#" data-section="dashboard">
                    <svg class="icon transition-transform duration-200" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg">
                        <path d="M218.83,103.77l-80-75.48a1.14,1.14,0,0,1-.11-.11,16,16,0,0,0-21.53,0l-.11.11L37.17,103.77A16,16,0,0,0,32,115.55V208a16,16,0,0,0,16,16H96a16,16,0,0,0,16-16V160h32v48a16,16,0,0,0,16,16h48a16,16,0,0,0,16-16V115.55A16,16,0,0,0,218.83,103.77ZM208,208H160V160a16,16,0,0,0-16-16H112a16,16,0,0,0-16,16v48H48V115.55l.11-.1L128,40l79.9,75.43.11.1Z"></path>
                    </svg>
                    <span>Ana Sayfa</span>
                </a>
                
                <a class="nav-link active flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-subtle-light dark:text-subtle-dark hover:text-foreground-light dark:hover:text-foreground-dark hover:bg-accent-light dark:hover:bg-accent-dark transition-all duration-200 relative" href="#" data-section="notes">
                    <svg class="icon transition-transform duration-200" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg">
                        <path d="M213.66,82.34l-56-56A8,8,0,0,0,152,24H56A16,16,0,0,0,40,40V216a16,16,0,0,0,16,16H200a16,16,0,0,0,16-16V88A8,8,0,0,0,213.66,82.34ZM152,88V44l44,44Z"></path>
                    </svg>
                    <span>Tüm Notlar</span>
                </a>
                
                <a class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-subtle-light dark:text-subtle-dark hover:text-foreground-light dark:hover:text-foreground-dark hover:bg-accent-light dark:hover:bg-accent-dark transition-all duration-200 relative" href="#" data-section="favorites">
                    <svg class="icon transition-transform duration-200" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg">
                        <path d="M224.6,88.1l-56-56A8,8,0,0,0,162.77,32H56A16,16,0,0,0,40,48V208a16,16,0,0,0,16,16H200a16,16,0,0,0,16-16V93.23A8,8,0,0,0,224.6,88.1ZM128,204.42l-26.12-17.41a8,8,0,0,1-.52-12.33L120.47,152l-23.77-19.81a8,8,0,0,1,10.6-12L128,136.69l20.69-16.55a8,8,0,0,1,10.6,12L135.53,152l19.11,22.93a8,8,0,0,1-11.06,11.56L128,204.42ZM208,208H56V48h96v48a8,8,0,0,0,8,8h48Z"></path>
                    </svg>
                    <span>Favoriler</span>
                </a>
                
                <a class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-subtle-light dark:text-subtle-dark hover:text-foreground-light dark:hover:text-foreground-dark hover:bg-accent-light dark:hover:bg-accent-dark transition-all duration-200 relative" href="#" data-section="kanban">
                    <svg class="icon transition-transform duration-200" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg">
                        <path d="M240,192h-8V56a16,16,0,0,0-16-16H40A16,16,0,0,0,24,56V192H16a8,8,0,0,0,0,16H240a8,8,0,0,0,0-16ZM40,56H216V192H200V168a8,8,0,0,0-8-8H120a8,8,0,0,0-8,8v24H72V88H184v48a8,8,0,0,0,16,0V80a8,8,0,0,0-8-8H64a8,8,0,0,0-8,8V192H40ZM184,192H128V176h56Z"></path>
                    </svg>
                    <span>Kanban Board</span>
                </a>
                
                <a class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-subtle-light dark:text-subtle-dark hover:text-foreground-light dark:hover:text-foreground-dark hover:bg-accent-light dark:hover:bg-accent-dark transition-all duration-200 relative" href="#" data-section="notebooklm">
                    <svg class="icon transition-transform duration-200" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg">
                        <path d="M224,48H32A16,16,0,0,0,16,64V192a16,16,0,0,0,16,16H224a16,16,0,0,0,16-16V64A16,16,0,0,0,224,48ZM32,64H224V192H32ZM48,80a8,8,0,0,1,8-8H72a8,8,0,0,1,0,16H56A8,8,0,0,1,48,80Zm0,32a8,8,0,0,1,8-8H72a8,8,0,0,1,0,16H56A8,8,0,0,1,48,112Zm0,32a8,8,0,0,1,8-8H72a8,8,0,0,1,0,16H56A8,8,0,0,1,48,144Zm0,32a8,8,0,0,1,8-8H72a8,8,0,0,1,0,16H56A8,8,0,0,1,48,176ZM200,80H88a8,8,0,0,0,0,16H200a8,8,0,0,0,0-16Zm0,32H88a8,8,0,0,0,0,16H200a8,8,0,0,0,0-16Zm0,32H88a8,8,0,0,0,0,16H200a8,8,0,0,0,0-16Zm0,32H88a8,8,0,0,0,0,16H200a8,8,0,0,0,0-16Z"></path>
                    </svg>
                    <span>NotebookLM</span>
                </a>
                
                
                <a class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-subtle-light dark:text-subtle-dark hover:text-foreground-light dark:hover:text-foreground-dark hover:bg-accent-light dark:hover:bg-accent-dark transition-all duration-200 relative" href="#" data-section="tags">
                    <svg class="icon transition-transform duration-200" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg">
                        <path d="M228,80v96a12,12,0,0,1-12,12H192a12,12,0,0,1-8.49-3.51L160,161.94,136.49,185.5a12,12,0,0,1-17,0L95.94,161.94,72.49,185.5A12,12,0,0,1,64,188H40a12,12,0,0,1-12-12V80A12,12,0,0,1,40,68H216A12,12,0,0,1,228,80Z"></path>
                    </svg>
                    <span>Etiketler</span>
                </a>
                
                <a class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-subtle-light dark:text-subtle-dark hover:text-foreground-light dark:hover:text-foreground-dark hover:bg-accent-light dark:hover:bg-accent-dark transition-all duration-200 relative" href="#" data-section="inbox">
                    <svg class="icon transition-transform duration-200" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg">
                        <path d="M224,48H32a8,8,0,0,0-8,8V192a16,16,0,0,0,16,16H216a16,16,0,0,0,16-16V56A8,8,0,0,0,224,48ZM98.71,128,40,181.81V74.19Zm11.84,10.85,12,11.05a8,8,0,0,0,10.82,0l12-11.05,58,53.15H52.57ZM157.29,128,216,74.19V181.81Z"></path>
                    </svg>
                    <span>Gelen Kutusu</span>
                    <span id="inboxBadge" class="hidden ml-auto px-2 py-0.5 text-xs rounded-full bg-red-500 text-white">0</span>
                </a>
                
                <a class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-subtle-light dark:text-subtle-dark hover:text-foreground-light dark:hover:text-foreground-dark hover:bg-accent-light dark:hover:bg-accent-dark transition-all duration-200 relative" href="#" data-section="planner">
                    <svg class="icon transition-transform duration-200" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg">
                        <path d="M208,32H184V24a8,8,0,0,0-16,0v8H88V24a8,8,0,0,0-16,0v8H48A16,16,0,0,0,32,48V208a16,16,0,0,0,16,16H208a16,16,0,0,0,16-16V48A16,16,0,0,0,208,32ZM72,48v8a8,8,0,0,0,16,0V48h80v8a8,8,0,0,0,16,0V48h24V80H48V48ZM208,208H48V96H208V208Zm-96-88v64a8,8,0,0,1-16,0V132.94l-4.42,2.22a8,8,0,0,1-7.16-14.32l16-8A8,8,0,0,1,112,120Zm59.16,30.45L152,176h16a8,8,0,0,1,0,16H136a8,8,0,0,1-6.4-12.8l28.78-38.37A8,8,0,1,0,145.07,132a8,8,0,1,1-13.85-8A24,24,0,0,1,176,136,23.76,23.76,0,0,1,171.16,150.45Z"></path>
                    </svg>
                    <span>Planlayıcı</span>
                </a>
                
                <a class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-subtle-light dark:text-subtle-dark hover:text-foreground-light dark:hover:text-foreground-dark hover:bg-accent-light dark:hover:bg-accent-dark transition-all duration-200 relative" href="#" data-section="friends">
                    <svg class="icon transition-transform duration-200" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg">
                        <path d="M117.25,157.92a60,60,0,1,0-66.5,0A95.83,95.83,0,0,0,3.53,195.63a8,8,0,1,0,13.4,8.74,80,80,0,0,1,134.14,0,8,8,0,0,0,13.4-8.74A95.83,95.83,0,0,0,117.25,157.92ZM40,108a44,44,0,1,1,44,44A44.05,44.05,0,0,1,40,108Zm210.27,98.63a8,8,0,0,1-11.29.74A80,80,0,0,0,172,168a8,8,0,0,1,0-16,96,96,0,0,1,66.27,26.37A8,8,0,0,1,250.27,206.63ZM172,120a44,44,0,1,1-16.34-84.87,8,8,0,1,1-5.94,14.85,28,28,0,1,0,0,52.06,8,8,0,1,1,5.94,14.85A43.85,43.85,0,0,1,172,120Z"></path>
                    </svg>
                    <span>Arkadaşlar</span>
                </a>
                
                <a class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-subtle-light dark:text-subtle-dark hover:text-foreground-light dark:hover:text-foreground-dark hover:bg-accent-light dark:hover:bg-accent-dark transition-all duration-200 relative" href="#" data-section="groups">
                    <svg class="icon transition-transform duration-200" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg">
                        <path d="M117.25,157.92a60,60,0,1,0-66.5,0A95.83,95.83,0,0,0,3.53,195.63a8,8,0,1,0,13.4,8.74,80,80,0,0,1,134.14,0,8,8,0,0,0,13.4-8.74A95.83,95.83,0,0,0,117.25,157.92ZM40,108a44,44,0,1,1,44,44A44.05,44.05,0,0,1,40,108Zm210.27,98.63a8,8,0,0,1-11.29.74A80,80,0,0,0,172,168a8,8,0,0,1,0-16,96,96,0,0,1,66.27,26.37A8,8,0,0,1,250.27,206.63ZM172,120a44,44,0,1,1-16.34-84.87,8,8,0,1,1-5.94,14.85,28,28,0,1,0,0,52.06,8,8,0,1,1,5.94,14.85A43.85,43.85,0,0,1,172,120Z"></path>
                    </svg>
                    <span>Grup Çalışması</span>
                </a>
                
                <a class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-subtle-light dark:text-subtle-dark hover:text-foreground-light dark:hover:text-foreground-dark hover:bg-accent-light dark:hover:bg-accent-dark transition-all duration-200 relative" href="#" data-section="gemini">
                    <svg class="icon transition-transform duration-200" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg">
                        <path d="M187.58,144.84l-32-80a8,8,0,0,0-15.16,0l-32,80a8,8,0,0,0,15.16,6.06L130.34,136h43.32l6.76,14.9a8,8,0,0,0,15.16-6.06ZM136.34,120,152,80.94,167.66,120ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128ZM128,48a8,8,0,0,0-8,8V72a8,8,0,0,0,16,0V56A8,8,0,0,0,128,48Zm0,144a8,8,0,0,0-8,8v16a8,8,0,0,0,16,0V200A8,8,0,0,0,128,192ZM88,128a8,8,0,0,0-8-8H64a8,8,0,0,0,0,16H80A8,8,0,0,0,88,128Zm104,0a8,8,0,0,0-8-8H168a8,8,0,0,0,0,16h16A8,8,0,0,0,192,128Z"></path>
                    </svg>
                    <span>Gemini AI Asistan</span>
                    <span class="ml-auto px-2 py-0.5 text-xs rounded-full bg-gradient-to-r from-blue-500 to-purple-500 text-white">AI</span>
                </a>
                
                <a class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-subtle-light dark:text-subtle-dark hover:text-foreground-light dark:hover:text-foreground-dark hover:bg-accent-light dark:hover:bg-accent-dark transition-all duration-200 relative" href="#" data-section="settings">
                    <svg class="icon transition-transform duration-200" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg">
                        <path d="M128,80a48,48,0,1,0,48,48A48.05,48.05,0,0,0,128,80Zm0,80a32,32,0,1,1,32-32A32,32,0,0,1,128,160Zm88-29.84q.06-2.16,0-4.32l14.92-18.64a8,8,0,0,0,1.48-7.06,107.6,107.6,0,0,0-10.88-26.25,8,8,0,0,0-6-3.93l-23.72-2.64q-1.48-1.56-3.18-3.18L186,40.54a8,8,0,0,0-3.94-6,107.29,107.29,0,0,0-26.25-10.87,8,8,0,0,0-7.06,1.49L130.16,40Q128,40,125.84,40L107.2,25.11a8,8,0,0,0-7.06-1.48A107.6,107.6,0,0,0,73.89,34.51a8,8,0,0,0-3.93,6L67.32,64.27q-1.56,1.49-3.18,3.18L40.54,70.05a8,8,0,0,0-6,3.94,107.71,107.71,0,0,0-10.87,26.25,8,8,0,0,0,1.49,7.06L40,125.84Q40,128,40,130.16L25.11,148.8a8,8,0,0,0-1.48,7.06,107.6,107.6,0,0,0,10.88,26.25,8,8,0,0,0,6,3.93l23.72,2.64q1.49,1.56,3.18,3.18L70,215.46a8,8,0,0,0,3.94,6,107.71,107.71,0,0,0,26.25,10.87,8,8,0,0,0,7.06-1.49L125.84,216q2.16.06,4.32,0l18.64,14.92a8,8,0,0,0,7.06,1.48,107.21,107.21,0,0,0,26.25-10.88,8,8,0,0,0,3.93-6l2.64-23.72q1.56-1.48,3.18-3.18L215.46,186a8,8,0,0,0,6-3.94,107.71,107.71,0,0,0,10.87-26.25,8,8,0,0,0-1.49-7.06ZM128,192a64,64,0,1,1,64-64A64.07,64.07,0,0,1,128,192Z"></path>
                    </svg>
                    <span>Ayarlar</span>
                </a>
            </nav>
            
            <div class="px-2 py-4 mt-auto border-t border-border-light dark:border-border-dark">
                <a class="flex items-center gap-3 px-3 py-2 rounded-DEFAULT text-sm font-medium hover:bg-red-500/10 dark:hover:bg-red-500/10 text-red-600 dark:text-red-400" href="#" onclick="confirmLogout()">
                    <svg class="icon" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg">
                        <path d="M112,216a8,8,0,0,1-8-8V48a8,8,0,0,1,16,0V208A8,8,0,0,1,112,216ZM40,128a8,8,0,0,0,8-8V48a16,16,0,0,1,16-16H192a16,16,0,0,1,16,16V80a8,8,0,0,0,16,0V48a32,32,0,0,0-32-32H64A32,32,0,0,0,32,48V120A8,8,0,0,0,40,128ZM203.06,128.61a8,8,0,0,0-8.72,1.73L180,149.66,165.66,135.32a8,8,0,0,0-11.32,11.32L168.68,161l-14.34,14.34a8,8,0,0,0,11.32,11.32L180,172.34l14.34,14.34a8,8,0,0,0,11.32-11.32L191.32,161l14.34-14.34A8,8,0,0,0,203.06,128.61Z"></path>
                        </svg>
                    <span>Çıkış Yap</span>
                </a>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="flex-1 p-8 bg-transparent">
            <!-- Notes Section (Default Active) -->
            <div id="notes-section" class="content-section active">
                <header class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-4">
                        <h1 class="text-4xl font-bold">Tüm Notlar</h1>
                        <button class="bg-primary text-white font-medium px-6 py-2.5 rounded-lg hover:bg-primary/90 transition-colors" id="newNoteBtn">
                            Yeni Not Ekle
                        </button>
                    </div>
                </header>
                
                <div class="mb-6">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-subtle-light dark:text-subtle-dark">
                            <svg class="icon" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg">
                                <path d="M229.66,218.34l-50.07-50.06a88.11,88.11,0,1,0-11.31,11.31l50.06,50.07a8,8,0,0,0,11.32-11.32ZM40,112a72,72,0,1,1,72,72A72.08,72.08,0,0,1,40,112Z"></path>
                            </svg>
                        </div>
                        <input class="w-full pl-12 pr-4 py-3 rounded-lg bg-card-light dark:bg-card-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary-accent focus:border-primary-accent outline-none transition-shadow" placeholder="Ara" type="text"/>
                    </div>
                </div>
                
                <div class="bg-card-light dark:bg-card-dark rounded-lg border border-border-light dark:border-border-dark overflow-hidden">
                    <table class="w-full text-left">
                        <thead class="bg-background-light dark:bg-background-dark border-b border-border-light dark:border-border-dark">
                            <tr>
                                <th class="px-6 py-4 text-sm font-medium text-subtle-light dark:text-subtle-dark uppercase tracking-wider">İsim</th>
                                <th class="px-6 py-4 text-sm font-medium text-subtle-light dark:text-subtle-dark uppercase tracking-wider">Oluşturulma</th>
                                <th class="px-6 py-4 text-sm font-medium text-subtle-light dark:text-subtle-dark uppercase tracking-wider">Son Güncelleme</th>
                                <th class="px-6 py-4 text-sm font-medium text-subtle-light dark:text-subtle-dark uppercase tracking-wider">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody id="notesTableBody">
                            <!-- Notlar buraya dinamik olarak yüklenecek -->
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Dashboard Section -->
            <div id="dashboard-section" class="content-section" style="display: none;">
                <header class="flex items-center justify-between mb-8">
                    <h1 class="text-4xl font-bold">Ana Sayfa</h1>
                </header>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
                    <!-- Toplam Not Kartı -->
                    <div class="group relative overflow-hidden bg-gradient-to-br from-blue-50/80 via-blue-100/60 to-cyan-100/80 dark:from-blue-900/30 dark:via-blue-800/20 dark:to-cyan-900/30 backdrop-blur-xl rounded-2xl border border-blue-200/50 dark:border-blue-700/50 p-8 hover:shadow-2xl hover:shadow-blue-500/20 dark:hover:shadow-blue-400/20 transition-all duration-500 hover:scale-105 hover:-translate-y-2">
                        <!-- Arka plan deseni -->
                        <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-400/20 to-cyan-400/20 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-700"></div>
                        <div class="absolute bottom-0 left-0 w-24 h-24 bg-gradient-to-tr from-blue-300/30 to-transparent rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
                        
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-6">
                                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-2xl flex items-center justify-center shadow-lg group-hover:shadow-blue-500/50 transition-all duration-300 group-hover:scale-110 group-hover:rotate-6">
                                    <svg class="w-8 h-8 text-white group-hover:scale-110 transition-transform duration-300" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M213.66,82.34l-56-56A8,8,0,0,0,152,24H56A16,16,0,0,0,40,40V216a16,16,0,0,0,16,16H200a16,16,0,0,0,16-16V88A8,8,0,0,0,213.66,82.34ZM152,88V44l44,44Z"></path>
                                    </svg>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-1">Toplam Not</p>
                                    <p class="text-4xl font-bold text-blue-700 dark:text-blue-300 group-hover:scale-110 transition-transform duration-300" id="totalNotes">-</p>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></div>
                                    <span class="text-xs font-medium text-blue-600 dark:text-blue-400">Aktif</span>
                                </div>
                                <div class="text-xs text-blue-500 dark:text-blue-400 font-medium group-hover:text-blue-600 dark:group-hover:text-blue-300 transition-colors">
                                    +12% bu ay
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Arkadaşlar Kartı -->
                    <div class="group relative overflow-hidden bg-gradient-to-br from-purple-50/80 via-purple-100/60 to-pink-100/80 dark:from-purple-900/30 dark:via-purple-800/20 dark:to-pink-900/30 backdrop-blur-xl rounded-2xl border border-purple-200/50 dark:border-purple-700/50 p-8 hover:shadow-2xl hover:shadow-purple-500/20 dark:hover:shadow-purple-400/20 transition-all duration-500 hover:scale-105 hover:-translate-y-2">
                        <!-- Arka plan deseni -->
                        <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-purple-400/20 to-pink-400/20 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-700"></div>
                        <div class="absolute bottom-0 left-0 w-24 h-24 bg-gradient-to-tr from-purple-300/30 to-transparent rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
                        
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-6">
                                <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center shadow-lg group-hover:shadow-purple-500/50 transition-all duration-300 group-hover:scale-110 group-hover:rotate-6">
                                    <svg class="w-8 h-8 text-white group-hover:scale-110 transition-transform duration-300" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M117.25,157.92a60,60,0,1,0-66.5,0A95.83,95.83,0,0,0,3.53,195.63a8,8,0,1,0,13.4,8.74,80,80,0,0,1,134.14,0,8,8,0,0,0,13.4-8.74A95.83,95.83,0,0,0,117.25,157.92ZM40,108a44,44,0,1,1,44,44A44.05,44.05,0,0,1,40,108Zm210.27,98.63a8,8,0,0,1-11.29.74A80,80,0,0,0,172,168a8,8,0,0,1,0-16,96,96,0,0,1,66.27,26.37A8,8,0,0,1,250.27,206.63ZM172,120a44,44,0,1,1-16.34-84.87,8,8,0,1,1-5.94,14.85,28,28,0,1,0,0,52.06,8,8,0,1,1,5.94,14.85A43.85,43.85,0,0,1,172,120Z"></path>
                                    </svg>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-purple-600 dark:text-purple-400 uppercase tracking-wider mb-1">Arkadaşlar</p>
                                    <p class="text-4xl font-bold text-purple-700 dark:text-purple-300 group-hover:scale-110 transition-transform duration-300" id="friendsCount">-</p>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 bg-purple-500 rounded-full animate-pulse"></div>
                                    <span class="text-xs font-medium text-purple-600 dark:text-purple-400">Bağlı</span>
                                </div>
                                <div class="text-xs text-purple-500 dark:text-purple-400 font-medium group-hover:text-purple-600 dark:group-hover:text-purple-300 transition-colors">
                                    +2 yeni
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Son Aktiviteler Kartı -->
                <div class="group relative overflow-hidden bg-gradient-to-br from-slate-50/90 via-gray-50/80 to-zinc-50/90 dark:from-slate-900/40 dark:via-gray-900/30 dark:to-zinc-900/40 backdrop-blur-xl rounded-2xl border border-slate-200/60 dark:border-slate-700/60 p-8 hover:shadow-2xl hover:shadow-slate-500/10 dark:hover:shadow-slate-400/10 transition-all duration-500">
                    <!-- Arka plan deseni -->
                    <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-slate-400/10 to-gray-400/10 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-700"></div>
                    <div class="absolute bottom-0 left-0 w-32 h-32 bg-gradient-to-tr from-gray-300/15 to-transparent rounded-full blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
                    
                    <div class="relative z-10">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-12 h-12 bg-gradient-to-br from-slate-600 to-gray-600 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-slate-500/50 transition-all duration-300 group-hover:scale-110 group-hover:rotate-3">
                                <svg class="w-6 h-6 text-white group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-slate-700 dark:text-slate-300 group-hover:text-slate-800 dark:group-hover:text-slate-200 transition-colors">Son Aktiviteler</h2>
                                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">En son yaptığınız işlemler</p>
                            </div>
                        </div>
                        
                        <div id="recentActivity" class="space-y-6">
                            <!-- Örnek aktivite kartları -->
                            <div class="flex items-start gap-4 p-4 bg-white/60 dark:bg-slate-800/40 rounded-xl border border-slate-200/50 dark:border-slate-700/50 backdrop-blur-sm hover:bg-white/80 dark:hover:bg-slate-800/60 transition-all duration-300 hover:shadow-lg hover:shadow-slate-200/50 dark:hover:shadow-slate-700/50">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Yeni not oluşturuldu</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">"Proje Planlaması" başlıklı not eklendi</p>
                                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-2">2 saat önce</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start gap-4 p-4 bg-white/60 dark:bg-slate-800/40 rounded-xl border border-slate-200/50 dark:border-slate-700/50 backdrop-blur-sm hover:bg-white/80 dark:hover:bg-slate-800/60 transition-all duration-300 hover:shadow-lg hover:shadow-slate-200/50 dark:hover:shadow-slate-700/50">
                                <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Arkadaş eklendi</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Ahmet Yılmaz arkadaş listesine eklendi</p>
                                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-2">1 gün önce</p>
                                </div>
                            </div>
                            
                            <!-- Gerçek aktiviteler buraya yüklenecek -->
                        </div>
                        
                        <div class="mt-8 text-center">
                            <button class="px-6 py-3 bg-gradient-to-r from-slate-600 to-gray-600 text-white font-medium rounded-xl hover:from-slate-700 hover:to-gray-700 transition-all duration-300 hover:shadow-lg hover:shadow-slate-500/25 hover:scale-105 flex items-center gap-2 mx-auto">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Tümünü Gör
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Favorites Section -->
            <div id="favorites-section" class="content-section" style="display: none;">
                <header class="flex items-center justify-between mb-8">
                    <h1 class="text-4xl font-bold">Favoriler</h1>
                </header>
                
                <div class="bg-card-light dark:bg-card-dark rounded-lg border border-border-light dark:border-border-dark overflow-hidden">
                    <table class="w-full text-left">
                        <thead class="bg-background-light dark:bg-background-dark border-b border-border-light dark:border-border-dark">
                            <tr>
                                <th class="px-6 py-4 text-sm font-medium text-subtle-light dark:text-subtle-dark uppercase tracking-wider">İsim</th>
                                <th class="px-6 py-4 text-sm font-medium text-subtle-light dark:text-subtle-dark uppercase tracking-wider">Oluşturulma</th>
                                <th class="px-6 py-4 text-sm font-medium text-subtle-light dark:text-subtle-dark uppercase tracking-wider">Son Güncelleme</th>
                                <th class="px-6 py-4 text-sm font-medium text-subtle-light dark:text-subtle-dark uppercase tracking-wider">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody id="favoritesTableBody">
                            <!-- Favori notlar buraya dinamik olarak yüklenecek -->
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Kanban Board Section -->
            <div id="kanban-section" class="content-section" style="display: none;">
                <header class="flex items-center justify-between mb-8">
                    <h1 class="text-4xl font-bold">Kanban Board</h1>
                </header>
                
                <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Active Tasks Column -->
                    <div class="lg:col-span-2">
                        <div class="kanban-column bg-card-light dark:bg-card-dark rounded-xl p-6 flex flex-col min-h-[600px]">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-xl font-bold text-foreground-light dark:text-foreground-dark">Aktif Görevler</h3>
                                <span id="active-tasks-count" class="text-lg font-semibold text-subtle-light dark:text-subtle-dark bg-accent-light dark:bg-accent-dark px-4 py-2 rounded-full">0</span>
                            </div>
                            <div class="space-y-4 flex-grow" id="active-tasks-container">
                                <!-- Active tasks will be loaded dynamically -->
                            </div>
                            <button id="kanbanNewTaskBtn" class="mt-6 flex items-center justify-center w-full gap-3 px-6 py-3 text-lg font-medium text-subtle-light dark:text-subtle-dark hover:bg-accent-light dark:hover:bg-accent-dark rounded-lg border-2 border-dashed border-border-light dark:border-border-dark hover:border-primary transition-colors">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"></path>
                                </svg>
                                <span>Yeni Görev Ekle</span>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Completed Tasks Column -->
                    <div class="lg:col-span-1">
                        <div class="kanban-column bg-card-light dark:bg-card-dark rounded-xl p-6 flex flex-col min-h-[600px]">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-lg font-bold text-green-600 dark:text-green-400">Tamamlanan Görevler</h3>
                                <span id="completed-tasks-count" class="text-lg font-semibold text-subtle-light dark:text-subtle-dark bg-green-100 dark:bg-green-900 px-4 py-2 rounded-full text-green-800 dark:text-green-200">0</span>
                            </div>
                            <div class="space-y-3 flex-grow" id="completed-tasks-container">
                                <!-- Completed tasks will be loaded dynamically -->
                            </div>
                            <div class="mt-6 text-center">
                                <div class="text-sm text-subtle-light dark:text-subtle-dark">
                                    <svg class="w-8 h-8 mx-auto mb-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p>Tamamlanan görevler burada görünür</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            
            <!-- Tags Section -->
            <div id="tags-section" class="content-section" style="display: none;">
                <header class="mb-8">
                    <div class="flex items-center gap-4">
                        <h1 class="text-4xl font-bold">Etiketler</h1>
                        <button class="bg-primary text-white font-medium px-6 py-2.5 rounded-lg hover:bg-primary/90 transition-colors" id="createTagBtn">
                            Yeni Etiket Ekle
                        </button>
                    </div>
                </header>
                
                <div id="tagsList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Etiketler buraya yüklenecek -->
                </div>
            </div>
            
            <!-- Inbox Section -->
            <div id="inbox-section" class="content-section" style="display: none;">
                <header class="mb-8 flex flex-col items-center">
                    <h1 class="text-4xl font-bold mb-4">Gelen Kutusu</h1>
                    <button onclick="markAllAsRead()" class="px-4 py-2 text-sm bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors">Tümünü Okundu İşaretle</button>
                </header>
                
                <div id="notificationsList" class="space-y-3">
                    <!-- Bildirimler buraya yüklenecek -->
                </div>
            </div>
            
            <!-- Planner Section -->
            <div id="planner-section" class="content-section" style="display: none;">
                <header class="mb-8 flex flex-col items-center">
                    <h1 class="text-4xl font-bold mb-6">Planlayıcı</h1>
                    <div class="flex items-center gap-4">
                        <button class="bg-primary text-white font-medium px-6 py-2.5 rounded-lg hover:bg-primary/90 transition-colors" id="createPlanBtn">
                            Yeni Plan Oluştur
                        </button>
                        <button onclick="openGeminiPlannerHelper()" class="flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-blue-500 to-purple-600 text-white font-medium rounded-lg hover:from-blue-600 hover:to-purple-700 transition-all">
                            <svg class="w-5 h-5" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg" fill="currentColor">
                                <path d="M187.58,144.84l-32-80a8,8,0,0,0-15.16,0l-32,80a8,8,0,0,0,15.16,6.06L130.34,136h43.32l6.76,14.9a8,8,0,0,0,15.16-6.06ZM136.34,120,152,80.94,167.66,120Z"></path>
                            </svg>
                            Gemini AI ile Plan Oluştur
                        </button>
                    </div>
                </header>
                
                <div id="plansList" class="space-y-4">
                    <!-- Planlar buraya yüklenecek -->
                </div>
            </div>
            
            <!-- Friends Section -->
            <div id="friends-section" class="content-section" style="display: none;">
                <header class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-4">
                        <h1 class="text-4xl font-bold">Arkadaşlar</h1>
                        <button class="bg-primary text-white font-medium px-6 py-2.5 rounded-lg hover:bg-primary/90 transition-colors" id="addFriendBtn">
                            Arkadaş Ekle
                        </button>
                    </div>
                </header>
                
                <!-- Gelen İstekler -->
                <div id="friendRequestsSection" class="mb-8" style="display: none;">
                    <h2 class="text-xl font-semibold mb-4">Gelen İstekler</h2>
                    <div id="friendRequestsList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- İstekler buraya yüklenecek -->
                    </div>
                </div>
                
                <!-- Arkadaşlar Listesi -->
                <div>
                    <h2 class="text-xl font-semibold mb-4">Arkadaşlarım</h2>
                    <div id="friendsList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Arkadaşlar buraya yüklenecek -->
                    </div>
                </div>
            </div>
            
            <!-- Groups Section -->
            <div id="groups-section" class="content-section" style="display: none;">
                <header class="mb-8">
                    <div class="flex items-center gap-4">
                        <h1 class="text-4xl font-bold">Grup Çalışması</h1>
                        <button class="bg-primary text-white font-medium px-6 py-2.5 rounded-lg hover:bg-primary/90 transition-colors" id="createGroupBtn">
                            Yeni Grup Oluştur
                        </button>
                    </div>
                </header>
                
                <div id="groupsList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    <!-- Gruplar buraya yüklenecek -->
                </div>
                
                <!-- Grup Detay Modal -->
                <div id="groupDetailModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
                    <div class="bg-card-light dark:bg-card-dark rounded-xl shadow-xl max-w-6xl w-full mx-4 max-h-[90vh] overflow-y-auto">
                        <div class="p-6 border-b border-border-light dark:border-border-dark">
                            <div class="flex items-center justify-between">
                                <h2 class="text-2xl font-bold" id="groupDetailName">Grup Adı</h2>
                                <button onclick="closeGroupDetail()" class="text-subtle-light dark:text-subtle-dark hover:text-foreground-light dark:hover:text-foreground-dark">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                            <p class="text-subtle-light dark:text-subtle-dark mt-2" id="groupDetailDescription"></p>
                        </div>
                        
                        <div class="p-6">
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                <!-- Üyeler -->
                                <div class="lg:col-span-1">
                                    <div class="flex items-center justify-between mb-4">
                                        <h3 class="text-lg font-semibold">Üyeler <span id="memberCountBadge" class="text-sm text-subtle-light dark:text-subtle-dark">(0/5)</span></h3>
                                        <button onclick="showAddMemberForm()" class="text-primary hover:text-primary/80 text-sm font-medium">
                                            + Çalışma Arkadaşı Ekle
                                        </button>
                                    </div>
                                    <div id="groupMembersList" class="space-y-2">
                                        <!-- Üyeler buraya yüklenecek -->
                                    </div>
                                    <div id="addMemberForm" class="hidden mt-4 p-4 bg-accent-light dark:bg-accent-dark rounded-lg">
                                        <label class="block text-sm font-medium mb-2">Arkadaşlarınızdan seçin:</label>
                                        <select id="friendSelector" class="w-full px-3 py-2 rounded-lg bg-card-light dark:bg-card-dark border border-border-light dark:border-border-dark mb-2">
                                            <option value="">Arkadaş seçin...</option>
                                        </select>
                                        <div class="flex gap-2">
                                            <button onclick="addGroupMemberFromFriend()" class="flex-1 px-3 py-2 bg-primary text-white rounded-lg text-sm">Ekle</button>
                                            <button onclick="hideAddMemberForm()" class="px-3 py-2 bg-subtle-light/20 rounded-lg text-sm">İptal</button>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Görevler -->
                                <div class="lg:col-span-2">
                                    <div class="flex items-center justify-between mb-4">
                                        <h3 class="text-lg font-semibold">Görevler</h3>
                                        <button onclick="showCreateTaskForm()" class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary/90">
                                            Yeni Görev
                                        </button>
                                    </div>
                                    
                                    <!-- Görev Oluşturma Formu -->
                                    <div id="createTaskForm" class="hidden mb-4 p-4 bg-accent-light dark:bg-accent-dark rounded-lg">
                                        <input type="text" id="taskTitle" placeholder="Görev başlığı" class="w-full px-3 py-2 rounded-lg bg-card-light dark:bg-card-dark border border-border-light dark:border-border-dark mb-2">
                                        <textarea id="taskDescription" placeholder="Açıklama" rows="2" class="w-full px-3 py-2 rounded-lg bg-card-light dark:bg-card-dark border border-border-light dark:border-border-dark mb-2"></textarea>
                                        <div class="grid grid-cols-2 gap-2 mb-2">
                                            <select id="taskAssignee" class="px-3 py-2 rounded-lg bg-card-light dark:bg-card-dark border border-border-light dark:border-border-dark">
                                                <option value="">Atanacak kişi seç</option>
                                            </select>
                                            <select id="taskPriority" class="px-3 py-2 rounded-lg bg-card-light dark:bg-card-dark border border-border-light dark:border-border-dark">
                                                <option value="low">Düşük</option>
                                                <option value="medium" selected>Orta</option>
                                                <option value="high">Yüksek</option>
                                            </select>
                                        </div>
                                        <input type="date" id="taskDueDate" class="w-full px-3 py-2 rounded-lg bg-card-light dark:bg-card-dark border border-border-light dark:border-border-dark mb-2">
                                        <div class="flex gap-2">
                                            <button onclick="createGroupTask()" class="flex-1 px-3 py-2 bg-primary text-white rounded-lg text-sm">Oluştur</button>
                                            <button onclick="hideCreateTaskForm()" class="px-3 py-2 bg-subtle-light/20 rounded-lg text-sm">İptal</button>
                                        </div>
                                    </div>
                                    
                                    <!-- Görev Listesi -->
                                    <div id="groupTasksList" class="space-y-3">
                                        <!-- Görevler buraya yüklenecek -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Settings Section -->
            <div id="settings-section" class="content-section" style="display: none;">
                <header class="flex items-center justify-between mb-8">
                    <h1 class="text-4xl font-bold">Ayarlar</h1>
                </header>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Settings Navigation -->
                    <div class="md:col-span-1">
                        <nav class="flex flex-col space-y-1">
                            <a class="px-3 py-2 rounded font-medium text-foreground-light dark:text-foreground-dark bg-primary/10 dark:bg-primary/20" href="#profile">Profil</a>
                            <a class="px-3 py-2 rounded font-medium text-subtle-light dark:text-subtle-dark hover:bg-accent-light dark:hover:bg-accent-dark" href="#notifications">Bildirimler</a>
                            <a class="px-3 py-2 rounded font-medium text-subtle-light dark:text-subtle-dark hover:bg-accent-light dark:hover:bg-accent-dark" href="#privacy">Gizlilik</a>
                        </nav>
                    </div>
                    
                    <!-- Settings Content -->
                    <div class="md:col-span-2 space-y-10">
                        <!-- Profile Section -->
                        <section id="profile">
                            <h2 class="text-xl font-bold text-foreground-light dark:text-foreground-dark border-b border-border-light dark:border-border-dark pb-4">Profil</h2>
                            <div class="space-y-6 pt-6">
                                <div class="max-w-md">
                                    <label class="block text-sm font-medium text-foreground-light dark:text-foreground-dark mb-2" for="fullName">Tam Ad</label>
                                    <input class="w-full px-4 py-2 rounded-lg bg-card-light dark:bg-card-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary focus:border-primary text-foreground-light dark:text-foreground-dark" id="fullName" type="text" value="<?php echo htmlspecialchars($user['username']); ?>"/>
                                </div>
                                <div class="max-w-md">
                                    <label class="block text-sm font-medium text-foreground-light dark:text-foreground-dark mb-2" for="email">E-posta</label>
                                    <input class="w-full px-4 py-2 rounded-lg bg-card-light dark:bg-card-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary focus:border-primary text-foreground-light dark:text-foreground-dark" id="email" type="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>"/>
                                </div>
                                <div class="max-w-md">
                                    <label class="block text-sm font-medium text-foreground-light dark:text-foreground-dark mb-2" for="password">Şifre</label>
                                    <input class="w-full px-4 py-2 rounded-lg bg-card-light dark:bg-card-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary focus:border-primary text-foreground-light dark:text-foreground-dark" id="password" placeholder="••••••••" type="password"/>
                                </div>
                            </div>
                            <div class="pt-6">
                                <button id="updateProfileBtn" class="px-4 py-2 rounded-lg bg-primary text-white text-sm font-bold hover:opacity-90">Profili Güncelle</button>
                            </div>
                        </section>
                        
                        <!-- Notifications Section -->
                        <section id="notifications">
                            <h2 class="text-xl font-bold text-foreground-light dark:text-foreground-dark border-b border-border-light dark:border-border-dark pb-4">Bildirimler</h2>
                            <div class="space-y-4 pt-6">
                                <label class="flex items-center gap-3">
                                    <input checked="" class="h-5 w-5 rounded border-border-light dark:border-border-dark bg-transparent text-primary checked:bg-primary checked:border-transparent focus:ring-primary/50 focus:ring-offset-0 focus:ring-2" type="checkbox"/>
                                    <span class="text-base text-foreground-light dark:text-foreground-dark">E-posta Bildirimleri</span>
                                </label>
                                <label class="flex items-center gap-3">
                                    <input checked="" class="h-5 w-5 rounded border-border-light dark:border-border-dark bg-transparent text-primary checked:bg-primary checked:border-transparent focus:ring-primary/50 focus:ring-offset-0 focus:ring-2" type="checkbox"/>
                                    <span class="text-base text-foreground-light dark:text-foreground-dark">Anlık Bildirimler</span>
                                </label>
                                <label class="flex items-center gap-3">
                                    <input class="h-5 w-5 rounded border-border-light dark:border-border-dark bg-transparent text-primary checked:bg-primary checked:border-transparent focus:ring-primary/50 focus:ring-offset-0 focus:ring-2" type="checkbox"/>
                                    <span class="text-base text-foreground-light dark:text-foreground-dark">Uygulama İçi Bildirimler</span>
                                </label>
                            </div>
                        </section>
                        
                        
                        <!-- Privacy Section -->
                        <section id="privacy">
                            <h2 class="text-xl font-bold text-foreground-light dark:text-foreground-dark border-b border-border-light dark:border-border-dark pb-4">Gizlilik</h2>
                            <div class="space-y-4 pt-6">
                                <label class="flex items-center gap-3">
                                    <input checked="" class="h-5 w-5 rounded border-border-light dark:border-border-dark bg-transparent text-primary checked:bg-primary checked:border-transparent focus:ring-primary/50 focus:ring-offset-0 focus:ring-2" type="checkbox"/>
                                    <span class="text-base text-foreground-light dark:text-foreground-dark">Profili Herkese Açık Paylaş</span>
                                </label>
                                <label class="flex items-center gap-3">
                                    <input class="h-5 w-5 rounded border-border-light dark:border-border-dark bg-transparent text-primary checked:bg-primary checked:border-transparent focus:ring-primary/50 focus:ring-offset-0 focus:ring-2" type="checkbox"/>
                                    <span class="text-base text-foreground-light dark:text-foreground-dark">Veri Toplamaya İzin Ver</span>
                                </label>
                            </div>
                            <div class="pt-6">
                                <button id="savePrivacyBtn" class="px-4 py-2 rounded-lg bg-primary text-white text-sm font-bold hover:opacity-90">Ayarları Kaydet</button>
                            </div>
                        </section>
                        
                    </div>
                </div>
            </div>
            
            <!-- Gemini AI Section -->
            <div id="gemini-section" class="content-section" style="display: none;">
                <header class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                            <svg class="w-7 h-7 text-white" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg" fill="currentColor">
                                <path d="M187.58,144.84l-32-80a8,8,0,0,0-15.16,0l-32,80a8,8,0,0,0,15.16,6.06L130.34,136h43.32l6.76,14.9a8,8,0,0,0,15.16-6.06ZM136.34,120,152,80.94,167.66,120Z"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-4xl font-bold">Gemini AI Asistan</h1>
                            <p class="text-sm text-subtle-light dark:text-subtle-dark mt-1">Plan yapma, proje geliştirme ve organizasyon konularında yardımcınız</p>
                        </div>
                    </div>
                    <div class="flex gap-2 items-center">
                        <button id="geminiContextGeneral" class="px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary/90 transition-colors">Genel</button>
                        <button id="geminiContextPlanner" class="px-4 py-2 rounded-lg bg-accent-light dark:bg-accent-dark text-foreground-light dark:text-foreground-dark text-sm font-medium hover:bg-primary hover:text-white transition-colors">Planlayıcı</button>
                        <button id="geminiContextNotes" class="px-4 py-2 rounded-lg bg-accent-light dark:bg-accent-dark text-foreground-light dark:text-foreground-dark text-sm font-medium hover:bg-primary hover:text-white transition-colors">Notlar</button>
                        <button id="geminiContextTasks" class="px-4 py-2 rounded-lg bg-accent-light dark:bg-accent-dark text-foreground-light dark:text-foreground-dark text-sm font-medium hover:bg-primary hover:text-white transition-colors">Görevler</button>
                    </div>
                </header>
                
                <!-- AI Önerileri Kartları -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-xl p-5 border border-blue-200 dark:border-blue-800 cursor-pointer hover:shadow-lg transition-all" onclick="fillGeminiPrompt('Bugün için verimli bir çalışma planı oluştur')">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg bg-blue-500 flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-blue-900 dark:text-blue-100 mb-1">Günlük Plan</h3>
                                <p class="text-sm text-blue-700 dark:text-blue-300">Bugün için verimli bir çalışma planı oluştur</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-xl p-5 border border-purple-200 dark:border-purple-800 cursor-pointer hover:shadow-lg transition-all" onclick="fillGeminiPrompt('Proje yönetimi için en iyi pratikleri öner')">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg bg-purple-500 flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-purple-900 dark:text-purple-100 mb-1">Proje Yönetimi</h3>
                                <p class="text-sm text-purple-700 dark:text-purple-300">En iyi pratikleri ve stratejileri öğren</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-xl p-5 border border-green-200 dark:border-green-800 cursor-pointer hover:shadow-lg transition-all" onclick="fillGeminiPrompt('Notlarımı daha iyi organize etmek için öneriler ver')">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg bg-green-500 flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-green-900 dark:text-green-100 mb-1">Not Organizasyonu</h3>
                                <p class="text-sm text-green-700 dark:text-green-300">Notlarını daha verimli düzenle</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Chat Arayüzü -->
                <div class="bg-card-light dark:bg-card-dark rounded-xl border border-border-light dark:border-border-dark overflow-hidden flex flex-col" style="height: calc(100vh - 400px);">
                    <!-- Chat Mesajları -->
                    <div id="geminiChatMessages" class="flex-1 overflow-y-auto p-6 space-y-4">
                        <!-- Hoş geldin mesajı -->
                        <div class="flex gap-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-white" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg" fill="currentColor">
                                    <path d="M187.58,144.84l-32-80a8,8,0,0,0-15.16,0l-32,80a8,8,0,0,0,15.16,6.06L130.34,136h43.32l6.76,14.9a8,8,0,0,0,15.16-6.06ZM136.34,120,152,80.94,167.66,120Z"></path>
                                </svg>
                            </div>
                            <div class="flex-1 bg-accent-light dark:bg-accent-dark rounded-xl p-4">
                                <p class="text-sm font-medium text-primary mb-1">Gemini AI</p>
                                <p class="text-foreground-light dark:text-foreground-dark">Merhaba! Ben Gemini AI asistanınızım. Size plan yapma, proje geliştirme, not organizasyonu ve görev yönetimi konularında yardımcı olabilirim. Nasıl yardımcı olabilirim?</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Chat Input -->
                    <div class="border-t border-border-light dark:border-border-dark p-4">
                        <form id="geminiChatForm" class="flex gap-3">
                            <input 
                                type="text" 
                                id="geminiChatInput" 
                                placeholder="Mesajınızı yazın..." 
                                class="flex-1 px-4 py-3 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-shadow"
                                required
                            />
                            <button 
                                type="submit" 
                                class="px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white font-medium rounded-lg hover:from-blue-600 hover:to-purple-700 transition-all flex items-center gap-2"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                                Gönder
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <!-- NotebookLM Section -->
            <div id="notebooklm-section" class="content-section" style="display: none;">
                <header class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-green-500 to-teal-600 flex items-center justify-center">
                            <svg class="w-7 h-7 text-white" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg" fill="currentColor">
                                <path d="M224,48H32A16,16,0,0,0,16,64V192a16,16,0,0,0,16,16H224a16,16,0,0,0,16-16V64A16,16,0,0,0,224,48ZM32,64H224V192H32ZM48,80a8,8,0,0,1,8-8H72a8,8,0,0,1,0,16H56A8,8,0,0,1,48,80Zm0,32a8,8,0,0,1,8-8H72a8,8,0,0,1,0,16H56A8,8,0,0,1,48,112Zm0,32a8,8,0,0,1,8-8H72a8,8,0,0,1,0,16H56A8,8,0,0,1,48,144Zm0,32a8,8,0,0,1,8-8H72a8,8,0,0,1,0,16H56A8,8,0,0,1,48,176ZM200,80H88a8,8,0,0,0,0,16H200a8,8,0,0,0,0-16Zm0,32H88a8,8,0,0,0,0,16H200a8,8,0,0,0,0-16Zm0,32H88a8,8,0,0,0,0,16H200a8,8,0,0,0,0-16Zm0,32H88a8,8,0,0,0,0,16H200a8,8,0,0,0,0-16Z"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-4xl font-bold">NotebookLM</h1>
                            <p class="text-sm text-subtle-light dark:text-subtle-dark mt-1">Dosya yükleyin, özetleyin ve şablonlara dönüştürün</p>
                        </div>
                    </div>
                </header>
                
                <!-- Dosya Yükleme Alanı -->
                <div class="bg-gradient-to-br from-green-50 to-teal-100 dark:from-green-900/20 dark:to-teal-800/20 rounded-xl p-8 border-2 border-dashed border-green-300 dark:border-green-700 mb-8 text-center">
                    <div class="mb-6">
                        <svg class="w-16 h-16 mx-auto text-green-500 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Dosya Yükleyin</h3>
                    <p class="text-sm text-subtle-light dark:text-subtle-dark mb-6">PDF, DOC, DOCX, TXT formatlarında dosya yükleyebilirsiniz</p>
                    <div class="flex justify-center gap-4">
                        <label for="notebookFileInput" class="bg-green-600 hover:bg-green-700 text-white font-medium px-6 py-3 rounded-lg cursor-pointer transition-colors inline-flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                            </svg>
                            Dosya Seç
                        </label>
                        <input type="file" id="notebookFileInput" class="hidden" accept=".pdf,.doc,.docx,.txt" onchange="handleNotebookFileUpload(event)">
                        <button onclick="processWithNotebookLM()" class="bg-teal-600 hover:bg-teal-700 text-white font-medium px-6 py-3 rounded-lg transition-colors inline-flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            İşle
                        </button>
                    </div>
                    <div id="notebookFileInfo" class="mt-4 text-sm text-subtle-light dark:text-subtle-dark"></div>
                </div>
                
                <!-- Sonuçlar Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Özet Kartı -->
                    <div class="bg-card-light dark:bg-card-dark rounded-xl border border-border-light dark:border-border-dark p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-lg bg-blue-500/10 flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold">Dosya Özeti</h3>
                        </div>
                        <div id="notebookSummary" class="prose dark:prose-invert max-w-none">
                            <p class="text-subtle-light dark:text-subtle-dark italic">Dosya yüklendiğinde özet burada görünecek...</p>
                        </div>
                        <button onclick="copyToClipboard('notebookSummary')" class="mt-4 text-sm text-primary hover:text-primary/80 transition-colors inline-flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            Kopyala
                        </button>
                        
                        <!-- Öneriler Bölümü -->
                        <div class="mt-6 pt-6 border-t border-border-light dark:border-border-dark">
                            <h4 class="text-lg font-semibold mb-3 flex items-center gap-2">
                                <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                </svg>
                                Öneriler
                            </h4>
                            <div id="notebookSuggestions" class="space-y-2">
                                <p class="text-subtle-light dark:text-subtle-dark italic text-sm">Dosya işlendiğinde öneriler burada görünecek...</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Şablon Kartı -->
                    <div class="bg-card-light dark:bg-card-dark rounded-xl border border-border-light dark:border-border-dark p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-lg bg-purple-500/10 flex items-center justify-center">
                                <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold">Görsel Tablo Şablonu</h3>
                        </div>
                        <div id="notebookTemplate" class="overflow-x-auto">
                            <div class="text-subtle-light dark:text-subtle-dark italic text-center py-8">
                                Dosya yüklendiğinde görsel tablo şablonu burada görünecek...
                            </div>
                        </div>
                        <div class="mt-4 flex gap-2">
                            <button onclick="copyToClipboard('notebookTemplate')" class="text-sm text-primary hover:text-primary/80 transition-colors inline-flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                Kopyala
                            </button>
                            <button onclick="saveTemplateAsNote()" class="text-sm text-primary hover:text-primary/80 transition-colors inline-flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                                </svg>
                                Not Olarak Kaydet
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- İşlem Geçmişi -->
                <div class="mt-8 bg-card-light dark:bg-card-dark rounded-xl border border-border-light dark:border-border-dark p-6">
                    <h3 class="text-xl font-bold mb-4">İşlem Geçmişi</h3>
                    <div id="notebookHistory" class="space-y-3">
                        <p class="text-subtle-light dark:text-subtle-dark italic text-center py-8">Henüz işlem geçmişi yok</p>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Dark Mode Toggle -->
    <button class="theme-toggle" id="themeToggle" aria-label="Toggle theme">
        <svg class="theme-icon theme-icon-light" width="20" height="20" viewBox="0 0 24 24" fill="none">
            <circle cx="12" cy="12" r="5" stroke="currentColor" stroke-width="2"/>
            <line x1="12" y1="1" x2="12" y2="3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <line x1="12" y1="21" x2="12" y2="23" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <line x1="4.22" y1="4.22" x2="5.64" y2="5.64" stroke="currentColor" stroke-width="2"/>
            <line x1="18.36" y1="18.36" x2="19.78" y2="19.78" stroke="currentColor" stroke-width="2"/>
            <line x1="1" y1="12" x2="3" y2="12" stroke="currentColor" stroke-width="2"/>
            <line x1="21" y1="12" x2="23" y2="12" stroke="currentColor" stroke-width="2"/>
            <line x1="4.22" y1="19.78" x2="5.64" y2="18.36" stroke="currentColor" stroke-width="2"/>
            <line x1="18.36" y1="5.64" x2="19.78" y2="4.22" stroke="currentColor" stroke-width="2"/>
        </svg>
        <svg class="theme-icon theme-icon-dark" width="20" height="20" viewBox="0 0 24 24" fill="none" style="display: none;">
            <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>

    <style>
        .content-section {
            display: none;
        }
        
        .content-section.active {
            display: block;
        }
        
        /* Navigation Link Styles */
        .nav-link {
            position: relative;
            overflow: hidden;
        }
        
        /* Modern Active Link Style */
        .nav-link.active {
            background: linear-gradient(135deg, rgba(74, 144, 226, 0.15) 0%, rgba(80, 227, 194, 0.15) 100%) !important;
            color: #4a90e2 !important;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(74, 144, 226, 0.15);
        }
        
        .dark .nav-link.active {
            background: linear-gradient(135deg, rgba(74, 144, 226, 0.2) 0%, rgba(80, 227, 194, 0.2) 100%) !important;
            color: #50e3c2 !important;
            box-shadow: 0 2px 8px rgba(80, 227, 194, 0.2);
        }
        
        /* Left Border Accent */
        .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(180deg, #4a90e2 0%, #50e3c2 100%);
            border-radius: 0 4px 4px 0;
            animation: slideIn 0.3s ease;
        }
        
        @keyframes slideIn {
            from {
                width: 0;
                opacity: 0;
            }
            to {
                width: 4px;
                opacity: 1;
            }
        }
        
        /* Icon Color for Active State */
        .nav-link.active svg {
            color: #4a90e2;
            transform: scale(1.1);
        }
        
        .dark .nav-link.active svg {
            color: #50e3c2;
        }
        
        /* Hover Effect Enhancement */
        .nav-link:hover:not(.active) {
            transform: translateX(4px);
        }
        
        .nav-link:hover:not(.active) svg {
            transform: scale(1.05);
        }
        
        /* Toast Animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fade-in {
            animation: fadeIn 0.3s ease;
        }
        
        /* Task Tab Styles */
        .task-tab {
            color: #6b7280;
            border-bottom: 2px solid transparent;
        }
        
        .task-tab.active {
            color: #4a90e2;
            border-bottom-color: #4a90e2;
            background: linear-gradient(to bottom, rgba(74, 144, 226, 0.05), transparent);
        }
        
        .dark .task-tab.active {
            color: #50e3c2;
            border-bottom-color: #50e3c2;
        }
        
        .task-tab:hover:not(.active) {
            color: #374151;
            background: rgba(74, 144, 226, 0.05);
        }
        
        .dark .task-tab:hover:not(.active) {
            color: #9ca3af;
        }
        
        .task-period-content {
            display: none;
        }
        
        .task-period-content.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }
        
        /* Dark mode toggle button */
        .theme-toggle {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            background: var(--tw-colors-card-light);
            border: 1px solid var(--tw-colors-border-light);
            border-radius: 50%;
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .dark .theme-toggle {
            background: var(--tw-colors-card-dark);
            border-color: var(--tw-colors-border-dark);
        }
        
        .theme-toggle:hover {
            transform: scale(1.1);
        }
        
        /* Kanban Board Styles */
        .kanban-column {
            transition: all 0.2s ease;
        }
        
        .kanban-column.drag-over {
            background-color: rgba(74, 144, 226, 0.1);
            border: 2px dashed #4a90e2;
        }
        
        .dragging {
            opacity: 0.5;
            transform: rotate(5deg);
        }
        
        .kanban-task-card {
            transition: all 0.2s ease;
        }
        
        .kanban-task-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .kanban-task-card:hover .task-checkbox {
            opacity: 1;
            transform: scale(1.1);
        }
        
        .task-checkbox {
            opacity: 0;
            transition: all 0.2s ease;
            transform: scale(0.9);
        }
        
        .task-checkbox:hover {
            opacity: 1 !important;
            transform: scale(1.2) !important;
        }
        
        .completed-task-card {
            opacity: 0.7;
            background: linear-gradient(135deg, rgba(34, 197, 94, 0.1) 0%, rgba(34, 197, 94, 0.05) 100%);
            border-color: rgba(34, 197, 94, 0.3);
        }
        
        .completed-task-card:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }
        
        /* Planlayıcı animasyonları */
        @keyframes bounce-slow {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(10px);
            }
        }
        
        .animate-bounce-slow {
            animation: bounce-slow 2s ease-in-out infinite;
        }
    </style>

    <script src="assets/js/stitch-app.js"></script>
    <script src="assets/js/inbox.js"></script>
    <script src="assets/js/planner.js"></script>
    <script src="assets/js/tags.js"></script>
    <script src="assets/js/friends.js"></script>
    <script src="assets/js/groups.js"></script>
    <script src="assets/js/gemini.js"></script>
    <script src="assets/js/notebooklm.js"></script>
    <script src="assets/js/kanban.js"></script>
    <script>
        // App is already initialized in stitch-app.js
    </script>
</body>
</html>