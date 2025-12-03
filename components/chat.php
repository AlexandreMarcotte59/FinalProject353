<?php
if(isset($_POST["comment"])){
        $stmt_text_chats = $pdo->prepare("INSERT INTO TextComments (text_id, comment) VALUES (?, ?)");
        $stmt_text_chats->execute([$t_id, $_POST["comment"]]);
        unset($_POST["comment"]);
}

?>
<div class="chat-viewer">
    <?php if ($selected_text): ?>
        <div style="padding:5px 15px;border-radius:8px;background-color:hsl(168, 0%, 15%); color:white;">
            <h3><?php echo htmlspecialchars($selected_text["title"]); ?></h3>
            <p>
                <strong>Author:</strong> <?php echo htmlspecialchars($selected_text["author"]); ?><br>
                <strong>Member Author:</strong> <?php echo htmlspecialchars($selected_text["member_author"]); ?><br>
                <strong>Popularity:</strong> <?php echo (int)$selected_text["popularity"]; ?><br>
                <strong>Date Published:</strong> <?php echo htmlspecialchars($selected_text["date_published"]); ?>
            </p> 
        </div>
    <?php endif ?>
    <div class="chat-history">
        <?php foreach ($chat as $msg): ?>
            <div class="chatbubble"><h3><?php echo htmlspecialchars($msg["title"]); ?></h3>
                <p>
                    <strong>Author:</strong> <?php echo htmlspecialchars($msg["author"]); ?><br>
                    <strong>Date Published:</strong> <?php echo htmlspecialchars($msg["date_published"]); ?>
                </p>
            </div>
        <?php endforeach ?>
    </div>
    <div>
        <form method="POST" action="chat.php">
            <input type="text" name="comment" placeholder="Comment here..."/>
        </form>
    </div>
</div>