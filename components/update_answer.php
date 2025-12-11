<?php
include_once("../database/db.php");
error_log("User ID: " . print_r($_SESSION, true));

$answer_id = $_POST['answer_id'] ?? 0;
$downvote = trim($_POST['downvote'] ?? 0);
$upvote = trim($_POST['upvote'] ?? 0);

if ($downvote == 1) {    
    
    error_log("Action: " . print_r($downvote, true));
    $insert = $pdo->prepare("CALL vote_down(?)");
    $insert->execute([$answer_id]);
    exit;
}

if ($upvote == 1) {    
    $insert = $pdo->prepare("UPDATE Answers SET upvotes=upvotes+1 WHERE answer_id = ?");
    $insert->execute([$answer_id]);
    exit;
}


http_response_code(400);
echo 'Comment cannot be empty.';
exit;
?>