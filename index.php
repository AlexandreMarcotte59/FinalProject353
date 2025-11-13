<?php
// --- Configuration and Connection (from previous tutorial) ---
define('DB_HOST', 'mvc353.encs.concordia.ca');
define('DB_USER', 'mvc353_2'); // Your MySQL username
define('DB_PASS', 'firstsound58'); // Your MySQL password
define('DB_NAME', 'mvc353_2'); // The name of your database

$dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
     $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (\PDOException $e) {
     die("ERROR: Could not connect to the database. " . $e->getMessage());
}

// --- CRUD Operations ---

// C - Create (Add Task)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_task'])) {
    $task_text = trim($_POST['task_text']);
    if (!empty($task_text)) {
        $sql = "INSERT INTO tasks (task_text) VALUES (?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$task_text]);
        // Redirect to prevent form resubmission on refresh
        header("Location: index.php");
        exit;
    }
}

// D - Delete (Remove Task)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];
    if ($id > 0) {
        $sql = "DELETE FROM tasks WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        // Redirect to clean the URL
        header("Location: index.php");
        exit;
    }
}

// R - Read (Fetch All Tasks)
$tasks = $pdo->query("SELECT * FROM tasks ORDER BY created_at DESC")->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple PHP To-Do List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #cececeff;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            margin-top: 50px;
        }

        .todo-container {
            background-color: #f9d6d6ff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 400px;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        /* Form Styling */
        .add-form {
            display: flex;
            margin-bottom: 20px;
        }

        .add-form input[type="text"] {
            flex-grow: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px 0 0 4px;
            font-size: 16px;
        }

        .add-form button {
            padding: 10px 15px;
            background-color: #5cb85c;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 0 4px 4px 0;
            font-size: 16px;
        }

        /* To-Do List Styling */
        .task-list {
            list-style: none;
            padding: 0;
        }

        .task-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            margin-bottom: 8px;
            background-color: #eee;
            border-radius: 4px;
        }

        .task-item span {
            flex-grow: 1;
            padding-right: 10px;
        }

        .delete-btn {
            background-color: #d9534f;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="todo-container">
        <h1>PHP To-Do List</h1>

        <form class="add-form" method="POST" action="index.php">
            <input type="text" name="task_text" placeholder="Enter new task..." required>
            <button type="submit" name="add_task">Add</button>
        </form>

        <ul class="task-list">
            <?php if (empty($tasks)): ?>
                <p style="text-align: center; color: #999;">Your to-do list is empty!</p>
            <?php endif; ?>

            <?php foreach ($tasks as $task): ?>
                <li class="task-item">
                    <span><?php echo htmlspecialchars($task['task_text']); ?></span>
                    <a href="?delete_id=<?php echo $task['id']; ?>" class="delete-btn">Delete</a>

                    <a href="https://www.youtube.com/" class="delete-btn">Youtube</a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>