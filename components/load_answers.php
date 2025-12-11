<?php
include_once("../database/db.php");

$question_id = $_GET['question_id'] ?? 0;

$q_stmt = $pdo->prepare("
    SELECT * 
    FROM Answers a 
    WHERE a.question_id = ?
    ORDER BY upvotes DESC
");
$q_stmt->execute([$question_id]);
$answers = $q_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php if ($answers): ?>
    <?php foreach ($answers as $answer): ?>
        <div class="cardlist" id='answer<?= $answer['answer_id'] ?>' style="width:100% !important;">
            <input type="hidden" name="answer_id" value="<?= $answer['answer_id'] ?>" />            
            <?php 
            $stmt = $pdo->prepare("SELECT m.name_or_username FROM Answers a JOIN Members m ON a.user_id = m.user_id WHERE a.answer_id = ? ");
            $stmt->execute([$answer['answer_id']]);
            $name = $stmt->fetch(PDO::FETCH_ASSOC); ?>
            <div style="display:flex;flex-direction:column;" class="answer" data-id="<?= $answer['answer_id'] ?>" >
                <p><?= htmlspecialchars($answer["answer"]) ?></p>
            <?php if ($_SESSION['is_admin']): ?>                        
                <sup class="ans-stats">
                <span class="likes">
                <button class="upvote">👍</button> <?= htmlspecialchars($answer["upvotes"] ?? 0); ?>
                <button class="downvote">👎</button> <?= htmlspecialchars($answer["downvotes"] ?? 0); ?>
                </span>
                <span>From: <?= htmlspecialchars($name['name_or_username']) ?></span></sup>
            <?php else: ?>                        
                <sup class="ans-stats">
                <span class="likes">👍 <?= htmlspecialchars($answer["upvotes"]); ?> 👎 <?= htmlspecialchars($answer["downvotes"]); ?></span>
                <span>From: <?= htmlspecialchars($name['name_or_username']) ?></span></sup>
            <?php endif ?>
            </div>
        </div>
    <?php endforeach ?>

<?php else: ?>
    <?= '<p style="text-align:center;">No answers yet.</p>' ?>
<?php endif ?>
