<script>
    document.querySelectorAll("[data-dialog]").forEach(btn => {
        btn.addEventListener("click", () => {
            const dialog = document.querySelector(`#${btn.dataset.dialog}`);
            dialog.showModal();
            dialog.querySelector(".closeDialog").addEventListener("click", () => dialog.close());
        });
    });
    function modalHandler() {
        const dialog = document.querySelector(`#sidebarFeatures`);
        dialog.showModal();
        dialog.querySelector(".closeDialog").addEventListener("click", () => dialog.close());
    }
</script>
<div class="navbar">
    <div class="left-side"><a id="hamburger" onclick="modalHandler()">≡</a></div>
</div>
<!-- The Modals -->
<dialog id="sidebarFeatures">
    <button class="closeDialog">X</button>
    <?php include "sidebar.php"; ?>
</dialog>