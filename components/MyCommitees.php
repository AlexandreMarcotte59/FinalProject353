<?php
include "./db.php";

// Handle content queries
$user_id = $_SESSION['user_id'];
$stmt_query_contents = $pdo->prepare("SELECT * FROM Committee AS c JOIN CommitteeVolunteers AS v WHERE v.committee_id = c.committee_id AND v.volunteer_id = ?");
$stmt_query_contents->execute([$user_id]);
$tuples = $pdo->query($stmt_query_contents)->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Committees</title>

    <link rel="stylesheet" href="../style/main.css">
</head>
<body>

    <div class="data-container">        
        <table>
        <?php  foreach ($tuples as $row): ?>
            <tr><?= $row ?></tr>
        <?php endforeach; ?>
        </table>      
    </div>
</body>
</html>