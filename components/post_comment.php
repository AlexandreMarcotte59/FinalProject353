<?php
include_once("../database/db.php");

$user_id = $_SESSION['user_id'] ?? null;
$text_id = $_POST['text_id'] ?? 0;
$comment = trim($_POST['comment'] ?? '');

if (!$user_id) {
            http_response_code(403);
                echo 'You must be logged in.';
                exit;
}
if (!$text_id || $comment === '') {
            http_response_code(400);
                echo 'Missing text_id or empty comment';
                exit;
}

$stmt = $pdo->prepare("INSERT INTO TextComments (reader_id, text_id, comment)  VALUES (?, ?, ?) ");
$result = $stmt->execute([$user_id, $text_id, $comment]);
if ($result) {
    echo 'Comment posted';
    } else {
        $errorInfo = $stmt->errorInfo();
    echo "DB error: " . $errorInfo[2];
}
?>