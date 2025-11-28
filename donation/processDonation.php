<?php
session_start();
include "./components/db.php";

if (!isset($_SESSION['user_id'])) {
    die("Not logged in.");
}
$member_id = $_SESSION['user_id'];

if (!isset($_POST['donate'])) {
    header("Location: donation.php?error=Invalid submission");
    exit;
}

$textid = $_POST['textid'];
$charity_id = $_POST['charity_id'];
$amount = $_POST['amount'];

$c = $_POST['charity_percent'];
$a = $_POST['author_percent'];
$f = $_POST['cfp_percent'];

if ($c < 60) {
    header("Location: donation.php?error=Charity must be ≥ 60%");
    exit;
}

if ($c + $a + $f != 100) {
    header("Location: donation.php?error=Percentages must total 100%");
    exit;
}

$stmt = $pdo->prepare("
    INSERT INTO donations (user_id, textid, charity_id, amount, charity_percent, author_percent, cfp_percent)
    VALUES (?,?,?,?,?,?,?)
");
$stmt->execute([$member_id, $textid, $charity_id, $amount, $c, $a, $f]);

header("Location: donation.php?success=1");
exit;
?>
