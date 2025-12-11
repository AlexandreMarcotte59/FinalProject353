<div class="text-viewer">
<?php if (!$file_fs_path || !is_file($file_fs_path)) {
echo "<p class='info-msg'>File not found on server.</p>" . var_dump($file_fs_path);
} else {
        if ($ext === "txt") {
                $content = file_get_contents($file_fs_path);
                echo "<pre style='overflow:scroll;height:90%;'>" . htmlspecialchars($content) . "</pre>";
        } elseif ($ext === "pdf") {
                echo "<iframe
                        style='border-radius:8px;top:8vh;right:10vw;' src='" . htmlspecialchars($file_web_path) . "#toolbar=0&navpanes=0' width='60%' height='90%'> </iframe>";
        } else {
                echo "<p class='info-msg'>Unsupported file type.</p>";
        }
}
?>
</div>