<?php
include_once("database/db.php");

?>
<!DOCTYPE html>
<html>
<head>
<title>Ask a Question</title>

<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

<style>
body {
    font-family: "Roboto", sans-serif;
    background-color: #f2f4f7;
    margin: 0;
    padding: 0;
}

h1 {
    background: linear-gradient(90deg, #1e88e5, #42a5f5);
    color: white;
    padding: 22px;
    margin: 0;
    text-align: center;
    font-size: 28px;
    font-weight: 500;
    box-shadow: 0px 2px 10px rgba(0,0,0,0.1);
}

.section-title {
    color: #1e4a6d;
    font-size: 22px;
    font-weight: 500;
    border-left: 4px solid #1e88e5;
    padding-left: 12px;
    margin-top: 30px;
    margin-bottom: 15px;
}


.card {
    background: white;
    width: 85%;
    margin: 25px auto;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0px 3px 10px rgba(0,0,0,0.08);
    border: 1px solid #e0e6ed;
}


table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed; 
}

th, td {
    padding: 12px;
    border-bottom: 1px solid #e0e6ed;
    text-align: left;   
    font-size: 14px;
}

th {
    background-color: #e3f2fd;
    color: #0d47a1;
    font-weight: 600;
}

td {
    padding: 10px;
    background-color: white;
    font-size: 14px;
    border-bottom: 1px solid #e0e6ed;
}

tr:hover td {
    background-color: #f5faff;
}


input, textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #c8d6e2;
    border-radius: 6px;
    margin-top: 6px;
    font-size: 14px;
}

button {
    background-color: #1e88e5;
    color: white;
    padding: 10px 18px;
    border: none;
    border-radius: 6px;
    margin-top: 12px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
}

button:hover {
    background-color: #1565c0;
}
</style>
</head>

<body>

<h1>Ask a Question</h1>


<div class="card">
    <div class="section-title">Submit Your Question</div>

    <?php
    if($_SERVER["REQUEST_METHOD"]=="POST"){
        $uid = $_SESSION["user_id"] ?? 1;
        $q = $_POST["question"];

        $stmt = $conn->prepare("INSERT INTO Questions (user_id, question) VALUES (?, ?)");
        $stmt->bind_param("is", $uid, $q);

        echo $stmt->execute()
        ? "<p style='color:green;'>Question submitted successfully!</p>"
        : "<p style='color:red;'>Error submitting question.</p>";

        $stmt->close();
    }
    ?>

    <form method="POST">
        <label>Your Question:</label>
        <textarea name="question" rows="4" required></textarea>

        <button type="submit">Submit Question</button>
    </form>
</div>


<div class="card">
    <div class="section-title">All Questions</div>

    <table>
        <tr><th>ID</th><th>User</th><th>Question</th></tr>

        <?php
        $res = $conn->query("SELECT * FROM Questions ORDER BY question_id DESC");
        while ($r = $res->fetch_assoc()) {
            echo "<tr>
                    <td>{$r['question_id']}</td>
                    <td>{$r['user_id']}</td>
                    <td>".htmlspecialchars($r['question'])."</td>
                 </tr>";
        }
        ?>
    </table>
</div>

<?php $conn->close(); ?>

</body>
</html>
