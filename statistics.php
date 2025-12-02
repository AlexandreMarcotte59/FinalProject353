<?php

$servername="mvc353.encs.concordia.ca"; 
$username="mvc353_2"; 
$password="firstsound58"; 
$dbname="mvc353_2";

if($conn->connect_error) die("Connection failed: ".$conn->connect_error);
?>
<!DOCTYPE html>
<html>
<head>
<title>CFP Statistics</title>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap');

    body {
        font-family: 'Roboto', sans-serif;
        background-color: #f2f4f7;
        margin: 0;
        padding: 0;
    }

    h1 {
        background: linear-gradient(90deg, #1e88e5, #42a5f5);
        color: white;
        padding: 22px;
        margin: 0;
        text-align: center;
        font-size: 30px;
        font-weight: 500;
        letter-spacing: 0.5px;
        box-shadow: 0px 2px 10px rgba(0,0,0,0.1);
    }

    h2 {
        color: #1e4a6d;
        margin: 0 0 15px 0;
        font-size: 20px;
        font-weight: 500;
        border-left: 4px solid #1e88e5;
        padding-left: 12px;
    }

    .card {
        background: white;
        width: 85%;
        margin: 25px auto;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0px 3px 10px rgba(0,0,0,0.08);
        border: 1px solid #e0e6ed;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    th {
        background-color: #e3f2fd;
        color: #0d47a1;
        padding: 10px;
        text-align: left;
        font-size: 14px;
        border-bottom: 2px solid #bbdefb;
    }

    td {
        padding: 10px;
        background-color: white;
        font-size: 14px;
        border-bottom: 1px solid #e0e6ed;
    }

    tr:hover td {
        background-color: #f5faff;
    }
</style>

</head>
<body>

<h1>Record of CFP Statistics</h1>

<div class="card">
<h2>Growth and Usage of Content (Uploads per Year)</h2>
<table>
<tr><th>Year</th><th>New Texts Uploaded</th></tr>
<?php
$sql = "SELECT YEAR(upload_date) AS year, COUNT(*) AS total FROM texts GROUP BY YEAR(upload_date) ORDER BY year";
$res = $conn->query($sql);
while ($row = $res->fetch_assoc()) {
    echo "<tr><td>{$row['year']}</td><td>{$row['total']}</td></tr>";
}
?>
</table>
</div>

<div class="card">
<h2>Annual Access to CFP Content</h2>
<table>
<tr><th>Year</th><th>Total Downloads</th></tr>
<?php
$sql = "SELECT YEAR(download_date) AS year, COUNT(*) AS downloads FROM downloads GROUP BY YEAR(download_date) ORDER BY year";
$res = $conn->query($sql);
while ($row = $res->fetch_assoc()) {
    echo "<tr><td>{$row['year']}</td><td>{$row['downloads']}</td></tr>";
}
?>
</table>
</div>

<div class="card">
<h2>Annual Access by Countries</h2>
<table>
<tr><th>Country</th><th>Total Downloads</th></tr>
<?php
$sql = "SELECT user_country, COUNT(*) AS total FROM downloads GROUP BY user_country ORDER BY total DESC";
$res = $conn->query($sql);
while ($row = $res->fetch_assoc()) {
    echo "<tr><td>{$row['user_country']}</td><td>{$row['total']}</td></tr>";
}
?>
</table>
</div>


<div class="card">
<h2>Annual Access by Authors</h2>
<table>
<tr><th>Author</th><th>Total Downloads</th></tr>
<?php
$sql = "SELECT u.name AS author, COUNT(d.id) AS downloads
        FROM users u
        JOIN texts t ON u.id = t.author_id
        LEFT JOIN downloads d ON t.id = d.text_id
        GROUP BY u.id
        ORDER BY downloads DESC";
$res = $conn->query($sql);
while ($row = $res->fetch_assoc()) {
    echo "<tr><td>{$row['author']}</td><td>{$row['downloads']}</td></tr>";
}
?>
</table>
</div>

<div class="card">
<h2>Most Downloaded Authors (Ranked)</h2>
<table>
<tr><th>Author</th><th>Total Downloads</th></tr>
<?php
$sql = "SELECT u.name AS author, COUNT(d.id) AS downloads
        FROM users u
        JOIN texts t ON u.id = t.author_id
        LEFT JOIN downloads d ON d.text_id = t.id
        GROUP BY u.id
        ORDER BY downloads DESC LIMIT 10";
$res = $conn->query($sql);
while ($row = $res->fetch_assoc()) {
    echo "<tr><td>{$row['author']}</td><td>{$row['downloads']}</td></tr>";
}
?>
</table>
</div>


<div class="card">
<h2>Most Downloaded Titles</h2>
<table>
<tr><th>Title</th><th>Total Downloads</th></tr>
<?php
$sql = "SELECT t.title, COUNT(d.id) AS downloads
        FROM texts t
        LEFT JOIN downloads d ON t.id = d.text_id
        GROUP BY t.id
        ORDER BY downloads DESC
        LIMIT 10";
$res = $conn->query($sql);
while ($row = $res->fetch_assoc()) {
    echo "<tr><td>{$row['title']}</td><td>{$row['downloads']}</td></tr>";
}
?>
</table>
</div>

</body>
</html>

<?php $conn->close(); ?>
