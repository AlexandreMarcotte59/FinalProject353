<?php

$servername="mvc353.encs.concordia.ca"; 
$username="mvc353_2"; 
$password="firstsound58"; 
$dbname="mvc353_2";

$conn=new mysqli($servername,$username,$password,$dbname);
if($conn->connect_error) die("Connection failed: ".$conn->connect_error);

session_start();

if($_SERVER["REQUEST_METHOD"]=="POST"){
  $user_id=$_SESSION["user_id"];
  $text_id=$_POST["text_id"];

  $stmt=$conn->prepare("INSERT IGNORE INTO Readers (user_id,text_id) VALUES(?,?)");
  $stmt->bind_param("ii",$user_id,$text_id);

  echo $stmt->execute()
  ? "<p style='color:green;'>Marked as read!</p>"
  : "<p style='color:red;'>Error.</p>";
  $stmt->close();
}
?>
<!DOCTYPE html><html><body>
<h2>Mark Text as Read</h2>
<form method="POST">
  Text ID: <input type="number" name="text_id">
  <button>Mark</button>
</form><hr>

<h2>Readers Table</h2>
<table border="1">
<tr><th>User</th><th>Text</th></tr>
<?php
$res=$conn->query("SELECT * FROM Readers ORDER BY user_id");
while($r=$res->fetch_assoc()){
  echo "<tr><td>{$r['user_id']}</td><td>{$r['text_id']}</td></tr>";
}
$conn->close();
?>
</table>
</body></html>

