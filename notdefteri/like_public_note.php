<?php
session_start();
require 'db.php';
if (!isset($_SESSION['username'])) {
    http_response_code(403);
    exit('Giriş gerekli');
}
// Kullanıcı id'sini bul
$stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
$stmt->execute([$_SESSION['username']]);
$user = $stmt->fetch();
$user_id = $user ? $user['id'] : 0;
$note_id = isset($_POST['note_id']) ? (int)$_POST['note_id'] : 0;
if ($note_id < 1) exit('Geçersiz istek');
// Daha önce beğenmiş mi?
$stmt = $pdo->prepare('SELECT id FROM public_likes WHERE user_id = ? AND note_id = ?');
$stmt->execute([$user_id, $note_id]);
$liked = $stmt->fetch();
if ($liked) {
    // Beğeniyi geri al
    $pdo->prepare('DELETE FROM public_likes WHERE id = ?')->execute([$liked['id']]);
    $pdo->prepare('UPDATE public_notes SET like_count = like_count - 1 WHERE id = ? AND like_count > 0')->execute([$note_id]);
    $likedNow = false;
} else {
    // Beğen
    $pdo->prepare('INSERT INTO public_likes (user_id, note_id) VALUES (?, ?)')->execute([$user_id, $note_id]);
    $pdo->prepare('UPDATE public_notes SET like_count = like_count + 1 WHERE id = ?')->execute([$note_id]);
    $likedNow = true;
}
// Beğeni sayısını çek
$stmt = $pdo->prepare('SELECT like_count FROM public_notes WHERE id = ?');
$stmt->execute([$note_id]);
$like_count = (int)($stmt->fetchColumn() ?: 0);
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) || isset($_POST['ajax']) || strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'json') !== false) {
    header('Content-Type: application/json');
    echo json_encode(['success'=>true, 'liked'=>$likedNow, 'like_count'=>$like_count]);
    exit();
}
header('Location: feed.php');
exit(); 