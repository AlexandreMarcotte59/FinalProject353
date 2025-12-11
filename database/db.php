<?php
session_start();

/* Detect local environment reliably */
$isLocal = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'
        || strtoupper(substr(PHP_OS, 0, 3)) === 'DAR'   // macOS (Darwin)
        || strpos(__DIR__, 'xampp') !== false
        || strpos(__DIR__, 'wamp') !== false
        || strpos(__DIR__, 'mamp') !== false;

if ($isLocal) {
    define('BASE_URL', '/FinalProject353'); // local project folder
    // --- LOCAL DEVELOPMENT ---
    define('DB_HOST', '127.0.0.1');
    define('DB_USER', 'root');     // local mysql user
    define('DB_PASS', '');         // local mysql password (usually empty for XAMPP/WAMP)
    define('DB_NAME', 'mvc353_2'); // your imported local DB name

} else {
    define('BASE_URL', '');
    // --- ENCS SERVER ---
    define('DB_HOST', 'mvc353.encs.concordia.ca');
    define('DB_USER', 'mvc353_2');
    define('DB_PASS', 'firstsound58');
    define('DB_NAME', 'mvc353_2');

    $servername = "mvc353.encs.concordia.ca";
    $username   = "mvc353_2";
    $password   = "firstsound58";
    $dbname     = "mvc353_2";
}

$ROOTPATH = $_SERVER['DOCUMENT_ROOT'];

$dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        throw new Exception("MySQLi Connection failed: " . $conn->connect_error);
    }

} catch (PDOException $e) {
    die("DB Connection failed: " . $e->getMessage());
} catch (Exception $e) {
    echo "Other Error: " . $e->getMessage();
    die("Connection failed: " . $conn->connect_error);
}


?>