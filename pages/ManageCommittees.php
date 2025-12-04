<?php
session_start();
require_once "./components/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$currentUserId = (int) $_SESSION['user_id'];

$errors = [];
$success_message = "";
$info_message = "";

$editCommitteeId = isset($_GET['edit_id']) ? (int) $_GET['edit_id'] : null;

// ------------------ Handle POST actions ------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $committee_id = isset($_POST['committee_id']) ? (int) $_POST['committee_id'] : 0;

    // 1) Member requests to join a committee
    if ($action === 'request_join') {
        if ($committee_id > 0) {
            try {
                // Already a member?
                $stmt = $pdo->prepare("
                    SELECT 1
                    FROM CommitteeVolunteers
                    WHERE committee_id = :c AND volunteer_id = :u
                ");
                $stmt->execute([':c' => $committee_id, ':u' => $currentUserId]);

                if ($stmt->fetch()) {
                    $info_message = "You are already a member of this committee.";
                } else {
                    // Already have a pending request?
                    $stmt = $pdo->prepare("
                        SELECT status
                        FROM CommitteeJoinRequests
                        WHERE committee_id = :c AND user_id = :u AND status = 'pending'
                    ");
                    $stmt->execute([':c' => $committee_id, ':u' => $currentUserId]);
                    if ($stmt->fetch()) {
                        $info_message = "You already have a pending request for this committee.";
                    } else {
                        $stmtIns = $pdo->prepare("
                            INSERT INTO CommitteeJoinRequests (committee_id, user_id)
                            VALUES (:c, :u)
                        ");
                        $stmtIns->execute([':c' => $committee_id, ':u' => $currentUserId]);
                        $success_message = "Join request sent to the committee owner.";
                    }
                }
            } catch (Exception $e) {
                $errors[] = "Error sending join request: " . $e->getMessage();
            }
        }

    // 2) Creator approves a join request
    } elseif ($action === 'approve_request') {
        $request_id = isset($_POST['request_id']) ? (int) $_POST['request_id'] : 0;
        if ($request_id > 0) {
            try {
                // Fetch request + verify current user is creator
                $stmt = $pdo->prepare("
                    SELECT r.request_id, r.committee_id, r.user_id, r.status,
                           c.created_by
                    FROM CommitteeJoinRequests r
                    JOIN Committees c ON r.committee_id = c.committee_id
                    WHERE r.request_id = :r AND r.status = 'pending'
                ");
                $stmt->execute([':r' => $request_id]);
                $req = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$req) {
                    $errors[] = "Request not found or already processed.";
                } elseif ((int)$req['created_by'] !== $currentUserId) {
                    $errors[] = "You are not allowed to approve this request.";
                } else {
                    $pdo->beginTransaction();

                    // Add user as member if not already
                    $stmtCheck = $pdo->prepare("
                        SELECT 1
                        FROM CommitteeVolunteers
                        WHERE committee_id = :c AND volunteer_id = :u
                    ");
                    $stmtCheck->execute([
                        ':c' => $req['committee_id'],
                        ':u' => $req['user_id']
                    ]);

                    if (!$stmtCheck->fetch()) {
                        $stmtIns = $pdo->prepare("
                            INSERT INTO CommitteeVolunteers (committee_id, volunteer_id)
                            VALUES (:c, :u)
                        ");
                        $stmtIns->execute([
                            ':c' => $req['committee_id'],
                            ':u' => $req['user_id']
                        ]);
                    }

                    // Mark request as approved
                    $stmtUpd = $pdo->prepare("
                        UPDATE CommitteeJoinRequests
                        SET status = 'approved'
                        WHERE request_id = :r
                    ");
                    $stmtUpd->execute([':r' => $request_id]);

                    $pdo->commit();
                    $success_message = "Join request approved.";
                }

            } catch (Exception $e) {
                $pdo->rollBack();
                $errors[] = "Error approving request: " . $e->getMessage();
            }
        }

    // 3) Creator denies a join request
    } elseif ($action === 'deny_request') {
        $request_id = isset($_POST['request_id']) ? (int) $_POST['request_id'] : 0;
        if ($request_id > 0) {
            try {
                $stmt = $pdo->prepare("
                    SELECT r.request_id, r.committee_id, r.user_id, r.status,
                           c.created_by
                    FROM CommitteeJoinRequests r
                    JOIN Committees c ON r.committee_id = c.committee_id
                    WHERE r.request_id = :r AND r.status = 'pending'
                ");
                $stmt->execute([':r' => $request_id]);
                $req = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$req) {
                    $errors[] = "Request not found or already processed.";
                } elseif ((int)$req['created_by'] !== $currentUserId) {
                    $errors[] = "You are not allowed to deny this request.";
                } else {
                    $stmtUpd = $pdo->prepare("
                        UPDATE CommitteeJoinRequests
                        SET status = 'denied'
                        WHERE request_id = :r
                    ");
                    $stmtUpd->execute([':r' => $request_id]);

                    $info_message = "Join request denied.";
                }
            } catch (Exception $e) {
                $errors[] = "Error denying request: " . $e->getMessage();
            }
        }

    // 4) Creator deletes committee
    } elseif ($action === 'delete') {
        if ($committee_id > 0) {
            try {
                $stmt = $pdo->prepare("
                    SELECT created_by FROM Committees WHERE committee_id = :c
                ");
                $stmt->execute([':c' => $committee_id]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$row) {
                    $errors[] = "Committee not found.";
                } elseif ((int)$row['created_by'] !== $currentUserId) {
                    $errors[] = "You are not allowed to delete this committee.";
                } else {
                    $pdo->beginTransaction();

                    $pdo->prepare("
                        DELETE FROM CommitteeVolunteers WHERE committee_id = :c
                    ")->execute([':c' => $committee_id]);

                    $pdo->prepare("
                        DELETE FROM CommitteeJoinRequests WHERE committee_id = :c
                    ")->execute([':c' => $committee_id]);

                    $pdo->prepare("
                        DELETE FROM Committees WHERE committee_id = :c
                    ")->execute([':c' => $committee_id]);

                    $pdo->commit();
                    $success_message = "Committee deleted successfully.";
                }
            } catch (Exception $e) {
                $pdo->rollBack();
                $errors[] = "Error deleting committee: " . $e->getMessage();
            }
        }

    // 5) Creator edits committee (subject + purpose)
    } elseif ($action === 'save_edit') {
        $newSubject = trim($_POST['subject'] ?? '');
        $newPurpose = trim($_POST['purpose'] ?? '');

        if ($committee_id <= 0) {
            $errors[] = "Invalid committee.";
        }
        if ($newSubject === '') {
            $errors[] = "Committee title is required.";
        }
        if ($newPurpose === '') {
            $errors[] = "Committee purpose is required.";
        }

        if (empty($errors)) {
            try {
                $stmt = $pdo->prepare("
                    SELECT created_by FROM Committees WHERE committee_id = :c
                ");
                $stmt->execute([':c' => $committee_id]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$row) {
                    $errors[] = "Committee not found.";
                } elseif ((int)$row['created_by'] !== $currentUserId) {
                    $errors[] = "You are not allowed to edit this committee.";
                } else {
                    $stmtUpd = $pdo->prepare("
                        UPDATE Committees
                        SET subject = :s, purpose = :p
                        WHERE committee_id = :c
                    ");
                    $stmtUpd->execute([
                        ':s' => $newSubject,
                        ':p' => $newPurpose,
                        ':c' => $committee_id
                    ]);

                    $success_message = "Committee updated successfully.";
                    $editCommitteeId = null;
                }
            } catch (Exception $e) {
                $errors[] = "Error editing committee: " . $e->getMessage();
            }
        }
    }
}

// ------------------ Fetch data for display ------------------

// All committees
$stmtCommittees = $pdo->query("
    SELECT c.committee_id,
           c.subject,
           c.purpose,
           c.created_by,
           m.name_or_username AS creator_name
    FROM Committees c
    JOIN Members m ON c.created_by = m.user_id
    ORDER BY c.committee_id DESC
");
$committees = $stmtCommittees->fetchAll(PDO::FETCH_ASSOC);

// Committees current user belongs to
$stmtMy = $pdo->prepare("
    SELECT committee_id
    FROM CommitteeVolunteers
    WHERE volunteer_id = :u
");
$stmtMy->execute([':u' => $currentUserId]);
$myCommitteeIds = array_map('intval', $stmtMy->fetchAll(PDO::FETCH_COLUMN, 0));

// Committees where current user has a pending request
$stmtPendingMine = $pdo->prepare("
    SELECT committee_id
    FROM CommitteeJoinRequests
    WHERE user_id = :u AND status = 'pending'
");
$stmtPendingMine->execute([':u' => $currentUserId]);
$myPendingRequests = array_map('intval', $stmtPendingMine->fetchAll(PDO::FETCH_COLUMN, 0));

// Pending requests for committees created by current user
$stmtRequests = $pdo->prepare("
    SELECT r.request_id,
           r.committee_id,
           r.user_id,
           r.request_date,
           u.name_or_username AS requester_name,
           c.subject
    FROM CommitteeJoinRequests r
    JOIN Members u ON r.user_id = u.user_id
    JOIN Committees c ON r.committee_id = c.committee_id
    WHERE c.created_by = :owner AND r.status = 'pending'
    ORDER BY r.request_date ASC
");
$stmtRequests->execute([':owner' => $currentUserId]);
$pendingRequests = $stmtRequests->fetchAll(PDO::FETCH_ASSOC);

// If editing, fetch committee info
$editCommittee = null;
if ($editCommitteeId !== null) {
    $stmtEd = $pdo->prepare("
        SELECT c.committee_id,
               c.subject,
               c.purpose,
               c.created_by,
               m.name_or_username AS creator_name
        FROM Committees c
        JOIN Members m ON c.created_by = m.user_id
        WHERE c.committee_id = :c
    ");
    $stmtEd->execute([':c' => $editCommitteeId]);
    $editCommittee = $stmtEd->fetch(PDO::FETCH_ASSOC);

    if (!$editCommittee || (int)$editCommittee['created_by'] !== $currentUserId) {
        $editCommittee = null;
    }
}

// Member list per committee (for display)
$membersByCommittee = [];
$stmtMembersForOne = $pdo->prepare("
    SELECT m.name_or_username
    FROM CommitteeVolunteers cv
    JOIN Members m ON cv.volunteer_id = m.user_id
    WHERE cv.committee_id = :c
    ORDER BY m.name_or_username ASC
");

foreach ($committees as $c) {
    $stmtMembersForOne->execute([':c' => $c['committee_id']]);
    $membersByCommittee[$c['committee_id']] = $stmtMembersForOne->fetchAll(PDO::FETCH_COLUMN, 0);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Committees</title>
    <link rel="stylesheet" href="./style/main.css">
    <style>
        .page-container {
            max-width: 1000px;
            margin: 30px auto;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        h1 { margin-top: 0; }
        .btn {
            display: inline-block;
            padding: 6px 12px;
            background: #007bff;
            color: white;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.9rem;
        }
        .btn:hover { background: #0056b3; }
        .btn-secondary { background: #6c757d; }
        .btn-secondary:hover { background: #5a6268; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
        .btn-small { padding: 4px 8px; font-size: 0.8rem; }
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
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }
        th, td {
            padding: 8px;
            border: 1px solid #dee2e6;
            text-align: left;
        }
        th { background: #e9ecef; }
        .badge {
            display: inline-block;
            padding: 3px 7px;
            border-radius: 4px;
            font-size: 0.8rem;
            background: #28a745;
            color: #fff;
        }
        .form-group { margin-bottom: 15px; }
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
        .pending-box {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 10px;
            margin-bottom: 20px;
        }
        .pending-item {
            border-bottom: 1px solid #eee;
            padding: 6px 0;
        }
        .pending-item:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>

<div class="navbar">
    <a href="Home.php" class="btn btn-secondary">← Back to Home</a>
</div>

<div class="page-container">
    <h1>Manage Committees</h1>

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

    <?php if ($info_message): ?>
        <div class="alert alert-info">
            <?= htmlspecialchars($info_message) ?>
        </div>
    <?php endif; ?>

    <!-- Pending join requests for committees you own -->
    <?php if (!empty($pendingRequests)): ?>
        <h2>Pending Join Requests for Your Committees</h2>
        <div class="pending-box">
            <?php foreach ($pendingRequests as $r): ?>
                <div class="pending-item">
                    <strong><?= htmlspecialchars($r['requester_name']); ?></strong>
                    requested to join
                    <em><?= htmlspecialchars($r['subject']); ?></em>
                    on <?= htmlspecialchars($r['request_date']); ?>

                    <form method="post" style="display:inline;">
                        <input type="hidden" name="action" value="approve_request">
                        <input type="hidden" name="request_id" value="<?= (int)$r['request_id']; ?>">
                        <button type="submit" class="btn btn-small">Approve</button>
                    </form>

                    <form method="post" style="display:inline;">
                        <input type="hidden" name="action" value="deny_request">
                        <input type="hidden" name="request_id" value="<?= (int)$r['request_id']; ?>">
                        <button type="submit" class="btn btn-danger btn-small">Deny</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Edit form (only for creator) -->
    <?php if ($editCommittee): ?>
        <h2>Edit Committee: <?= htmlspecialchars($editCommittee['subject']); ?></h2>

        <form method="post" style="margin-bottom: 30px;">
            <input type="hidden" name="action" value="save_edit">
            <input type="hidden" name="committee_id" value="<?= (int)$editCommittee['committee_id']; ?>">

            <div class="form-group">
                <label for="subject">Committee Title</label>
                <input type="text" id="subject" name="subject"
                       value="<?= htmlspecialchars($editCommittee['subject']); ?>" required>
            </div>

            <div class="form-group">
                <label for="purpose">Committee Purpose</label>
                <textarea id="purpose" name="purpose" required><?= htmlspecialchars($editCommittee['purpose']); ?></textarea>
            </div>

            <button type="submit" class="btn">Save Changes</button>
            <a href="ManageCommittees.php" class="btn btn-secondary">Cancel</a>
        </form>
    <?php endif; ?>

    <h2>All Committees</h2>

    <?php if (empty($committees)): ?>
        <p>No committees created yet.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Purpose</th>
                    <th>Creator</th>
                    <th>Members</th>
                    <th>Your Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($committees as $c): ?>
                <?php
                    $cid = (int)$c['committee_id'];
                    $isCreator = ((int)$c['created_by'] === $currentUserId);
                    $isMember = in_array($cid, $myCommitteeIds, true);
                    $hasPending = in_array($cid, $myPendingRequests, true);
                    $membersList = $membersByCommittee[$cid] ?? [];
                ?>
                <tr>
                    <td><?= $cid; ?></td>
                    <td><?= htmlspecialchars($c['subject']); ?></td>
                    <td><?= nl2br(htmlspecialchars($c['purpose'])); ?></td>
                    <td><?= htmlspecialchars($c['creator_name']); ?> (ID: <?= (int)$c['created_by']; ?>)</td>
                    <td>
                        <?php if (empty($membersList)): ?>
                            <em>No members yet</em>
                        <?php else: ?>
                            <?= htmlspecialchars(implode(', ', $membersList)); ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($isMember): ?>
                            <span class="badge">Member</span>
                        <?php elseif ($hasPending): ?>
                            <span class="badge" style="background:#ffc107;color:#000;">Request Pending</span>
                        <?php else: ?>
                            Not a member
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (!$isMember && !$hasPending): ?>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="action" value="request_join">
                                <input type="hidden" name="committee_id" value="<?= $cid; ?>">
                                <button type="submit" class="btn btn-small">Request to Join</button>
                            </form>
                        <?php endif; ?>

                        <?php if ($isCreator): ?>
                            <a href="ManageCommittees.php?edit_id=<?= $cid; ?>"
                               class="btn btn-small">Edit</a>

                            <form method="post"
                                  style="display:inline;"
                                  onsubmit="return confirm('Delete this committee? This cannot be undone.');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="committee_id" value="<?= $cid; ?>">
                                <button type="submit" class="btn btn-danger btn-small">Delete</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</div>

</body>
</html>
