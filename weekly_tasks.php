<?php
if (!isset($_SESSION['username'])) return;
require 'db.php';
$username = $_SESSION['username'];
$stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
$stmt->execute([$username]);
$user = $stmt->fetch();
if (!$user) {
    echo '<div class="no-notes">Henüz haftalık görevin yok!</div>';
    return;
}
$stmt = $pdo->prepare('SELECT id, task, is_done, week_start FROM weekly_tasks WHERE user_id = ? ORDER BY week_start DESC, id DESC');
$stmt->execute([$user['id']]);
$tasks = $stmt->fetchAll();
$calendarTasks = [];
foreach ($tasks as $task) {
    $calendarTasks[$task['week_start']][] = htmlspecialchars($task['task']);
}
echo '<script>window.weeklyTasksForCalendar = '.json_encode($calendarTasks).';</script>';
if (!$tasks) {
    echo '<div class="no-notes">Henüz haftalık görevin yok!</div>';
} else {
    echo '<ul class="task-list">';
    foreach ($tasks as $task) {
        echo '<li>';
        echo '<form method="POST" action="task_toggle.php" style="display:inline;">';
        echo '<input type="hidden" name="type" value="weekly">';
        echo '<input type="hidden" name="id" value="'.$task['id'].'">';
        echo '<input type="checkbox" name="is_done" onchange="this.form.submit()" '.($task['is_done'] ? 'checked' : '').'>';
        echo '</form> ';
        echo '<span class="'.($task['is_done'] ? 'done-task' : '').'">'.htmlspecialchars($task['task']).' <small>('.htmlspecialchars($task['week_start']).')</small></span>';
        echo '<a href="task_delete.php?type=weekly&id='.$task['id'].'" class="delete-btn" onclick="return confirm(\'Silinsin mi?\')">Sil</a>';
        echo '</li>';
    }
    echo '</ul>';
} 