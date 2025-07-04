<?php
session_start();
require 'db.php';
if (!isset($_SESSION['username']) || !isset($_GET['id']) || !isset($_GET['type'])) {
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
$type = $_GET['type'];
$id = $_GET['id'];
if ($type === 'daily') {
    $stmt = $pdo->prepare('DELETE FROM daily_tasks WHERE id = ? AND user_id = ?');
    $stmt->execute([$id, $user['id']]);
} elseif ($type === 'weekly') {
    $stmt = $pdo->prepare('DELETE FROM weekly_tasks WHERE id = ? AND user_id = ?');
    $stmt->execute([$id, $user['id']]);
}
header('Location: index.php');
exit(); 