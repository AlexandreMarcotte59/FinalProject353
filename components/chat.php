<script>
    function replyChat(chat) {

    }
</script>
<div class="chat-viewer">
    <?php if ($selected_text): ?>
        <div style="padding:5px 15px;border-radius:8px;background-color:hsl(168, 0%, 15%); color:white;">
            <h3><?php echo htmlspecialchars($selected_text["title"]); ?></h3>
            <p>
                <strong>Author:</strong> <?php echo htmlspecialchars($selected_text["author"]); ?><br>
                <strong>Member Author:</strong> <?php echo htmlspecialchars($selected_text["member_author"]); ?><br>
                <strong>Popularity:</strong> <?php echo (int) $selected_text["popularity"]; ?><br>
                <strong>Date Published:</strong> <?php echo htmlspecialchars($selected_text["date_published"]); ?>
            </p>
        </div>
    <?php endif ?>
    <div class="chat-history">
        <?php if ($chats): ?>
            <?php foreach ($chats as $msg): ?>
                <div class="chatbubble" id='comment<?= $msg['comment_id'] ?>'>
                    <div style="display:flex;flex-direction:column;">
                        <p><?= htmlspecialchars($msg["comment"]) . $msg['comment_id']; ?></p>
                        <sup>date published: <?= htmlspecialchars($msg["date_published"]); ?></sup>
                    </div>
                    <?php if ($msg['reader_id'] == $_SESSION['user_id'] || $_SESSION['is_admin']): ?>
                        <form method="POST" action="" id='form<?= $msg['comment_id'] ?>' onsubmit="return confirm('Are you sure?')">
                            <input type="hidden" name="id" value="<?= $t_id ?>">
                            <input type="hidden" name="comment_id" value="<?= $msg['comment_id'] ?>">
                            <button type="submit">X</button>
                        </form>
                    <?php endif ?>
                    <?php if ($_SESSION['is_admin']): ?>
                        <form><button onclick="replyChat()">Reply</button></form>
                    <?php endif ?>
                </div>
            <?php endforeach ?>
        <?php endif ?>
    </div>
    <div>
        <form method="POST" action="" style="display:flex; flex-direction: row;">
            <input type="hidden" name="id" value="<?= $t_id ?>">
            <input type="text" name="comment" placeholder="Comment here..." />
            <button type="submit">Send</button>
        </form>
    </div>

</div>