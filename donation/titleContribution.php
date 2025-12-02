<?php
include "./components/db.php";

if (!isset($_GET['id'])) die("Missing id");

$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT title FROM uploads WHERE textid = ?");
$stmt->execute([$id]);
$title = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("
SELECT 
SUM(amount) AS total,
SUM(amount * charity_percent/100) AS charity,
SUM(amount * author_percent/100) AS author,
SUM(amount * cfp_percent/100) AS cfp
FROM donations
WHERE textid = ?
");
$stmt->execute([$id]);
$sum = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<title>Title Contributions</title>
<style>
body { 
font-family:Arial; 
background:#f5f7fa; 
padding:20px; 
}
.container {
 max-width:700px; 
margin:auto; 
background:white; 
padding:25px; 
border-radius:10px; 
}
.box { 
background:#eef1f5; 
padding:20px; 
border-radius:10px; 
}
</style>
</head>
<body>
<div class="container">
<h2>Contributions for: <?= htmlspecialchars($title['title']) ?></h2>

<div class="box">
<p><strong>Total Donations:</strong> $<?= $sum['total'] ?? 0 ?></p>
<p><strong>Charity:</strong> $<?= $sum['charity'] ?? 0 ?></p>
<p><strong>Author:</strong> $<?= $sum['author'] ?? 0 ?></p>
<p><strong>CFP:</strong> $<?= $sum['cfp'] ?? 0 ?></p>
</div>

</div>
</body>
</html>
