<?php
$servername="localhost"; $username="your_encs_username"; 
$password="your_encs_mysql_password"; $dbname="your_encs_username_db";
$conn=new mysqli($servername,$username,$password,$dbname);
if($conn->connect_error) die("Connection failed: ".$conn->connect_error);

session_start();

$uid=$_SESSION["user_id"];

$stmt=$conn->prepare("SELECT * FROM InboxMessages WHERE recipient_id=? ORDER BY sent_datetime DESC");
$stmt->bind_param("i",$uid);
$stmt->execute();
$res=$stmt->get_result();
?>
<!DOCTYPE html><html><body>
<h2>Your Inbox</h2>
<table border="1">
<tr><th>From</th><th>Subject</th><th>Date</th><th>Open</th></tr>
<?php
while($r=$res->fetch_assoc()){
  echo "<tr>
          <td>{$r['sender_id']}</td>
          <td>".htmlspecialchars($r['subject'])."</td>
          <td>{$r['sent_datetime']}</td>
          <td><a href='read_message.php?id={$r['message_id']}'>Open</a></td>
        </tr>";
}
$conn->close();
?>
</table>
</body></html>
