<?php
include_once("../database/db.php");
error_log("User ID: " . print_r($_SESSION, true));
$user_id = $_SESSION['user_id'] ?? null;
$question_id = $_POST['question_id'] ?? 0;
$answer = trim($_POST['answer'] ?? '');

if (!$user_id) {
    http_response_code(403);
    echo 'You must be logged in.';
    exit;
}

if ($answer === '') {
    http_response_code(400);
    echo 'Comment cannot be empty.';
    exit;
}

$insert = $pdo->prepare("INSERT INTO Answers (user_id, question_id, answer) VALUES (?, ?, ?)");
$result = $insert->execute([$user_id, $question_id, $answer]);
if ($result) {
    echo 'Comment posted';
    } else {
        $errorInfo = $stmt->errorInfo();
    echo "DB error: " . $errorInfo[2];
}
?>