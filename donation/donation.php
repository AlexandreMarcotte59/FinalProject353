<?php
include "../database/db.php";

if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
}
$member_id = $_SESSION['user_id'];

$message = "";

$stmt = $pdo->prepare("
    SELECT u.text_id, u.title
    FROM Texts u
    JOIN Downloads d ON u.text_id = d.text_id
    WHERE d.user_id = ?
    ");
$stmt->execute([$member_id]);
$titles = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("
        SELECT c.char_id, c.name
        FROM Charities c");
//        JOIN SuggestedCharities s ON c.char_id = s.suggestion_id WHERE status='approved'");
$stmt->execute();
$charities = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("
    SELECT d.*, c.name AS charity
    FROM Donations d
    JOIN Charities c ON c.char_id = d.destination
    WHERE d.user_id = ?
    ORDER BY d.date DESC
    ");
$stmt->execute([$member_id]);
$history = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>

<head>
    <title>Make a Donation</title>
    <style>
        body {
            font-family: Arial;
            background: #f5f7fa;
            padding: 20px;
        }

        .container {
            max-width: 700px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
        }

        label {
            font-weight: bold;
            margin-top: 10px;
            display: block;
        }

        input,
        select {
            width: 100%;
            padding: 10px;
            border-radius: 6px;
            margin-top: 5px;
            border: 1px solid #999;
        }

        button {
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
        }

        button:hover {
            background: #0056b3;
        }

        .success {
            background: #d4edda;
            padding: 12px;
            border-left: 4px solid #28a745;
            margin: 10px 0;
        }

        .error {
            background: #f8d7da;
            padding: 12px;
            border-left: 4px solid #dc3545;
            margin: 10px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
        }

        th {
            background: #eee;
        }
    </style> 
    <link rel="stylesheet" href="../style/main.css" />
</head>

<body>
<?php  include("../components/navbar.php"); ?>
    <div class="container">

        <h2>Make a Donation</h2>

        <?php if (isset($_GET['success'])): ?>
            <div class="success">Donation Successful!</div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="error"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>

        <form action="processDonation.php" method="POST" onsubmit="return validateForm();">

            <label>Select Title</label>
            <select name="text_id" required>
                <?php foreach ($titles as $t): ?>
                    <option value="<?= $t['text_id'] ?>"><?= htmlspecialchars($t['title']) ?></option>
                <?php endforeach; ?>
            </select>

            <label>Donation Amount ($)</label>
            <input type="number" name="amount" min="1" required>

            <label>Select Charity</label>
            <select name="charity_id" default="<?= $selected_charity ?: 'None' ?>" required>
                <?php foreach ($charities as $c): ?>
                    <option value="<?= $c['charity_id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                <?php endforeach; ?>
            </select>

            <label>Charity % (min 60%)</label>
            <input type="number" id="charity" name="charity_percent" min="60" max="100" required>

            <label>Author %</label>
            <input type="number" id="author" name="author_percent" min="0" max="100" required>

            <label>CFP %</label>
            <input type="number" id="cfp" name="cfp_percent" min="0" max="100" required>

            <button name="donate">Submit Donation</button>
        </form>

        <h2>My Donation History</h2>

        <table>
            <tr>
                <th>Title</th>
                <th>Amount</th>
                <th>Charity</th>
                <th>Date</th>
                <th>Breakdown</th>
            </tr>

            <?php foreach ($history as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td>$<?= $row['quantity'] ?></td>
                    <td><?= htmlspecialchars($row['charity']) ?></td>
                    <td><?= $row['date'] ?></td>
                    <td>
                        Charity: <?= $row['charity_percent'] ?>%<br>
                        Author: <?= $row['author_percent'] ?>%<br>
                        CFP: <?= $row['cfp_percent'] ?>%
                    </td>
                </tr>
            <?php endforeach; ?>

        </table>

    </div>

    <script>
        function validateForm() {
            let c = parseInt(document.getElementById("charity").value);
            let a = parseInt(document.getElementById("author").value);
            let f = parseInt(document.getElementById("cfp").value);

            if (c < 60) {
                alert("Charity % must be at least 60%");
                return false;
            }
            if (c + a + f !== 100) {
                alert("Percentages must total 100%");
                return false;
            }
            return true;
        }
    </script>

</body>

</html>