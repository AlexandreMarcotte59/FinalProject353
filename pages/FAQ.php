<?php
include_once("../database/db.php");

$user_id = $_SESSION['user_id'];

$stmt_query_contents = $pdo->prepare("SELECT * FROM Questions WHERE flagged = 0 ORDER BY question");
$stmt_query_contents->execute();
$tuples = $stmt_query_contents->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['search']) && !empty(trim($_POST['search']))) {
    $sql_author_query = $pdo->prepare("SELECT * FROM Questions WHERE flagged = 0 AND question LIKE CONCAT('%', ? ,'%') ORDER BY question");
    $sql_author_query->execute([$_POST['search']]);
    $search_result = $sql_author_query->fetchAll(PDO::FETCH_ASSOC);
    if (empty($search_result)) {
        $search_result_msg = "No relevant questions found.";
    }
}

if (isset($_POST['new_question']) && !empty(trim($_POST['new_question']))) {
    $q = trim($_POST['new_question']);
    $stmt_dup_q = $pdo->prepare("SELECT * FROM Questions WHERE question LIKE ?");
    $stmt_dup_q->execute([$q]);
    $is_dup_q = $stmt_dup_q->fetchAll(PDO::FETCH_ASSOC);
    if(empty($is_dup_q)) {
        $sql_author_query = $pdo->prepare("INSERT INTO Questions (user_id, question) VALUES (?, ?)");
        $sql_author_query->execute([$user_id, $q]);
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    } else {
        echo "<script>alert('Question already exists.');window.location.href = '{$_SERVER['REQUEST_URI']}';</script>";
        exit;     
    }
}

if (isset($_POST['question_id'])) {
    $stmt = $pdo->prepare("UPDATE Questions SET flagged = 1 WHERE question_id = ?");
    $stmt->execute([$_POST['question_id']]);
    echo json_encode(['success' => true, 'deleted_id' => $_POST['question_id']]);
    exit;
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
    
    <div class="main-container">
    <form class="search-form" method="POST" action="">
        <h1>FAQs</h1>
            <div class="bar">
                <input type="search" name="search" value="<?= isset($_POST['search']) ? $_POST['search'] : '' ?>"
                    placeholder='Search for a question...' />
                <button type="submit"style="color:transparent;text-shadow: 0 0 0 white;">🔍︎</button>
            </div>
        <div class="faq-input bar" style="margin-top: 2%;">
            <form class="search-form" method="POST" action="" style="display:flex; flex-direction: row;">
                <input type="text" name="new_question" placeholder="Post a new question..."  id="question_text" />
                <button type="submit" style="color:transparent;text-shadow: 0 0 0 white;">➕︎</button>
            </form>
        </div>
    </form>
    <div class="faq-container">
        
        <?php if (isset($_POST['search']) && !empty($search_result)): ?>

            <div class="card-container">
                <?php foreach ($search_result as $res): ?>
                    <div class="cardlist rmenu-target" onclick="this.querySelector('form').submit()" data-id="<?= $res['question_id'] ?>">
                        <form method="POST" action="QuestionForum.php">
                            <input value="<?= $res["question_id"] ?>" type="hidden" name="question_id" >
                            <input value="<?= $res["question"] ?>" type="hidden" name="question" >
                            <h3><?= $res["question"] ?></h3>
                            <p>By: <?= $res["user_id"] ?></p>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <?php  foreach ($tuples as $faq):  ?>                
                <div class="cardlist rmenu-target" onclick="this.querySelector('form').submit()" data-id="<?= $faq['question_id'] ?>">
                    <form action="QuestionForum.php" method="POST" class="faq">
                        <input type="hidden" name="question_id" value="<?= $faq['question_id'] ?>">
                        <input type="hidden" name="question" value="<?= $faq['question'] ?>">
                        <label><?= $faq['question']?></label>
                        <p>By: <?= $faq["user_id"] ?></p>
                    </form>
                </div>
            <?php endforeach; ?>   
        <?php endif ?>     
    </div>
    
    <div class="hide" id="rmenu" hidden>
        <button class="btn" id="delete-btn" type="button">Delete?</button>
    </div>
</div>
</body>
</html>
<?php if($_SESSION['is_admin']): ?>
<script>
const menu = document.getElementById("rmenu");
const targets = document.querySelectorAll(".rmenu-target");
let currentTarget = null;
document.addEventListener("click", () => menu.hidden = true);

document.addEventListener("contextmenu", function(e) {
    const target = e.target.closest(".rmenu-target");
    if (!target) return; 
    e.preventDefault();

    currentTarget = target;
    menu.hidden = false;
    menu.style.position = "absolute";
    menu.style.top = e.pageY + "px";
    menu.style.left = e.pageX + "px";
});
document.getElementById("delete-btn").addEventListener("click", () => {
    if (!currentTarget) return;
    const id = currentTarget.dataset.id;
    alert("Deleting element with data-id: " + id);
    
    currentTarget.remove();
    
    fetch('FAQ.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'question_id=' + encodeURIComponent(id)
    })
    .then(res => res.text())
    .then(data => console.log('Server response:', data))
    .catch(err => console.error(err));

    menu.hidden = true;
});
</script>
<?php endif ?>