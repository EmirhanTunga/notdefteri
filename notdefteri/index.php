<?php
// Oturum başlat
session_start();
// Kullanıcı giriş kontrolü
if (!isset($_SESSION['username'])) {
    header('Location: stitch-login.php');
    exit();
}
// Yeni Stitch tasarımına yönlendir
header('Location: stitch-index.php');
exit();
// Kullanıcı bilgilerini çek
require_once 'db.php';

$stmt = $pdo->prepare('SELECT username, email FROM users WHERE username = ?');
$stmt->execute([$_SESSION['username']]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Not Defteri - ClickUp Style</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/design-system.css">
    <link rel="stylesheet" href="assets/css/layout.css">
    <link rel="stylesheet" href="assets/css/components.css">
    <link rel="stylesheet" href="assets/css/themes.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
</head>
<body>
    <!-- Main App Container -->
    <div class="app-container" id="app">
        
        <!-- Header Component -->
        <header class="header" id="header">
            <div class="header-left">
                <button class="mobile-menu-toggle" id="mobileSidebarToggle" aria-label="Toggle sidebar">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <line x1="3" y1="6" x2="21" y2="6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <line x1="3" y1="12" x2="21" y2="12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <line x1="3" y1="18" x2="21" y2="18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <div class="header-title">
                    <h1 id="pageTitle">Dashboard</h1>
                </div>
            </div>
            
            <div class="header-center">
                <div class="search-container">
                    <div class="search-input-wrapper">
                        <svg class="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none">
                            <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/>
                            <path d="m21 21-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <input type="text" class="search-input" placeholder="Notlar ve görevlerde ara..." id="globalSearch">
                    </div>
                    <div class="search-results" id="searchResults" style="display: none;">
                        <!-- Search results will be populated here -->
                    </div>
                </div>
            </div>
            
            <div class="header-right">
                <button class="theme-toggle" id="themeToggle" aria-label="Toggle theme">
                    <svg class="theme-icon theme-icon-light" width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="5" stroke="currentColor" stroke-width="2"/>
                        <line x1="12" y1="1" x2="12" y2="3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <line x1="12" y1="21" x2="12" y2="23" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <line x1="18.36" y1="18.36" x2="19.78" y2="19.78" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <line x1="1" y1="12" x2="3" y2="12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <line x1="21" y1="12" x2="23" y2="12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <line x1="18.36" y1="5.64" x2="19.78" y2="4.22" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <svg class="theme-icon theme-icon-dark" width="20" height="20" viewBox="0 0 24 24" fill="none" style="display: none;">
                        <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                
                <div class="user-dropdown" id="userDropdown">
                    <button class="user-dropdown-trigger" id="userDropdownTrigger" aria-label="User menu">
                        <div class="user-avatar">
                            <span class="user-initial"><?php echo strtoupper(substr($user['username'], 0, 1)); ?></span>
                        </div>
                        <span class="user-name"><?php echo htmlspecialchars($user['username']); ?></span>
                        <svg class="dropdown-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none">
                            <polyline points="6,9 12,15 18,9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    
                    <div class="user-dropdown-menu" id="userDropdownMenu" style="display: none;">
                        <div class="dropdown-header">
                            <div class="user-info">
                                <div class="user-name"><?php echo htmlspecialchars($user['username']); ?></div>
                                <div class="user-email"><?php echo htmlspecialchars($user['email'] ?? 'Email not set'); ?></div>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a href="#settings" class="dropdown-item" data-section="settings">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
                                <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z" stroke="currentColor" stroke-width="2"/>
                            </svg>
                            Ayarlar
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="logout.php" class="dropdown-item dropdown-item-danger">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Çıkış Yap
                        </a>
                    </div>
                </div>
            </div>
        </header>
        
        <!-- Sidebar Navigation -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <svg class="logo-icon" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="logo-text">Not Defteri</span>
                </div>
                <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <path d="M3 12h18m-9-9l9 9-9 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
            
            <nav class="sidebar-nav" role="navigation">
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="#dashboard" class="nav-link active" data-section="dashboard">
                            <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none">
                                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span class="nav-text">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#notes" class="nav-link" data-section="notes">
                            <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none">
                                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <polyline points="14,2 14,8 20,8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span class="nav-text">Notlarım</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#daily-tasks" class="nav-link" data-section="daily-tasks">
                            <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2"/>
                                <line x1="16" y1="2" x2="16" y2="6" stroke="currentColor" stroke-width="2"/>
                                <line x1="8" y1="2" x2="8" y2="6" stroke="currentColor" stroke-width="2"/>
                                <line x1="3" y1="10" x2="21" y2="10" stroke="currentColor" stroke-width="2"/>
                            </svg>
                            <span class="nav-text">Günlük Görevler</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#weekly-tasks" class="nav-link" data-section="weekly-tasks">
                            <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2"/>
                                <line x1="16" y1="2" x2="16" y2="6" stroke="currentColor" stroke-width="2"/>
                                <line x1="8" y1="2" x2="8" y2="6" stroke="currentColor" stroke-width="2"/>
                                <line x1="3" y1="10" x2="21" y2="10" stroke="currentColor" stroke-width="2"/>
                                <path d="M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01M16 18h.01" stroke="currentColor" stroke-width="2"/>
                            </svg>
                            <span class="nav-text">Haftalık Görevler</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#public-notes" class="nav-link" data-section="public-notes">
                            <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                                <line x1="2" y1="12" x2="22" y2="12" stroke="currentColor" stroke-width="2"/>
                                <path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z" stroke="currentColor" stroke-width="2"/>
                            </svg>
                            <span class="nav-text">Herkese Açık</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#friends" class="nav-link" data-section="friends">
                            <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none">
                                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
                                <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span class="nav-text">Arkadaşlar</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#messages" class="nav-link" data-section="messages">
                            <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none">
                                <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span class="nav-text">Mesajlar</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#settings" class="nav-link" data-section="settings">
                            <svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
                                <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z" stroke="currentColor" stroke-width="2"/>
                            </svg>
                            <span class="nav-text">Ayarlar</span>
                        </a>
                    </li>
                </ul>
            </nav>
            
            <div class="sidebar-footer">
                <div class="sidebar-user">
                    <div class="user-avatar">
                        <span class="user-initial"><?php echo strtoupper(substr($user['username'], 0, 1)); ?></span>
                    </div>
                    <div class="user-info">
                        <div class="user-name"><?php echo htmlspecialchars($user['username']); ?></div>
                        <div class="user-email"><?php echo htmlspecialchars($user['email'] ?? ''); ?></div>
                    </div>
                </div>
            </div>
        </aside>
        
        <!-- Main Content Area -->
        <main class="main-content" id="mainContent">
            <div class="content-wrapper">
                
                <!-- Dashboard Section -->
                <section class="content-section active" id="dashboard-section">
                    <div class="dashboard-container">
                        <!-- Dashboard content will be loaded here -->
                    </div>
                </section>
                
                <!-- Notes Section -->
                <section class="content-section" id="notes-section">
                    <div class="section-header">
                        <h2>Notlarım</h2>
                        <button class="btn btn-primary" id="addNoteBtn">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <line x1="12" y1="5" x2="12" y2="19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <line x1="5" y1="12" x2="19" y2="12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Yeni Not
                        </button>
                    </div>
                    <div class="notes-container" id="notesContainer">
                        <!-- Notes will be loaded here -->
                    </div>
                </section>
                
                <!-- Daily Tasks Section -->
                <section class="content-section" id="daily-tasks-section">
                    <div class="section-header">
                        <h2>Günlük Görevler</h2>
                        <button class="btn btn-primary" id="addDailyTaskBtn">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <line x1="12" y1="5" x2="12" y2="19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <line x1="5" y1="12" x2="19" y2="12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Yeni Görev
                        </button>
                    </div>
                    <div class="tasks-container" id="dailyTasksContainer">
                        <!-- Daily tasks will be loaded here -->
                    </div>
                </section>
                
                <!-- Weekly Tasks Section -->
                <section class="content-section" id="weekly-tasks-section">
                    <div class="section-header">
                        <h2>Haftalık Görevler</h2>
                        <button class="btn btn-primary" id="addWeeklyTaskBtn">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <line x1="12" y1="5" x2="12" y2="19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <line x1="5" y1="12" x2="19" y2="12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Yeni Görev
                        </button>
                    </div>
                    <div class="tasks-container" id="weeklyTasksContainer">
                        <!-- Weekly tasks will be loaded here -->
                    </div>
                </section>
                
                <!-- Public Notes Section -->
                <section class="content-section" id="public-notes-section">
                    <div class="section-header">
                        <h2>Herkese Açık Notlar</h2>
                    </div>
                    <div class="public-notes-container" id="publicNotesContainer">
                        <!-- Public notes will be loaded here -->
                    </div>
                </section>
                
                <!-- Friends Section -->
                <section class="content-section" id="friends-section">
                    <div class="section-header">
                        <h2>Arkadaşlar</h2>
                    </div>
                    <div class="friends-container" id="friendsContainer">
                        <!-- Friends will be loaded here -->
                    </div>
                </section>
                
                <!-- Messages Section -->
                <section class="content-section" id="messages-section">
                    <div class="section-header">
                        <h2>Mesajlar</h2>
                    </div>
                    <div class="messages-container" id="messagesContainer">
                        <!-- Messages will be loaded here -->
                    </div>
                </section>
                
                <!-- Settings Section -->
                <section class="content-section" id="settings-section">
                    <div class="settings-container" id="settingsContainer">
                        <div class="settings-header">
                            <div class="settings-header-content">
                                <div class="settings-header-icon">
                                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none">
                                        <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
                                        <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                </div>
                                <div class="settings-header-text">
                                    <h2>Ayarlar</h2>
                                    <p>Hesap ayarlarınızı ve tercihlerinizi yönetin</p>
                                </div>
                            </div>
                            <div class="settings-stats">
                                <div class="settings-stat">
                                    <div class="settings-stat-value" id="totalNotesCount">-</div>
                                    <div class="settings-stat-label">Toplam Not</div>
                                </div>
                                <div class="settings-stat">
                                    <div class="settings-stat-value" id="totalTasksCount">-</div>
                                    <div class="settings-stat-label">Toplam Görev</div>
                                </div>
                                <div class="settings-stat">
                                    <div class="settings-stat-value" id="accountAge">-</div>
                                    <div class="settings-stat-label">Hesap Yaşı</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="settings-content">
                            <div class="settings-grid">
                                <!-- Appearance Settings -->
                                <div class="settings-category">
                                    <div class="settings-category-header">
                                        <h3>Görünüm</h3>
                                        <p>Arayüz teması ve görünüm ayarları</p>
                                    </div>
                                    
                                    <!-- Theme Settings -->
                                    <div class="settings-card">
                                        <div class="settings-card-header">
                                            <div class="settings-card-icon theme-icon">
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                                    <circle cx="12" cy="12" r="5" stroke="currentColor" stroke-width="2"/>
                                                    <line x1="12" y1="1" x2="12" y2="3" stroke="currentColor" stroke-width="2"/>
                                                    <line x1="12" y1="21" x2="12" y2="23" stroke="currentColor" stroke-width="2"/>
                                                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64" stroke="currentColor" stroke-width="2"/>
                                                    <line x1="18.36" y1="18.36" x2="19.78" y2="19.78" stroke="currentColor" stroke-width="2"/>
                                                    <line x1="1" y1="12" x2="3" y2="12" stroke="currentColor" stroke-width="2"/>
                                                    <line x1="21" y1="12" x2="23" y2="12" stroke="currentColor" stroke-width="2"/>
                                                    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36" stroke="currentColor" stroke-width="2"/>
                                                    <line x1="18.36" y1="5.64" x2="19.78" y2="4.22" stroke="currentColor" stroke-width="2"/>
                                                </svg>
                                            </div>
                                            <div class="settings-card-title">
                                                <h4>Tema Seçimi</h4>
                                                <p>Arayüz temasını değiştirin</p>
                                            </div>
                                        </div>
                                        <div class="settings-card-content">
                                            <div class="theme-selector">
                                                <div class="theme-option" data-theme="light">
                                                    <div class="theme-preview theme-preview-light">
                                                        <div class="theme-preview-header"></div>
                                                        <div class="theme-preview-sidebar"></div>
                                                        <div class="theme-preview-content">
                                                            <div class="theme-preview-card"></div>
                                                            <div class="theme-preview-card"></div>
                                                        </div>
                                                    </div>
                                                    <div class="theme-option-info">
                                                        <span class="theme-option-name">Açık Tema</span>
                                                        <span class="theme-option-desc">Parlak ve temiz görünüm</span>
                                                    </div>
                                                    <input type="radio" name="theme" value="light" id="theme-light">
                                                </div>
                                                <div class="theme-option" data-theme="dark">
                                                    <div class="theme-preview theme-preview-dark">
                                                        <div class="theme-preview-header"></div>
                                                        <div class="theme-preview-sidebar"></div>
                                                        <div class="theme-preview-content">
                                                            <div class="theme-preview-card"></div>
                                                            <div class="theme-preview-card"></div>
                                                        </div>
                                                    </div>
                                                    <div class="theme-option-info">
                                                        <span class="theme-option-name">Koyu Tema</span>
                                                        <span class="theme-option-desc">Gözleri yormayan koyu renk</span>
                                                    </div>
                                                    <input type="radio" name="theme" value="dark" id="theme-dark">
                                                </div>
                                                <div class="theme-option" data-theme="auto">
                                                    <div class="theme-preview theme-preview-auto">
                                                        <div class="theme-preview-header"></div>
                                                        <div class="theme-preview-sidebar"></div>
                                                        <div class="theme-preview-content">
                                                            <div class="theme-preview-card"></div>
                                                            <div class="theme-preview-card"></div>
                                                        </div>
                                                    </div>
                                                    <div class="theme-option-info">
                                                        <span class="theme-option-name">Otomatik</span>
                                                        <span class="theme-option-desc">Sistem ayarını takip et</span>
                                                    </div>
                                                    <input type="radio" name="theme" value="auto" id="theme-auto">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Interface Settings -->
                                    <div class="settings-card">
                                        <div class="settings-card-header">
                                            <div class="settings-card-icon interface-icon">
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2"/>
                                                    <line x1="9" y1="9" x2="15" y2="9" stroke="currentColor" stroke-width="2"/>
                                                    <line x1="9" y1="15" x2="15" y2="15" stroke="currentColor" stroke-width="2"/>
                                                </svg>
                                            </div>
                                            <div class="settings-card-title">
                                                <h4>Arayüz Ayarları</h4>
                                                <p>Arayüz davranışlarını özelleştirin</p>
                                            </div>
                                        </div>
                                        <div class="settings-card-content">
                                            <div class="settings-toggles">
                                                <div class="settings-toggle">
                                                    <div class="toggle-info">
                                                        <span class="toggle-name">Animasyonlar</span>
                                                        <span class="toggle-desc">Geçiş animasyonlarını etkinleştir</span>
                                                    </div>
                                                    <label class="toggle-switch">
                                                        <input type="checkbox" id="animationsEnabled" checked>
                                                        <span class="toggle-slider"></span>
                                                    </label>
                                                </div>
                                                <div class="settings-toggle">
                                                    <div class="toggle-info">
                                                        <span class="toggle-name">Otomatik Kaydetme</span>
                                                        <span class="toggle-desc">Değişiklikleri otomatik kaydet</span>
                                                    </div>
                                                    <label class="toggle-switch">
                                                        <input type="checkbox" id="autoSave" checked>
                                                        <span class="toggle-slider"></span>
                                                    </label>
                                                </div>
                                                <div class="settings-toggle">
                                                    <div class="toggle-info">
                                                        <span class="toggle-name">Kompakt Görünüm</span>
                                                        <span class="toggle-desc">Daha az boşluk kullan</span>
                                                    </div>
                                                    <label class="toggle-switch">
                                                        <input type="checkbox" id="compactView">
                                                        <span class="toggle-slider"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Account Settings -->
                                <div class="settings-category">
                                    <div class="settings-category-header">
                                        <h3>Hesap</h3>
                                        <p>Hesap bilgileri ve güvenlik ayarları</p>
                                    </div>
                                    
                                    <!-- Profile Settings -->
                                    <div class="settings-card">
                                        <div class="settings-card-header">
                                            <div class="settings-card-icon profile-icon">
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                                    <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" stroke="currentColor" stroke-width="2"/>
                                                    <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
                                                </svg>
                                            </div>
                                            <div class="settings-card-title">
                                                <h4>Profil Bilgileri</h4>
                                                <p>Hesap bilgilerinizi görüntüleyin</p>
                                            </div>
                                        </div>
                                        <div class="settings-card-content">
                                            <div class="profile-info">
                                                <div class="profile-avatar">
                                                    <div class="avatar-circle">
                                                        <span class="avatar-initial"><?php echo strtoupper(substr($user['username'], 0, 1)); ?></span>
                                                    </div>
                                                    <div class="avatar-info">
                                                        <div class="avatar-name"><?php echo htmlspecialchars($user['username']); ?></div>
                                                        <div class="avatar-email"><?php echo htmlspecialchars($user['email'] ?? 'Email belirtilmemiş'); ?></div>
                                                    </div>
                                                </div>
                                                <div class="profile-details">
                                                    <div class="profile-field">
                                                        <label>Kullanıcı Adı</label>
                                                        <div class="profile-value"><?php echo htmlspecialchars($user['username']); ?></div>
                                                    </div>
                                                    <div class="profile-field">
                                                        <label>E-posta Adresi</label>
                                                        <div class="profile-value"><?php echo htmlspecialchars($user['email'] ?? 'Belirtilmemiş'); ?></div>
                                                    </div>
                                                    <div class="profile-field">
                                                        <label>Hesap Durumu</label>
                                                        <div class="profile-value">
                                                            <span class="status-badge status-active">Aktif</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Security Settings -->
                                    <div class="settings-card">
                                        <div class="settings-card-header">
                                            <div class="settings-card-icon security-icon">
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2" stroke="currentColor" stroke-width="2"/>
                                                    <circle cx="12" cy="16" r="1" stroke="currentColor" stroke-width="2"/>
                                                    <path d="M7 11V7a5 5 0 0110 0v4" stroke="currentColor" stroke-width="2"/>
                                                </svg>
                                            </div>
                                            <div class="settings-card-title">
                                                <h4>Güvenlik</h4>
                                                <p>Şifre ve güvenlik ayarları</p>
                                            </div>
                                        </div>
                                        <div class="settings-card-content">
                                            <form id="changePasswordForm" class="password-form">
                                                <div class="form-row">
                                                    <div class="form-group">
                                                        <label for="currentPassword">
                                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" stroke="currentColor" stroke-width="2"/>
                                                                <circle cx="12" cy="16" r="1" stroke="currentColor" stroke-width="2"/>
                                                                <path d="M7 11V7a5 5 0 0110 0v4" stroke="currentColor" stroke-width="2"/>
                                                            </svg>
                                                            Mevcut Şifre
                                                        </label>
                                                        <input type="password" id="currentPassword" name="current_password" required placeholder="Mevcut şifrenizi girin">
                                                    </div>
                                                </div>
                                                <div class="form-row">
                                                    <div class="form-group">
                                                        <label for="newPassword">
                                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" stroke="currentColor" stroke-width="2"/>
                                                                <circle cx="12" cy="16" r="1" stroke="currentColor" stroke-width="2"/>
                                                                <path d="M7 11V7a5 5 0 0110 0v4" stroke="currentColor" stroke-width="2"/>
                                                            </svg>
                                                            Yeni Şifre
                                                        </label>
                                                        <input type="password" id="newPassword" name="new_password" required minlength="6" placeholder="En az 6 karakter">
                                                        <div class="password-strength" id="passwordStrength"></div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="confirmPassword">
                                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                                                <polyline points="20,6 9,17 4,12" stroke="currentColor" stroke-width="2"/>
                                                            </svg>
                                                            Şifre Onayı
                                                        </label>
                                                        <input type="password" id="confirmPassword" name="confirm_password" required minlength="6" placeholder="Yeni şifreyi tekrar girin">
                                                    </div>
                                                </div>
                                                <div class="form-actions">
                                                    <button type="submit" class="btn btn-primary">
                                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                                            <polyline points="20,6 9,17 4,12" stroke="currentColor" stroke-width="2"/>
                                                        </svg>
                                                        Şifreyi Güncelle
                                                    </button>
                                                    <button type="button" class="btn btn-secondary" id="generatePasswordBtn">
                                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                                            <path d="M23 4v6h-6M1 20v-6h6M20.49 9A9 9 0 005.64 5.64L1 10m22 4a9 9 0 01-14.85 4.36L3 14" stroke="currentColor" stroke-width="2"/>
                                                        </svg>
                                                        Güçlü Şifre Oluştur
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Data & Privacy -->
                                <div class="settings-category">
                                    <div class="settings-category-header">
                                        <h3>Veri ve Gizlilik</h3>
                                        <p>Veri yönetimi ve gizlilik ayarları</p>
                                    </div>
                                    
                                    <!-- Data Management -->
                                    <div class="settings-card">
                                        <div class="settings-card-header">
                                            <div class="settings-card-icon data-icon">
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" stroke="currentColor" stroke-width="2"/>
                                                    <polyline points="14,2 14,8 20,8" stroke="currentColor" stroke-width="2"/>
                                                    <line x1="16" y1="13" x2="8" y2="13" stroke="currentColor" stroke-width="2"/>
                                                    <line x1="16" y1="17" x2="8" y2="17" stroke="currentColor" stroke-width="2"/>
                                                </svg>
                                            </div>
                                            <div class="settings-card-title">
                                                <h4>Veri Yönetimi</h4>
                                                <p>Verilerinizi yedekleyin veya silin</p>
                                            </div>
                                        </div>
                                        <div class="settings-card-content">
                                            <div class="data-management">
                                                <div class="data-stats">
                                                    <div class="data-stat">
                                                        <div class="data-stat-icon">
                                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                                                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" stroke="currentColor" stroke-width="2"/>
                                                            </svg>
                                                        </div>
                                                        <div class="data-stat-info">
                                                            <span class="data-stat-value" id="dataNotesCount">-</span>
                                                            <span class="data-stat-label">Not</span>
                                                        </div>
                                                    </div>
                                                    <div class="data-stat">
                                                        <div class="data-stat-icon">
                                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                                                <polyline points="9,11 12,14 22,4" stroke="currentColor" stroke-width="2"/>
                                                            </svg>
                                                        </div>
                                                        <div class="data-stat-info">
                                                            <span class="data-stat-value" id="dataTasksCount">-</span>
                                                            <span class="data-stat-label">Görev</span>
                                                        </div>
                                                    </div>
                                                    <div class="data-stat">
                                                        <div class="data-stat-icon">
                                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                                                            </svg>
                                                        </div>
                                                        <div class="data-stat-info">
                                                            <span class="data-stat-value" id="dataSize">-</span>
                                                            <span class="data-stat-label">KB</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="data-actions">
                                                    <button class="btn btn-secondary" id="exportDataBtn">
                                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                                            <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3" stroke="currentColor" stroke-width="2"/>
                                                        </svg>
                                                        Verileri Dışa Aktar
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Danger Zone -->
                                    <div class="settings-card danger-card">
                                        <div class="settings-card-header">
                                            <div class="settings-card-icon danger-icon">
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                                    <path d="M12 9v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2"/>
                                                </svg>
                                            </div>
                                            <div class="settings-card-title">
                                                <h4>Tehlikeli İşlemler</h4>
                                                <p>Bu işlemler geri alınamaz</p>
                                            </div>
                                        </div>
                                        <div class="settings-card-content">
                                            <div class="danger-actions">
                                                <div class="danger-action">
                                                    <div class="danger-action-info">
                                                        <h5>Hesabı Sil</h5>
                                                        <p>Hesabınızı ve tüm verilerinizi kalıcı olarak silin</p>
                                                    </div>
                                                    <button class="btn btn-danger" id="deleteAccountBtn">
                                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                                            <polyline points="3,6 5,6 21,6" stroke="currentColor" stroke-width="2"/>
                                                            <path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2" stroke="currentColor" stroke-width="2"/>
                                                        </svg>
                                                        Hesabı Sil
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                
            </div>
        </main>
    </div>
    
    <!-- JavaScript Files -->
    <script src="assets/js/utils/helpers.js"></script>
    <script src="assets/js/utils/api.js"></script>
    <script src="assets/js/utils/theme.js"></script>
    <script src="assets/js/components/sidebar.js"></script>
    <script src="assets/js/components/header.js"></script>
    <script src="assets/js/components/dashboard.js"></script>
    <script src="assets/js/components/notes.js"></script>
    <script src="assets/js/components/settings.js"></script>
    <script src="assets/js/app.js"></script>
</body>
</html>