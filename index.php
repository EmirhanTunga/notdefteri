<?php
// Oturum başlat
session_start();
// Kullanıcı giriş kontrolü
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}
// Kullanıcı avatarını çek
require_once 'db.php';
require_once 'check_google_user.php';
$stmt = $pdo->prepare('SELECT avatar FROM users WHERE username = ?');
$stmt->execute([$_SESSION['username']]);
$user = $stmt->fetch();
$avatar = $user && $user['avatar'] ? $user['avatar'] : 'cat';
$avatarList = [
    'cat' => '🐱',
    'dog' => '🐶',
    'panda' => '🐼',
    'rabbit' => '🐰',
    'bear' => '🐻',
    'fox' => '🦊',
    'koala' => '🐨',
    'tiger' => '🐯',
    'monkey' => '🐵',
    'penguin' => '🐧'
];
$themeList = [
    'blue' => 'Mavi',
    'green' => 'Yeşil',
    'purple' => 'Mor',
    'orange' => 'Turuncu',
    'navy' => 'Lacivert',
    'black' => 'Siyah',
    'darkpurple' => 'Koyu Mor'
];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Not Defteri</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="bg-paws" id="bgPaws"></div>
    <div class="bg-notes" id="bgNotes"></div>
    <div class="container-wrapper">
        <div class="container">
            <div class="main-nav-bar">
                <a href="feed.php" class="main-nav-btn">🌟 Sosyal Akış</a>
                <a href="notlarim.php" class="main-nav-btn">📒 Notlarım</a>
                <a href="friends.php" class="main-nav-btn">👥 Arkadaşlar</a>
            </div>
            <div class="top-bar">
                <h1>Merhaba, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
                <?php if (isset($_SESSION['google_user']) && $_SESSION['google_user']): ?>
                    <div style="background: #e8f5e8; border: 1px solid #4caf50; color: #2e7d32; padding: 8px 12px; border-radius: 8px; margin-bottom: 10px; text-align: center; font-size: 0.9em;">
                        <strong>🔐 Google ile giriş yaptınız</strong>
                    </div>
                <?php endif; ?>
                <div class="profile-area">
                    <button id="avatarBtn" class="avatar-btn" title="Profil Resmini Değiştir">
                        <span class="avatar-emoji" id="avatarEmoji"><?php echo $avatarList[$avatar] ?? '🐱'; ?></span>
                    </button>
                    <button id="themeBtn" class="theme-btn" title="Tema Rengini Değiştir">🎨</button>
                    <button id="darkModeToggle" class="dark-toggle" title="Karanlık Modu Aç/Kapat">🌙</button>
                </div>
            </div>
            <form id="noteForm" method="POST" action="save_note.php" enctype="multipart/form-data">
                <textarea name="note" id="note" placeholder="Notunu buraya yaz..." required></textarea>
                <input type="text" name="tags" id="noteTags" placeholder="#etiket1, #etiket2 (isteğe bağlı)" class="note-tags">
                <!-- Renk seçici kaldırıldı -->
                <input type="file" name="note_file" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt">
            </form>
            <div class="note-form-actions-centered">
                <button type="submit" form="noteForm" name="save_only" class="note-save-btn">Notu Kaydet</button>
                <button type="submit" form="noteForm" name="save_and_share" class="note-share-btn">Sosyal Akışta Paylaş</button>
            </div>
            <a href="logout.php" class="logout logout-top">Çıkış Yap</a>
        </div>
        <div class="plan-container">
            <div class="plan-header">
                <button class="plan-tab active" id="tab-daily" onclick="showPlan('daily')">Günlük Plan</button>
                <button class="plan-tab" id="tab-weekly" onclick="showPlan('weekly')">Haftalık Plan</button>
                <div class="add-bubble" id="add-bubble" onclick="openTaskModal()">+</div>
            </div>
            <div id="plan-daily" class="plan-list">
                <?php include 'daily_tasks.php'; ?>
            </div>
            <div id="plan-weekly" class="plan-list" style="display:none;">
                <div class="calendar-view" id="calendarView"></div>
                <?php include 'weekly_tasks.php'; ?>
            </div>
        </div>
    </div>
    <div id="task-modal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close" onclick="closeTaskModal()">&times;</span>
            <form id="taskForm" method="POST" action="task_add.php">
                <label>Görev:</label>
                <input type="text" name="task" required>
                <input type="hidden" name="type" id="task-type" value="daily">
                <div id="week-start-container" style="display:none;">
                    <label>Hafta Başlangıcı:</label>
                    <input type="date" name="week_start">
                </div>
                <button type="submit">Ekle</button>
            </form>
        </div>
    </div>
    <div id="avatarModal" class="modal" style="display:none;">
        <div class="modal-content avatar-modal-content">
            <span class="close" onclick="closeAvatarModal()">&times;</span>
            <h3>Profil Resmini Seç</h3>
            <form id="avatarForm" method="POST" action="set_avatar.php">
                <div class="avatar-list">
                    <?php foreach ($avatarList as $key => $emoji): ?>
                        <label class="avatar-radio">
                            <input type="radio" name="avatar" value="<?php echo $key; ?>" <?php if ($avatar === $key) echo 'checked'; ?>>
                            <span class="avatar-emoji-big"><?php echo $emoji; ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
                <button type="submit">Kaydet</button>
            </form>
        </div>
    </div>
    <div id="themeModal" class="modal" style="display:none;">
        <div class="modal-content theme-modal-content">
            <span class="close" onclick="closeThemeModal()">&times;</span>
            <h3>Tema Rengini Seç</h3>
            <form id="themeForm">
                <div class="theme-list">
                    <?php foreach ($themeList as $key => $name): ?>
                        <label class="theme-radio theme-<?php echo $key; ?>">
                            <input type="radio" name="theme" value="<?php echo $key; ?>">
                            <span class="theme-color"></span> <?php echo $name; ?>
                        </label>
                    <?php endforeach; ?>
                </div>
                <button type="button" onclick="saveTheme()">Kaydet</button>
            </form>
        </div>
    </div>
    <div id="toast" class="toast"></div>
    <script src="script.js"></script>
</body>
</html> 