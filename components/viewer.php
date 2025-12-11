<div class="text-viewer">
    <?php if ($selected_text): ?>
<?php if (!$file || !is_file($file)) {
echo "<p class='info-msg'>File not found on server.</p>";
} else {
        if ($ext === "txt") {
                $content = file_get_contents($file);
                echo "<pre style='overflow:scroll;height:90%;'>" . htmlspecialchars($content) . "</pre>";
        } elseif ($ext === "pdf") {
                echo "<iframe
                        style='border-radius:8px;top:8vh;right:10vw;' src='" . htmlspecialchars($filepath) . "#toolbar=0&navpanes=0' width='60%' height='90%'> </iframe>";
        } else {
                echo "<p class='info-msg'>Unsupported file type.</p>";
        }
}
?>
    <?php endif ?>
</div>