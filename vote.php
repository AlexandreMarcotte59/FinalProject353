<?php
session_start();

$servername="mvc353.encs.concordia.ca";
$username="mvc353_2";
$password="firstsound58";
$dbname="mvc353_2";

$conn=new mysqli($servername,$username,$password,$dbname);
if($conn->connect_error) die("Connection failed: ".$conn->connect_error);

if(!isset($_SESSION["user_id"])){
    echo "<p>You must be logged in to vote.</p>";
    exit;
}

$user_id = $_SESSION["user_id"];


$committees = $conn->query("SELECT committee_id, subject FROM Committee");


$texts = $conn->query("SELECT text_id, title FROM Texts WHERE is_blacklisted = 0");


if($_SERVER["REQUEST_METHOD"] == "POST"){

    $committee_id = $_POST["committee_id"];
    $text_id = $_POST["text_id"];
    $result = $_POST["result"];

    
    $check = $conn->prepare("
        SELECT 1 FROM CommitteeVolunteers
        WHERE committee_id = ? AND volunteer_id = ?
    ");
    $check->bind_param("ii", $committee_id, $user_id);
    $check->execute();
    $vol = $check->get_result();

    if($vol->num_rows == 0){
        echo "<p style='color:red;'>You are NOT a member of this committee.</p>";
        exit;
    }

    
    $checkVote = $conn->prepare("
        SELECT 1 FROM Vote
        WHERE plagiarized_item = ? AND voter_id = ?
    ");
    $checkVote->bind_param("ii", $text_id, $user_id);
    $checkVote->execute();
    $voteRes = $checkVote->get_result();

    if($voteRes->num_rows > 0){
        echo "<p style='color:red;'>You already voted on this text.</p>";
        exit;
    }

    
    $stmt = $conn->prepare("
        INSERT INTO Vote (plagiarized_item, result, voter_id)
        VALUES (?, ?, ?)
    ");
    $stmt->bind_param("isi", $text_id, $result, $user_id);

    if($stmt->execute()){
        echo "<p style='color:green;'>Vote recorded successfully!</p>";
    } else {
        echo "<p style='color:red;'>Error saving vote.</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Committee Voting</title>

<style>
    body {
        font-family: Arial;
        margin: 25px;
    }
    h2 {
        font-size: 26px;
        font-weight: bold;
    }
    label {
        font-size: 18px;
        font-weight: bold;
        display: block;
        margin-top: 15px;
    }

    select, input[type="number"] {
        width: 300px;
        padding: 10px;
        font-size: 16px;
        border: 2px solid #555;
        border-radius: 6px;
        margin-bottom: 10px;
    }

    button {
        margin-top: 15px;
        padding: 10px 20px;
        font-size: 16px;
        border-radius: 6px;
        background-color: #007bff;
        color: white;
        border: none;
        cursor: pointer;
    }
    button:hover {
        background-color: #0056b3;
    }

    table {
        border-collapse: collapse;
        margin-top: 20px;
        font-size: 16px;
    }

    th, td {
        padding: 10px 15px;
        border: 1px solid #333;
    }

    th {
        background-color: #eee;
    }
</style>

</head>
<body>

<h2>Committee Voting</h2>

<form method="POST">

    <label>Committee:</label>
    <select name="committee_id" required>
        <option value="">-- Select Committee --</option>
        <?php while($c = $committees->fetch_assoc()): ?>
            <option value="<?= $c['committee_id'] ?>"><?= $c['subject'] ?></option>
        <?php endwhile; ?>
    </select>

    <label>Text:</label>
    <select name="text_id" required>
        <option value="">-- Select Text --</option>
        <?php while($t = $texts->fetch_assoc()): ?>
            <option value="<?= $t['text_id'] ?>"><?= $t['title'] ?></option>
        <?php endwhile; ?>
    </select>

    <label>Your Vote:</label>
    <select name="result">
        <option value="plagiarized">Plagiarized</option>
        <option value="not_plagiarized">Not Plagiarized</option>
    </select>

    <br>
    <button type="submit">Submit Vote</button>

</form>

<hr>

<h2>All Votes</h2>

<table>
<tr>
    <th>ID</th>
    <th>Text</th>
    <th>Result</th>
    <th>Voter</th>
</tr>

<?php
$votes = $conn->query("SELECT * FROM Vote ORDER BY vote_id DESC");
while($row = $votes->fetch_assoc()){
    echo "<tr>
            <td>{$row['vote_id']}</td>
            <td>{$row['plagiarized_item']}</td>
            <td>{$row['result']}</td>
            <td>{$row['voter_id']}</td>
          </tr>";
}
?>
</table>

</body>
</html>
