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
// Notları çek
$notes = [];
if ($user_id) {
    $stmt = $pdo->prepare('SELECT * FROM notes WHERE user_id = ? ORDER BY id DESC');
    $stmt->execute([$user_id]);
    $notes = $stmt->fetchAll();
}
// Etiketleri bul
$allTags = [];
foreach ($notes as $n) {
    $tags = array_filter(array_map('trim', explode(',', $n['tags'])));
    foreach ($tags as $t) {
        if ($t) $allTags[$t] = true;
    }
}
$allTags = array_keys($allTags);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notlarım</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container-wrapper">
        <div class="container">
            <div class="top-bar">
                <h1>Notlarım</h1>
                <a href="index.php" class="feed-btn" style="margin-left:auto;">➕ Not Ekle</a>
            </div>
            <a href="index.php" class="back-home-btn">🏠 Ana Sayfa</a>
            <div class="note-filters">
                <input type="text" id="noteSearch" placeholder="Notlarda ara..." class="note-search" onkeyup="filterNotes()">
                <select id="tagFilter" class="tag-filter" onchange="filterNotes()">
                    <option value="">Tüm Etiketler</option>
                    <?php foreach ($allTags as $tag): ?>
                        <option value="<?php echo htmlspecialchars($tag); ?>"><?php echo htmlspecialchars($tag); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div id="notes">
                <ul class="note-list">
                <?php foreach ($notes as $note): ?>
                    <li class="note-color-<?php echo htmlspecialchars($note['color']); ?>">
                        <span class="note-date"><?php echo date('d M Y H:i', strtotime($note['created_at'] ?? 'now')); ?></span>
                        <span class="note-text"><?php echo nl2br(htmlspecialchars($note['note'])); ?></span>
                        <?php if ($note['tags']): ?>
                            <div>
                                <?php foreach (array_filter(array_map('trim', explode(',', $note['tags']))) as $tag): ?>
                                    <span class="note-tag">#<?php echo htmlspecialchars($tag); ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
                </ul>
                <?php if (empty($notes)): ?>
                    <div class="no-notes">Henüz notun yok.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script>
    function filterNotes() {
        var search = document.getElementById('noteSearch').value.toLowerCase();
        var tag = document.getElementById('tagFilter').value;
        var notes = document.querySelectorAll('.note-list li');
        notes.forEach(function(note) {
            var text = note.innerText.toLowerCase();
            var tags = Array.from(note.querySelectorAll('.note-tag')).map(e=>e.innerText);
            var tagMatch = !tag || tags.some(t=>t.replace('#','')===tag);
            var searchMatch = !search || text.includes(search);
            note.style.display = (tagMatch && searchMatch) ? '' : 'none';
        });
    }
    </script>
</body>
</html> 