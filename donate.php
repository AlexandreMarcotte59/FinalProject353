<?php
$servername="localhost"; 
$username="your_encs_username"; 
$password="your_encs_mysql_password"; 
$dbname="your_encs_username_db";
$conn=new mysqli($servername,$username,$password,$dbname);
if($conn->connect_error) die("Connection failed: ".$conn->connect_error);

session_start();

if($_SERVER["REQUEST_METHOD"]=="POST"){
  $uid=$_SESSION["user_id"];
  $text=$_POST["text_id"];
  $amt=$_POST["amount"];
  $dest=$_POST["destination"];

  $stmt=$conn->prepare("INSERT INTO Donations (text_id,user_id,quantity,destination,date)
                        VALUES(?,?,?,?,CURDATE())");
  $stmt->bind_param("iids",$text,$uid,$amt,$dest);

  echo $stmt->execute()
  ? "<p style='color:green;'>Donation successful!</p>"
  : "<p style='color:red;'>Error.</p>";
  $stmt->close();
}
?>
<!DOCTYPE html><html><body>
<h2>Donate</h2>
<form method="POST">
  Text ID: <input name="text_id"><br><br>
  Amount: <input name="amount" type="number" step="0.01"><br><br>
  Destination: <input name="destination"><br><br>
  <button>Donate</button>
</form><hr>

<h2>Donation History</h2>
<table border="1"><tr><th>ID</th><th>Text</th><th>User</th><th>Amount</th><th>Destination</th></tr>
<?php
$res=$conn->query("SELECT * FROM Donations ORDER BY donation_id DESC");
while($r=$res->fetch_assoc()){
  echo "<tr>
          <td>{$r['donation_id']}</td>
          <td>{$r['text_id']}</td>
          <td>{$r['user_id']}</td>
          <td>{$r['quantity']}</td>
          <td>{$r['destination']}</td>
        </tr>";
}
$conn->close();
?>
</table>
</body></html>
