<?php
include_once("database/db.php");

// Handle Texts queries
$sql_query_popular = "select * from Texts order by popularity desc limit 6";
$result_pop = $pdo->query($sql_query_popular);
$top6P = $result_pop->fetchAll(PDO::FETCH_ASSOC);

$sql_query_dloads = "select * from Texts order by downloads desc limit 6";
$result_dloads = $pdo->query($sql_query_dloads);
$top6D = $result_dloads->fetchAll(PDO::FETCH_ASSOC);

$sql_query_recent = "select * from Texts order by uploaded_at desc limit 6";
$result_recent = $pdo->query($sql_query_recent);
$top6R = $result_recent->fetchAll(PDO::FETCH_ASSOC);


if (!isset($_SESSION['user_id'])) {
        //die("Not logged in");
        $is_member = false;
} else {
        $is_member = true;
        $user_id = $_SESSION['user_id'];
        $stmt_query_user = $pdo->prepare("SELECT 1 FROM Members WHERE user_id = ?");
        $stmt_query_user->execute([$user_id]);
        $user_tuple = $stmt_query_user->fetch(PDO::FETCH_ASSOC);
        
        // Compute donations
        $stmt = $pdo->prepare("SELECT SUM(amount) FROM Donations WHERE user_id=?");
        $stmt->execute([$user_id]);
        $total = $stmt->fetchColumn();
        $total = floatval($total);

        
        $stmtdono = $pdo->prepare("SELECT SUM(amount) FROM Donations WHERE user_id=? AND donation_date >= NOW() - INTERVAL 31 DAY");
        $stmtdono->execute([$user_id]);
        $recent_dono = $stmtdono->fetchColumn();
        $recent_dono = floatval($recent_dono);

        // Compute allowed downloads
        $base_window = 31;
        $bonusday = floor($total / 100); // 3100 in 20 years = 31
        $bonusnum = floor($recent_dono / 10); // 10 in last month = 1
        $effective_window = max($base_window - $bonusday, 1);
        if ($effective_window == 1) {
            $downloads_allowed = 1 + max($bonusday - $base_window, 0) + max($bonusnum - ($base_window - 1), 0);
        } else {
            $downloads_allowed = 1;
        }

        // Count recent downloads
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM Downloads WHERE user_id=? AND download_date >= NOW() - INTERVAL ? DAY");
        $stmt->execute([$user_id, $effective_window]);
        $recent = $stmt->fetchColumn();

        if ($recent < $downloads_allowed) {
            // allow download
        } else {
            // deny download
        }
}

if (isset($_POST['search'])){
    if($_POST['filter'] == "author"){
            $sql_author_query = $pdo->prepare("SELECT * FROM Texts WHERE author LIKE CONCAT('%', ? ,'%') ORDER BY author ASC LIMIT 6");
            $sql_author_query->execute([$_POST['search']]);
            $search_result = $sql_author_query->fetchAll(PDO::FETCH_ASSOC);
            if (empty($search_result)){
                    $search_result_msg = "No relevant authors found.";
            }
    } elseif ($_POST['filter'] == "title") {
            $sql_title_query = $pdo->prepare("SELECT * FROM Texts WHERE title LIKE CONCAT('%', ? ,'%') ORDER BY title ASC LIMIT 6");
            $sql_title_query->execute([$_POST['search']]);
            $search_result = $sql_title_query->fetchAll(PDO::FETCH_ASSOC);
            if (empty($search_result)){
                    $search_result_msg = "No relevant titles found.";
            }
    } else { unset($_POST['filter']); }
}




?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Home</title>
    <script>
        document.querySelectorAll("[data-dialog]").forEach(btn => {
            btn.addEventListener("click", () => {        
                const dialog = document.querySelector(`#${ btn.dataset.dialog }`);   
                dialog.showModal();                
                dialog.querySelector(".closeDialog").addEventListener("click", () => dialog.close());            
            });
        });
        function ModalHandler(){
        		const dialog = document.querySelector(`#dialogMessage`);   
		dialog.showModal();
		dialog.querySelector(".closeDialog").addEventListener("click", () => dialog.close()); 
      	       return false;
	}
	function SubmitCard(card) {
	    const form = card.querySelector('form');
	    if (form) {
		    form.submit();
	    }
	}
    </script>

    <link rel="stylesheet" href="./style/main.css">
</head>
<body>
        <div class="navbar">
        	<div class="left-side">
            	<a id="hamburger" onclick="ModalHandler()">≡</a>
            </div>
        	<div class="right-side">
                <?php if ($is_member): ?>                
                <a id="login" onclick="window.location='/components/logout.php'">Log Out</a>
            	<?php else: ?>
            	<a id="login" >Log In</a>
                <a id="signup">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>

    <div class="main-container">
        <h1>CopyForward Publishing</h1>
        <form class="search-form" method="POST" action="Home.php">
            <div class="bar">
                <input type="search" name="search" value="<?= isset($_POST['search']) ? $_POST['search'] : '' ?>" placeholder='Enter query...' />
                <select name="filter">
                        <option></option>
                        <option value="title">Title</option>
                        <option value="author">Author</option>
                </select>
                <button type="submit">Search</button>
            </div>
        </form>
        <div class="home-container">
            <?php if (isset($_POST['search']) && !empty($search_result)): ?>
                 <div class="card-container">
                    <?php  foreach ($search_result as $res): ?>
                        <div class="card" onclick="submitCard(this)">
                            <form method="POST" action="pages/TextViewer.php">
                                <!--<input value="1" type="hidden">-->
                                <h3><?= $res["title"] ?></h3>
                                <p>Author: <?= $res["author"] ?></p>
                                <p>Year: <?= $res["date_published"] ?></p>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="text-align: center;"><?= $search_result_msg ?></p>                
                 <h4>Most Recent</h4>
                 <div class="card-container">
                        <?php  foreach ($top6R as $rowr): ?>
                        <div class="card" onclick="submitCard(this)"> <form method="POST" action="pages/TextViewer.php">
                                <input name="id" value=<?= $rowr['text_id'] ?> type="hidden">
                                <h3><?= htmlspecialchars($rowr['title']) ?></h3>
                                <p>Author: <?= htmlspecialchars($rowr['author']) ?></p>
                                <p>Published: <?= htmlspecialchars($rowr['date_published']) ?></p>
                                <p>Uploaded: <?= htmlspecialchars($rowr['uploaded_at']) ?></p></form>
                        </div>
                        <?php endforeach; ?>
                </div>
                <h4>Most Popular</h4>
                 <div class="card-container">
                        <?php  foreach ($top6P as $rowp): ?>
                        <div class="card" onclick="submitCard(this)"> <form method="POST" action="pages/TextViewer.php">
                                <input name="id" value=<?= $rowp['text_id'] ?> type="hidden">
                                <h3><?= htmlspecialchars($rowp['title']) ?></h3>
                                <p>Author: <?= htmlspecialchars($rowp['author']) ?></p>
                                <p>Published: <?= htmlspecialchars($rowp['date_published']) ?></p>
                                <p>Reads: <?= htmlspecialchars($rowp['popularity']) ?></p></form>
                        </div>
                        <?php endforeach; ?>
                </div>
                 <h4>Most Downloaded</h4>
                 <div class="card-container">
                        <?php  foreach ($top6D as $rowd): ?>
                        <div class="card" onclick="submitCard(this)"> <form method="POST" action="pages/TextViewer.php">
                                <input name="id" value=<?= $rowd['text_id'] ?> type="hidden">
                                <h3><?= htmlspecialchars($rowd['title']) ?></h3>
                                <p>Author: <?= htmlspecialchars($rowd['author']) ?></p>
                                <p>Published: <?= htmlspecialchars($rowd['date_published']) ?></p>
                                <p>Downloads: <?= htmlspecialchars($rowd['downloads']) ?></p></form>
                        </div>
                        <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <div>
        <!-- The Modals -->
        <dialog id="dialogMessage">
            <button class="closeDialog">X</button>
            <p>You are not signed in. You have no access to extra features.</p>
        </dialog>
        <dialog id="sidebarFeatures">
            <button class="closeDialog">X</button>
            <?php include("./components/SideBar.php");  ?>  
        </dialog>
    </div>
</body>
</html>