<?php
session_start();
include "./components/db.php"; // must set $pdo (PDO connection)

// ---------------------
// Check membership
// ---------------------
$is_member = false;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT * FROM members WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user_tuple = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user_tuple) {
        $is_member = true;
    }
}

// ---------------------
// If a specific text is requested via ?id=
// ---------------------
$selected_text = null;
if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    // Increment popularity
    $stmt = $pdo->prepare("UPDATE uploads SET popularity = popularity + 1 WHERE textid = ?");
    $stmt->execute([$id]);

    // Fetch the row
    $stmt = $pdo->prepare("SELECT * FROM uploads WHERE textid = ?");
    $stmt->execute([$id]);
    $selected_text = $stmt->fetch(PDO::FETCH_ASSOC);
}

// ---------------------
// Fetch all uploads for the list
// ---------------------
$stmt = $pdo->query("
    SELECT textid, title, author, member_author, filename, popularity, date_published, uploaded_at
    FROM uploads
    ORDER BY popularity DESC, uploaded_at DESC
");
$texts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Helper to detect extension
function get_extension($filename) {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>View Texts</title>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .layout { display: grid; grid-template-columns: 1.2fr 1.8fr; gap: 30px; align-items: flex-start; }
        .text-viewer { border: 1px solid #ccc; padding: 10px; min-height: 400px; background:#fafafa; }
        pre { white-space: pre-wrap; word-wrap: break-word; }
        .back-btn,
        .home-btn {
            display: inline-block;
            padding: 8px 14px;
            background: #007bff;
            color: white;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            margin-right: 10px;
        }
        .back-btn:hover,
        .home-btn:hover {
            background: #0056b3;
        }
        .download-btn {
            display: inline-block;
            padding: 6px 12px;
            background: #28a745;
            color: #fff;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.9rem;
            margin-top: 10px;
        }
        .download-btn:hover {
            background: #1e7e34;
        }
        .info-msg { color: #777; font-style: italic; margin-top: 10px; }
    </style>
</head>
<body>
    <a href="home.php" class="home-btn">← Back to Home</a>
    <h1>View Texts</h1>
    <p>Click on a text in the list to view it. Each view increases its popularity.</p>

    <div class="layout">
        <!-- LEFT: List of texts -->
        <div>
            <h2>Available Texts</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Member Author</th>
                    <th>Popularity</th>
                    <th>Date Published</th>
                    <th>Action</th>
                </tr>
                <?php if ($texts): ?>
                    <?php foreach ($texts as $row): ?>
                        <tr>
                            <td><?php echo (int)$row["textid"]; ?></td>
                            <td><?php echo htmlspecialchars($row["title"]); ?></td>
                            <td><?php echo htmlspecialchars($row["author"]); ?></td>
                            <td><?php echo htmlspecialchars($row["member_author"]); ?></td>
                            <td><?php echo (int)$row["popularity"]; ?></td>
                            <td><?php echo htmlspecialchars($row["date_published"]); ?></td>
                            <td>
                                <a href="view_text.php?id=<?php echo (int)$row['textid']; ?>">View</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7">No texts uploaded yet.</td></tr>
                <?php endif; ?>
            </table>
        </div>

        <!-- RIGHT: Viewer for selected text -->
        <div>
            <h2>Text Viewer</h2>
            <div class="text-viewer">
                <?php if ($selected_text): ?>
                    <h3><?php echo htmlspecialchars($selected_text["title"]); ?></h3>
                    <p><strong>Author:</strong> <?php echo htmlspecialchars($selected_text["author"]); ?>
                    <br><strong>Member Author:</strong> <?php echo htmlspecialchars($selected_text["member_author"]); ?>
                    <br><strong>Popularity:</strong> <?php echo (int)$selected_text["popularity"]; ?>
                    <br><strong>Date Published:</strong> <?php echo htmlspecialchars($selected_text["date_published"]); ?></p>

                    <?php
                        $file = "uploads/" . $selected_text["filename"];
                        $ext  = get_extension($selected_text["filename"]);

                        if (!is_file($file)) {
                            echo "<p class='info-msg'>File not found on server.</p>";
                        } else {
                            if ($ext === "txt") {
                                // Show txt inline
                                $content = file_get_contents($file);
                                echo "<pre>" . htmlspecialchars($content) . "</pre>";
                            } elseif ($ext === "pdf") {
                                // Embed PDF
                                echo "<iframe src='" . htmlspecialchars($file) . "#toolbar=1' width='100%' height='500px'></iframe>";
                            } else {
                                echo "<p class='info-msg'>Unsupported file type.</p>";
                            }
                        }
                    ?>

                    <?php if ($is_member && is_file($file)): ?>
                        <a class="download-btn" href="<?php echo htmlspecialchars($file); ?>" download>
                            Download Text
                        </a>
                    <?php else: ?>
                        <p class="info-msg">
                            <?php if (!$is_member): ?>
                                Log in as a member to download the text.
                            <?php endif; ?>
                        </p>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="info-msg">Select a text from the list on the left to view its content.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>
</html>
