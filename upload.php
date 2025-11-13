<?php
// ---------------------
// 1. CONNECT TO MYSQL
// ---------------------

$servername = "localhost"; // stays the same
$username = "your_encs_username"; // e.g., am12345
$password = "your_encs_mysql_password"; // check in ENCS database info
$dbname = "your_encs_username_db"; // e.g., am12345_db

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// ---------------------
// 2. HANDLE FILE UPLOAD
// ---------------------
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["fileToUpload"])) {
  $target_dir = "uploads/";

  // Create folder if not exists
  if (!is_dir($target_dir)) {
    mkdir($target_dir, 0755, true);
  }

  $file_name = basename($_FILES["fileToUpload"]["name"]);
  $target_file = $target_dir . $file_name;
  $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

  if ($fileType != "txt") {
    echo "<p style='color:red;'>Only .txt files are allowed.</p>";
  } else if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
    $stmt = $conn->prepare("INSERT INTO uploads (filename) VALUES (?)");
    $stmt->bind_param("s", $file_name);
    $stmt->execute();
    $stmt->close();

    echo "<p style='color:green;'>✅ File uploaded successfully!</p>";
  } else {
    echo "<p style='color:red;'>❌ Error uploading file.</p>";
  }
}
?>

<!-- ----------------------
  3. UPLOAD FORM + TABLE
----------------------- -->
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Upload Text Files</title>
</head>
<body>
  <h2>Upload a Text File</h2>
  <form action="upload.php" method="POST" enctype="multipart/form-data">
    <input type="file" name="fileToUpload" accept=".txt" required>
    <button type="submit">Upload</button>
  </form>

  <hr>

  <h2>Uploaded Files</h2>
  <table border="1" cellpadding="5">
    <tr>
      <th>ID</th>
      <th>File Name</th>
      <th>Date Uploaded</th>
      <th>View</th>
    </tr>

    <?php
    $result = $conn->query("SELECT * FROM uploads ORDER BY uploaded_at DESC");
    while ($row = $result->fetch_assoc()) {
      echo "<tr>";
      echo "<td>{$row['id']}</td>";
      echo "<td>" . htmlspecialchars($row['filename']) . "</td>";
      echo "<td>{$row['uploaded_at']}</td>";
      echo "<td><a href='uploads/" . urlencode($row['filename']) . "' target='_blank'>Open</a></td>";
      echo "</tr>";
    }
    $conn->close();
    ?>
  </table>
</body>
</html>
