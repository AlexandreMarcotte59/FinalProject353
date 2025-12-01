<?php
include_once("../database/db.php");

// Handle content queries
$user_id = $_SESSION['user_id'];
$stmt_query_contents = $pdo->prepare("SELECT * FROM Texts WHERE member_author = ?");
$stmt_query_contents->execute([$user_id]);
$tuples = $pdo->query($stmt_query_contents)->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Sidebar</title>

    <link rel="stylesheet" href="../style/main.css">
</head>
<body>

    <div class="data-container">
        <?php  foreach ($tuples as $text): ?>
            <form action="TextForum.php" method="get" style="display:inline-block;">
                <input type="hidden" name="text_id" value="<?= $text['text_id'] ?>">
                <label>Popularity: <?= $text['popularity']?></label>
                <button type="submit"><?= htmlspecialchars($text['title']) ?></button>
            </form>
        <?php endforeach; ?>        
    </div>
</body>
</html>