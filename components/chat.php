<div>
    <div class="chat-viewer" style="display:flex; flex-direction: column;">
         <?php if ($selected_text): ?>
            <div style="background-color: gray; color:white;"><h3><?php echo htmlspecialchars($selected_text["title"]); ?></h3>
            <p>
                <strong>Author:</strong> <?php echo htmlspecialchars($selected_text["author"]); ?><br>
                <strong>Member Author:</strong> <?php echo htmlspecialchars($selected_text["member_author"]); ?><br>
                <strong>Popularity:</strong> <?php echo (int)$selected_text["popularity"]; ?><br>
                <strong>Date Published:</strong> <?php echo htmlspecialchars($selected_text["date_published"]); ?>
            </p>   </div>         
        <?php endif ?>
        <?php foreach ($_SESSION["chat"] as $msg): ?>
            <h3><?php echo htmlspecialchars($msg["title"]); ?></h3>
            <p>
                <strong>Author:</strong> <?php echo htmlspecialchars($msg["author"]); ?><br>
                <strong>Date Published:</strong> <?php echo htmlspecialchars($msg["date_published"]); ?>
            </p>
            <?php endforeach ?>
    </div>
    <div>
        <form>
            <input type="text" placeholder="Comment here..."/>
        </form>
    </div>
</div>