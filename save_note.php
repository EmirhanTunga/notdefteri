<?php
session_start();
require 'db.php';
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['note'])
) {
    $username = $_SESSION['username'];
    $note = trim($_POST['note']);
    $color = $_POST['color'] ?? 'yellow';
    $tags = trim($_POST['tags'] ?? '');
    $file_path = null;
    // Dosya yükleme işlemi
    if (isset($_FILES['note_file']) && $_FILES['note_file']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = [
            'image/jpeg', 'image/png', 'image/gif', 'image/webp',
            'application/pdf',
            'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain'
        ];
        $file_type = $_FILES['note_file']['type'];
        if (in_array($file_type, $allowed_types)) {
            $uploads_dir = __DIR__ . '/uploads';
            if (!is_dir($uploads_dir)) {
                mkdir($uploads_dir, 0777, true);
            }
            $filename = uniqid('note_') . '_' . basename($_FILES['note_file']['name']);
            $target_path = $uploads_dir . '/' . $filename;
            if (move_uploaded_file($_FILES['note_file']['tmp_name'], $target_path)) {
                $file_path = 'uploads/' . $filename;
            }
        }
    }
    if ($note !== '') {
        // Kullanıcı id'sini bul
        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        if ($user) {
            $stmt = $pdo->prepare('INSERT INTO notes (user_id, note, color, tags, file_path) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$user['id'], $note, $color, $tags, $file_path]);
            if (isset($_POST['save_and_share'])) {
                // Sosyal akışa da ekle
                $stmt2 = $pdo->prepare('INSERT INTO public_notes (user_id, content, file_path) VALUES (?, ?, ?)');
                $stmt2->execute([$user['id'], $note, $file_path]);
            }
        }
    }
}
header('Location: index.php?toast=not_eklendi');
exit(); 