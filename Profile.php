<?php
$is_member = true;
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

    <style>
    	:root{
        --bkg-page-clr: #f0eae4ff;
        --bkg-container-clr: #f7e8c8ff;
        --border-clr: #fdbf8cff;
        --clr-pop: #fdbf8cff;
        --clr-good: limegreen;
		}
        body {
            font-family: Arial, sans-serif;
            background-color: var(--bkg-page-clr);
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            margin-top: 50px;
        }

        .main-container {
            /*
            border-top-left-radius: 57px 140px;
 			border-top-right-radius: 61px 100px;
  			border-bottom-left-radius: 80px 42px;
  			border-bottom-right-radius: 120px 24px;*/
            border-radius: 20px;
            background-color: var(--bkg-container-clr);
            padding: 30px;
            border: solid 3px var(--border-clr);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 400px;
            margin: 10vh auto;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        
        .navbar{
        	position: fixed;
            background-color: var(--bkg-container-clr);
            top: 0;
            width: 100%;
        	height: 5vh;
            border-radius: 0 0 8px 4px;
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
        }
        
        .navbar div{
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 5px;
            justify-content: normal;
            height: 70%;
            padding: 0 10px;
        }
        
        .navbar a{
            display: flex;
        	text-align: center;
            align-items: center;
            padding: 0 1vw;
            border-radius: 8px;
            color: white;            
            cursor: pointer;
            height: 100%;
        } .navbar a:hover{
        	 filter: grayscale(50%);
		}
        
        .navbar #login{background-color: var(--clr-pop);}
        .navbar #signup{ background-color: var(--clr-good);}
        .navbar #hamburger{ font-size: 40px; color: var(--clr-pop);}
        
        .search-form {
            display: flex;
            margin: 20px auto;
        }

        .search-form input[type="text"] {
            flex-grow: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px 0 0 8px;
            font-size: 16px;
        }

        .search-form button {
            padding: 10px 15px;
            background-color: limegreen;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 0 4px 4px 0;
            font-size: 16px;
        }
        body:has(dialog:modal) {
            overflow: hidden;
        }

        ::backdrop {
        backdrop-filter: blur(2px);
        background: hsl(0 0 0 / 50%);
        }
    </style>
</head>
<body>

    <div class="modal-container">
        <form class="search-form" method="POST" action="index.php">
            <input type="text" name="searchquery" placeholder="Enter text title or author..." required>
            <button type="submit">Search</button>
        </form>
    </div>
</body>
</html>