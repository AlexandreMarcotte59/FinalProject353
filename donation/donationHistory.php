<?php
session_start();
include "./components/db.php";

if (!isset($_SESSION['user_id'])) die("Not logged in");

$member_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT d.*, u.title, c.name AS charity
    FROM donations d
    JOIN uploads u ON d.textid = u.textid
    JOIN charities c ON d.charity_id = c.charity_id
    WHERE d.user_id = ?
");
$stmt->execute([$member_id]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<title>My Donations</title>
<style>
body { font-family:Arial; 
padding:20px; 
background:#f5f7fa; 
}
.container { 
max-width:900px; 
margin:auto; 
background:white; 
padding:25px; }
table { 
width:100%; 
border-collapse:collapse; 
margin-top:20px; }
th,td { border:1px solid #ccc; 
padding:10px; }
th { 
background:#eee; }
</style>
</head>
<body>
<div class="container">
<h2>My Donation History</h2>

<table>
<tr>
<th>Title</th>
<th>Amount</th>
<th>Charity</th>
<th>Date</th>
<th>Breakdown</th>
</tr>

<?php foreach($rows as $r): ?>
<tr>
<td><?= htmlspecialchars($r['title']) ?></td>
<td>$<?= $r['amount'] ?></td>
<td><?= htmlspecialchars($r['charity']) ?></td>
<td><?= $r['donation_date'] ?></td>
<td>
Charity: <?= $r['charity_percent'] ?>%<br>
Author: <?= $r['author_percent'] ?>%<br>
CFP: <?= $r['cfp_percent'] ?>%
</td>
</tr>
<?php endforeach; ?>

</table>

</div>
</body>
</html>
