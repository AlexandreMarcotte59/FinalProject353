<?php
include_once('../database/db.php');


if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
}

$user_id = $_SESSION['user_id'];


$sql = "SELECT m.*, u.name_or_username AS sender_name
        FROM InboxMessages m
        JOIN Members u ON m.sender_id = u.user_id
        WHERE m.recipient_id = ?
        ORDER BY m.sent_datetime DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<?php 
include "../components/navbar.php";
include "includes/header.php";
?>
<div class="inbox-container">
    <nav>
        <a href="inbox.php">Inbox</a> |
        <a href="sent_messages.php">Sent</a> |
        <a href="compose_message.php" target="_blank">Compose</a>
    </nav>

    <hr>

    <div class="container">
        <h2>Inbox</h2>

        <table>
            <tr>
                <th>From</th>
                <th>Subject</th>
                <th>Date</th>
                <th>Status</th>
            </tr>

            <?php foreach ($messages as $row): ?>
                <tr onclick="window.open('read_message.php?id=<?= $row['message_id'] ?>','popup','width=600,height=400');">
                    <td><?= htmlspecialchars($row['sender_name']) ?></td>
                    <td><?= htmlspecialchars($row['subject']) ?></td>
                    <td><?= htmlspecialchars($row['sent_datetime']) ?></td>
                    <td><?= $row['is_read'] ? 'Read' : 'Unread' ?></td>
                </tr>
            <?php endforeach; ?>

        </table>
    </div>
    <?php include "footer.php"; ?>
</div>