<?php
include_once("../database/db.php");

$user_id = $_SESSION['user_id'];
$stmt_query_contents = $pdo->prepare("SELECT * FROM Charities");
$tuples = $stmt_query_contents->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Charities</title>

    <link rel="stylesheet" href="../style/main.css">
</head>
<body>

    <?php include("../components/navbar.php"); ?>
    <div class="charity-container">        
        <?php  foreach ($tuples as $char): ?>
             <div class="card">
                <label>Name: "<?= htmlspecialchars($char['name']) ?>"</label>
                <label >Organization: <?= htmlspecialchars($char['organization']) ?></label>
                <div style="display:flex;flex-direction: row;">
                <form action="CharityViewer.php" method="POST"><button type="submit">About</button>
               <input type="hidden" name="id" value="<?= $char['char_id'] ?>">
                </form>
                <form action="../donation/donation.php" method="POST"><button type="submit">Donate</button>
               <input type="hidden" name="char_id" value="<?= $char['char_id'] ?>">
                </form></div>
            </div>
        <?php endforeach; ?>            
    </div>
</body>
</html>