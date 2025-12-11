<?php
include_once("../database/db.php");

$user_id = $_SESSION['user_id'];
$stmt_query_contents = $pdo->prepare("SELECT * FROM InboxMessages WHERE recipient_id = ?");
$stmt_query_contents->execute([$user_id]);
$tuples = $stmt_query_contents->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Email</title>

    <link rel="stylesheet" href="../style/main.css">
</head>
<body>
        <?php include("../components/navbar.php"); ?>
    <div class="inbox-container" style="margin: var(--navHeight); padding: 10vw; width: 60%; height: 60%; border-radius: 8px; background-color: white;">
        <?php if (empty($tuples)): ?>
                <p style="text-align: center;"> No Messages </p>
        <?php endif; ?>
        <?php  foreach ($tuples as $msg): ?>
            <form action="MyEmail.php" method="get" style="display:inline-block;" class="mailmessage">
                <input type="hidden" name="text_id" value="<?= $text['text_id'] ?>">
                <label>Subject: <?= $msg['subject']?></label>
                <label>Body: <?= $msg['body']?></label>
                <label>Date: <?= $msg['sent_datetime']?></label>
            </form>
        <?php endforeach; ?>
    </div>
</body>
</html>