<?php
include_once("../database/db.php");

$user_id = $_SESSION['user_id'];
$stmt_reader_contents = $pdo->prepare("SELECT * FROM Texts t JOIN Readers r ON t.text_id = r.text_id WHERE r.user_id = ?");
$stmt_reader_contents->execute([$user_id]);
$tuplesRead = $stmt_reader_contents->fetchAll(PDO::FETCH_ASSOC);

$stmt_up_contents = $pdo->prepare("SELECT * FROM Texts WHERE uploader = ? AND is_member_author = 0");
$stmt_up_contents->execute([$user_id]);
$tuplesUp = $stmt_up_contents->fetchAll(PDO::FETCH_ASSOC);
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
        <?php  if (empty($tuplesRead) && (empty($tuplesUp))): ?>
            <form action="MyLibrary.php" method="POST">
               <label style="padding:5px;font-variant:small-caps;" >No texts downloaded.</label>
            </form>
        <?php  endif ?>
        <?php  foreach ($tuplesRead as $text): ?>
             <div class="card" style="display:flex;flex-direction:column;">
                <label style="padding:5px;font-variant:small-caps;" >Title: "<?= htmlspecialchars($text['title']) ?>"</label>
                <label style="padding:5px;font-variant:small-caps;" >Author: <?= htmlspecialchars($text['author']) ?></label>
                <label style="padding:5px;font-variant:small-caps;" >Downloader: <?= htmlspecialchars($text['user_id']) ?> (You)</label>
                <label style="padding:5px;font-variant:small-caps;" >Views: <?= $text['popularity']?></label>
                <div class="card-footer">
                <form action="TextViewer.php" method="POST"><button type="submit">View</button>
               <input type="hidden" name="id" value="<?= $text['text_id'] ?>">
                </form>
                <form action="TextForum.php" method="POST"><button type="submit">Discuss</button>
               <input type="hidden" name="id" value="<?= $text['text_id'] ?>">
                </form></div>
            </div>
        <?php endforeach; ?>  
        
        <?php  foreach ($tuplesUp as $text): ?>
             <div class="card" style="display:flex;flex-direction:column;">
                <label style="padding:5px;font-variant:small-caps;" >Title: "<?= htmlspecialchars($text['title']) ?>"</label>
                <label style="padding:5px;font-variant:small-caps;" >Author: <?= htmlspecialchars($text['author']) ?></label>
                <label style="padding:5px;font-variant:small-caps;" >Uploader: <?= htmlspecialchars($text['uploader']) ?> (You)</label>
                <label style="padding:5px;font-variant:small-caps;" >Views: <?= $text['popularity']?></label>
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