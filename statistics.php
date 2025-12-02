<?php
// ----- DATABASE CONNECTION -----
$servername = "mvc353.encs.concordia.ca"; 
$username   = "mvc353_2"; 
$password   = "firstsound58"; 
$dbname     = "mvc353_2";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
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

<!-- 1. Growth and Usage of Content in CFP (Uploads per Year) -->
<div class="card">
    <h2>Growth and Usage of Content in CFP (Uploads per Year)</h2>
    <table>
        <tr><th>Year</th><th>New Texts Uploaded</th></tr>
        <?php
        $sql = "SELECT YEAR(date_published) AS year, COUNT(*) AS total
                FROM Texts
                WHERE date_published IS NOT NULL
                GROUP BY YEAR(date_published)
                ORDER BY year";
        $res = $conn->query($sql);

        if (!$res) {
            echo "<tr><td colspan='2'>SQL error: " . htmlspecialchars($conn->error) . "</td></tr>";
        } elseif ($res->num_rows === 0) {
            echo "<tr><td colspan='2'>No data available.</td></tr>";
        } else {
            while ($row = $res->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['year']) . "</td>
                        <td>" . htmlspecialchars($row['total']) . "</td>
                      </tr>";
            }
        }
        ?>
    </table>
</div>

<!-- 2. Annual Access to CFP’s Content -->
<div class="card">
    <h2>Annual Access to CFP’s Content</h2>
    <table>
        <tr><th>Year</th><th>Total Downloads</th></tr>
        <?php
        $sql = "SELECT YEAR(download_date) AS year, COUNT(*) AS downloads
                FROM Downloads
                GROUP BY YEAR(download_date)
                ORDER BY year";
        $res = $conn->query($sql);

        if (!$res) {
            echo "<tr><td colspan='2'>SQL error: " . htmlspecialchars($conn->error) . "</td></tr>";
        } elseif ($res->num_rows === 0) {
            echo "<tr><td colspan='2'>No data available.</td></tr>";
        } else {
            while ($row = $res->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['year']) . "</td>
                        <td>" . htmlspecialchars($row['downloads']) . "</td>
                      </tr>";
            }
        }
        ?>
    </table>
</div>

<!-- 3. Annual Access of CFP’s Content by Countries/Organizations -->
<div class="card">
    <h2>Annual Access of CFP’s Content by Countries / Organizations</h2>
    <table>
        <tr><th>Organization (Proxy for Country)</th><th>Total Downloads</th></tr>
        <?php
        $sql = "SELECT 
                    COALESCE(m.organization, 'Unknown') AS org,
                    COUNT(*) AS total
                FROM Downloads d
                LEFT JOIN Members m ON d.user_id = m.user_id
                GROUP BY org
                ORDER BY total DESC";
        $res = $conn->query($sql);

        if (!$res) {
            echo "<tr><td colspan='2'>SQL error: " . htmlspecialchars($conn->error) . "</td></tr>";
        } elseif ($res->num_rows === 0) {
            echo "<tr><td colspan='2'>No data available.</td></tr>";
        } else {
            while ($row = $res->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['org']) . "</td>
                        <td>" . htmlspecialchars($row['total']) . "</td>
                      </tr>";
            }
        }
        ?>
    </table>
</div>

<!-- 4. Annual Access of CFP’s Content by Authors -->
<div class="card">
    <h2>Annual Access of CFP’s Content by Authors</h2>
    <table>
        <tr><th>Year</th><th>Author</th><th>Total Downloads</th></tr>
        <?php
        $sql = "SELECT 
                    YEAR(d.download_date) AS year,
                    COALESCE(m.name_or_username, t.author, 'Unknown') AS author_name,
                    COUNT(d.download_id) AS downloads
                FROM Downloads d
                JOIN Texts t ON d.text_id = t.text_id
                LEFT JOIN Members m ON t.member_author = m.user_id
                GROUP BY year, author_name
                ORDER BY year, downloads DESC";
        $res = $conn->query($sql);

        if (!$res) {
            echo "<tr><td colspan='3'>SQL error: " . htmlspecialchars($conn->error) . "</td></tr>";
        } elseif ($res->num_rows === 0) {
            echo "<tr><td colspan='3'>No data available.</td></tr>";
        } else {
            while ($row = $res->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['year']) . "</td>
                        <td>" . htmlspecialchars($row['author_name']) . "</td>
                        <td>" . htmlspecialchars($row['downloads']) . "</td>
                      </tr>";
            }
        }
        ?>
    </table>
</div>

<!-- 5. Most Downloaded CFP Authors – Ranked -->
<div class="card">
    <h2>Most Downloaded CFP Authors – Ranked</h2>
    <table>
        <tr><th>Author</th><th>Total Downloads</th></tr>
        <?php
        $sql = "SELECT 
                    COALESCE(m.name_or_username, t.author, 'Unknown') AS author_name,
                    COUNT(d.download_id) AS downloads
                FROM Texts t
                LEFT JOIN Members m ON t.member_author = m.user_id
                LEFT JOIN Downloads d ON t.text_id = d.text_id
                GROUP BY author_name
                ORDER BY downloads DESC
                LIMIT 10";
        $res = $conn->query($sql);

        if (!$res) {
            echo "<tr><td colspan='2'>SQL error: " . htmlspecialchars($conn->error) . "</td></tr>";
        } elseif ($res->num_rows === 0) {
            echo "<tr><td colspan='2'>No data available.</td></tr>";
        } else {
            while ($row = $res->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['author_name']) . "</td>
                        <td>" . htmlspecialchars($row['downloads']) . "</td>
                      </tr>";
            }
        }
        ?>
    </table>
</div>

<!-- 6. Most Downloaded Titles -->
<div class="card">
    <h2>Most Downloaded Titles</h2>
    <table>
        <tr><th>Title</th><th>Total Downloads</th></tr>
        <?php
        $sql = "SELECT 
                    t.title,
                    COUNT(d.download_id) AS downloads
                FROM Texts t
                LEFT JOIN Downloads d ON t.text_id = d.text_id
                GROUP BY t.text_id, t.title
                ORDER BY downloads DESC
                LIMIT 10";
        $res = $conn->query($sql);

        if (!$res) {
            echo "<tr><td colspan='2'>SQL error: " . htmlspecialchars($conn->error) . "</td></tr>";
        } elseif ($res->num_rows === 0) {
            echo "<tr><td colspan='2'>No data available.</td></tr>";
        } else {
            while ($row = $res->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['title']) . "</td>
                        <td>" . htmlspecialchars($row['downloads']) . "</td>
                      </tr>";
            }
        }
        ?>
    </table>
</div>

<!-- 7. Most Viewed Articles (using Texts.popularity) -->
<div class="card">
    <h2>Most Viewed Articles</h2>
    <table>
        <tr><th>Title</th><th>Views (Popularity)</th></tr>
        <?php
        $sql = "SELECT title, popularity
                FROM Texts
                ORDER BY popularity DESC
                LIMIT 10";
        $res = $conn->query($sql);

        if (!$res) {
            echo "<tr><td colspan='2'>SQL error: " . htmlspecialchars($conn->error) . "</td></tr>";
        } elseif ($res->num_rows === 0) {
            echo "<tr><td colspan='2'>No data available.</td></tr>";
        } else {
            while ($row = $res->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['title']) . "</td>
                        <td>" . htmlspecialchars($row['popularity']) . "</td>
                      </tr>";
            }
        }
        ?>
    </table>
</div>

</body>
</html>

<?php
$conn->close();
?>
