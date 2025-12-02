<?php
include_once( "../database/db.php");

$t_id = $_POST['id'];
$stmt_query_contents = $pdo->prepare("SELECT * FROM Texts WHERE text_id = ?");
$stmt_query_contents->execute([$t_id]);
$selected_text = $stmt_query_contents->fetch(PDO::FETCH_ASSOC);
if ($selected_text && !empty($selected_text["filename"])) {
        $filepath = "/uploads/" . $selected_text["filename"];
        $file = $ROOTPATH . $filepath;
        $ext  = strtolower(pathinfo($selected_text["filename"], PATHINFO_EXTENSION));
}


$stmt_text_chats = $pdo->prepare("SELECT * FROM TextComments WHERE text_id = ?");
$stmt_text_chats->execute([$t_id]);
$chats = $stmt_text_chats->fetch(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Text Forum</title>

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