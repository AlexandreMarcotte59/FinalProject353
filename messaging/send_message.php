<?php
session_start();
include "./components/db.php";

if (!isset($_SESSION['user_id'])) {
    die("Not logged in.");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request.");
}

$sender = $_SESSION['user_id'];
$recipient = $_POST['recipient_id'];
$subject = trim($_POST['subject']);
$body = trim($_POST['body']);


if (strlen($body) > 2048) {
    die("Message exceeds 2048 character limit.");
}

$sql = "INSERT INTO messages (sender_id, recipient_id, subject, body)
        VALUES (?, ?, ?, ?)";

$stmt = $pdo->prepare($sql);
$stmt->execute([$sender, $recipient, $subject, $body]);

echo "<script>alert('Message Sent!'); window.close();</script>";
exit;
?>
