<?php
$servername="localhost"; $username="your_encs_username"; 
$password="your_encs_mysql_password"; $dbname="your_encs_username_db";
$conn=new mysqli($servername,$username,$password,$dbname);
if($conn->connect_error) die("Connection failed: ".$conn->connect_error);

session_start();

if($_SERVER["REQUEST_METHOD"]=="POST"){
  $cid=$_POST["committee_id"];
  $vid=$_SESSION["user_id"];

  $stmt=$conn->prepare("INSERT INTO CommitteeVolunteers (committee_id,volunteer_id)
                        VALUES(?,?)");
  $stmt->bind_param("ii",$cid,$vid);

  echo $stmt->execute()
  ? "<p style='color:green;'>You joined the committee!</p>"
  : "<p style='color:red;'>Error joining committee.</p>";
  $stmt->close();
}
?>
<!DOCTYPE html><html><body>
<h2>Join Committee</h2>
<form method="POST">
  Committee ID: <input name="committee_id"><br><br>
  <button>Join</button>
</form><hr>

<h2>All Committee Volunteers</h2>
<table border="1">
<tr><th>Committee</th><th>User</th></tr>
<?php
$res=$conn->query("SELECT * FROM CommitteeVolunteers ORDER BY committee_id");
while($r=$res->fetch_assoc()){
  echo "<tr><td>{$r['committee_id']}</td><td>{$r['volunteer_id']}</td></tr>";
}
$conn->close();
?>
</table>
</body></html>
