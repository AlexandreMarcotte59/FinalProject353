<?php
include_once( "../database/db.php");
?>

<h2>Compose Message</h2>

<form method="POST" action="send_message.php">

    <label>To:</label><br>
    <select name="recipient_id" required>
        <?php

        $stmt = $pdo->prepare("SELECT user_id, name_or_username FROM Members WHERE user_id != ?");
        $stmt->execute([$_SESSION['user_id']]);
                $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($users as $u):
                                ?>
            <option value="<?= $u['user_id'] ?>">
                <?= htmlspecialchars($u['name_or_username']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <br><br>

    <label>Subject:</label><br>
    <input type="text" name="subject" required><br><br>

    <label>Message:</label><br>
    <textarea name="body" maxlength="2048" rows="10" cols="50" required></textarea><br><br>

    <button type="submit">Send</button>
</form>

<br/>
<button class="closeDialog">Cancel</button>