<?php
session_start();
require 'db.php';
header('Content-Type: application/json');
if (!isset($_SESSION['username'])) {
    echo json_encode([]);
    exit();
}
// Kullanıcı id'sini bul
$stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
$stmt->execute([$_SESSION['username']]);
$user = $stmt->fetch();
$user_id = $user ? $user['id'] : 0;
$events = [];
// Notlar
$stmt = $pdo->prepare('SELECT id, note, created_at FROM notes WHERE user_id = ?');
$stmt->execute([$user_id]);
foreach ($stmt->fetchAll() as $n) {
    $events[] = [
        'title' => '📝 ' . (mb_strlen($n['note']) > 20 ? mb_substr($n['note'],0,20).'...' : $n['note']),
        'start' => date('Y-m-d', strtotime($n['created_at'])),
        'color' => '#fda085',
        'url' => null
    ];
}
// Görevler (örnek: daily_tasks ve weekly_tasks dosyalarına göre)
$stmt = $pdo->prepare('SELECT id, task, date, type, completed FROM tasks WHERE user_id = ?');
if ($stmt->execute([$user_id])) {
    foreach ($stmt->fetchAll() as $t) {
        $events[] = [
            'title' => ($t['completed'] ? '✅ ' : '🔔 ') . (mb_strlen($t['task']) > 20 ? mb_substr($t['task'],0,20).'...' : $t['task']),
            'start' => $t['date'],
            'color' => $t['completed'] ? '#43a047' : '#90caf9',
            'url' => null
        ];
    }
}
echo json_encode($events); 