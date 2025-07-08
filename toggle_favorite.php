<?php
session_start();
require 'db.php';
if (!isset($_SESSION['username']) || !isset($_GET['id'])) exit();
$username = $_SESSION['username'];
$noteId = intval($_GET['id']);
$stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
$stmt->execute([$username]);
$user = $stmt->fetch();
if ($user) {
    $stmt = $pdo->prepare('SELECT is_favorite FROM notes WHERE id = ? AND user_id = ?');
    $stmt->execute([$noteId, $user['id']]);
    $note = $stmt->fetch();
    if ($note) {
        $newFav = $note['is_favorite'] ? 0 : 1;
        $stmt = $pdo->prepare('UPDATE notes SET is_favorite = ? WHERE id = ? AND user_id = ?');
        $stmt->execute([$newFav, $noteId, $user['id']]);
        if (isset($_GET['redirect'])) {
            $msg = $newFav ? 'favori_eklendi' : 'favori_silindi';
            header('Location: index.php?toast='.$msg);
            exit();
        }
    }
} 