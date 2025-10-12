<?php
// Test script to check database tables
session_start();
require_once 'db.php';

echo "<h2>Database Table Check</h2>";

// Check daily_tasks table structure
echo "<h3>Daily Tasks Table:</h3>";
$stmt = $pdo->query("DESCRIBE daily_tasks");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
foreach ($columns as $column) {
    echo $column['Field'] . " - " . $column['Type'] . " - " . $column['Default'] . "\n";
}
echo "</pre>";

// Check weekly_tasks table structure
echo "<h3>Weekly Tasks Table:</h3>";
$stmt = $pdo->query("DESCRIBE weekly_tasks");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
foreach ($columns as $column) {
    echo $column['Field'] . " - " . $column['Type'] . " - " . $column['Default'] . "\n";
}
echo "</pre>";

// Check if monthly_tasks exists
echo "<h3>Monthly Tasks Table:</h3>";
try {
    $stmt = $pdo->query("DESCRIBE monthly_tasks");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>";
    foreach ($columns as $column) {
        echo $column['Field'] . " - " . $column['Type'] . " - " . $column['Default'] . "\n";
    }
    echo "</pre>";
} catch (Exception $e) {
    echo "Monthly tasks table does not exist: " . $e->getMessage();
}

// Test insert
echo "<h3>Test Insert:</h3>";
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user) {
        echo "User ID: " . $user['id'] . "<br>";
        
        // Try to insert a test task
        try {
            $stmt = $pdo->prepare('INSERT INTO daily_tasks (user_id, task, description, priority, duration_minutes, due_date) VALUES (?, ?, ?, ?, ?, ?)');
            $result = $stmt->execute([$user['id'], 'Test Task', 'Test Description', 'medium', 60, '2024-01-01 18:00:00']);
            
            if ($result) {
                echo "✅ Test insert successful!<br>";
                // Delete the test task
                $stmt = $pdo->prepare('DELETE FROM daily_tasks WHERE user_id = ? AND task = ?');
                $stmt->execute([$user['id'], 'Test Task']);
                echo "✅ Test task deleted.<br>";
            } else {
                echo "❌ Test insert failed.<br>";
            }
        } catch (Exception $e) {
            echo "❌ Test insert error: " . $e->getMessage() . "<br>";
        }
    } else {
        echo "❌ User not found.<br>";
    }
} else {
    echo "❌ No session username.<br>";
}
?>
