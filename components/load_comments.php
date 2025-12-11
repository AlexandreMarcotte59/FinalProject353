<?php
include_once("../database/db.php");

$user_id = $_SESSION['user_id'] ?? null;
$text_id = $_GET['text_id'] ?? 0;

$is_member_author = $pdo->prepare("
    SELECT is_member_author 
    FROM Texts t 
    WHERE t.text_id = ?
");
$is_member_author->execute([$text_id]);
$is_text_uploader_author = $is_member_author->fetchColumn();



$commentsStmt = $pdo->prepare("
    SELECT tc.*, m.name_or_username 
    FROM TextComments tc
    LEFT JOIN Members m ON tc.reader_id = m.user_id
    WHERE tc.text_id = ?
    ORDER BY tc.date_published ASC
");
$commentsStmt->execute([$text_id]);
$comments = $commentsStmt->fetchAll();
?>

<?php if ($comments): ?>
    <?php foreach ($comments as $msg): ?>
        <div class="chatbubble" id='comment<?= $msg['comment_id'] ?>'>
            <div style="display:flex;flex-direction:column;">
                <p><?= htmlspecialchars($msg["comment"]) . $msg['comment_id']; ?></p>
                <sup>date published: <?= htmlspecialchars($msg["date_published"]); ?></sup>
            </div>
            <?php if ($msg['reader_id'] == $_SESSION['user_id'] || $_SESSION['is_admin']): ?>
                <form method="POST" action="" id='form<?= $msg['comment_id'] ?>' onsubmit="return confirm('Are you sure?')">
                    <input type="hidden" name="id" value="<?= $msg['text_id'] ?>">
                    <input type="hidden" name="comment_id" value="<?= $msg['comment_id'] ?>">
                    <button type="submit">X</button>
                </form>
            <?php endif ?>
            <?php if ($is_text_uploader_author): ?>
                <form><button onclick="replyChat()">Reply</button></form>
            <?php endif ?>
        </div>
    <?php endforeach ?>
<?php endif ?>
