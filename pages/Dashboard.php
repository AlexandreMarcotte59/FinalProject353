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
<div class="dashboard"><div class="dashboard-top">
    <div class="profile-container" >
        <h3 style="text-align:center">You</h3>

        <div class="profile-data"  style="display:flex;flex-direction:column;">
                <label><em>Name:               </em><span> <?= $mem['name_or_username']?></span></label>
                <label><em>Address:            </em><span> <?= $mem['address']?> </span></label>
                <label><em>Affiliation:        </em><span> <?= $mem['organization']?> </span></label>
                <label><em>Email:              </em><span> <?= $mem['recovery_email']?> </span></label>
                <label><em>Total Donated:      </em><span> <?= $_SESSION['total_donated']? : "$0.00"?> </span></label>
                <label><em>Download / Day:     </em><span> <?= $_SESSION['downloads_allowed'] . "/" . $_SESSION['downloads_window']?> </span></label>
                <label><em>Referral:      </em><span> <?= $mem['referral_code']?> </span></label>
                <span class="separator"><hr/>*<hr/></span>
                <label><em>Membership:  </em><span> <?= ($mem['is_admin']) ? "Admin" : "Normal" ?>  </span></label>
                <?php if ($_SESSION["has_works"]): ?>
                <label><em>Total Revenue:  </em><span> <?= $_SESSION["total_raised"] ?> </span></label>
                <?php endif ?>
                <label><a href="/pages/MyEmail.php"> Email ▶</a></label>
        </div>
        <form><button>Edit Profile</button><button>Retract Membership</button></form>

    </div>
    <div class="work-stats">
    <?php if ($_SESSION["has_works"]): ?>
        <div class="most-popular"></div>
        <div class="most-downloaded"></div>
        <div class="most-discussed"></div>
    <?php else: ?>
        <p>You have not contributed any of your works.</p>
    </div>
    <?php endif ?>

    </div>
    <div class="dashboard-end">
    <?php if ($mem['is_admin']): ?>
        <div class="stats-container">
            <?php include("../components/stats.php");?>
        </div>
    <?php endif ?>
    </div>
</div>
</body>