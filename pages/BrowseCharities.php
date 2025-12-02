<?php
include_once("../database/db.php");

// Handle content queries
$user_id = $_SESSION['user_id'];
$stmt_query_contents = $pdo->prepare("SELECT * FROM Charities");
$tuples = $pdo->query($stmt_query_contents)->fetchAll(PDO::FETCH_ASSOC);
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
             <div class="card" style="display:flex;flex-direction:column;">
                <label style="padding:5px;font-variant:small-caps;">Name: "<?= htmlspecialchars($char['name']) ?>"</label>
                <label style="padding:5px;font-variant:small-caps;" >Organization: <?= htmlspecialchars($char['organization']) ?></label>
                <div style="display:flex;flex-direction: row;">
                <form action="CharityViewer.php" method="POST"><button type="submit">About</button>
               <input type="hidden" name="id" value="<?= $char['char_id'] ?>">
                </form>
                <form action="Donation.php" method="POST"><button type="submit">Donate</button>
               <input type="hidden" name="id" value="<?= $char['char_id'] ?>">
                </form></div>
            </div>
        <?php endforeach; ?>            
    </div>
</body>
</html>