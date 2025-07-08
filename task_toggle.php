<?php
session_start();
require 'db.php';
if (!isset($_SESSION['username']) || !isset($_POST['id']) || !isset($_POST['type'])) {
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
$id = $_POST['id'];
$is_done = isset($_POST['is_done']) ? 1 : 0;
if ($type === 'daily') {
    $stmt = $pdo->prepare('UPDATE daily_tasks SET is_done = ? WHERE id = ? AND user_id = ?');
    $stmt->execute([$is_done, $id, $user['id']]);
} elseif ($type === 'weekly') {
    $stmt = $pdo->prepare('UPDATE weekly_tasks SET is_done = ? WHERE id = ? AND user_id = ?');
    $stmt->execute([$is_done, $id, $user['id']]);
}
header('Location: index.php');
exit(); 