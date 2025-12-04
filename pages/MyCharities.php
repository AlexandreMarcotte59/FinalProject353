<?php
include_once("../database/db.php");

$user_id = $_SESSION['user_id'];
$stmt_query_contents = $pdo->prepare("SELECT * FROM Charities c JOIN Donations d ON c.char_id = d.destination where d.user_id = ?");
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
        <?php  foreach ($tuples as $char): ?>
             <div class="card">
                <label style="font-weight:bold;color:white;">Name: "<?= htmlspecialchars($char['name']) ?>"</label>
                <?php                     
                    $stmt_t = $pdo->prepare("SELECT 1 FROM Texts where text_id = ?");
                    $stmt_t->execute([$char["text_id"]]);
                    $text = $stmt_t->fetch(PDO::FETCH_ASSOC);

                    $stmt_adono = $pdo->prepare("SELECT SUM(quantity) FROM Donations WHERE user_id = ? AND destination = ?");
                    $stmt_adono->execute([$user_id, $char["char_id"]]);
                    $total = $stmt_adono->fetchColumn();
                    $total = floatval($total);
                    
                    $stmt_rdono = $pdo->prepare("SELECT SUM(quantity) FROM Donations WHERE user_id=? AND destination = ? AND donation_date >= NOW() - INTERVAL 31 DAY");
                    $stmt_rdono->execute([$user_id, $char["char_id"]]);
                    $recent_rdono = $stmt_rdono->fetchColumn();
                    $recent_rdono = floatval($recent_rdono);
                ?>
                <label>Text: <?= htmlspecialchars($text['title']) ?></label>
                <label>Organization: <?= $char['organization']?></label>
                <label>This donation: <?= $char['quantity'] ?></label>
                <label>Recent donations: <?= $recent_rdono ?></label>
                <label>All donations: <?= $total ?></label>
                <div style="display:flex;flex-direction: row;justify-content: space-between;">
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