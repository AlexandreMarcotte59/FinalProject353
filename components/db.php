<?php
session_start();

define('DB_HOST', 'mvc353.encs.concordia.ca');
define('DB_USER', 'mvc353_2');
define('DB_PASS', 'firstsound58');
define('DB_NAME', 'mvc353_2'); 

$dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    die("DB Connection failed: " . $e->getMessage());
}
?>