<?php
$servername="localhost"; $username="your_encs_username"; 
$password="your_encs_mysql_password"; $dbname="your_encs_username_db";
$conn=new mysqli($servername,$username,$password,$dbname);
if($conn->connect_error) die("Connection failed: ".$conn->connect_error);
?>
<!DOCTYPE html><html><body>
<h2>Statistics</h2>
<table border="1">
<tr><th>ID</th><th>Description</th><th>Year</th><th>Value</th></tr>
<?php
$res=$conn->query("SELECT * FROM Statistics ORDER BY year DESC");
while($r=$res->fetch_assoc()){
  echo "<tr>
          <td>{$r['stat_id']}</td>
          <td>".htmlspecialchars($r['description'])."</td>
          <td>{$r['year']}</td>
          <td>{$r['value']}</td>
        </tr>";
}
$conn->close();
?>
</table>
</body></html>
