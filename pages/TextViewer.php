<?php
include_once("../database/db.php");

// ---------------------
// Check membership
// ---------------------
$is_member = false;
$user_tuple = null;

if (isset($_SESSION['user_id'])) {
    $user_id = (int) $_SESSION['user_id'];

    // Use correct table name: Members (capital M)
    $stmt = $pdo->prepare("SELECT * FROM Members WHERE user_id = ?");
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
$file_web_path = null;   // path used in href for browser
$file_fs_path  = null;   // path used for is_file() on server
$ext  = null;
if (isset($_POST['id'])) {
    $id = (int) $_POST['id'];

    // Increment popularity in Texts using text_id
    $stmt = $pdo->prepare("UPDATE Texts SET popularity = popularity + 1 WHERE text_id = ?");
    $stmt->execute([$id]);

    // Fetch the row from Texts
    $stmt = $pdo->prepare("SELECT * FROM Texts WHERE text_id = ?");
    $stmt->execute([$id]);
    $selected_text = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($selected_text && !empty($selected_text["filename"])) {
        // Physical files stored in /uploads at project root
        $filename = $selected_text["filename"];

        // Web path (used in links)
        $file_web_path = "../uploads/" . $filename;

        // File system path (used for is_file)
        $file_fs_path = __DIR__ . "/../uploads/" . $filename;

        $ext  = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }
}

// ---------------------
// Fetch ALL texts for the list (from Texts) – currently unused but fine
// ---------------------
$stmt = $pdo->query("
    SELECT text_id, title, author, member_author, filename, popularity, date_published, uploaded_at
    FROM Texts
    ORDER BY uploaded_at DESC
");
$texts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>View Texts</title>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; font-size: 0.9rem; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .layout { display: grid; grid-template-columns: 1.3fr 1.7fr; gap: 30px; align-items: flex-start; margin: 10vh auto; }
        /*.text-viewer { border: 1px solid #ccc; padding: 10px; min-height: 400px; background:#fafafa; }*/
        .text-container { width: 100%; min-height: 75dvh; display: flex; flex-direction: column; align-items: center; }
        .row-container { display: flex; flex-direction: column; align-items: center; width: 100%; height: 100%;}
        .text-viewer {width: 70%; height:70%;}
        .text-viewer:has(iframe), .text-container:has(iframe) {height:95%;}
        .row-container:has(iframe) .infocard { height: 5dvh; }
        .row-container:has(iframe) .infocard:hover { height: auto !important; }
        iframe { position: unset !important; width: 100% !important; height: 100% !important;}
        pre { 
            white-space: pre-wrap; 
            word-wrap: break-word; 
            width: fit-content; 
            margin: 0 auto; 
            scrollbar-width: none; 
            background-color: lightgray;
            border-radius: 8px; 
            padding: 8px;
        }
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
        .home-btn:hover { background: #0056b3; }
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
        .download-btn:hover { background: #1e7e34; }
        .info-msg { color: #777; font-style: italic; margin-top: 10px; }
        .infocard { margin-bottom: 10px; }
    </style>
</head>
<body style="flex-direction: column !important;">
    <?php include("../components/navbar.php"); ?>

    <div class="text-container">
        <div class="row-container">
            <h2>Text Viewer</h2>

            <?php if ($selected_text): ?>
                <div class="infocard">
                    <h3><?php echo htmlspecialchars($selected_text["title"]); ?></h3>
                    <p>
                        <strong>Author:</strong> <?php echo htmlspecialchars($selected_text["author"]); ?><br>
                        <strong>Member Author:</strong> <?php echo htmlspecialchars($selected_text["member_author"]); ?><br>
                        <strong>Popularity:</strong> <?php echo (int)$selected_text["popularity"]; ?><br>
                        <strong>Date Published:</strong> <?php echo htmlspecialchars($selected_text["date_published"]); ?>
                    </p>
                </div>

                <?php include("../components/viewer.php"); ?>

                <!-- DOWNLOAD SECTION -->
                <div class="download">
                    <hr style="margin:5px 0;">
                    <h3 style="margin:0;text-align:center;">Download</h3>

                    <?php if (!$is_member): ?>
                        <p class="info-msg">
                            <strong>You must be logged in as a member to download this text.</strong>
                        </p>

                    <?php elseif (!$file_fs_path || !is_file($file_fs_path)): ?>
                        <p class="info-msg">
                            File not found on the server.
                        </p>

                    <?php else: ?>
                        <!-- This link will work for TXT, PDF, etc. -->
                        <a class="download-btn"
                           href="../download.php?file=<?php echo urlencode($file_web_path); ?>&id=<?php echo (int)$selected_text['text_id']; ?>">
                            ⬇️ Download Text (<?php echo strtoupper($ext); ?>)
                        </a>

                    <?php endif; ?>
                </div>              
            <?php endif; ?>  
        </div>
    </div>

</body>
</html>
