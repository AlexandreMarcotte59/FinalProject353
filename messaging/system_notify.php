<?php

function sendSystemMessage($recipient_id, $subject, $body)
{
    require "../database/db.php"; 

    $sql = "INSERT INTO InboxMessages
            (sender_id, recipient_id, subject, body, is_system_message, is_read)
            VALUES (?, ?, ?, ?, 1, 0)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([0, $recipient_id, $subject, $body]);
}

?>
