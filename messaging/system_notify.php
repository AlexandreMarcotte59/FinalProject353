<?php

function sendSystemMessage($recipient_id, $subject, $body)
{
    require "./components/db.php"; 

    $sql = "INSERT INTO messages
            (sender_id, recipient_id, subject, body, is_system_message, is_read)
            VALUES (?, ?, ?, ?, 1, 0)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([0, $recipient_id, $subject, $body]);
}

?>
