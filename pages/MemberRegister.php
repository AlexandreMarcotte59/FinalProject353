<?php
// MemberRegister.php
//http://localhost/FinalProject353/MemberRegister.php

// --- Configuration and Connection ---
define('DB_HOST', 'mvc353.encs.concordia.ca');
define('DB_USER', 'mvc353_2'); 
define('DB_PASS', 'firstsound58'); 
define('DB_NAME', 'mvc353_2'); 

$dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];
try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (\PDOException $e) {
    die("ERROR: Could not connect to DB. " . $e->getMessage());
}
session_start();
$success_token = "";
$error = "";
$admin_confirm = "";

/**
 * To add to a member, we need:
 * 
    user_id INT 
    recovery_email 
    name_or_username 
    organization 
    address 
    verification_token 
    download_limit INT DEFAULT 0,
    referral_code 
    is_admin BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES Users(user_id)

 */


// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['member_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $org = trim($_POST['organization'] ?? '');
    $addr = trim($_POST['address'] ?? '');
    $ref = trim($_POST['referral'] ?? '');
    $admin_secret = trim($_POST['admin_secret'] ?? '');

    // Basic validation
    if (empty($name) || empty($email) || empty($org) || empty($addr)) {
        $error = "All fields except referral code are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address.";
    } else {

   
        // --- Create Users row and use its auto-increment id ---
        // Pretty much we need a userID for the member table, so we need to make a user first
        try {
            $pdo->beginTransaction();
            //DEV: NO LONGER NEED TO MAKE A USER
            /**
            // build a username and a random password hash (you can adjust as needed)
            $username_for_users = preg_replace('/\s+/', '_', strtolower($name));
            $random_pw = bin2hex(random_bytes(8));
            $pw_hash = password_hash($random_pw, PASSWORD_DEFAULT);

           insert into Users (parent)
           $stmtUser = $pdo->prepare("INSERT INTO Users (username, password_hash, email) VALUES (?, ?, ?)");
           $stmtUser->execute([$username_for_users, $pw_hash, $email]);
           */
            // Members.user_id is an auto-increment primary key (no Users table). Do not set/insert user_id manually.

            // Generate verification token: 5 rows of 5 random characters
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            $lines = [];
            $maxIndex = strlen($chars) - 1;
            for ($i = 0; $i < 5; $i++) {
                $line = '';
                for ($j = 0; $j < 5; $j++) {
                    $line .= $chars[random_int(0, $maxIndex)];
                }
                $lines[] = $line;
            }
            $token = implode("\n", $lines);

            // determine admin flag from optional secret
            $is_admin = ($admin_secret === 'firstsound58') ? 1 : 0;

            // insert into Members (auto-increment user_id)
            // TEMPORARY WORKAROUND: manually assign user_id because the column is not AUTO_INCREMENT.
            // TODO: Fix the schema so Members.user_id is AUTO_INCREMENT and remove this manual increment.
            $stmt = $pdo->query("SELECT COALESCE(MAX(user_id), 0) AS maxid FROM Members FOR UPDATE");
            $row = $stmt->fetch();
            $next_user_id = ((int)($row['maxid'] ?? 0)) + 1;

            $sql = "INSERT INTO Members
                    (user_id, recovery_email, name_or_username, organization, address, verification_token, referral_code, is_admin)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$next_user_id, $email, $name, $org, $addr, $token, $ref, $is_admin]);
            // store the new member primary key we manually assigned
            $new_member_id = $next_user_id;

            $pdo->commit();

            $success_token = $token;
            if ($is_admin) {
                $admin_confirm = "Admin secret accepted — account created as administrator.";
            }
        } catch (Exception $e) {
            if ($pdo->inTransaction()) { $pdo->rollBack(); }
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        form {
            background: white;
            padding: 30px;
            border-radius: 8px;
            width: 350px;
            box-shadow: 0 0 10px rgba(0,0,0,0.15);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        input[type="text"], input[type="email"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            width: 100%;
            background: #28a745;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: #1e7e34;
        }
        .message { color: green; text-align: center; margin-top: 10px; }
        .error { color: red; text-align: center; margin-top: 10px; }
        .token-box {
            background: #e8ffe8;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
            word-break: break-all;
            font-weight: bold;
        }
    </style>
</head>
<body>

<form method="POST">
    <h2>Create Member Account</h2>

    <input type="text" name="member_name" placeholder="Name / Username" required>
    <input type="email" name="email" placeholder="Recovery Email" required>
    <input type="text" name="organization" placeholder="Organization" required>
    <input type="text" name="address" placeholder="Address" required>
    <input type="text" name="referral" placeholder="Referral Code (Optional)">
    <input type="password" name="admin_secret" placeholder="Admin secret (optional)">

    <button type="submit">Register</button>

    <?php if (!empty($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (!empty($success_token)): ?>
        <div class="message">Account created! Your verification token:</div>
        <div class="token-box"><?= nl2br(htmlspecialchars($success_token)) ?></div>
    <?php endif; ?>

    <?php if (!empty($admin_confirm)): ?>
        <div class="message"><?= htmlspecialchars($admin_confirm) ?></div>
    <?php endif; ?>
</form>

</body>
</html>
