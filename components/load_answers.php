<?php
include_once("../database/db.php");

$question_id = $_GET['question_id'] ?? 0;

$q_stmt = $pdo->prepare("
    SELECT * 
    FROM Answers a 
    WHERE a.question_id = ?
");
$q_stmt->execute([$question_id]);
$answers = $q_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php if ($answers): ?>
    <?php foreach ($answers as $answer): ?>
        <div class="cardlist" id='answer<?= $answer['answer_id'] ?>' style="width:100% !important;">
            <input type="hidden" name="answer_id" value="<?= $answer['answer_id'] ?>" />            
            <div style="display:flex;flex-direction:column;" class="answer" data-id="<?= $answer['answer_id'] ?>" >
                <p><?= htmlspecialchars($answer["answer"]) ?></p>
            <?php if ($_SESSION['is_admin']): ?>                        
                <sup style="white-space:nowrap;"><button class="upvote">👍</button> <?= htmlspecialchars($answer["upvotes"]); ?> <button class="downvote">👎</button> <?= htmlspecialchars($answer["downvotes"]); ?></sup>
            <?php else: ?>                        
                <sup>👍 <?= htmlspecialchars($answer["upvotes"]); ?> 👎 <?= htmlspecialchars($answer["downvotes"]); ?></sup>
            <?php endif ?>
            </div>
        </div>
    <?php endforeach ?>

<?php else: ?>
    <?= "No answers yet." ?>
<?php endif ?>
