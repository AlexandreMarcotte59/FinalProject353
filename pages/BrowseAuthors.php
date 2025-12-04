<?php
include_once("../database/db.php");

$user_id = $_SESSION['user_id'];
$stmt_query_contents = $pdo->prepare("SELECT author FROM Texts");
$authors = $stmt_query_contents->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Authors</title>

    <link rel="stylesheet" href="../style/main.css" />
</head>
<body>

    <?php include("../components/navbar.php"); ?>
    <div class="author-container" style="width:100%; height: 100%; padding: var(--navHeight);">
        <ul>
        <?php  foreach ($authors as $author): ?>
        <form class="card" method="POST" action="AuthorWorks.php" onclick="submit(this)">
            <input type="hidden" name="author" value="<?= $author ?>" />
            <li><?= htmlspecialchars($author) ?></li>
        </form>
        <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>