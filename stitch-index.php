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
require_once 'check_google_user.php';

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
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-foreground-light dark:text-foreground-dark">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-card-light dark:bg-card-dark border-r border-border-light dark:border-border-dark flex flex-col">
            <div class="p-4 border-b border-border-light dark:border-border-dark">
                <h1 class="text-lg font-bold">Not Defteri</h1>
                <p class="text-sm text-subtle-light dark:text-subtle-dark">Tüm notlar</p>
            </div>
            
            <nav class="flex-1 px-2 py-4 space-y-1">
                <a class="flex items-center gap-3 px-3 py-2 rounded-DEFAULT text-sm font-medium hover:bg-accent-light dark:hover:bg-accent-dark" href="#" data-section="dashboard">
                    <svg class="icon" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg">
                        <path d="M218.83,103.77l-80-75.48a1.14,1.14,0,0,1-.11-.11,16,16,0,0,0-21.53,0l-.11.11L37.17,103.77A16,16,0,0,0,32,115.55V208a16,16,0,0,0,16,16H96a16,16,0,0,0,16-16V160h32v48a16,16,0,0,0,16,16h48a16,16,0,0,0,16-16V115.55A16,16,0,0,0,218.83,103.77ZM208,208H160V160a16,16,0,0,0-16-16H112a16,16,0,0,0-16,16v48H48V115.55l.11-.1L128,40l79.9,75.43.11.1Z"></path>
                    </svg>
                    <span>Ana Sayfa</span>
                </a>
                
                <a class="flex items-center gap-3 px-3 py-2 rounded-DEFAULT text-sm font-medium bg-primary-accent/20 text-primary-accent" href="#" data-section="notes">
                    <svg class="icon" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg">
                        <path d="M213.66,82.34l-56-56A8,8,0,0,0,152,24H56A16,16,0,0,0,40,40V216a16,16,0,0,0,16,16H200a16,16,0,0,0,16-16V88A8,8,0,0,0,213.66,82.34ZM152,88V44l44,44Z"></path>
                    </svg>
                    <span>Tüm Notlar</span>
                </a>
                
                <a class="flex items-center gap-3 px-3 py-2 rounded-DEFAULT text-sm font-medium hover:bg-accent-light dark:hover:bg-accent-dark" href="#" data-section="favorites">
                    <svg class="icon" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg">
                        <path d="M224.6,88.1l-56-56A8,8,0,0,0,162.77,32H56A16,16,0,0,0,40,48V208a16,16,0,0,0,16,16H200a16,16,0,0,0,16-16V93.23A8,8,0,0,0,224.6,88.1ZM128,204.42l-26.12-17.41a8,8,0,0,1-.52-12.33L120.47,152l-23.77-19.81a8,8,0,0,1,10.6-12L128,136.69l20.69-16.55a8,8,0,0,1,10.6,12L135.53,152l19.11,22.93a8,8,0,0,1-11.06,11.56L128,204.42ZM208,208H56V48h96v48a8,8,0,0,0,8,8h48Z"></path>
                    </svg>
                    <span>Favoriler</span>
                </a>
                
                <a class="flex items-center gap-3 px-3 py-2 rounded-DEFAULT text-sm font-medium hover:bg-accent-light dark:hover:bg-accent-dark" href="#" data-section="kanban">
                    <svg class="icon" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg">
                        <path d="M240,192h-8V56a16,16,0,0,0-16-16H40A16,16,0,0,0,24,56V192H16a8,8,0,0,0,0,16H240a8,8,0,0,0,0-16ZM40,56H216V192H200V168a8,8,0,0,0-8-8H120a8,8,0,0,0-8,8v24H72V88H184v48a8,8,0,0,0,16,0V80a8,8,0,0,0-8-8H64a8,8,0,0,0-8,8V192H40ZM184,192H128V176h56Z"></path>
                    </svg>
                    <span>Kanban Board</span>
                </a>
                
                <a class="flex items-center gap-3 px-3 py-2 rounded-DEFAULT text-sm font-medium hover:bg-accent-light dark:hover:bg-accent-dark" href="#" data-section="archived">
                    <svg class="icon" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg">
                        <path d="M213.66,82.34l-56-56A8,8,0,0,0,152,24H56A16,16,0,0,0,40,40v88H56V40h96V88a8,8,0,0,0,8,8h48v40h16V88A8,8,0,0,0,213.66,82.34ZM104,152v64a8,8,0,0,0,8,8h32a8,8,0,0,0,8-8V152a8,8,0,0,0-8-8H112A8,8,0,0,0,104,152Zm8,0h32v64H112Zm112,56H176a8,8,0,0,0,0,16h40a8,8,0,0,0,8-8V160a8,8,0,0,0-16,0Zm-8-48a8,8,0,0,0-8,8v24H176a8,8,0,0,0,0,16h24v24a8,8,0,0,0,16,0V168a8,8,0,0,0-8-8Zm-64-48H40v72a16,16,0,0,0,16,16H88a8,8,0,0,0,0-16H56V144H88a8,8,0,0,0,0-16H56V112Z"></path>
                    </svg>
                    <span>Arşivlenmiş Notlar</span>
                </a>
                
                <a class="flex items-center gap-3 px-3 py-2 rounded-DEFAULT text-sm font-medium hover:bg-accent-light dark:hover:bg-accent-dark" href="#" data-section="tags">
                    <svg class="icon" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg">
                        <path d="M228,80v96a12,12,0,0,1-12,12H192a12,12,0,0,1-8.49-3.51L160,161.94,136.49,185.5a12,12,0,0,1-17,0L95.94,161.94,72.49,185.5A12,12,0,0,1,64,188H40a12,12,0,0,1-12-12V80A12,12,0,0,1,40,68H216A12,12,0,0,1,228,80Z"></path>
                    </svg>
                    <span>Etiketler</span>
                </a>
                
                <a class="flex items-center gap-3 px-3 py-2 rounded-DEFAULT text-sm font-medium hover:bg-accent-light dark:hover:bg-accent-dark" href="#" data-section="settings">
                    <svg class="icon" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg">
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
        <main class="flex-1 p-8">
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
                    <div class="flex items-center gap-4">
                        <h1 class="text-4xl font-bold">Ana Sayfa</h1>
                    <button class="bg-primary text-white font-medium px-6 py-2.5 rounded-lg hover:bg-primary/90 transition-colors" id="newItemBtn">
                        Yeni Öğe
                    </button>
                    </div>
                </header>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-card-light dark:bg-card-dark rounded-lg border border-border-light dark:border-border-dark p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-subtle-light dark:text-subtle-dark">Toplam Not</p>
                                <p class="text-2xl font-bold" id="totalNotes">-</p>
                            </div>
                            <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                                <svg class="icon-sm text-primary" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M213.66,82.34l-56-56A8,8,0,0,0,152,24H56A16,16,0,0,0,40,40V216a16,16,0,0,0,16,16H200a16,16,0,0,0,16-16V88A8,8,0,0,0,213.66,82.34ZM152,88V44l44,44Z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-card-light dark:bg-card-dark rounded-lg border border-border-light dark:border-border-dark p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-subtle-light dark:text-subtle-dark">Günlük Görevler</p>
                                <p class="text-2xl font-bold" id="dailyTasks">-</p>
                            </div>
                            <div class="w-12 h-12 bg-green-500/10 rounded-lg flex items-center justify-center">
                                <svg class="icon-sm text-green-500" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-card-light dark:bg-card-dark rounded-lg border border-border-light dark:border-border-dark p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-subtle-light dark:text-subtle-dark">Haftalık Görevler</p>
                                <p class="text-2xl font-bold" id="weeklyTasks">-</p>
                            </div>
                            <div class="w-12 h-12 bg-orange-500/10 rounded-lg flex items-center justify-center">
                                <svg class="icon-sm text-orange-500" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M208,32H184V24a8,8,0,0,0-16,0v8H88V24a8,8,0,0,0-16,0v8H48A16,16,0,0,0,32,48V208a16,16,0,0,0,16,16H208a16,16,0,0,0,16-16V48A16,16,0,0,0,208,32ZM72,48v8a8,8,0,0,0,16,0V48h80v8a8,8,0,0,0,16,0V48h24V80H48V48ZM208,208H48V96H208V208Z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-card-light dark:bg-card-dark rounded-lg border border-border-light dark:border-border-dark p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-subtle-light dark:text-subtle-dark">Arkadaşlar</p>
                                <p class="text-2xl font-bold" id="friendsCount">-</p>
                            </div>
                            <div class="w-12 h-12 bg-blue-500/10 rounded-lg flex items-center justify-center">
                                <svg class="icon-sm text-blue-500" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M117.25,157.92a60,60,0,1,0-66.5,0A95.83,95.83,0,0,0,3.53,195.63a8,8,0,1,0,13.4,8.74,80,80,0,0,1,134.14,0,8,8,0,0,0,13.4-8.74A95.83,95.83,0,0,0,117.25,157.92ZM40,108a44,44,0,1,1,44,44A44.05,44.05,0,0,1,40,108Zm210.27,98.63a8,8,0,0,1-11.29.74A80,80,0,0,0,172,168a8,8,0,0,1,0-16,96,96,0,0,1,66.27,26.37A8,8,0,0,1,250.27,206.63ZM172,120a44,44,0,1,1-16.34-84.87,8,8,0,1,1-5.94,14.85,28,28,0,1,0,0,52.06,8,8,0,1,1,5.94,14.85A43.85,43.85,0,0,1,172,120Z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-card-light dark:bg-card-dark rounded-lg border border-border-light dark:border-border-dark p-6">
                    <h2 class="text-xl font-semibold mb-4">Son Aktiviteler</h2>
                    <div id="recentActivity" class="space-y-4">
                        <!-- Recent activity will be loaded here -->
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
                    <button class="bg-primary text-white font-medium px-6 py-2.5 rounded-lg hover:bg-primary/90 transition-colors">
                        Yeni Görev
                    </button>
                </header>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- To Do Column -->
                    <div class="bg-card-light dark:bg-card-dark rounded-xl p-4 flex flex-col">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-base font-bold uppercase tracking-wider text-blue-500">Yapılacaklar</h3>
                            <span class="text-base font-semibold text-subtle-light dark:text-subtle-dark bg-accent-light dark:bg-accent-dark px-3 py-1 rounded-full">3</span>
                        </div>
                        <div class="space-y-4 flex-grow">
                            <div class="bg-accent-light dark:bg-accent-dark p-4 rounded-lg border border-border-light dark:border-border-dark cursor-grab active:cursor-grabbing">
                                <h4 class="text-base font-semibold text-foreground-light dark:text-foreground-dark mb-2">Landing sayfası tasarla</h4>
                                <p class="text-sm text-subtle-light dark:text-subtle-dark mb-3">Ana sayfa için modern ve responsive tasarım oluştur.</p>
                                <div class="flex items-center justify-between text-sm text-subtle-light dark:text-subtle-dark">
                                    <span class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                            </svg>
                                        15 Temmuz
                                    </span>
                                    <div class="flex items-center -space-x-2">
                                        <div class="w-6 h-6 rounded-full bg-primary text-white text-xs flex items-center justify-center border-2 border-card-light dark:border-card-dark">A</div>
                                        <div class="w-6 h-6 rounded-full bg-green-500 text-white text-xs flex items-center justify-center border-2 border-card-light dark:border-card-dark">B</div>
                        </div>
                    </div>
                </div>
                
                            <div class="bg-accent-light dark:bg-accent-dark p-4 rounded-lg border border-border-light dark:border-border-dark cursor-grab active:cursor-grabbing">
                                <h4 class="text-base font-semibold text-foreground-light dark:text-foreground-dark mb-2">Kullanıcı kimlik doğrulama</h4>
                                <p class="text-sm text-subtle-light dark:text-subtle-dark mb-3">Kayıt, giriş ve şifre sıfırlama işlevselliğini uygula.</p>
                                <div class="flex items-center justify-between text-sm text-subtle-light dark:text-subtle-dark">
                                    <span class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                        </svg>
                                        20 Temmuz
                                    </span>
                                    <div class="flex items-center -space-x-2">
                                        <div class="w-6 h-6 rounded-full bg-blue-500 text-white text-xs flex items-center justify-center border-2 border-card-light dark:border-card-dark">C</div>
                    </div>
                </div>
            </div>
            
                            <div class="bg-accent-light dark:bg-accent-dark p-4 rounded-lg border border-border-light dark:border-border-dark cursor-grab active:cursor-grabbing">
                                <h4 class="text-base font-semibold text-foreground-light dark:text-foreground-dark mb-2">CI/CD pipeline kurulumu</h4>
                                <p class="text-sm text-subtle-light dark:text-subtle-dark mb-3">Uygulama için test ve dağıtım süreçlerini otomatikleştir.</p>
                                <div class="flex items-center justify-between text-sm text-subtle-light dark:text-subtle-dark">
                                    <span class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                        </svg>
                                        25 Temmuz
                                    </span>
                                    <div class="flex items-center -space-x-2">
                                        <div class="w-6 h-6 rounded-full bg-purple-500 text-white text-xs flex items-center justify-center border-2 border-card-light dark:border-card-dark">D</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button class="mt-4 flex items-center justify-center w-full gap-2 px-4 py-2 text-base font-medium text-subtle-light dark:text-subtle-dark hover:bg-accent-light dark:hover:bg-accent-dark rounded-lg">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"></path>
                            </svg>
                            <span>Görev Ekle</span>
                        </button>
                    </div>
                    
                    <!-- In Progress Column -->
                    <div class="bg-card-light dark:bg-card-dark rounded-xl p-4 flex flex-col">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-base font-bold uppercase tracking-wider text-orange-500">Devam Eden</h3>
                            <span class="text-base font-semibold text-subtle-light dark:text-subtle-dark bg-accent-light dark:bg-accent-dark px-3 py-1 rounded-full">1</span>
                        </div>
                        <div class="space-y-4 flex-grow">
                            <div class="bg-accent-light dark:bg-accent-dark p-4 rounded-lg border border-border-light dark:border-border-dark cursor-grab active:cursor-grabbing">
                                <h4 class="text-base font-semibold text-foreground-light dark:text-foreground-dark mb-2">Veritabanı şeması oluştur</h4>
                                <p class="text-sm text-subtle-light dark:text-subtle-dark mb-3">Proje verileri için tablolar, sütunlar ve ilişkileri tanımla.</p>
                                <div class="flex items-center justify-between text-sm text-subtle-light dark:text-subtle-dark">
                                    <span class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                        </svg>
                                        18 Temmuz
                                    </span>
                                    <div class="flex items-center -space-x-2">
                                        <div class="w-6 h-6 rounded-full bg-yellow-500 text-white text-xs flex items-center justify-center border-2 border-card-light dark:border-card-dark">E</div>
                                        <div class="w-6 h-6 rounded-full bg-red-500 text-white text-xs flex items-center justify-center border-2 border-card-light dark:border-card-dark">F</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button class="mt-4 flex items-center justify-center w-full gap-2 px-4 py-2 text-base font-medium text-subtle-light dark:text-subtle-dark hover:bg-accent-light dark:hover:bg-accent-dark rounded-lg">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"></path>
                            </svg>
                            <span>Görev Ekle</span>
                    </button>
                    </div>
                    
                    <!-- Completed Column -->
                    <div class="bg-card-light dark:bg-card-dark rounded-xl p-4 flex flex-col">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-base font-bold uppercase tracking-wider text-green-500">Tamamlanan</h3>
                            <span class="text-base font-semibold text-subtle-light dark:text-subtle-dark bg-accent-light dark:bg-accent-dark px-3 py-1 rounded-full">2</span>
                        </div>
                        <div class="space-y-4 flex-grow">
                            <div class="bg-accent-light dark:bg-accent-dark p-4 rounded-lg border border-border-light dark:border-border-dark cursor-grab active:cursor-grabbing opacity-60">
                                <h4 class="text-base font-semibold text-subtle-light dark:text-subtle-dark mb-2 line-through">Proje kurulumu</h4>
                                <p class="text-sm text-subtle-light dark:text-subtle-dark mb-3">Repository'yi başlat ve geliştirme ortamını yapılandır.</p>
                                <div class="flex items-center justify-between text-sm text-subtle-light dark:text-subtle-dark">
                                    <span class="flex items-center gap-1.5 font-semibold text-green-500">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Tamamlandı
                                    </span>
                                    <div class="flex items-center -space-x-2">
                                        <div class="w-6 h-6 rounded-full bg-gray-500 text-white text-xs flex items-center justify-center border-2 border-card-light dark:border-card-dark">G</div>
                                    </div>
                </div>
            </div>
            
                            <div class="bg-accent-light dark:bg-accent-dark p-4 rounded-lg border border-border-light dark:border-border-dark cursor-grab active:cursor-grabbing opacity-60">
                                <h4 class="text-base font-semibold text-subtle-light dark:text-subtle-dark mb-2 line-through">Teknoloji yığını seçimi</h4>
                                <p class="text-sm text-subtle-light dark:text-subtle-dark mb-3">Frontend ve backend için framework ve kütüphaneleri belirle.</p>
                                <div class="flex items-center justify-between text-sm text-subtle-light dark:text-subtle-dark">
                                    <span class="flex items-center gap-1.5 font-semibold text-green-500">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Tamamlandı
                                    </span>
                                    <div class="flex items-center -space-x-2">
                                        <div class="w-6 h-6 rounded-full bg-indigo-500 text-white text-xs flex items-center justify-center border-2 border-card-light dark:border-card-dark">H</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button class="mt-4 flex items-center justify-center w-full gap-2 px-4 py-2 text-base font-medium text-subtle-light dark:text-subtle-dark hover:bg-accent-light dark:hover:bg-accent-dark rounded-lg">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"></path>
                            </svg>
                            <span>Görev Ekle</span>
                    </button>
                    </div>
                </div>
            </div>
            
            <!-- Archived Section -->
            <div id="archived-section" class="content-section" style="display: none;">
                <header class="flex items-center justify-between mb-8">
                    <h1 class="text-4xl font-bold">Arşivlenmiş Notlar</h1>
                </header>
                <div class="bg-card-light dark:bg-card-dark rounded-lg border border-border-light dark:border-border-dark p-6">
                    <p class="text-subtle-light dark:text-subtle-dark">Arşivlenmiş notlarınız burada görünecek.</p>
                </div>
            </div>
            
            <!-- Tags Section -->
            <div id="tags-section" class="content-section" style="display: none;">
                <header class="flex items-center justify-between mb-8">
                    <h1 class="text-4xl font-bold">Etiketler</h1>
                </header>
                <div class="bg-card-light dark:bg-card-dark rounded-lg border border-border-light dark:border-border-dark p-6">
                    <p class="text-subtle-light dark:text-subtle-dark">Not etiketleriniz burada görünecek.</p>
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
                            <a class="px-3 py-2 rounded font-medium text-subtle-light dark:text-subtle-dark hover:bg-accent-light dark:hover:bg-accent-dark" href="#help">Yardım ve Destek</a>
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
                                <button class="px-4 py-2 rounded-lg bg-primary text-white text-sm font-bold hover:opacity-90">Profili Güncelle</button>
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
                                <button class="px-4 py-2 rounded-lg bg-primary text-white text-sm font-bold hover:opacity-90">Ayarları Kaydet</button>
                            </div>
                        </section>
                        
                        <!-- Help Section -->
                        <section id="help">
                            <h2 class="text-xl font-bold text-foreground-light dark:text-foreground-dark border-b border-border-light dark:border-border-dark pb-4">Yardım ve Destek</h2>
                            <div class="space-y-4 pt-6">
                                <a class="flex items-center justify-between p-4 rounded-lg bg-accent-light dark:bg-accent-dark hover:bg-accent-light/80 dark:hover:bg-accent-dark/80" href="#">
                                    <span class="text-base text-foreground-light dark:text-foreground-dark font-medium">SSS</span>
                                    <svg class="w-5 h-5 text-subtle-light dark:text-subtle-dark" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8.25 4.5l7.5 7.5-7.5 7.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                </a>
                                <a class="flex items-center justify-between p-4 rounded-lg bg-accent-light dark:bg-accent-dark hover:bg-accent-light/80 dark:hover:bg-accent-dark/80" href="#">
                                    <span class="text-base text-foreground-light dark:text-foreground-dark font-medium">Kullanıcı Kılavuzları</span>
                                    <svg class="w-5 h-5 text-subtle-light dark:text-subtle-dark" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8.25 4.5l7.5 7.5-7.5 7.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                </a>
                                <a class="flex items-center justify-between p-4 rounded-lg bg-accent-light dark:bg-accent-dark hover:bg-accent-light/80 dark:hover:bg-accent-dark/80" href="#">
                                    <span class="text-base text-foreground-light dark:text-foreground-dark font-medium">Destekle İletişime Geçin</span>
                                    <svg class="w-5 h-5 text-subtle-light dark:text-subtle-dark" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8.25 4.5l7.5 7.5-7.5 7.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                </a>
                            </div>
                        </section>
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
    </style>

    <script src="assets/js/stitch-app.js"></script>
    <script>
        // Initialize the app when page loads
        document.addEventListener('DOMContentLoaded', function() {
            window.stitchApp = new StitchApp();
        });
    </script>
</body>
</html>