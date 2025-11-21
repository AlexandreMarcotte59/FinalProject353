
<div class="modal-container">
    <form class="search-form" method="POST" action="./components/Sidebar.php">
        <input type="hidden" name="userID" value="<?=  $user_id ?>">
        <a href="">My Email</a>
        <a href="./components/MyContents.php">My Contents</a>
        <a href="./components/MyCommitees.php">My Committees</a>
        <span class="separator"></span>
        <a href="./components/BrowseCommitees.php">Browse Committees</a>
        <a href="./components/BrowseCharities.php">Browse Charities</a>
        <span class="separator"></span>
        <p>My Download Limit: <?= $user_tuple['download_limit'] ?></p>
    </form>
</div>