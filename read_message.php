<?php

$servername="mvc353.encs.concordia.ca"; 
$username="mvc353_2"; 
$password="firstsound58"; 
$dbname="mvc353_2";

if($conn->connect_error) die("Connection failed: ".$conn->connect_error);

$id=$_GET["id"];

$mark=$conn->prepare("UPDATE InboxMessages SET is_read=1 WHERE message_id=?");
$mark->bind_param("i",$id);
$mark->execute();

$stmt=$conn->prepare("SELECT * FROM InboxMessages WHERE message_id=?");
$stmt->bind_param("i",$id);
$stmt->execute();
$msg=$stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html><html><body>
<h2><?= htmlspecialchars($msg['subject']) ?></h2>
<p><strong>From:</strong> <?= $msg['sender_id'] ?></p>
<p><?= nl2br(htmlspecialchars($msg['body'])) ?></p>
</body></html>

