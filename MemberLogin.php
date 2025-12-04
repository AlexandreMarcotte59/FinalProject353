<?php
// MemberLogin.php
//http://localhost/FinalProject353/MemberLogin.php

// --- Configuration and Connection - Connects to concorida DB
define('DB_HOST', 'mvc353.encs.concordia.ca');
define('DB_USER', 'mvc353_2'); // Your MySQL username
define('DB_PASS', 'firstsound58'); // Your MySQL password
define('DB_NAME', 'mvc353_2'); // The name of your database



$dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
     $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (\PDOException $e) {
     die("ERROR: Could not connect to the database. " . $e->getMessage());
}

session_start();
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_name = trim($_POST['member_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $token = trim($_POST['token'] ?? '');

    // Basic validation
    if (empty($member_name) || empty($email) || empty($token)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        try {
            // select stored token and admin flag (do not require token match in SQL)
            $stmt = $pdo->prepare(
                'SELECT user_id, verification_token, is_admin
                 FROM Members
                 WHERE name_or_username = ? AND recovery_email = ?
                 LIMIT 1'
            );
            $stmt->execute([$member_name, $email]);
            $row = $stmt->fetch();

            if ($row) {
                // Normalize: remove all whitespace (spaces, tabs, newlines) so format differences won't break matching
                $stored = preg_replace('/\s+/', '', (string)$row['verification_token']);
                $input  = preg_replace('/\s+/', '', (string)$token);

                if (hash_equals($stored, $input)) {
                    $_SESSION['message'] = "Login successful!";
                    $_SESSION['user_id'] = $row['user_id'];
                    $_SESSION['is_admin'] = !empty($row['is_admin']);
                    if (!empty($row['is_admin'])) {
                        $_SESSION['admin_message'] = "You are logged in as an administrator.";
                    }
                } else {
                    $error = "Invalid name/email/token combination.";
                }
            } else {
                $error = "Invalid name/email/token combination.";
            }
        } catch (PDOException $e) {
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
    <title>Member Login</title>
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
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 320px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            width: 100%;
            background: #007BFF;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
        .message {
            text-align: center;
            margin-top: 10px;
            color: green;
        }
        .error {
            text-align: center;
            margin-top: 10px;
            color: red;
        }
    </style>
</head>
<body>

<form method="POST" action="">
    <h2>Member Login</h2>

    <input type="text" name="member_name" placeholder="Member Name" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="token" placeholder="Verification Token" required>

    <button type="submit">Login</button>

    <?php if (!empty($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php elseif (!empty($_SESSION['message'])): ?>
        <div class="message"><?= htmlspecialchars($_SESSION['message']) ?></div>
        <?php if (!empty($_SESSION['admin_message'])): ?>
            <div class="message"><?= htmlspecialchars($_SESSION['admin_message']) ?></div>
            <?php unset($_SESSION['admin_message']); ?>
        <?php endif; ?>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
</form>

</body>
</html>
