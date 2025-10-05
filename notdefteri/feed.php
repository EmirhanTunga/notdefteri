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
// Paylaşım ekleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['public_note'])) {
    $content = trim($_POST['public_note']);
    if ($content !== '') {
        $stmt = $pdo->prepare('INSERT INTO public_notes (user_id, content) VALUES (?, ?)');
        $stmt->execute([$user_id, $content]);
        if (isset($_POST['ajax']) || isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'json') !== false) {
            header('Content-Type: application/json');
            echo json_encode(['success'=>true]);
            exit();
        }
        header('Location: feed.php?toast=paylasildi');
        exit();
    }
}
// Paylaşımları çek
$stmt = $pdo->prepare('SELECT n.*, u.username, u.avatar FROM public_notes n JOIN users u ON n.user_id = u.id ORDER BY n.created_at DESC');
$stmt->execute();
$notes = $stmt->fetchAll();
// Kullanıcının beğendiği notlar
$stmt = $pdo->prepare('SELECT note_id FROM public_likes WHERE user_id = ?');
$stmt->execute([$user_id]);
$liked = array_column($stmt->fetchAll(), 'note_id');
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
?>
<a href="index.php" class="back-home-btn" style="position: absolute; top: 18px; left: 18px; z-index: 10; margin: 0;">🏠 Ana Sayfa</a>
<div class="feed-center-wrapper" style="max-width: 700px; margin: 0 auto;">
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paylaşımlar - Not Defterim</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Quicksand:wght@400;600&display=swap" rel="stylesheet">
    <style>
    .feed-container {
        max-width: 540px;
        margin: 40px auto 0 auto;
        background: rgba(255,255,255,0.97);
        border-radius: 24px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.13);
        padding: 32px 22px 22px 22px;
        min-height: 600px;
        position: relative;
    }
    .feed-title {
        font-family: 'Pacifico', cursive;
        font-size: 2.1em;
        color: #f76d6d;
        text-align: center;
        margin-bottom: 18px;
        letter-spacing: 1.2px;
        text-shadow: 0 2px 8px #fda08533;
    }
    .public-note-form textarea {
        width: 100%;
        min-height: 60px;
        border-radius: 12px;
        border: 1.5px solid #fda085;
        padding: 12px;
        font-size: 1.08em;
        font-family: 'Quicksand', sans-serif;
        background: #fff7e6;
        color: #d35400;
        margin-bottom: 8px;
        resize: vertical;
        transition: box-shadow 0.2s, border 0.2s;
    }
    .public-note-form textarea:focus {
        box-shadow: 0 0 0 2px #f6d365;
        border: 2px solid #fda085;
        outline: none;
        background: #fffbe6;
    }
    .public-note-form button {
        background: linear-gradient(90deg, #fda085, #f6d365);
        color: #fff;
        border: none;
        border-radius: 10px;
        font-weight: bold;
        font-size: 1.08em;
        padding: 10px 24px;
        cursor: pointer;
        transition: background 0.2s, transform 0.1s;
        box-shadow: 0 2px 8px #fda08522;
        margin-left: auto;
        display: block;
    }
    .public-note-form button:hover {
        background: linear-gradient(90deg, #f6d365, #fda085);
        transform: scale(1.04);
    }
    .public-feed-list {
        margin-top: 28px;
        display: flex;
        flex-direction: column;
        gap: 22px;
    }
    .public-feed-item {
        background: #fffbe6;
        border-radius: 16px;
        box-shadow: 0 2px 8px #fda08522;
        padding: 18px 18px 12px 18px;
        position: relative;
        animation: fadein 0.7s;
    }
    .public-feed-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 6px;
    }
    .public-feed-avatar {
        font-size: 1.7em;
        border-radius: 50%;
        background: #fff;
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px #fda08522;
    }
    .public-feed-username {
        font-weight: bold;
        color: #f76d6d;
        font-family: 'Quicksand', sans-serif;
        font-size: 1.08em;
    }
    .public-feed-date {
        color: #bdbdbd;
        font-size: 0.98em;
        margin-left: auto;
    }
    .public-feed-content {
        font-size: 1.13em;
        color: #333;
        margin-bottom: 10px;
        font-family: 'Quicksand', sans-serif;
        white-space: pre-line;
    }
    .public-feed-actions {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 6px;
    }
    .public-like-btn {
        background: none;
        border: none;
        font-size: 1.3em;
        color: #ffd600;
        cursor: pointer;
        transition: transform 0.2s, color 0.2s;
        outline: none;
        margin-right: 2px;
    }
    .public-like-btn.liked {
        color: #f76d6d;
        text-shadow: 0 2px 8px #fda08533;
        transform: scale(1.15) rotate(-8deg);
    }
    .public-feed-likes {
        font-size: 1em;
        color: #f76d6d;
        margin-right: 8px;
    }
    .public-feed-comments {
        margin-top: 8px;
        padding-left: 8px;
        border-left: 2.5px solid #fda08533;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .public-feed-comment {
        font-size: 1em;
        color: #555;
        font-family: 'Quicksand', sans-serif;
        background: #fff;
        border-radius: 8px;
        padding: 6px 10px;
        box-shadow: 0 1px 4px #fda08511;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .public-feed-comment-user {
        color: #f76d6d;
        font-weight: bold;
        margin-right: 4px;
    }
    .public-feed-comment-date {
        color: #bdbdbd;
        font-size: 0.93em;
        margin-left: auto;
    }
    .public-comment-form {
        display: flex;
        gap: 8px;
        margin-top: 6px;
    }
    .public-comment-form input {
        flex: 1 1 0;
        border-radius: 8px;
        border: 1.5px solid #fda085;
        padding: 7px 10px;
        font-size: 1em;
        font-family: 'Quicksand', sans-serif;
        background: #fffbe6;
        color: #d35400;
        transition: box-shadow 0.2s, border 0.2s;
    }
    .public-comment-form input:focus {
        box-shadow: 0 0 0 2px #f6d365;
        border: 2px solid #fda085;
        outline: none;
        background: #fff7e6;
    }
    .public-comment-form button {
        background: #f76d6d;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-weight: bold;
        font-size: 1em;
        padding: 7px 16px;
        cursor: pointer;
        transition: background 0.2s, transform 0.1s;
    }
    .public-comment-form button:hover {
        background: #fda085;
        transform: scale(1.07);
    }
    </style>
</head>
<body>
    <button id="theme-toggle-btn" title="Tema Değiştir">🌓</button>
    <div class="feed-container">
        <div class="feed-title">Paylaşımlar</div>
        <div class="public-feed-list">
            <?php foreach ($notes as $note): ?>
                <div class="public-feed-item">
                    <div class="public-feed-header">
                        <span class="public-feed-avatar"><?php echo $avatarList[$note['avatar']] ?? '🐱'; ?></span>
                        <span class="public-feed-username" style="margin-left: 8px;"><?php echo htmlspecialchars($note['username']); ?></span>
                        <span class="public-feed-date"><?php echo date('d.m.Y H:i', strtotime($note['created_at'])); ?></span>
                    </div>
                    <div class="public-feed-content"><?php echo nl2br(htmlspecialchars($note['content'])); ?></div>
                    <div class="public-feed-actions">
                        <form method="POST" action="like_public_note.php" style="display:inline;" onsubmit="event.preventDefault(); return likePublicNote(<?php echo $note['id']; ?>, this.querySelector('button'))">
                            <input type="hidden" name="note_id" value="<?php echo $note['id']; ?>">
                            <button type="submit" class="public-like-btn<?php if (in_array($note['id'], $liked)) echo ' liked'; ?>" title="Beğen"><?php echo in_array($note['id'], $liked) ? '❤️' : '🤍'; ?></button>
                        </form>
                        <span class="public-feed-likes"><?php echo (int)$note['like_count']; ?> beğeni</span>
                    </div>
                    <div class="public-feed-comments">
                        <?php
                        $stmt2 = $pdo->prepare('SELECT c.*, u.username FROM public_comments c JOIN users u ON c.user_id = u.id WHERE c.note_id = ? ORDER BY c.created_at ASC');
                        $stmt2->execute([$note['id']]);
                        $comments = $stmt2->fetchAll();
                        foreach ($comments as $comment): ?>
                            <div class="public-feed-comment">
                                <span class="public-feed-comment-user"><?php echo htmlspecialchars($comment['username']); ?></span>
                                <span><?php echo htmlspecialchars($comment['comment']); ?></span>
                                <span class="public-feed-comment-date"><?php echo date('d M H:i', strtotime($comment['created_at'])); ?></span>
                            </div>
                        <?php endforeach; ?>
                        <form class="public-comment-form" method="POST" action="add_public_comment.php" onsubmit="event.preventDefault(); return addPublicComment(this, <?php echo $note['id']; ?>)">
                            <input type="hidden" name="note_id" value="<?php echo $note['id']; ?>">
                            <input type="text" name="comment" maxlength="200" placeholder="Yorum yaz..." required>
                            <button type="submit">Gönder</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>
</div> 