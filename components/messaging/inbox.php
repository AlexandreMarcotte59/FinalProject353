<?php
session_start();
include "header.php";
include "./components/db.php"; 


if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
}

$user_id = $_SESSION['user_id'];


$sql = "SELECT m.*, u.username AS sender_name
        FROM messages m
        JOIN users u ON m.sender_id = u.user_id
        WHERE m.recipient_id = ?
        ORDER BY m.sent_datetime DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

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

<?php include "footer.php"; ?>
