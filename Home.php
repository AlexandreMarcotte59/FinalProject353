<?php
include "./components/db.php";

// Handle Texts queries
$sql_query_popular = "select * from texts order by popularity desc limit 20";
$result = $pdo->query($sql_query_popular);
$top20 = $result->fetchAll(PDO::FETCH_ASSOC);

if (!isset($_SESSION['user_id'])) {
    //die("Not logged in");
    $is_member = false;
} else {
    $is_member = true;
    $user_id = $_SESSION['user_id'];
    $stmt_query_user = $pdo->prepare("SELECT * FROM members WHERE user_id = ?");
    $stmt_query_user->execute([$user_id]);
    $user_tuple = $pdo->query($stmt_query_user)->fetch(PDO::FETCH_ASSOC);
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
        function test(){
        <?php if ($is_member): ?> 
    	//alert("test");        
        const dialog = document.querySelector(`#profileFeatures`);   
        dialog.showModal();
        dialog.querySelector(".closeDialog").addEventListener("click", () => dialog.close()); 
      	<?php else: ?>
    	//alert("You are not signed in. You have no access to profile features.");
        const dialog = document.querySelector(`#dialogMessage`);   
        dialog.showModal();
        dialog.querySelector(".closeDialog").addEventListener("click", () => dialog.close()); 
      	<?php endif; ?>
       return false;
        } 
    </script>

    <link rel="stylesheet" href="./style/main.css">
</head>
<body>
        <div class="navbar">
        	<div class="left-side">
            	<a id="hamburger" onclick="test()">≡</a>
        		<a id="?"></a>
            </div>
        	<div class="right-side">
                <?php if ($is_member): ?>                
                <a id="login">Log Out</a>
            	<?php else: ?>
            	<a id="login" >Log In</a>
                <a id="signup">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>

    <div class="main-container">
        <h1>CopyForward Publishing</h1>
        <form class="search-form" method="POST" action="index.php">
            <input type="text" name="searchquery" placeholder="Enter text title or author..." required>
            <button type="submit">Search</button>
        </form>
        <div class="data-container">
            <table>
            <?php  foreach ($top20 as $row): ?>
                <tr><?= $row ?></tr>
            <?php endforeach; ?>
            </table>
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