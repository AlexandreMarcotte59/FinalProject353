<?php
session_start();

// Adjust this include path to match your project structure
// If this file is in components/, "../components/db.php" might be needed instead
require_once "../components/db.php"; 

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = (int) $_SESSION['user_id'];

// 1) Committees you CREATED
$stmt_created = $pdo->prepare("
    SELECT committee_id, subject, purpose
    FROM Committee
    WHERE created_by = :u
    ORDER BY committee_id DESC
");
$stmt_created->execute([':u' => $user_id]);
$createdCommittees = $stmt_created->fetchAll(PDO::FETCH_ASSOC);

// 2) Committees you are a MEMBER of (but not creator)
$stmt_member = $pdo->prepare("
    SELECT c.committee_id, c.subject, c.purpose, c.created_by
    FROM CommitteeVolunteers cv
    JOIN Committee c ON cv.committee_id = c.committee_id
    WHERE cv.volunteer_id = :u
    ORDER BY c.committee_id DESC
");
$stmt_member->execute([':u' => $user_id]);
$allMemberships = $stmt_member->fetchAll(PDO::FETCH_ASSOC);

// Filter out the ones you created (so this section is only “other people’s committees”)
$memberCommittees = [];
$createdIds = array_map(fn($c) => (int)$c['committee_id'], $createdCommittees);

foreach ($allMemberships as $c) {
    if (!in_array((int)$c['committee_id'], $createdIds, true)) {
        $memberCommittees[] = $c;
    }
}

// 3) Your pending join requests
$stmt_pending = $pdo->prepare("
    SELECT r.committee_id,
           r.request_date,
           c.subject,
           c.purpose
    FROM CommitteeJoinRequests r
    JOIN Committee c ON r.committee_id = c.committee_id
    WHERE r.user_id = :u AND r.status = 'pending'
    ORDER BY r.request_date ASC
");
$stmt_pending->execute([':u' => $user_id]);
$pendingRequests = $stmt_pending->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Committees</title>
    <link rel="stylesheet" href="../style/main.css">
    <style>
        .section-title {
            margin-top: 25px;
            margin-bottom: 10px;
            font-size: 1.2rem;
            font-weight: bold;
        }
        .committee-card {
            display: block;
            margin-bottom: 8px;
            padding: 8px 10px;
            border-radius: 6px;
            background: #f5f5f5;
        }
        .committee-card button {
            margin-left: 10px;
        }
        .meta {
            font-size: 0.85rem;
            color: #555;
        }
    </style>
</head>
<body>

    <div class="data-container">

        <!-- Committees you created -->
        <div class="section-title">Committees You Created</div>
        <?php if (empty($createdCommittees)): ?>
            <p>You haven’t created any committees yet.</p>
        <?php else: ?>
            <?php foreach ($createdCommittees as $c): ?>
                <div class="committee-card">
                    <strong><?= htmlspecialchars($c['subject']) ?></strong><br>
                    <span class="meta"><?= nl2br(htmlspecialchars($c['purpose'])) ?></span>
                    <!-- If you later create a committee detail page, change the action below -->
                    <!-- Example: CommitteeForum.php?committee_id=... -->
                    <!--
                    <form action="CommitteeForum.php" method="get" style="display:inline-block;">
                        <input type="hidden" name="committee_id" value="<?= (int)$c['committee_id'] ?>">
                        <button type="submit">Open</button>
                    </form>
                    -->
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Committees you are a member of -->
        <div class="section-title">Committees You’re a Member Of</div>
        <?php if (empty($memberCommittees)): ?>
            <p>You are not a member of any committees yet.</p>
        <?php else: ?>
            <?php foreach ($memberCommittees as $c): ?>
                <div class="committee-card">
                    <strong><?= htmlspecialchars($c['subject']) ?></strong><br>
                    <span class="meta"><?= nl2br(htmlspecialchars($c['purpose'])) ?></span>
                    <!-- Same idea: hook this to a committee page if you make one -->
                    <!--
                    <form action="CommitteeForum.php" method="get" style="display:inline-block;">
                        <input type="hidden" name="committee_id" value="<?= (int)$c['committee_id'] ?>">
                        <button type="submit">Open</button>
                    </form>
                    -->
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Your pending join requests -->
        <div class="section-title">Your Pending Join Requests</div>
        <?php if (empty($pendingRequests)): ?>
            <p>You have no pending join requests.</p>
        <?php else: ?>
            <?php foreach ($pendingRequests as $r): ?>
                <div class="committee-card">
                    <strong><?= htmlspecialchars($r['subject']) ?></strong><br>
                    <span class="meta">
                        <?= nl2br(htmlspecialchars($r['purpose'])) ?><br>
                        Requested on: <?= htmlspecialchars($r['request_date']) ?>
                    </span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>
</body>
</html>
