<?php
include_once("../database/db.php");


$user_id = $_SESSION['user_id'];
$stmt_query_contents = $pdo->prepare("SELECT * FROM Members WHERE user_id = ?");
$stmt_query_contents->execute([$user_id]);
$mem = $stmt_query_contents->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard</title>

    <link rel="stylesheet" href="../style/main.css">
</head>
<body>
<?php include("../components/navbar.php"); ?>
<div class="dashboard">
    <div class="profile-container">
        <h3 style="text-align:center">You</h3>
        <div style="display:flex;dlex-direction:column;" class="profile">
            <input type="hidden" name="text_id" value="<?= $mem['text_id'] ?>">
            <label>Member Name: <?= $mem['name_or_username']?></label>
            <label>Member Inbox: <a href="/pages/MyEmail.php">My Email</a></label>
            <label>Status:  <?= ($mem['is_admin']) ? "Admin" : "Normal" ?></label>
            <form><button>Edit Profile</button><button>Retract Membership</button></form>
        </div>
    </div>
    <?php if ($mem['is_admin']): ?>
        <div class="stats-container">            
            <?php include("../components/stats.php");?>
        </div>  
    <?php endif ?>
</div>
</body>
</html>