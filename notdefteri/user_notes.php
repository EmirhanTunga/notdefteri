<?php
if (!isset($_SESSION['username'])) return;
require 'db.php';
$username = $_SESSION['username'];
// Kullanıcı id'sini bul
$stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
$stmt->execute([$username]);
$user = $stmt->fetch();
if (!$user) {
    return;
}
$stmt = $pdo->prepare('SELECT id, note, created_at, color, tags, is_favorite FROM notes WHERE user_id = ? ORDER BY is_favorite DESC, created_at DESC');
$stmt->execute([$user['id']]);
$notes = $stmt->fetchAll();
if (!$notes) {
    return;
} else {
    $allTags = [];
    echo '<ul class="note-list">';
    foreach ($notes as $note) {
        $colorClass = 'note-color-' . htmlspecialchars($note['color']);
        $favClass = $note['is_favorite'] ? 'favorite' : '';
        echo '<li id="note-'.$note['id'].'" class="'.$colorClass.' '.$favClass.'">';
        // Favori yıldız butonu
        echo '<button class="star-btn" onclick="toggleFavorite('.$note['id'].')" title="Favori">'.($note['is_favorite'] ? '★' : '☆').'</button>';
        // Emoji reaksiyon butonu
        echo '<button class="react-btn" onclick="showReaction(this)" title="Tebrik Et">🎉</button>';
        echo '<span class="note-date">' . htmlspecialchars($note['created_at']) . '</span><br>';
        echo '<span class="note-text" id="note-text-'.$note['id'].'">' . nl2br(htmlspecialchars($note['note'])) . '</span>';
        // Etiketler
        if (!empty($note['tags'])) {
            $tagsArr = array_map('trim', explode(',', $note['tags']));
            foreach ($tagsArr as $tag) {
                if ($tag) {
                    $tagClean = ltrim($tag, '#');
                    $allTags[$tagClean] = true;
                    echo '<span class="note-tag">#'.htmlspecialchars($tagClean).'</span> ';
                }
            }
        }
        echo '<div class="note-actions">';
        echo '<button class="edit-btn" onclick="editNote('.$note['id'].')">Düzenle</button> ';
        echo '<a href="not_sil.php?id='.$note['id'].'" class="delete-btn" onclick="return confirm(\'Silmek istediğine emin misin?\')">Sil</a>';
        echo '</div>';
        // Düzenleme formu (gizli)
        echo '<form class="edit-form" id="edit-form-'.$note['id'].'" method="POST" action="not_duzenle.php" style="display:none; margin-top:8px;">';
        echo '<textarea name="note">'.htmlspecialchars($note['note']).'</textarea>';
        echo '<input type="hidden" name="id" value="'.$note['id'].'">';
        echo '<button type="submit">Kaydet</button>';
        echo '<button type="button" onclick="cancelEdit('.$note['id'].')">İptal</button>';
        echo '</form>';
        echo '</li>';
    }
    echo '</ul>';
    // Etiketleri JS ile filtreye aktar
    echo '<script>window.allNoteTags = '.json_encode(array_keys($allTags)).';</script>';
} 