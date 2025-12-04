<script>
function modalHandler(elemid){
        let dialog = document.querySelector(`#${elemid}`);
        if (!dialog) {
            console.warn("Modal not found:", elemid);
            return false;
        }
        dialog.querySelector(".closeDialog").addEventListener("click", () => dialog.close());
        dialog.showModal();
}
</script>
<div class="navbar">
        <div class="left-side">
                <?php if (isset($_SESSION['user_id'])): ?>
                <a id="hamburger" onclick="modalHandler('sidebarFeatures')">≡</a>
                <?php else: ?>
                <a id="hamburger" onclick="modalHandler('dialogMessage')">≡</a>
                <?php endif ?>
        </div>
        <div class="right-side">
                <?php if (isset($_SESSION['user_id'])): ?>
                <a id="login" onclick="window.location='components/logout.php'">Log Out</a>
                <?php else: ?>
                <a id="login" href="../pages/MemberLogin.php">Log In</a>
                <a id="signup" href="../pages/MemberRegister.php">Sign Up</a>
                <?php endif; ?>
        </div>
</div>


<!-- The Modals -->
<dialog id="sidebarFeatures">
    <button class="closeDialog">X</button>
    <?php include "sidebar.php"; ?>
</dialog>

<dialog id="dialogMessage">
    <button class="closeDialog">X</button>
    <p>You are not signed in. You have no access to extra features.</p>
</dialog>