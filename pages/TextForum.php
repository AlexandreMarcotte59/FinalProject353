<?php
include_once("../database/db.php");

$t_id = $_POST['id'] || $_GET['id'];
$stmt_query_contents = $pdo->prepare("SELECT * FROM Texts WHERE text_id = ?");
$stmt_query_contents->execute([$t_id]);
$selected_text = $stmt_query_contents->fetch(PDO::FETCH_ASSOC);

if ($selected_text && !empty($selected_text["filename"])) {
    // Physical files stored in /uploads at project root
    $filename = $selected_text["filename"];
    $filepath = '/uploads/' . $selected_text["filename"];
    //$file = $ROOTPATH . $filepath;
    // Web path (used in links)
    $file_web_path = (BASE_URL ?? '') . $filepath;

    // File system path (used for is_file)
    $file_fs_path = $ROOTPATH . (BASE_URL ?? '') . $filepath;

    $ext  = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

if (isset($_POST['comment_id'])) {
    $stmt = $pdo->prepare("DELETE FROM TextComments WHERE comment_id = ?");
    $stmt->execute([$_POST['comment_id']]);
    if (!isset($_GET['text_id'])){
            header('Location: ' . $_SERVER['REQUEST_URI'] . '?text_id=' . $_POST['id']);
            exit;
    } else {
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
    }
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