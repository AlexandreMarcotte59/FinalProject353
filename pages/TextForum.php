<?php
include_once("../database/db.php");

$t_id = $_POST['id'] || $_GET['id'];
$stmt_query_contents = $pdo->prepare("SELECT * FROM Texts WHERE text_id = ?");
$stmt_query_contents->execute([$t_id]);
$selected_text = $stmt_query_contents->fetch(PDO::FETCH_ASSOC);
if ($selected_text && !empty($selected_text["filename"])) {
    $filepath = "/uploads/" . $selected_text["filename"];
    $file = $ROOTPATH . $filepath;
    $ext = strtolower(pathinfo($selected_text["filename"], PATHINFO_EXTENSION));
}


$stmt_text_chats = $pdo->prepare("SELECT * FROM TextComments WHERE text_id = ?");
$stmt_text_chats->execute([$t_id]);
$chats = $stmt_text_chats->fetch(PDO::FETCH_ASSOC);
if (isset($_POST["comment"]) && !empty(trim($_POST["comment"]))) {
    $stmt_new_chat = $pdo->prepare(
        "INSERT INTO TextComments (reader_id, text_id, comment, date_published) VALUES (?, ?, ?, NOW())"
    );
    $stmt_new_chat->execute([$_SESSION["user_id"], $t_id, $_POST["comment"]]);
    unset($_POST['comment']);

}
if (isset($_POST['comment_id'])) {
    $stmt = $pdo->prepare("DELETE FROM TextComments WHERE comment_id = ?");
    $stmt->execute([$_POST['comment_id']]);
    unset($_POST['comment_id']);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Text Forum</title>
    <script>
        window.addEventListener('load', () => {
            let hidden = JSON.parse(localStorage.getItem('hiddenComments') || '[]');
            hidden.forEach(id => {
                let chat = document.getElementById('comment' + id);
                if (chat) chat.classList.add('hidden');
            });
        });


    </script>
    <link rel="stylesheet" href="../style/main.css">
</head>

<body>
    <?php include("../components/navbar.php"); ?>
    <div class="data-container" style="height:100%;padding:5vw; flex-direction:row!important;">
        <div class="chatbox"><?php include("../components/chat.php") ?></div>
        <div class="viewer"><?php include("../components/viewer.php") ?></div>
    </div>
</body>

</html>