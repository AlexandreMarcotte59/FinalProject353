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