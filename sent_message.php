<?php
$servername="localhost"; 
$username="your_encs_username"; 
$password="your_encs_mysql_password"; 
$dbname="your_encs_username_db";
$conn=new mysqli($servername,$username,$password,$dbname);
if($conn->connect_error) die("Connection failed: ".$conn->connect_error);

session_start();

if($_SERVER["REQUEST_METHOD"]=="POST"){
  $sender=$_SESSION["user_id"];
  $rec=$_POST["recipient"];
  $sub=$_POST["subject"];
  $body=$_POST["body"];

  $stmt=$conn->prepare("INSERT INTO InboxMessages (sender_id,recipient_id,subject,body)
                        VALUES(?,?,?,?)");
  $stmt->bind_param("iiss",$sender,$rec,$sub,$body);

  echo $stmt->execute()
  ? "<p style='color:green;'>Message sent!</p>"
  : "<p style='color:red;'>Error sending message.</p>";
  $stmt->close();
}
?>
<!DOCTYPE html><html><body>
<h2>Send a Message</h2>
<form method="POST">
  To (User ID): <input name="recipient"><br><br>
  Subject: <input name="subject"><br><br>
  Message:<br>
  <textarea name="body" rows="4" cols="40"></textarea><br><br>
  <button>Send</button>
</form>
</body></html>
