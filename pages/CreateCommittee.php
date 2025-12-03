
<?php
session_start();
require_once "./components/db.php"; // defines $pdo

// 1) Require login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$currentUserId = (int) $_SESSION['user_id'];

$errors = [];
$success_message = "";
$info_message = "";
$subject = "";
$selectedVolunteers = [];

// 2) Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = trim($_POST['subject'] ?? '');
    $selectedVolunteers = $_POST['volunteers'] ?? [];

    // Basic validation
    if ($subject === '') {
        $errors[] = "Committee subject is required.";
    }

    if (empty($selectedVolunteers)) {
        $errors[] = "Please select at least one volunteer.";
    }

    // Optionally ensure the creator is in the committee too
    if (!in_array($currentUserId, $selectedVolunteers, true)) {
        $selectedVolunteers[] = $currentUserId;
        $info_message .= "You were automatically added to the committee as a member.<br>";
    }

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            // 3) Insert into Committee
            $stmtCommittee = $pdo->prepare("
                INSERT INTO Committees (subject)
                VALUES (:subject)
            ");
            $stmtCommittee->execute([':subject' => $subject]);
            $committee_id = (int) $pdo->lastInsertId();

            // 4) Insert volunteers into CommitteeVolunteers with duplicate check
            $alreadyMembers = [];
            $addedMembers = [];

            $stmtCheck = $pdo->prepare("
                SELECT 1 FROM CommitteeVolunteers
                WHERE committee_id = :committee_id AND volunteer_id = :volunteer_id
            ");

            $stmtInsert = $pdo->prepare("
                INSERT INTO CommitteeVolunteers (committee_id, volunteer_id)
                VALUES (:committee_id, :volunteer_id)
            ");

            foreach ($selectedVolunteers as $volunteer_id) {
                $volunteer_id = (int) $volunteer_id;

                // Check if already in committee
                $stmtCheck->execute([
                    ':committee_id' => $committee_id,
                    ':volunteer_id' => $volunteer_id
                ]);

                if ($stmtCheck->fetch()) {
                    $alreadyMembers[] = $volunteer_id;
                    continue;
                }

                // Insert new relation
                $stmtInsert->execute([
                    ':committee_id' => $committee_id,
                    ':volunteer_id' => $volunteer_id
                ]);
                $addedMembers[] = $volunteer_id;
            }

            $pdo->commit();

            // 5) Build feedback messages
            if (!empty($addedMembers)) {
                $success_message = "Committee created successfully!";
            } else {
                $errors[] = "No new members were added. All selected members were already part of this committee.";
            }

            if (!empty($alreadyMembers)) {
                $info_message .= "Some selected members were already in this committee and were skipped.";
            }

            // Reset subject/selection after success
            if ($success_message) {
                $subject = "";
                $selectedVolunteers = [];
            }

        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = "Error creating committee: " . $e->getMessage();
        }
    }
}

// 6) Get all members to show as possible volunteers
$stmtMembers = $pdo->query("SELECT user_id, username FROM Members ORDER BY username ASC");
$members = $stmtMembers->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Committee</title>
    <link rel="stylesheet" href="./style/main.css">

    <style>
        .page-container {
            max-width: 800px;
            margin: 30px auto;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        h1 {
            margin-top: 0;
        }
        .btn {
            display: inline-block;
            padding: 8px 16px;
            background: #007bff;
            color: white;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 600;
        }
        .btn:hover {
            background: #0056b3;
        }
        .btn-secondary {
            background: #6c757d;
        }
        .btn-secondary:hover {
            background: #5a6268;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            font-weight: 600;
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"] {
            width: 100%;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ced4da;
        }
        .volunteer-list {
            max-height: 250px;
            overflow-y: auto;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            background: white;
        }
        .volunteer-item {
            margin-bottom: 5px;
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
        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        .actions {
            margin-top: 15px;
            display: flex;
            gap: 10px;
        }
    </style>
</head>
<body>

<div class="navbar">
    <div class="left-side">
        <a href="Home.php" class="btn btn-secondary">← Back to Home</a>
    </div>
    <div class="right-side">
        <a id="logout" href="logout.php">Log Out</a>
    </div>
</div>

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
            <?= $success_message ?>
        </div>
    <?php endif; ?>

    <?php if ($info_message): ?>
        <div class="alert alert-info">
            <?= $info_message ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label for="subject">Committee Subject</label>
            <input type="text" id="subject" name="subject"
                   value="<?= htmlspecialchars($subject) ?>"
                   required>
        </div>

        <div class="form-group">
            <label>Select Volunteers (members)</label>
            <div class="volunteer-list">
                <?php if (empty($members)): ?>
                    <p>No members available.</p>
                <?php else: ?>
                    <?php foreach ($members as $m): ?>
                        <div class="volunteer-item">
                            <label>
                                <input
                                    type="checkbox"
                                    name="volunteers[]"
                                    value="<?= (int)$m['user_id']; ?>"
                                    <?= in_array($m['user_id'], $selectedVolunteers, true) ? 'checked' : ''; ?>
                                >
                                <?= htmlspecialchars($m['username']); ?> (ID: <?= (int)$m['user_id']; ?>)
                            </label>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="actions">
            <button type="submit" class="btn">Create Committee</button>
            <a href="Home.php" class="btn btn-secondary">Cancel / Back to Home</a>
        </div>
    </form>
</div>

</body>
</html>
