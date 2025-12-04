<?php
include "../database/db.php";


if (!isset($_SESSION['user_id'])) {
    die("Access denied.");
}

$current_user = $_SESSION['user_id'];


if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    die("Invalid message ID.");
}

$id = (int)$_GET['id'];


$sql = "SELECT m.*, 
        s.username AS sender_name, 
        r.username AS recipient_name
        FROM messages m
        JOIN users s ON m.sender_id = s.user_id
        JOIN users r ON m.recipient_id = r.user_id
        WHERE m.message_id = ?
        AND (m.sender_id = ? OR m.recipient_id = ?)";

$stmt = $pdo->prepare($sql);
$stmt->execute([$id, $current_user, $current_user]);
$message = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$message) {
    die("Message not found or you do not have permission to view it.");
}


if ($message['recipient_id'] == $current_user) {
    $update = $pdo->prepare("UPDATE messages SET is_read = 1 WHERE message_id = ?");
    $update->execute([$id]);
}
?>

<h2><?= htmlspecialchars($message['subject']) ?></h2>

<p><strong>From:</strong> <?= htmlspecialchars($message['sender_name']) ?></p>
<p><strong>To:</strong> <?= htmlspecialchars($message['recipient_name']) ?></p>
<p><strong>Date:</strong> <?= htmlspecialchars($message['sent_datetime']) ?></p>

<hr>

<p><?= nl2br(htmlspecialchars($message['body'])) ?></p>

<br>
<button onclick="window.close()">Close</button>
