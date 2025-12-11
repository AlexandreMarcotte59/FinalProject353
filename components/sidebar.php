<div class="modal-container sidebar">
    <form class="" method="POST" action="/components/sidebar.php">
        <input type="hidden" name="userID" value="<?=  $user_id ?>">
        <h3><a href="<?php echo BASE_URL; ?>/">CopyForward</a></h3>
        <?php if ($_SESSION["user_id"]): ?>
        <a class="btn-1" href="<?php echo BASE_URL; ?>/pages/MyLibrary.php">My Library</a>
        <a class="btn-1" href="<?php echo BASE_URL; ?>/pages/MyContents.php">My Contents</a>
        <a class="btn-1" href="<?php echo BASE_URL; ?>/pages/MyCommittees.php">My Committees</a>
        <span class="separator"><hr/>*<hr/></span>
        <a class="btn-2" href="<?php echo BASE_URL; ?>/pages/ManageCommittees.php">Manage Committees</a>
        <a class="btn-2" href="<?php echo BASE_URL; ?>/pages/BrowseCharities.php">Browse Charities</a>
        <span class="separator"><hr/>*<hr/></span>
        <a class="btn-3" href="<?php echo BASE_URL; ?>/pages/TextUpload.php">Upload Text</a>
        <a class="btn-3" href="<?php echo BASE_URL; ?>/pages/CreateCommittee.php">Create a Committee</a>
        <span class="separator"><hr/>*<hr/></span>
        <a class="btn-4" href="<?php echo BASE_URL; ?>/pages/Dashboard.php">Dashboard</a>
        <?php endif ?>
        <a class="btn-4" href="<?php echo BASE_URL; ?>/pages/FAQ.php">FAQs</a>
        
        <?php if ($_SESSION["user_id"] && isset($_SESSION["downloads_allowed"])): ?>
        <p>Download Limit: <?php  
            echo $_SESSION["download_count"] . " / " 
            . $_SESSION["downloads_allowed"] . " per " 
            . $_SESSION["downloads_window"] . " day(s)";
        ?></p>
        <?php endif ?>

    </form>
</div>