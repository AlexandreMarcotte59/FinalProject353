<?php
session_start();
include "./components/db.php";

$message = "";

if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $desc = $_POST['description'];

    $stmt = $pdo->prepare("INSERT INTO charities (name, description, status) VALUES (?,?,'pending')");
    $stmt->execute([$name, $desc]);
    $message = "Charity submitted for approval!";
}

$stmt = $pdo->query("SELECT * FROM charities");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<title>Manage Charities</title>
<style>
body { 
font-family:Arial; 
padding:20px; 
background:#f5f7fa; 
}
.container { 
max-width:900px; 
margin:auto; 
background:white;
padding:25px; 
}
table { 
width:100%; 
border-collapse:collapse; 
margin-top:20px; 
}
th,td { 
border:1px solid #ccc; 
padding:10px; }
th { 
background:#eee; 
}
</style>
</head>
<body>
<div class="container">
<h2>Charity Management</h2>

<?php if ($message): ?><p><?= $message ?></p><?php endif; ?>

<table>
<tr>
<th>Name</th>
<th>Description</th>
<th>Status</th>
<th>Actions</th>
</tr>

<?php foreach($rows as $r): ?>
<tr>
<td><?= htmlspecialchars($r['name']) ?></td>
<td><?= htmlspecialchars($r['description']) ?></td>
<td><?= $r['status'] ?></td>
<td>
<a href="approveCharity.php?id=<?= $r['charity_id'] ?>">Approve</a> | 
<a href="deleteCharity.php?id=<?= $r['charity_id'] ?>">Delete</a>
</td>
</tr>
<?php endforeach; ?>

</table>

<h3>Add New Charity</h3>

<form method="POST">
<label>Name</label>
<input type="text" name="name" required>

<label>Description</label>
<textarea name="description" required></textarea>

<button name="add">Add Charity</button>
</form>

</div>
</body>
</html>
