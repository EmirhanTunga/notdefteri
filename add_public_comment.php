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
$comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';
if ($note_id < 1 || $comment === '' || mb_strlen($comment) > 200) exit('Geçersiz istek');
$stmt = $pdo->prepare('INSERT INTO public_comments (note_id, user_id, comment) VALUES (?, ?, ?)');
$stmt->execute([$note_id, $user_id, $comment]);
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) || isset($_POST['ajax']) || strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'json') !== false) {
    header('Content-Type: application/json');
    echo json_encode([
        'success'=>true,
        'username'=>$_SESSION['username'],
        'comment'=>$comment,
        'date'=>date('d M H:i')
    ]);
    exit();
}
header('Location: feed.php');
exit(); 