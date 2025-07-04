<?php
session_start();
require 'db.php';
if (!isset($_SESSION['username']) || !isset($_POST['avatar'])) {
    header('Location: index.php');
    exit();
}
$avatar = $_POST['avatar'];
$allowed = ['cat','dog','panda','rabbit','bear','fox','koala','tiger','monkey','penguin'];
if (!in_array($avatar, $allowed)) {
    $avatar = 'cat';
}
$stmt = $pdo->prepare('UPDATE users SET avatar = ? WHERE username = ?');
$stmt->execute([$avatar, $_SESSION['username']]);
header('Location: index.php');
exit(); 