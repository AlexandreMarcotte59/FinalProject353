<?php
include_once("database/db.php");

// Handle Texts queries
$sql_query_popular = "select * from Texts order by popularity desc limit 10";
$result = $pdo->query($sql_query_popular);
$top10 = $result->fetchAll(PDO::FETCH_ASSOC);

if (!isset($_SESSION['user_id'])) {
    //die("Not logged in");
    $is_member = false;
} else {
    $is_member = true;
    $user_id = $_SESSION['user_id'];
    $stmt_query_user = $pdo->prepare("SELECT * FROM members WHERE user_id = ?");
    $stmt_query_user->execute([$user_id]);
    $user_tuple = $pdo->query($stmt_query_user)->fetch(PDO::FETCH_ASSOC);

    $_SESSION["download_limit"] = $user_tuple["download_limit"];
} 

if (isset($_POST['search'])) {
    $sql_query_search = $pdo->prepare("SELECT * FROM Texts WHERE title LIKE CONCAT('%', ?, '%') order by title");
    $sql_query_search->execute([$_POST['search']]);
    $search_result= $sql_query_search->fetchAll(PDO::FETCH_ASSOC);
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
    
	function Logout() {
	    <?php 
            session_unset();  
            session_destroy();
            header('Location: /')
        ?>
	}
    </script>

    <link rel="stylesheet" href="./style/main.css">
</head>
<body>
        <div class="navbar">
        	<div class="left-side">
            	<a id="hamburger" onclick="ModalHandler()">≡</a>
        		<a id="?"></a>
            </div>
        	<div class="right-side">
                <?php if ($is_member): ?>                
                <a id="login" onclick="Logout()">Log Out</a>
            	<?php else: ?>
            	<a id="login" >Log In</a>
                <a id="signup">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>

    <div class="main-container">
        <h1>CopyForward Publishing</h1>
        <form class="search-form" method="POST" action="Home.php">
            <input type="text" name="search" placeholder="Enter text title or author..." required>
            <button type="submit">Search</button>
        </form>
        <div class="data-container">
            <?php  foreach ($search_result as $res): ?>
                <div class="card" onclick="SubmitCard(this)">
                    <form method="POST" action="pages/TextViewer.php">
                        <!--<input value="1" type="hidden">-->
                        <h3><?= $res["title"] ?></h3>
                        <p>Author: <?= $res["author"] ?></p>
                        <p>Year: <?= $res["date_published"] ?></p>
                    </form>
                </div>
            <?php endforeach; ?>
            <?php  foreach ($top10 as $row): ?>
                <div class="card" onclick="SubmitCard(this)">
                    <form method="POST" action="pages/TextViewer.php">
                        <!--<input value="1" type="hidden">-->
                        <h3><?= $row["title"] ?></h3>
                        <p>Author: <?= $row["author"] ?></p>
                        <p>Year: <?= $row["date_published"] ?></p>
                    </form>
                </div>
            <?php endforeach; ?>
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