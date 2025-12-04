<?php
include_once('../database/db.php');

// Require login: accept either numeric user_id or member_name set by login code
//Redirect to login page if not logged in
if (empty($_SESSION['user_id']) && empty($_SESSION['member_name'])) {
    header('Location: MemberLogin.php');
    exit;
}

// Determine lookup column and value (where in table )
if (!empty($_SESSION['user_id'])) {
    $whereCol = 'user_id';
    $whereVal = (int)$_SESSION['user_id'];
} else {
    // fallback to name-based session
    $whereCol = 'name_or_username';
    $whereVal = $_SESSION['member_name'];
}

//empty error and success messages used later
$error = '';
$success = '';

// Handle update and delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // If the form requested deletion, remove the member row and log out
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        try {
            // Remove the member row (use transaction to be safe)
            $pdo->beginTransaction();

            // NOTE: If other tables reference Members.user_id you may need to delete dependent rows first
            $sql = "DELETE FROM Members WHERE {$whereCol} = ? LIMIT 1";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$whereVal]);

            $pdo->commit();

            // Destroy session and redirect to Home
            session_unset();
            session_destroy();
            header('Location: Home.php');
            exit;
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) { $pdo->rollBack(); }
            $error = 'Database error: ' . $e->getMessage();
        }
    } else {
        $name = trim($_POST['member_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $org = trim($_POST['organization'] ?? '');
        $addr = trim($_POST['address'] ?? '');
        $ref = trim($_POST['referral'] ?? '');

        //Referral is optional for now, may be changed later
        if ($name === '' || $email === '' || $org === '' || $addr === '') {
            $error = 'All fields except referral are required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Invalid email address.';
        } else {
            try {
                $sql = "UPDATE Members
                        SET name_or_username = ?, recovery_email = ?, organization = ?, address = ?, referral_code = ?
                        WHERE {$whereCol} = ?
                        LIMIT 1";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$name, $email, $org, $addr, $ref, $whereVal]);
                $success = 'Profile updated.';
            } catch (PDOException $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        }
    }
}

// Load current member data
try {
    $sql = "SELECT user_id, recovery_email, name_or_username, organization, address, referral_code, is_admin, verification_token
            FROM Members
            WHERE {$whereCol} = ?
            LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$whereVal]);
    $member = $stmt->fetch();
    if (!$member) {
        // session refers to missing member
        session_destroy();
        header('Location: MemberLogin.php');
        exit;
    }
} catch (PDOException $e) {
    die('DB error: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Edit Member Profile</title>
    <style>
        body { font-family: Arial, sans-serif; background:#f7f7f7; padding:20px; }
        .profile-editor > form { background:#fff; padding:16px; border-radius:6px; max-width:600px; }
        input[type=text], input[type=email] { width:100%; padding:8px; margin:6px 0; box-sizing:border-box; }
        input, textarea {border-radius: 8px; border: none;}
        textarea {
                width:100%;
                padding:8px;
                margin:6px 0;
                font-family: monospace;
        } textarea[readonly] {  color: grey;background-color: lightgrey;}
        .profile-editor button { padding:8px 14px; border-radius: 8px; border: none;}
        .profile-editor button:hover {filter: brightness(75%);}
        .message { color:green; }
        .error { color:red; }
        .meta { margin:10px 0; font-size:0.95em; color:#333; }
        .profile-editor{display: flex; flex-direction: column;}
        .profile-editor .footer {display: flex; justify-content: space-between;}
    </style>
</head>
<body>

<h2>Edit Profile</h2>

<?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="message"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<form method="post" action="">
    <label>Member Name</label>
    <input type="text" name="member_name" value="<?= htmlspecialchars($member['name_or_username']) ?>" required>

    <label>Recovery Email</label>
    <input type="email" name="email" value="<?= htmlspecialchars($member['recovery_email']) ?>" required>

    <label>Organization</label>
    <input type="text" name="organization" value="<?= htmlspecialchars($member['organization']) ?>" required>

    <label>Address</label>
    <input type="text" name="address" value="<?= htmlspecialchars($member['address']) ?>" required>

    <div class="meta">
        <strong>Admin:</strong> <?= !empty($member['is_admin']) ? 'Yes' : 'No' ?>
        <?php if (!empty($_SESSION['is_admin'])): ?>
            <br><em>You are currently logged in as an administrator.</em>
        <?php endif; ?>
    </div>

    <label>Referral Code (read-only)</label><br/><sub style="color:grey">*Used to invite new members</sub>
    <textarea name="referral" readonly disabled><?= htmlspecialchars($member['referral_code']) ?></textarea>


    <label>Verification Matrix (read-only)</label>
    <textarea rows="5" readonly disabled><?= htmlspecialchars($member['verification_token']) ?></textarea>
    <div class="footer">
        <button type="submit">Update</button>

        <!-- Delete account: asks for confirmation, posts action=delete -->
        <form method="post" onsubmit="return confirm('Are you SURE you want to DELETE your account? This cannot be undone.');" style="display:inline">
            <input type="hidden" name="action" value="delete">
            <button type="submit" style="background:#c00;color:#fff;border:none;padding:8px 12px;margin-left:12px">Delete Account</button>
        </form>
    </div>
    <a href="../pages/MemberLogin.php" style="text-align:center;margin:12px">Back / Logout</a>
</form>

</body>
</html>