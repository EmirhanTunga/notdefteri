<?php
session_start();
require 'db.php';
if (!isset($_SESSION['username']) || !isset($_POST['id']) || !isset($_POST['note'])) {
    header('Location: index.php');
    exit();
}
$username = $_SESSION['username'];
$stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
$stmt->execute([$username]);
$user = $stmt->fetch();
if ($user) {
    $stmt = $pdo->prepare('UPDATE notes SET note = ? WHERE id = ? AND user_id = ?');
    $stmt->execute([$_POST['note'], $_POST['id'], $user['id']]);
}
header('Location: index.php?toast=not_guncellendi');
exit(); 