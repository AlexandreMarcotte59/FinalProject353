<?php
    include("database/db.php");
    $request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $request = rtrim($request, '/');
    $parts = explode('/', $request);
    $last = '/' . end($parts);

    switch ($last) {
        case '/':
        case '':
            header('Location: Home.php');
            exit;
        case '/about':
        header('Location: ReadMe.txt');
            exit;
        case '/readme':
            header('Location: ReadMe.txt');
            exit;
        default:
            http_response_code(404);
            exit;
    }
?>