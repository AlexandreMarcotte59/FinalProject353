<?php
$servername="localhost"; $username="your_encs_username"; 
$password="your_encs_mysql_password"; $dbname="your_encs_username_db";
$conn=new mysqli($servername,$username,$password,$dbname);
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
