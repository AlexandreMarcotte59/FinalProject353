<?php
include "./components/db.php";

$id = $_GET['id'];

$stmt = $pdo->prepare("DELETE FROM charities WHERE charity_id = ?");
$stmt->execute([$id]);

echo "<h2>Charity Deleted Successfully!</h2>";
echo "<a href='manageCharities.php'>Back</a>";
?>
