<?php
$servername="localhost"; $username="your_encs_username"; 
$password="your_encs_mysql_password"; $dbname="your_encs_username_db";
$conn=new mysqli($servername,$username,$password,$dbname);
if($conn->connect_error) die("Connection failed: ".$conn->connect_error);

session_start();

if($_SERVER["REQUEST_METHOD"]=="POST"){
  $uid=$_SESSION["user_id"];
  $qid=$_POST["question_id"];
  $ans=$_POST["answer"];

  $stmt=$conn->prepare("INSERT INTO Answers (user_id,question_id,answer) VALUES(?,?,?)");
  $stmt->bind_param("iis",$uid,$qid,$ans);

  echo $stmt->execute()
  ? "<p style='color:green;'>Answer submitted!</p>"
  : "<p style='color:red;'>Error.</p>";
  $stmt->close();
}
?>
<!DOCTYPE html><html><body>
<h2>Answer a Question</h2>
<form method="POST">
  Question ID: <input type="number" name="question_id"><br><br>
  <textarea name="answer" rows="4" cols="40"></textarea><br><br>
  <button>Submit Answer</button>
</form><hr>

<h2>All Answers</h2>
<table border="1"><tr><th>ID</th><th>User</th><th>Question</th><th>Answer</th></tr>
<?php
$res=$conn->query("SELECT * FROM Answers ORDER BY answer_id DESC");
while($r=$res->fetch_assoc()){
  echo "<tr>
          <td>{$r['answer_id']}</td>
          <td>{$r['user_id']}</td>
          <td>{$r['question_id']}</td>
          <td>".htmlspecialchars($r['answer'])."</td>
        </tr>";
}
$conn->close();
?>
</table>
</body></html>
