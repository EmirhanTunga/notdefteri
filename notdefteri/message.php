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
// Hedef kullanıcıyı bul
$to_username = $_GET['to'] ?? '';
$stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
$stmt->execute([$to_username]);
$to_user = $stmt->fetch();
if (!$to_user) {
    die('Kullanıcı bulunamadı.');
}
$to_id = $to_user['id'];
// Arkadaşlık kontrolü
$stmt = $pdo->prepare('SELECT * FROM friends WHERE ((user_id = ? AND friend_id = ?) OR (user_id = ? AND friend_id = ?)) AND status = "accepted"');
$stmt->execute([$user_id, $to_id, $to_id, $user_id]);
if (!$stmt->fetch()) {
    die('Sadece arkadaşlarınıza mesaj gönderebilirsiniz.');
}
// Mesaj gönderme
if (isset($_POST['message']) && trim($_POST['message']) !== '') {
    $msg = trim($_POST['message']);
    $stmt = $pdo->prepare('INSERT INTO messages (sender_id, receiver_id, message, sent_at) VALUES (?, ?, ?, NOW())');
    $stmt->execute([$user_id, $to_id, $msg]);
}
// Mesajları çek
$stmt = $pdo->prepare('SELECT m.*, u.username FROM messages m JOIN users u ON m.sender_id = u.id WHERE (m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?) ORDER BY m.sent_at ASC');
$stmt->execute([$user_id, $to_id, $to_id, $user_id]);
$messages = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mesajlaşma - <?php echo htmlspecialchars($to_username); ?></title>
    <link rel="stylesheet" href="style.css">
    <style>
        .msg-chat-box { max-width: 500px; margin: 40px auto; background: #fffbe6; border-radius: 16px; box-shadow: 0 2px 8px #fda08522; padding: 24px 18px 18px 18px; }
        .msg-list { max-height: 340px; overflow-y: auto; margin-bottom: 18px; display: flex; flex-direction: column; gap: 10px; }
        .msg-item { padding: 8px 14px; border-radius: 12px; background: #f6d36533; max-width: 80%; word-break: break-word; }
        .msg-item.me { background: #fda08533; align-self: flex-end; }
        .msg-item.them { background: #fff; align-self: flex-start; }
        .msg-meta { font-size: 0.85em; color: #bdbdbd; margin-bottom: 2px; }
        .msg-form { display: flex; gap: 8px; }
        .msg-form input { flex: 1; padding: 8px; border-radius: 8px; border: 1px solid #fda085; }
        .msg-form button { padding: 8px 18px; border-radius: 8px; border: none; background: linear-gradient(90deg, #fda085, #f6d365); color: #fff; font-weight: bold; cursor: pointer; }
        .msg-form button:hover { background: linear-gradient(90deg, #f6d365, #fda085); }
    </style>
</head>
<body>
    <a href="friends.php" class="back-home-btn" style="position: absolute; top: 18px; left: 18px; z-index: 10; margin: 0;">👥 Arkadaşlar</a>
    <div class="msg-chat-box">
        <h2><?php echo htmlspecialchars($to_username); ?> ile Mesajlaşma</h2>
        <div class="msg-list">
            <?php foreach ($messages as $m): ?>
                <div class="msg-item <?php echo $m['sender_id'] == $user_id ? 'me' : 'them'; ?>">
                    <div class="msg-meta"><?php echo htmlspecialchars($m['username']); ?> • <?php echo date('d.m.Y H:i', strtotime($m['sent_at'])); ?></div>
                    <?php echo nl2br(htmlspecialchars($m['message'])); ?>
                </div>
            <?php endforeach; ?>
        </div>
        <form class="msg-form" method="post">
            <input type="text" name="message" placeholder="Mesajınızı yazın... 😊" maxlength="500" autocomplete="off">
            <button type="submit">Gönder</button>
        </form>
    </div>
</body>
</html> 