<?php
session_start();
require 'db.php';
if (!isset($_SESSION['username']) || !isset($_POST['task']) || !isset($_POST['type'])) {
    header('Location: index.php');
    exit();
}
$username = $_SESSION['username'];
$stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
$stmt->execute([$username]);
$user = $stmt->fetch();
if (!$user) {
    header('Location: index.php');
    exit();
}
$type = $_POST['type'];
$task = trim($_POST['task']);
if ($type === 'daily') {
    $stmt = $pdo->prepare('INSERT INTO daily_tasks (user_id, task) VALUES (?, ?)');
    $stmt->execute([$user['id'], $task]);
} elseif ($type === 'weekly' && isset($_POST['week_start'])) {
    $week_start = $_POST['week_start'];
    $stmt = $pdo->prepare('INSERT INTO weekly_tasks (user_id, task, week_start) VALUES (?, ?, ?)');
    $stmt->execute([$user['id'], $task, $week_start]);
}
header('Location: index.php');
exit(); 