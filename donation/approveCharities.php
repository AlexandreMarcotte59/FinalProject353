<?php
include "./components/db.php";

$id = $_GET['id'];

$stmt = $pdo->prepare("UPDATE charities SET status='approved' WHERE charity_id = ?");
$stmt->execute([$id]);

echo "<h2>Charity Approved Successfully!</h2>";
echo "<a href='manageCharities.php'>Back</a>";
?>
