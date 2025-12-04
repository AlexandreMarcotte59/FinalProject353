<?php
include_once("../database/db.php"); // sets up $pdo and session

if (!isset($_SESSION['user_id'])) {
    header("Location: MemberLogin.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch member info
$stmt_query_contents = $pdo->prepare("SELECT * FROM Members WHERE user_id = ?");
$stmt_query_contents->execute([$user_id]);
$mem = $stmt_query_contents->fetch(PDO::FETCH_ASSOC);

// ---- Top 3 stats for dashboard (for everyone) ----

// 1) Top 3 authors by downloads
$sqlTopAuthors = "
    SELECT
        COALESCE(m.name_or_username, t.author, 'Unknown') AS author_name,
        COUNT(d.download_id) AS downloads
    FROM Texts t
    LEFT JOIN Members m ON t.member_author = m.user_id
    LEFT JOIN Downloads d ON t.text_id = d.text_id
    GROUP BY author_name
    ORDER BY downloads DESC
    LIMIT 3
";
$stmt = $pdo->query($sqlTopAuthors);
$topAuthors = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

// 2) Top 3 titles by views (popularity)
$sqlTopViewed = "
    SELECT
        title,
        popularity
    FROM Texts
    ORDER BY popularity DESC
    LIMIT 3
";
$stmt = $pdo->query($sqlTopViewed);
$topViewedTitles = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

// 3) Top 3 titles by downloads
$sqlTopDownloadedTitles = "
    SELECT
        t.title,
        COUNT(d.download_id) AS downloads
    FROM Texts t
    JOIN Downloads d ON t.text_id = d.text_id
    GROUP BY t.text_id, t.title
    ORDER BY downloads DESC
    LIMIT 3
";
$stmt = $pdo->query($sqlTopDownloadedTitles);
$topDownloadedTitles = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="../style/main.css">

    <!-- Extra styling just for the stats cards; you can move this into main.css -->
    <style>
        .stats-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 1.2vw;
            max-width: fit-content;
        }

        .stats-card {
            background: var(--bkg-container-clr);
            border-radius: 14px;
            padding: 0;
            min-width: 10vw;
            max-width: 13vw;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.18);
            transition: transform .15s;
            max-height: 7vh;
        }

        .stats-card:hover {
            transform: scale(1.02);
        }

        .stats-card-header {
            background: var(--clr-pop);
            color: white;
            padding: 10px 14px;
            border-radius: 14px 14px 0 0;
            font-weight: bold;
            font-size: 15px;
        }

        .stats-card-body {
            padding: 12px 14px;
            font-size: 14px;
            color: var(--clr-dark);
        }
    </style>
</head>

<body>
    <?php include("../components/navbar.php"); ?>

    <div class="dashboard">
        <div class="dashboard-top">

            <!-- LEFT: PROFILE -->
            <div class="profile-container">
                <h3 style="text-align:center">You</h3>

                <div class="profile-data" style="display:flex;flex-direction:column;">
                    <label><em>Name: </em><span> <?= htmlspecialchars($mem['name_or_username']) ?></span></label>
                    <label><em>Address: </em><span> <?= htmlspecialchars($mem['address']) ?> </span></label>
                    <label><em>Affiliation: </em><span> <?= htmlspecialchars($mem['organization']) ?> </span></label>
                    <label><em>Email: </em><span> <?= htmlspecialchars($mem['recovery_email']) ?> </span></label>
                    <label><em>Total Donated: </em><span> <?= $_SESSION['total_donated'] ?? "$0.00" ?> </span></label>
                    <label><em>Download / Day: </em><span>
                            <?= htmlspecialchars($_SESSION['downloads_allowed'] . "/" . $_SESSION['downloads_window']) ?>
                        </span></label>
                    <label><em>Referral: </em><span> <?= htmlspecialchars($mem['referral_code']) ?> </span></label>

                    <span class="separator">
                        <hr />*
                        <hr />
                    </span>

                    <label><em>Membership: </em><span> <?= ($mem['is_admin']) ? "Admin" : "Normal" ?> </span></label>

                    <?php if (!empty($_SESSION["has_works"])): ?>
                        <label><em>Total Revenue: </em><span> <?= htmlspecialchars($_SESSION["total_raised"]) ?>
                            </span></label>
                    <?php endif; ?>

                    <label><a href="/pages/MyEmail.php"> Inbox ▶</a></label>
                </div>

                <form>
                    <a href='../pages/MemberEdit.php'>Edit Profile ▶</a>
                </form>
            </div>

            <!-- RIGHT: STATS GRIDS -->
            <div class="work-stats">
                <?php if ($_SESSION["has_works"]): ?>
                    <div class="most-popular"></div>
                    <div class="most-downloaded"></div>
                    <div class="most-discussed"></div>
                <?php else: ?>
                    <p style="text-align: center;">You have not contributed any of your works.</p>
                </div>
            <?php endif ?>
        </div>


        <div class="dashboard-end">
            <h3>CFP Statistics Overview</h3>

            <!-- Top 3 Authors by Downloads -->
            <h4 style="margin-top:10px;">Top 3 Authors by Downloads</h4>
            <div class="stats-grid">
                <?php if (empty($topAuthors)): ?>
                    <p>No author download data available.</p>
                <?php else: ?>
                    <?php foreach ($topAuthors as $author): ?>
                        <div class="stats-card">
                            <div class="stats-card-header">
                                <?= htmlspecialchars($author['author_name']) ?>
                            </div>
                            <div class="stats-card-body">
                                Downloads: <?= (int) $author['downloads'] ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <!-- Top 3 Titles by Views -->
            <h4 style="margin-top:20px;">Top 3 Titles by Views</h4>
            <div class="stats-grid">
                <?php if (empty($topViewedTitles)): ?>
                    <p>No view data available.</p>
                <?php else: ?>
                    <?php foreach ($topViewedTitles as $text): ?>
                        <div class="stats-card">
                            <div class="stats-card-header">
                                <?= htmlspecialchars($text['title']) ?>
                            </div>
                            <div class="stats-card-body">
                                Views: <?= (int) $text['popularity'] ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <!-- Top 3 Titles by Downloads -->
            <h4 style="margin-top:20px;">Top 3 Titles by Downloads</h4>
            <div class="stats-grid">
                <?php if (empty($topDownloadedTitles)): ?>
                    <p>No download data available.</p>
                <?php else: ?>
                    <?php foreach ($topDownloadedTitles as $text): ?>
                        <div class="stats-card">
                            <div class="stats-card-header">
                                <?= htmlspecialchars($text['title']) ?>
                            </div>
                            <div class="stats-card-body">
                                Downloads: <?= (int) $text['downloads'] ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <p style="margin-top:15px;">
                <a href="../Statistics.php" style="color: var(--clr-pop); text-decoration:none;">
                    View full stats &raquo;
                </a>
            </p>
        </div>
    </div>
</body>