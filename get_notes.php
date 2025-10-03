<?php
session_start();
header('Content-Type: application/json');

// Kullanıcı giriş kontrolü
if (!isset($_SESSION['username'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

require_once 'db.php';

$userId = $_SESSION['user_id'] ?? null;

// Get user ID if not in session
if (!$userId) {
    $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
    $stmt->execute([$_SESSION['username']]);
    $user = $stmt->fetch();
    $userId = $user['id'] ?? null;
    $_SESSION['user_id'] = $userId;
}

if (!$userId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'User not found']);
    exit();
}

// Get pagination parameters
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = min(50, max(1, (int)($_GET['limit'] ?? 20)));
$offset = ($page - 1) * $limit;

try {
    // Check if requesting favorites only
    $favoritesOnly = isset($_GET['favorites']) && $_GET['favorites'] == '1';
    
    // Get total count
    $whereClause = $favoritesOnly ? 'WHERE user_id = ? AND is_favorite = 1' : 'WHERE user_id = ?';
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM notes $whereClause");
    $countStmt->execute([$userId]);
    $totalCount = (int)$countStmt->fetchColumn();
    
    // Check if requesting single note
    $noteId = $_GET['id'] ?? null;
    
    if ($noteId) {
        // Get single note for editing
        $stmt = $pdo->prepare('
            SELECT id, title, content, is_public, created_at, updated_at
            FROM notes 
            WHERE id = ? AND user_id = ?
        ');
        $stmt->execute([$noteId, $userId]);
        $note = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($note) {
            echo json_encode(['success' => true, 'note' => $note]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Note not found']);
        }
        exit();
    }
    
    // Get notes with pagination
    $stmt = $pdo->prepare("
        SELECT id, title, content, created_at, updated_at, is_public, is_favorite
        FROM notes 
        $whereClause
        ORDER BY created_at DESC 
        LIMIT ? OFFSET ?
    ");
    $stmt->execute([$userId, $limit, $offset]);
    $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate pagination info
    $totalPages = ceil($totalCount / $limit);
    $hasMore = $page < $totalPages;
    
    echo json_encode([
        'success' => true,
        'notes' => $notes,
        'pagination' => [
            'page' => $page,
            'limit' => $limit,
            'total' => $totalCount,
            'totalPages' => $totalPages,
            'hasMore' => $hasMore
        ]
    ]);
    
} catch (Exception $e) {
    error_log('Get notes error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database error']);
}
?>