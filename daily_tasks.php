<?php
if (!isset($_SESSION['username'])) return;
require 'db.php';
$username = $_SESSION['username'];
$stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
$stmt->execute([$username]);
$user = $stmt->fetch();
if (!$user) {
    echo '<div class="no-notes">Henüz günlük görevin yok!</div>';
    return;
}
$stmt = $pdo->prepare('SELECT id, task, is_done FROM daily_tasks WHERE user_id = ? ORDER BY id DESC');
$stmt->execute([$user['id']]);
$tasks = $stmt->fetchAll();
if (!$tasks) {
    echo '<div class="no-notes">Henüz günlük görevin yok!</div>';
} else {
    echo '<ul class="task-list">';
    foreach ($tasks as $task) {
        echo '<li>';
        echo '<form method="POST" action="task_toggle.php" style="display:inline;">';
        echo '<input type="hidden" name="type" value="daily">';
        echo '<input type="hidden" name="id" value="'.$task['id'].'">';
        echo '<input type="checkbox" name="is_done" onchange="this.form.submit()" '.($task['is_done'] ? 'checked' : '').'>';
        echo '</form> ';
        echo '<span class="'.($task['is_done'] ? 'done-task' : '').'">'.htmlspecialchars($task['task']).'</span>';
        echo '<a href="task_delete.php?type=daily&id='.$task['id'].'" class="delete-btn" onclick="return confirm(\'Silinsin mi?\')">Sil</a>';
        echo '</li>';
    }
    echo '</ul>';
} 