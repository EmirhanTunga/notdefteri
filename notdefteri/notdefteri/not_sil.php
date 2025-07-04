<?php
session_start();
require 'db.php';
if (!isset($_SESSION['username']) || !isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}
$username = $_SESSION['username'];
$stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
$stmt->execute([$username]);
$user = $stmt->fetch();
if ($user) {
    $stmt = $pdo->prepare('DELETE FROM notes WHERE id = ? AND user_id = ?');
    $stmt->execute([$_GET['id'], $user['id']]);
}
header('Location: index.php?toast=not_silindi');
exit(); 