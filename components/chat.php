<div class="chat-viewer">
    <?php if ($selected_text): ?>
        <div class="text-info">
            <h3><?php echo htmlspecialchars($selected_text["title"]); ?></h3>
            <p>
                <strong>Author:</strong> <?php echo htmlspecialchars($selected_text["author"]); ?><br>
                <strong>Uploader:</strong> <?php echo htmlspecialchars($selected_text["uploader"]); ?><br>
                <strong>Popularity:</strong> <?php echo (int)$selected_text["popularity"]; ?><br>
                <strong>Date Published:</strong> <?php echo htmlspecialchars($selected_text["date_published"]); ?>
            </p>
        </div>
    <?php endif ?>
    <div class="chat-history" id="chat-history">
    </div>
    <div class="chat-input">
        <form method="POST" action="" style="display:flex; flex-direction: row;">
            <input type="hidden" name="id" value="<?= $t_id ?>">
            <input type="textarea" name="comment" placeholder="Comment here..."  id="comment_text" />
            <button id="post_comment">🔺</button>
        </form>
    </div>

</div>
<script>
    function replyChat(chat) {

    }

    function loadComments() {
        fetch('../components/load_comments.php?text_id=' + <?=$t_id?>)
            .then(res => res.text())
            .then(html => {
                document.getElementById('chat-history').innerHTML = html;
            });
    }
    document.getElementById('post_comment')?.addEventListener('click', () => {
        const comment = document.getElementById('comment_text').value.trim();
        if (!comment) return alert('Comment cannot be empty.');

        fetch('../components/post_comment.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'text_id=' + <?=$t_id?> + '&comment=' + encodeURIComponent(comment)
        })
        .then(res => res.text())
        .then(text => {
            document.getElementById('comment_text').value = '';
            loadComments(); 
        });
    });

    loadComments();
</script>