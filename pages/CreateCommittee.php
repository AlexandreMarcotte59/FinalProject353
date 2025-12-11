<?php
require_once( "../database/db.php"); // defines $pdo

// 1) Require login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/MemberLogin.php");
    exit;
}

$currentUserId = (int) $_SESSION['user_id'];

$errors = [];
$success_message = "";
$subject = "";
$purpose = "";

// 2) Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = trim($_POST['subject'] ?? '');
    $purpose = trim($_POST['purpose'] ?? '');

    if ($subject === '') {
        $errors[] = "Committee title (subject) is required.";
    }
    if ($purpose === '') {
        $errors[] = "Committee purpose is required.";
    }

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            // 1) Insert into Committee
            $stmtCommittee = $pdo->prepare("
                INSERT INTO Committees (subject, purpose, created_by)
                VALUES (:subject, :purpose, :created_by)
            ");
            $stmtCommittee->execute([
                ':subject'    => $subject,
                ':purpose'    => $purpose,
                ':created_by' => $currentUserId
            ]);
            $committee_id = (int) $pdo->lastInsertId();

            // 2) Auto-add creator as member of the committee
            $stmtInsert = $pdo->prepare("
                INSERT INTO CommitteeVolunteers (committee_id, volunteer_id)
                VALUES (:committee_id, :volunteer_id)
            ");
            $stmtInsert->execute([
                ':committee_id' => $committee_id,
                ':volunteer_id' => $currentUserId
            ]);

            $pdo->commit();
            $success_message = "Committee created successfully!";
            $subject = "";
            $purpose = "";
        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = "Error creating committee: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Committee</title>
    <link rel="stylesheet" href="../style/main.css">

    <style>
        .page-container {
            max-width: 800px;
            margin: 15dvh auto;
            padding: 5dvh;
            background: #f8f9fa;
            border-radius: 8px;
        }
        h1 { margin-top: 0; }
        .btn {
            display: inline-block;
            padding: 8px 16px;
            background: #007bff;
            color: white;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 600;
        }
        .btn:hover { background: #0056b3; }
        .btn-secondary {
            background: #6c757d;
        }
        .btn-secondary:hover { background: #5a6268; }
        .form-group { margin-bottom: 15px; }
        label {
            font-weight: 600;
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ced4da;
        }        
        textarea {
            min-height: 120px;
            resize: vertical;
        }
        .alert {
            padding: 10px 12px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .actions {
            margin-top: 15px;
            display: flex;
            gap: 10px;
        }
    </style>
</head>
<body>

    <?php include("../components/navbar.php"); ?>

<div class="page-container">
    <h1>Create a New Committee</h1>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <ul>
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($success_message): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($success_message) ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label for="subject">Committee Title</label>
            <input type="text" id="subject" name="subject"
                   value="<?= htmlspecialchars($subject) ?>" required>
        </div>

        <div class="form-group">
            <label for="purpose">Committee Purpose</label>
            <textarea id="purpose" name="purpose" required><?= htmlspecialchars($purpose) ?></textarea>
        </div>

        <div class="actions">
            <button type="submit" class="btn">Create Committee</button>
            <a href="<?= BASE_URL?>/" class="btn btn-secondary">Cancel / Back to Home</a>
        </div>
    </form>
</div>

</body>
</html>
