<?php
include_once("database/db.php"); // gives $pdo + session

if (!isset($_SESSION['user_id'])) {
    header("Location: pages/MemberLogin.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>CFP Statistics</title>
    <link rel="stylesheet" href="style/main.css">

    <style>
        /* Page shell uses your existing theme variables */
        body {
            background-color: var(--bkg-page-clr);
            display: block;
            min-height: auto;
            overflow-y: auto;
        }

        .stat-page-wrapper {
            margin-top: var(--navHeight);
            width: 100%;
            display: flex;
            justify-content: center;
        }

        .stat-page-container {
            width: 80vw;
            padding: 2vh 1vw 4vh 1vw;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .stat-page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: var(--clr-dark);
            color: white;
            padding: 12px 18px;
            border-radius: 12px;
        }

        .stat-page-header h1 {
            margin: 0;
            font-size: 22px;
        }

        .stat-page-header a {
            text-decoration: none;
            background-color: var(--clr-pop);
            color: white;
            padding: 6px 14px;
            border-radius: 8px;
        }

        .stat-page-header a:hover {
            filter: grayscale(20%);
        }

        .stat-card {
            background-color: var(--bkg-container-clr);
            border-radius: 12px;
            padding: 18px 20px;
            box-shadow: 0 3px 6px rgba(0,0,0,0.18);
        }

        .stat-card h2 {
            margin: 0 0 10px 0;
            font-size: 18px;
            color: var(--clr-dark);
        }

        .stat-card table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
        }

        .stat-card th {
            background-color: var(--border-clr);
            color: var(--clr-dark);
            padding: 8px;
            text-align: left;
            font-size: 14px;
        }

        .stat-card td {
            padding: 8px;
            font-size: 14px;
            border-top: 1px solid #ddd;
        }

        .stat-card tr:nth-child(even) td {
            background-color: #f9f1e6;
        }

        .stat-card tr:hover td {
            background-color: #f5e4d2;
        }
    </style>
</head>
<body>

<?php include("components/navbar.php"); ?>

<div class="stat-page-wrapper">
    <div class="stat-page-container">

        <div class="stat-page-header">
            <h1>Record of CFP Statistics</h1>
            <a href="pages/Dashboard.php">&laquo; Back to dashboard</a>
        </div>

        <!-- 1. Growth and Usage of Content in CFP (Uploads per Year) -->
        <div class="stat-card">
            <h2>Growth and Usage of Content in CFP (Uploads per Year)</h2>
            <table>
                <tr><th>Year</th><th>New Texts Uploaded</th></tr>
                <?php
                $sql = "SELECT YEAR(date_published) AS year, COUNT(*) AS total
                        FROM Texts
                        WHERE date_published IS NOT NULL
                        GROUP BY YEAR(date_published)
                        ORDER BY year";
                $stmt = $pdo->query($sql);

                if (!$stmt) {
                    $err = $pdo->errorInfo();
                    echo "<tr><td colspan='2'>SQL error: " . htmlspecialchars($err[2]) . "</td></tr>";
                } else {
                    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    if (empty($rows)) {
                        echo "<tr><td colspan='2'>No data available.</td></tr>";
                    } else {
                        foreach ($rows as $row) {
                            echo "<tr>
                                    <td>" . htmlspecialchars($row['year']) . "</td>
                                    <td>" . htmlspecialchars($row['total']) . "</td>
                                  </tr>";
                        }
                    }
                }
                ?>
            </table>
        </div>

        <!-- 2. Annual Access to CFP’s Content -->
        <div class="stat-card">
            <h2>Annual Access to CFP’s Content</h2>
            <table>
                <tr><th>Year</th><th>Total Downloads</th></tr>
                <?php
                $sql = "SELECT YEAR(download_date) AS year, COUNT(*) AS downloads
                        FROM Downloads
                        GROUP BY YEAR(download_date)
                        ORDER BY year";
                $stmt = $pdo->query($sql);

                if (!$stmt) {
                    $err = $pdo->errorInfo();
                    echo "<tr><td colspan='2'>SQL error: " . htmlspecialchars($err[2]) . "</td></tr>";
                } else {
                    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    if (empty($rows)) {
                        echo "<tr><td colspan='2'>No data available.</td></tr>";
                    } else {
                        foreach ($rows as $row) {
                            echo "<tr>
                                    <td>" . htmlspecialchars($row['year']) . "</td>
                                    <td>" . htmlspecialchars($row['downloads']) . "</td>
                                  </tr>";
                        }
                    }
                }
                ?>
            </table>
        </div>

        <!-- 3. Annual Access of CFP’s Content by Countries/Organizations -->
        <div class="stat-card">
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
                $stmt = $pdo->query($sql);

                if (!$stmt) {
                    $err = $pdo->errorInfo();
                    echo "<tr><td colspan='2'>SQL error: " . htmlspecialchars($err[2]) . "</td></tr>";
                } else {
                    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    if (empty($rows)) {
                        echo "<tr><td colspan='2'>No data available.</td></tr>";
                    } else {
                        foreach ($rows as $row) {
                            echo "<tr>
                                    <td>" . htmlspecialchars($row['org']) . "</td>
                                    <td>" . htmlspecialchars($row['total']) . "</td>
                                  </tr>";
                        }
                    }
                }
                ?>
            </table>
        </div>

        <!-- 4. Annual Access of CFP’s Content by Authors -->
        <div class="stat-card">
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
                $stmt = $pdo->query($sql);

                if (!$stmt) {
                    $err = $pdo->errorInfo();
                    echo "<tr><td colspan='3'>SQL error: " . htmlspecialchars($err[2]) . "</td></tr>";
                } else {
                    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    if (empty($rows)) {
                        echo "<tr><td colspan='3'>No data available.</td></tr>";
                    } else {
                        foreach ($rows as $row) {
                            echo "<tr>
                                    <td>" . htmlspecialchars($row['year']) . "</td>
                                    <td>" . htmlspecialchars($row['author_name']) . "</td>
                                    <td>" . htmlspecialchars($row['downloads']) . "</td>
                                  </tr>";
                        }
                    }
                }
                ?>
            </table>
        </div>

        <!-- 5. Most Downloaded CFP Authors – Ranked -->
        <div class="stat-card">
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
                $stmt = $pdo->query($sql);

                if (!$stmt) {
                    $err = $pdo->errorInfo();
                    echo "<tr><td colspan='2'>SQL error: " . htmlspecialchars($err[2]) . "</td></tr>";
                } else {
                    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    if (empty($rows)) {
                        echo "<tr><td colspan='2'>No data available.</td></tr>";
                    } else {
                        foreach ($rows as $row) {
                            echo "<tr>
                                    <td>" . htmlspecialchars($row['author_name']) . "</td>
                                    <td>" . htmlspecialchars($row['downloads']) . "</td>
                                  </tr>";
                        }
                    }
                }
                ?>
            </table>
        </div>

        <!-- 6. Most Downloaded Titles -->
        <div class="stat-card">
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
                $stmt = $pdo->query($sql);

                if (!$stmt) {
                    $err = $pdo->errorInfo();
                    echo "<tr><td colspan='2'>SQL error: " . htmlspecialchars($err[2]) . "</td></tr>";
                } else {
                    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    if (empty($rows)) {
                        echo "<tr><td colspan='2'>No data available.</td></tr>";
                    } else {
                        foreach ($rows as $row) {
                            echo "<tr>
                                    <td>" . htmlspecialchars($row['title']) . "</td>
                                    <td>" . htmlspecialchars($row['downloads']) . "</td>
                                  </tr>";
                        }
                    }
                }
                ?>
            </table>
        </div>

        <!-- 7. Most Viewed Articles (using Texts.popularity) -->
        <div class="stat-card">
            <h2>Most Viewed Articles</h2>
            <table>
                <tr><th>Title</th><th>Views (Popularity)</th></tr>
                <?php
                $sql = "SELECT title, popularity
                        FROM Texts
                        ORDER BY popularity DESC
                        LIMIT 10";
                $stmt = $pdo->query($sql);

                if (!$stmt) {
                    $err = $pdo->errorInfo();
                    echo "<tr><td colspan='2'>SQL error: " . htmlspecialchars($err[2]) . "</td></tr>";
                } else {
                    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    if (empty($rows)) {
                        echo "<tr><td colspan='2'>No data available.</td></tr>";
                    } else {
                        foreach ($rows as $row) {
                            echo "<tr>
                                    <td>" . htmlspecialchars($row['title']) . "</td>
                                    <td>" . htmlspecialchars($row['popularity']) . "</td>
                                  </tr>";
                        }
                    }
                }
                ?>
            </table>
        </div>

    </div> <!-- /stat-page-container -->
</div> <!-- /stat-page-wrapper -->

</body>
</html>
