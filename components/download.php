<?php
// 1) Correct include path to db.php
include_once("../database/db.php");

$isAjax = isset($_GET['ajax']);
// 2) Validate request
if (!isset($_GET['file']) || !isset($_GET['id'])) {
        die("Invalid request");
}

$text_id = (int) $_GET['id'];

// 3) Only accept a filename, not a full path
$filename = basename($_GET['file']);
$filepath = __DIR__ . "/../uploads/" . $filename;

if (!file_exists($filepath)) {
        http_response_code(404);
        exit;
}

$readerAlready = $pdo->prepare("SELECT * FROM Readers WHERE user_id = ? AND text_id = ?");
$readerAlready->execute([$_SESSION['user_id'], $text_id]);
$existingReader = $readerAlready->fetch(PDO::FETCH_ASSOC);

if ($existingReader) {
        if ($isAjax) {
                http_response_code(403);
                if ($_SESSION['downloads_capped'] == true){
                        http_response_code(405); exit;
                }
        }
       exit; 
}

$stmt = $pdo->prepare("UPDATE Texts SET downloads = downloads + 1 WHERE text_id = ?");
$stmt->execute([$text_id]);

$stmtR = $pdo->prepare("INSERT INTO Readers (user_id, text_id) VALUES (?,?)");
$stmtR->execute([$_SESSION['user_id'], $text_id]);

$stmtR = $pdo->prepare("INSERT INTO Downloads (user_id, text_id) VALUES (?,?)");
$stmtR->execute([$_SESSION['user_id'], $text_id]);

header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Content-Length: " . filesize($filepath));

readfile($filepath);
exit;

if (!$isAjax) {
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Length: " . filesize($filepath));
        readfile($filepath);
}

exit;

?>
