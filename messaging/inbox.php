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

<?php include "../components/navbar.php"; ?>

<div class="inbox">
    <?php include "includes/header.php"; ?>
    <h2 style="text-align: center;">Inbox</h2>
    <div class="inbox-container">
        <nav>
            <!--<a href="inbox.php">Inbox</a> |-->
            <a onclick="modalHandler('compose_message')" target="_blank">Compose</a>
            <a onclick="modalHandler('sent_messages')" target="_blank">Sent</a>
        </nav>

        <hr>

        <div class="container">
            <table>
                <thead>
                    <th>From</th>
                    <th>Subject</th>
                    <th>Date</th>
                    <th>Status</th>
                </thead>

                <tbody>
                    <?php foreach ($messages as $row): ?>
                        <tr
                            onclick="window.open('read_message.php?id=<?= $row['message_id'] ?>','popup','width=600,height=400');">
                            <td><?= htmlspecialchars($row['sender_name']) ?></td>
                            <td><?= htmlspecialchars($row['subject']) ?></td>
                            <td><?= htmlspecialchars($row['sent_datetime']) ?></td>
                            <td><?= $row['is_read'] ? 'Read' : 'Unread' ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php include "footer.php"; ?>
        </div>
    </div>
    <dialog id="compose_message">
        <button class="closeDialog">X</button>
        <?php include("compose_message.php"); ?>
    </dialog>
    <dialog id="sent_messages">
        <button class="closeDialog">X</button>
        <?php include("sent_messages.php"); ?>
    </dialog>
</div>