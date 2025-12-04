<?php
include_once("../database/db.php");

if(isset($_POST["author"])) {

$stmt_query_contents = $pdo->prepare("SELECT * FROM Texts WHERE author = ?");
$stmt_query_contents->execute([$_POST["author"]]);
$authorworks = $stmt_query_contents->fetchAll(PDO::FETCH_ASSOC);

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Authors</title>

    <link rel="stylesheet" href="../style/main.css" />
</head>
<body>

    <?php include("../components/navbar.php"); ?>
    <div class="works-container">
        <?php  foreach ($authorworks as $work): ?>
             <div class="card">
                <label>Title: "<?= htmlspecialchars($work['title']) ?>"</label>
                <label >Author: <?= htmlspecialchars($work['author']) ?></label>
                <label >Views: <?= $work['popularity']?></label>
                <label >Downloads: <?= $work['downloads']?></label>
                <div style="display:flex;flex-direction: row;justify-content: space-between;">
                <form action="TextViewer.php" method="POST"><button type="submit">View</button>
               <input type="hidden" name="id" value="<?= $work['text_id'] ?>">
                </form>
                <form action="TextForum.php" method="POST"><button type="submit">Discuss</button>
               <input type="hidden" name="id" value="<?= $work['text_id'] ?>">
                </form></div>
            </div>
        <?php endforeach; ?>        
    </div>
</body>
</html>