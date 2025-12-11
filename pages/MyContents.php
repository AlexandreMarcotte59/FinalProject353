<?php
include_once("../database/db.php");

// Handle content queries
$user_id = $_SESSION['user_id'];
$stmt_query_contents = $pdo->prepare("SELECT * FROM Texts WHERE is_member_author = 1 AND uploader = ?");
$stmt_query_contents->execute([$user_id]);
$tuples = $stmt_query_contents->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Works</title>

    <link rel="stylesheet" href="../style/main.css">
</head>
<body>

    <?php include("../components/navbar.php"); ?>
    <div class="data-container">
        
        <?php  if (empty($tuples)): ?>
            <form action="MyLibrary.php" method="POST">
               <label style="padding:5px;font-variant:small-caps;" >No personal works contributed.</label>
            </form>
        <?php  else: ?>
            <?php $_SESSION['has_works'] = true;?>
        <?php  endif ?>
        <?php  foreach ($tuples as $text): ?>
             <div class="card" style="display:flex;flex-direction:column;">
                <label style="padding:5px;font-variant:small-caps;">Title: "<?= htmlspecialchars($text['title']) ?>"</label>
                <label style="padding:5px;font-variant:small-caps;" >Author: <?= htmlspecialchars($text['author']) ?></label>
                <label style="padding:5px;font-variant:small-caps;" >Views: <?= $text['popularity']?></label>
                <label style="padding:5px;font-variant:small-caps;" >Downloads: <?= $text['downloads']?></label>
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