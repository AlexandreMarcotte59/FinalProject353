<?php
// CONNECT
$servername = "mvc353.encs.concordia.ca";
$username = "mvc353_2";
$password = "firstsound58";
$dbname   = "mvc353_2";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

session_start();

// HANDLE COMMENT
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $reader_id = $_SESSION["user_id"];
  $text_id   = $_POST["text_id"];
  $comment   = $_POST["comment"];

  $stmt = $conn->prepare("INSERT INTO TextComments (reader_id, text_id, comment, date_published)
                          VALUES (?, ?, ?, CURDATE())");
  $stmt->bind_param("iis", $reader_id, $text_id, $comment);

  echo $stmt->execute()
    ? "<p style='color:green;'>Comment added!</p>"
    : "<p style='color:red;'>Error adding comment.</p>";
  $stmt->close();
}
?>
<!DOCTYPE html><html><body>
<h2>Add Comment</h2>
<form method="POST">
  Text ID: <input type="number" name="text_id"><br><br>
  Comment:<br><textarea name="comment" rows="4" cols="40"></textarea><br><br>
  <button type="submit">Submit</button>
</form><hr>

<h2>All Comments</h2>
<table border="1" cellpadding="5">
<tr><th>Reader</th><th>Text</th><th>Comment</th><th>Date</th></tr>
<?php
$res = $conn->query("SELECT * FROM TextComments ORDER BY comment_id DESC");
while ($row = $res->fetch_assoc()) {
  echo "<tr>
          <td>{$row['reader_id']}</td>
          <td>{$row['text_id']}</td>
          <td>".htmlspecialchars($row['comment'])."</td>
          <td>{$row['date_published']}</td>
        </tr>";
}
$conn->close();
?>
</table>
</body></html>


