<?php
include "./db.php";

// Handle content queries
$user_id = $_SESSION['user_id'];
$stmt_query_contents = $pdo->prepare("SELECT * FROM charitees");
$tuples = $pdo->query($stmt_query_contents)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Sidebar</title>

    <link rel="stylesheet" href="../style/main.css">
</head>
<body>

    <div class="data-container">
        <?php 
        
        ?>        
    </div>
</body>
</html>