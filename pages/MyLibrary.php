<?php
include_once("../database/db.php");

$user_id = $_SESSION['user_id'];
$stmt_reader_contents = $pdo->prepare("SELECT * FROM Texts t JOIN Readers r ON t.text_id = r.text_id WHERE r.user_id = ?");
$stmt_reader_contents->execute([$user_id]);
$tuplesRead = $stmt_reader_contents->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Library</title>

    <link rel="stylesheet" href="../style/main.css">
</head>
<body>

    <?php include("../components/navbar.php"); ?>
    <div class="data-container">
        <?php  foreach ($tuplesRead as $text): ?>
             <div class="card" style="display:flex;flex-direction:column;">
                <label style="padding:5px;font-variant:small-caps;">Title: "<?= htmlspecialchars($text['title']) ?>"</label>
                <label style="padding:5px;font-variant:small-caps;" >Author: <?= htmlspecialchars($text['author']) ?></label>
                <div class="card-footer">
                <form action="TextViewer.php" method="POST"><button type="submit">View</button>
               <input type="hidden" name="id" value="<?= $text['text_id'] ?>">
                </form>
                <form action="TextForum.php" method="POST"><button type="submit">Discuss</button>
               <input type="hidden" name="id" value="<?= $text['text_id'] ?>">
                </form></div>
            </div>
        <?php endforeach; ?>        
    </div>
</body>
</html>