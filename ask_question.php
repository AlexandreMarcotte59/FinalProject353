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
  $q=$_POST["question"];

  $stmt=$conn->prepare("INSERT INTO Questions (user_id,question) VALUES(?,?)");
  $stmt->bind_param("is",$uid,$q);

  echo $stmt->execute()
  ? "<p style='color:green;'>Question submitted!</p>"
  : "<p style='color:red;'>Error.</p>";
  $stmt->close();
}
?>
<!DOCTYPE html><html><body>
<h2>Ask a Question</h2>
<form method="POST">
  <textarea name="question" rows="4" cols="40"></textarea><br><br>
  <button>Submit</button>
</form><hr>

<h2>All Questions</h2>
<table border="1"><tr><th>ID</th><th>User</th><th>Question</th></tr>
<?php
$res=$conn->query("SELECT * FROM Questions ORDER BY question_id DESC");
while($r=$res->fetch_assoc()){
  echo "<tr>
          <td>{$r['question_id']}</td>
          <td>{$r['user_id']}</td>
          <td>".htmlspecialchars($r['question'])."</td>
        </tr>";
}
$conn->close();
?>
</table>
</body></html>
