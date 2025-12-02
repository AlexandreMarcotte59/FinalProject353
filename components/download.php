<?php
include_once("database/db.php");

if (!isset($_GET['file']) || !isset($_GET['id'])) {
    die("Invalid request");
}

$filepath = $_GET['file'];
$text_id  = intval($_GET['id']);

$stmt = $pdo->prepare("UPDATE Texts SET download_count = download_count + 1 WHERE text_id = ?");
$stmt->execute([$text_id]);

if (!file_exists($filepath)) {
    die("File not found.");
}

$filename = basename($filepath);

header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Content-Length: " . filesize($filepath));

readfile($filepath);
exit;
