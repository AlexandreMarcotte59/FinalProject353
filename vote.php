<?php
$servername="localhost"; $username="your_encs_username"; 
$password="your_encs_mysql_password"; $dbname="your_encs_username_db";
$conn=new mysqli($servername,$username,$password,$dbname);
if($conn->connect_error) die("Connection failed: ".$conn->connect_error);

if($_SERVER["REQUEST_METHOD"]=="POST"){
  $text=$_POST["text_id"];
  $res=$_POST["result"];

  $stmt=$conn->prepare("INSERT INTO Vote (plagiarized_item,result) VALUES(?,?)");
  $stmt->bind_param("is",$text,$res);

  echo $stmt->execute()
  ? "<p style='color:green;'>Vote recorded!</p>"
  : "<p style='color:red;'>Error.</p>";
  $stmt->close();
}
?>
<!DOCTYPE html><html><body>
<h2>Vote on Text</h2>
<form method="POST">
  Text ID: <input name="text_id"><br><br>
  <select name="result">
    <option value="plagiarized">Plagiarized</option>
    <option value="not_plagiarized">Not Plagiarized</option>
  </select><br><br>
  <button>Submit Vote</button>
</form><hr>

<h2>All Votes</h2>
<table border="1">
<tr><th>ID</th><th>Text</th><th>Result</th></tr>
<?php
$res=$conn->query("SELECT * FROM Vote ORDER BY vote_id DESC");
while($r=$res->fetch_assoc()){
  echo "<tr>
          <td>{$r['vote_id']}</td>
          <td>{$r['plagiarized_item']}</td>
          <td>{$r['result']}</td>
        </tr>";
}
$conn->close();
?>
</table>
</body></html>
