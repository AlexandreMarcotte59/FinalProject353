<?php
include "../database/db.php";

if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
}

$user_id = $_SESSION['user_id'];


$sql = "SELECT m.*, u.username AS recipient_name
        FROM messages m
        JOIN users u ON m.recipient_id = u.user_id
        WHERE m.sender_id = ?
        ORDER BY m.sent_datetime DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<?php 
include "../components/navbar.php";
include "includes/header.php";
?>
<h2>Sent Messages</h2>

<table>
<tr>
    <th>To</th>
    <th>Subject</th>
    <th>Date</th>
</tr>

<?php foreach ($messages as $row): ?>
<tr onclick="window.open('read_message.php?id=<?= $row['message_id'] ?>','popup','width=600,height=400');">
    <td><?= htmlspecialchars($row['recipient_name']) ?></td>
    <td><?= htmlspecialchars($row['subject']) ?></td>
    <td><?= htmlspecialchars($row['sent_datetime']) ?></td>
</tr>
<?php endforeach; ?>

</table>

<?php include "footer.php"; ?>
