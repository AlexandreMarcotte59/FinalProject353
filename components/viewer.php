<div class="text-viewer">
        <?php if ($selected_text):?>
            <h3><?php echo htmlspecialchars($selected_text["title"]); ?></h3>
            <p>
                <strong>Author:</strong> <?php echo htmlspecialchars($selected_text["author"]); ?><br>
                <strong>Member Author:</strong> <?php echo htmlspecialchars($selected_text["member_author"]); ?><br>
                <strong>Popularity:</strong> <?php echo (int)$selected_text["popularity"]; ?><br>
                <strong>Date Published:</strong> <?php echo htmlspecialchars($selected_text["date_published"]); ?>
            </p>

             <?php
                if (!$file || !is_file($file)) {
                    echo "<p class='info-msg'>File not found on server.</p>";
                } else {
                    if ($ext === "txt") {
                        $content = file_get_contents($file);
                        echo "<pre>" . htmlspecialchars($content) . "</pre>";
                    } elseif ($ext === "pdf") {
                        echo "<iframe 
                        style='position:fixed;border-radius:8px;top:8vh;right:10vw;'
                        src='" . htmlspecialchars($filepath) . "#toolbar=0' width='60%' height='90%'></iframe>";
                    } else {
                        echo "<p class='info-msg'>Unsupported file type.</p>";
                    }
                }
            ?>
            <?php endif ?>
</div>