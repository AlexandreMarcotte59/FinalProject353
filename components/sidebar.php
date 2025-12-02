<div class="modal-container sidebar">
    <form class="" method="POST" action="/components/sidebar.php">
        <input type="hidden" name="userID" value="<?=  $user_id ?>">
        <h3><a href="/">CopyForward</a></h3>
        <?php if ($_SESSION["user_id"]): ?>
        <a href="/pages/MyEmail.php">My Email</a>
        <a href="/pages/MyContents.php">My Contents</a>
        <a href="/pages/MyCommittees.php">My Committees</a>
        <span class="separator"><hr/>*<hr/></span>
        <a href="/pages/ManageCommittees.php">Manage Committees</a>
        <a href="/pages/BrowseCharities.php">Browse Charities</a>
        <span class="separator"><hr/>*<hr/></span>
        <a href="/pages/TextUpload.php">Upload Text</a>
        <a href="/pages/CreateCommittee.php">Create a Committee</a>
        <?php endif ?>
        <a href="/pages/FAQ.php">FAQs</a>
        
        <?php if ($_SESSION["user_id"]): ?>
        <p>My Download Limit: <?php  
            echo $_SESSION["download_count"] . " "; 
            if (isset($_SESSION["download_limit"])) {
                echo $_SESSION["download_limit"];
            } else { echo " / 1"; }
        ?></p>
        <?php endif ?>

    </form>
</div>