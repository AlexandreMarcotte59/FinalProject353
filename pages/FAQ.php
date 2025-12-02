<?php
include_once("../database/db.php");

$user_id = $_SESSION['user_id'];

$stmt_query_contents = $pdo->prepare("SELECT * FROM Questions WHERE user_id = ?");
$stmt_query_contents->execute([$user_id]);
$tuples = $stmt_query_contents->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>FAQs</title>

    <link rel="stylesheet" href="../style/main.css">
</head>
<body>
    <?php include("../components/navbar.php"); ?>
    <div class="faq-container">
        <h2>FAQs</h2>
        <?php  foreach ($tuples as $faq):  ?>
            <form action="FAQ.php" method="get" style="display:inline-block;" class="faq">
                <input type="hidden" name="id" value="<?= $faq['question_id'] ?>">
                <label>Question: <?= $faq['question']?></label>
            </form>
        <?php endforeach; ?>        
    </div>
</body>
</html>