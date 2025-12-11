<?php
include_once("../database/db.php");

$question_id = $_POST['question_id'];

$q_text = null;
if (!isset($_POST['question'])){
    $stmt_q = $pdo->prepare("SELECT question FROM Questions WHERE question_id = ?");
    $stmt_q->execute([$question_id]);
    $q_text = $stmt_q->fetch(PDO::FETCH_ASSOC);
}
$question = $_POST['question'] ?? $q_text['question'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Question Forum</title>
    <link rel="stylesheet" href="../style/main.css">
</head>

<body>
    <?php include("../components/navbar.php"); ?>
    <div class="data-container" style="height:100%;flex-direction:column!important;justify-content:flex-start;align-content:center;">
        <div class="answer-input" style="align-content:center;">
            <h1 onclick="window.location.href='../pages/FAQ.php'" style="cursor:pointer;"><?= $question ?></h1>
            <form method="POST" action="" class="bar">
                <input type="hidden" name="question_id" value="<?= $question_id ?>" />
                <textarea id="answer_text" cols="50" rows="2" name="answer" placeholder="Add answer here..."></textarea>
                <button type="button" id="post_answer" style="color:transparent;text-shadow: 0 0 0 salmon;">➕</button>
            </form>
        </div>
        <div class="answerbox" id="answerbox">
            
        </div>
    </div>
</body>

</html>

    <script>
    const q = <?=$question_id?>;
    function loadAnswers() {
        fetch('../components/load_answers.php?question_id=' + encodeURIComponent(q))
            .then(res => res.text())
            .then(html => {
                document.getElementById('answerbox').innerHTML = html;
            });
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('post_answer').addEventListener('click', () => {
            const answer = document.getElementById('answer_text').value.trim();
            if (!answer) return alert('Answer cannot be empty.');

            fetch('../components/post_answer.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'question_id=' + q + '&answer=' + encodeURIComponent(answer)
            })
            .then(res => res.text())
            .then(text => {
                console.log("SERVER RESPONDED:", text);
                document.getElementById('answer_text').value = '';
                loadAnswers();
            });
        });
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains("upvote")) {
                const answerDiv = e.target.closest(".answer");
                const a = answerDiv.dataset.id;
                fetch('../components/update_answer.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'upvote=1&answer_id=' + a
                })
                .then(res => res.text())
                .then(text => {
                    console.log("Server:", text);
                    loadAnswers();
                });
            }
        });
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains("downvote")) {
                    const answerDiv = e.target.closest(".answer");
                    const a = answerDiv.dataset.id;
                fetch('../components/update_answer.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'downvote=1&answer_id=' + a
                })
                .then(res => res.text())
                .then(text => {
                    console.log("Server:", text);
                    loadAnswers();
                });
            }
        });
    });

    loadAnswers();
    </script>