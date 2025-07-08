<?php
session_start();
require 'db.php';
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Kullanıcı id'sini bul
$stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
$stmt->execute([$_SESSION['username']]);
$user = $stmt->fetch();
$user_id = $user ? $user['id'] : 0;

// Kullanıcı arama
$search_results = [];
if (isset($_GET['q']) && strlen(trim($_GET['q'])) > 0) {
    $q = '%' . trim($_GET['q']) . '%';
    $stmt = $pdo->prepare('SELECT id, username FROM users WHERE username LIKE ? AND id != ?');
    $stmt->execute([$q, $user_id]);
    $search_results = $stmt->fetchAll();
}

// Gelen arkadaşlık istekleri
$incoming_requests = [];
$stmt = $pdo->prepare('SELECT f.id, u.username FROM friends f JOIN users u ON f.user_id = u.id WHERE f.friend_id = ? AND f.status = "pending"');
$stmt->execute([$user_id]);
$incoming_requests = $stmt->fetchAll();

// İstek kabul/ret işlemi
if (isset($_POST['accept_friend_id'])) {
    $fid = intval($_POST['accept_friend_id']);
    $stmt = $pdo->prepare('UPDATE friends SET status = "accepted" WHERE id = ? AND friend_id = ?');
    $stmt->execute([$fid, $user_id]);
    $msg = 'Arkadaşlık isteği kabul edildi!';
}
if (isset($_POST['decline_friend_id'])) {
    $fid = intval($_POST['decline_friend_id']);
    $stmt = $pdo->prepare('UPDATE friends SET status = "declined" WHERE id = ? AND friend_id = ?');
    $stmt->execute([$fid, $user_id]);
    $msg = 'Arkadaşlık isteği reddedildi!';
}

// Mevcut arkadaşlar
$friends = [];
$stmt = $pdo->prepare('SELECT u.username FROM friends f JOIN users u ON (u.id = f.friend_id OR u.id = f.user_id) WHERE (f.user_id = ? OR f.friend_id = ?) AND f.status = "accepted" AND u.id != ?');
$stmt->execute([$user_id, $user_id, $user_id]);
$friends = $stmt->fetchAll();

// Arkadaşlık isteği gönderme
if (isset($_POST['add_friend_id'])) {
    $friend_id = intval($_POST['add_friend_id']);
    // Zaten istek var mı kontrol et
    $stmt = $pdo->prepare('SELECT * FROM friends WHERE (user_id = ? AND friend_id = ?) OR (user_id = ? AND friend_id = ?)');
    $stmt->execute([$user_id, $friend_id, $friend_id, $user_id]);
    if (!$stmt->fetch()) {
        $stmt = $pdo->prepare('INSERT INTO friends (user_id, friend_id, status) VALUES (?, ?, "pending")');
        $stmt->execute([$user_id, $friend_id]);
        $msg = 'Arkadaşlık isteği gönderildi!';
    } else {
        $msg = 'Zaten istek gönderdiniz veya arkadaşsınız.';
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arkadaşlar</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .friend-search-box { max-width: 400px; margin: 32px auto 18px auto; display: flex; gap: 8px; }
        .friend-search-box input { flex: 1; padding: 8px; border-radius: 8px; border: 1px solid #fda085; }
        .friend-search-box button { padding: 8px 18px; border-radius: 8px; border: none; background: linear-gradient(90deg, #fda085, #f6d365); color: #fff; font-weight: bold; cursor: pointer; }
        .friend-search-box button:hover { background: linear-gradient(90deg, #f6d365, #fda085); }
        .friend-result { background: #fffbe6; border-radius: 10px; padding: 12px 18px; margin-bottom: 10px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 2px 8px #fda08522; }
        .friend-result .username { font-weight: bold; color: #f76d6d; }
        .friend-result form { margin: 0; }
        .msg-info { text-align: center; color: #43a047; margin-bottom: 12px; }
    </style>
</head>
<body>
    <a href="index.php" class="back-home-btn" style="position: absolute; top: 18px; left: 18px; z-index: 10; margin: 0;">🏠 Ana Sayfa</a>
    <div class="container-wrapper">
        <div class="container">
            <h2>Arkadaşlar</h2>
            <form class="friend-search-box" method="get">
                <input type="text" name="q" placeholder="Kullanıcı ara..." value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>">
                <button type="submit">Ara</button>
            </form>
            <?php if (!empty($msg)): ?>
                <div class="msg-info"><?php echo $msg; ?></div>
            <?php endif; ?>
            <?php if (isset($_GET['q'])): ?>
                <?php if (count($search_results) > 0): ?>
                    <?php foreach ($search_results as $u): ?>
                        <div class="friend-result">
                            <span class="username"><?php echo htmlspecialchars($u['username']); ?></span>
                            <form method="post" style="margin:0;">
                                <input type="hidden" name="add_friend_id" value="<?php echo $u['id']; ?>">
                                <button type="submit">Arkadaşlık İsteği Gönder</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="msg-info">Kullanıcı bulunamadı.</div>
                <?php endif; ?>
            <?php endif; ?>
            <h3>Gelen Arkadaşlık İstekleri</h3>
            <?php if (count($incoming_requests) > 0): ?>
                <?php foreach ($incoming_requests as $req): ?>
                    <div class="friend-result">
                        <span class="username"><?php echo htmlspecialchars($req['username']); ?></span>
                        <form method="post" style="margin:0; display:inline-block;">
                            <input type="hidden" name="accept_friend_id" value="<?php echo $req['id']; ?>">
                            <button type="submit">Kabul Et</button>
                        </form>
                        <form method="post" style="margin:0; display:inline-block;">
                            <input type="hidden" name="decline_friend_id" value="<?php echo $req['id']; ?>">
                            <button type="submit">Reddet</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="msg-info">Gelen arkadaşlık isteği yok.</div>
            <?php endif; ?>
            <h3>Arkadaşlarım</h3>
            <?php if (count($friends) > 0): ?>
                <?php foreach ($friends as $fr): ?>
                    <div class="friend-result">
                        <span class="username"><?php echo htmlspecialchars($fr['username']); ?></span>
                        <a href="message.php?to=<?php echo urlencode($fr['username']); ?>" class="back-home-btn" style="padding:4px 12px; font-size:0.95em; margin:0;">Mesaj Gönder</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="msg-info">Henüz arkadaşın yok.</div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html> 