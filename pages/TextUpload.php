<?php
// --- Database connection ---
// db.php already starts the session, according to your notice
include_once("../database/db.php");

// Safety check: if for some reason the session is not active, start it
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// --------------------------------------------------------------
// Resolve current member (user_id + display name)
// --------------------------------------------------------------

// Try to get user_id directly from session
$currentMemberId = $_SESSION['user_id'] ?? null;
$currentMemberName = null;

if ($currentMemberId !== null) {
    // We have user_id in session; get name for display
    $stmt = $conn->prepare("SELECT name_or_username FROM Members WHERE user_id = ?");
    $stmt->bind_param("i", $currentMemberId);
    $stmt->execute();
    $stmt->bind_result($currentMemberName);
    $stmt->fetch();
    $stmt->close();
} else {
    // No user_id in session; try to get by username
    $currentMemberName = $_SESSION['name_or_username'] 
        ?? ($_SESSION['username'] ?? null);

    if ($currentMemberName !== null) {
        $stmt = $conn->prepare("SELECT user_id FROM Members WHERE name_or_username = ?");
        $stmt->bind_param("s", $currentMemberName);
        $stmt->execute();
        $stmt->bind_result($currentMemberId);
        $stmt->fetch();
        $stmt->close();
    }
}

// Final guard: must have both an ID and a name
if (!$currentMemberId || !$currentMemberName) {
    die("You must be logged in to access this page.");
}

// Message to show in HTML
$upload_message = "";

// ====================================================================================
// 1) HANDLE DELETE REQUESTS
// ====================================================================================
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_id"])) {
    $delete_id = (int) $_POST["delete_id"];

    // Get filename so we can delete the physical file, but only if it belongs to this member
    $stmt = $conn->prepare("
        SELECT filename 
        FROM Texts 
        WHERE text_id = ? AND member_author = ?
    ");
    $stmt->bind_param("ii", $delete_id, $currentMemberId);
    $stmt->execute();
    $stmt->bind_result($filename_to_delete);
    $has_row = $stmt->fetch();
    $stmt->close();

    if ($has_row) {
        if ($filename_to_delete) {
            $file_path = "../uploads/" . $filename_to_delete;
            if (is_file($file_path)) {
                unlink($file_path); // delete the file from disk
            }
        }

        // Delete DB row (again restricted to this member)
        $stmt = $conn->prepare("
            DELETE FROM Texts 
            WHERE text_id = ? AND member_author = ?
        ");
        $stmt->bind_param("ii", $delete_id, $currentMemberId);
        $stmt->execute();
        $stmt->close();

        $upload_message = "Upload deleted successfully.";
    } else {
        // Either doesn't exist or doesn't belong to this user
        $upload_message = "Cannot delete this upload (not found or not yours).";
    }
}

// ====================================================================================
// 2) HANDLE FILE UPLOADS
// ====================================================================================
elseif ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["textfile"])) {

    $target_dir = "../uploads/";  // real path on disk

    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0775, true);
    }

    $original_name = $_FILES["textfile"]["name"] ?? '';
    $tmp_name      = $_FILES["textfile"]["tmp_name"] ?? '';
    $error_code    = $_FILES["textfile"]["error"] ?? UPLOAD_ERR_NO_FILE;

    $title    = trim($_POST["title"] ?? "");
    $author   = trim($_POST["author"] ?? "");
    // In DB, member_author is the INT user_id of the logged-in member
    $member_author_id = $currentMemberId;
    $date_raw = $_POST["date_published"] ?? "";

    // ------------------------------
    // Basic server-side validation
    // ------------------------------
    $is_valid = true;

    if ($error_code !== UPLOAD_ERR_OK) {
        $upload_message = "Upload error (code: $error_code).";
        $is_valid = false;
    }

    if ($title === "") {
        $upload_message = "Title is required.";
        $is_valid = false;
    }

    // Validate extension (txt or pdf)
    $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
    $allowed = ["txt", "pdf"];
    if (!in_array($ext, $allowed, true)) {
        $upload_message = "Only .txt and .pdf files are allowed. You tried: .$ext";
        $is_valid = false;
    }

    // Validate/normalize date: allow empty (NULL) or YYYY-MM-DD
    if ($date_raw === "") {
        $date_published = null;  // store NULL in DB
    } else {
        $d = DateTime::createFromFormat('Y-m-d', $date_raw);
        $errors = DateTime::getLastErrors();
        if ($d && $errors['warning_count'] === 0 && $errors['error_count'] === 0) {
            $date_published = $d->format('Y-m-d');
        } else {
            $upload_message = "Invalid date format.";
            $is_valid = false;
        }
    }

    // If anything invalid, do NOT move or insert
    if ($is_valid) {
        // Make filename safe & unique
        $safe_name   = time() . "_" . preg_replace("/[^A-Za-z0-9_\.-]/", "_", $original_name);
        $target_file = $target_dir . $safe_name;

        if (move_uploaded_file($tmp_name, $target_file)) {
            // Insert into Texts(title, author, member_author, filename, date_published)
            $stmt = $conn->prepare("
                INSERT INTO Texts (title, author, member_author, filename, date_published)
                VALUES (?, ?, ?, ?, ?)
            ");
            // member_author is INT, others are strings
            $stmt->bind_param("ssiss", $title, $author, $member_author_id, $safe_name, $date_published);
            $stmt->execute();
            $stmt->close();

            $upload_message = "File '" . htmlspecialchars($original_name) . "' uploaded successfully.";
        } else {
            $upload_message = "Error moving uploaded file.";
        }
    }
}

// ====================================================================================
// 3) FETCH DATA TO DISPLAY (ONLY THIS MEMBER'S TEXTS)
// ====================================================================================
$stmt = $conn->prepare("
    SELECT text_id, title, author, member_author, filename, popularity, date_published, uploaded_at
    FROM Texts
    WHERE member_author = ?
    ORDER BY uploaded_at DESC
");
$stmt->bind_param("i", $currentMemberId);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Text / PDF Upload System</title>
    <link rel="stylesheet" href="../style/main.css">
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        input, select { margin: 5px 0; padding: 5px; }
        .msg { margin-top: 15px; font-weight: bold; }
        .msg.ok { color: green; }
        .msg.err { color: red; }
        form.inline { display: inline; }
.back-btn {
    display: inline-block;
    padding: 10px 18px;
    background: #007bff;
    color: white;
    border-radius: 6px;
    text-decoration: none;
    font-weight: bold;
    margin-top: 10px;
    margin-bottom: 25px;
}
.back-btn:hover {
    background: #0056b3;
}

    </style>
</head>
<body>
<?php include_once("../components/navbar.php"); ?>

<div class="section-container" style="padding: var(--navHeight); display: flex; flex-direction: column;">
    <div class="left">
        <h1>Upload a Text or PDF File</h1>

        <?php if ($upload_message !== ""): ?>
            <div class="msg <?php echo (strpos($upload_message, 'successfully') !== false || strpos($upload_message, 'deleted') !== false) ? 'ok' : 'err'; ?>">
                <?php echo htmlspecialchars($upload_message); ?>
            </div>
        <?php endif; ?>

        <form action="TextUpload.php" method="post" enctype="multipart/form-data" style="margin: auto;width: fit-content;">
            <label>Title:</label><br>
            <input type="text" name="title" required><br>

            <label>Author:</label><br>
            <input type="text" name="author"><br>

            <!-- Show the logged-in member name (no need to type it) -->
            <label>Member Author (your name):</label><br>
            <input type="text" value="<?php echo htmlspecialchars($currentMemberName); ?>" disabled><br>

            <label>Date Published:</label><br>
            <input type="date" name="date_published"><br>

            <label>Select file (.txt or .pdf):</label><br>
            <input type="file" name="textfile" accept=".txt,.pdf" required><br><br>

            <input type="submit" value="Upload">
        </form>
    </div>

    <div class="right">
        <h2>Uploaded Files</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Author</th>
                <th>Member Author</th>
                <th>Filename</th>
                <th>Popularity</th>
                <th>Date Published</th>
                <th>Uploaded At</th>
                <th>Actions</th>
            </tr>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row["text_id"]; ?></td>
                        <td><?php echo htmlspecialchars($row["title"]); ?></td>
                        <td><?php echo htmlspecialchars($row["author"]); ?></td>
                        <!-- All rows belong to this member, so show their name -->
                        <td><?php echo htmlspecialchars($currentMemberName); ?></td>
                        <td>
                            <a href="../uploads/<?php echo htmlspecialchars($row["filename"]); ?>" target="_blank">
                                <?php echo htmlspecialchars($row["filename"]); ?>
                            </a>
                        </td>
                        <td><?php echo $row["popularity"]; ?></td>
                        <td><?php echo $row["date_published"]; ?></td>
                        <td><?php echo $row["uploaded_at"]; ?></td>
                        <td>
                            <form method="post" class="inline" onsubmit="return confirm('Delete this upload?');">
                                <input type="hidden" name="delete_id" value="<?php echo (int)$row['text_id']; ?>">
                                <button type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            <?php else: ?>
                <tr><td colspan="9">No uploads yet.</td></tr>
            <?php endif; ?>
        </table>
    </div>
</div>
</body>
</html>
<?php
$conn->close();
?>