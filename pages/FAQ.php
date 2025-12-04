<?php
include_once("../database/db.php");

$user_id = $_SESSION['user_id'];

$stmt_query_contents = $pdo->prepare("SELECT * FROM Questions WHERE user_id = ?");
$stmt_query_contents->execute([$user_id]);
$tuples = $stmt_query_contents->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['search'])) {
    $sql_author_query = $pdo->prepare("SELECT * FROM Questions WHERE question LIKE CONCAT('%', ? ,'%') ORDER BY question");
    $sql_author_query->execute([$_POST['search']]);
    $search_result = $sql_author_query->fetchAll(PDO::FETCH_ASSOC);
    if (empty($search_result)) {
        $search_result_msg = "No relevant authors found.";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>FAQs</title>

    <link rel="stylesheet" href="../style/main.css">
</head>

<body>
    <?php include("../components/navbar.php"); ?>
    <form class="search-form" method="POST" action="Home.php">
            <div class="bar">
                <input type="search" name="search" value="<?= isset($_POST['search']) ? $_POST['search'] : '' ?>"
                    placeholder='Enter query...' />
                <select name="filter">
                    <option></option>
                    <option value="title">Title</option>
                    <option value="author">Author</option>
                </select>
                <button type="submit">Search</button>
            </div>
    </form>


    <div class="faq-container">
        <h2>FAQs</h2>
        
        <?php if (isset($_POST['search']) && !empty($search_result)): ?>

            <div class="card-container">
                <?php foreach ($search_result as $res): ?>
                    <div class="cardlist" onclick="submit(this)">
                        <form method="POST" action="pages/QuestionForum.php">
                            <input value="<?= $res["question_id"] ?>" type="hidden">
                            <h3><?= $res["question"] ?></h3>
                            <p>Author: <?= $res["user_id"] ?></p>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <?php  foreach ($tuples as $faq):  ?>                
                <div class="cardlist" onclick="submit(this)">
                    <form action="pages/QuestionForum.php" method="POST" style="display:inline-block;" class="faq">
                        <input type="hidden" name="id" value="<?= $faq['question_id'] ?>">
                        <label>Question: <?= $faq['question']?></label>
                        <p>Author: <?= $res["user_id"] ?></p>
                    </form>
                </div>
            <?php endforeach; ?>   
        <?php endif ?>     
    </div>
</body>
</html>