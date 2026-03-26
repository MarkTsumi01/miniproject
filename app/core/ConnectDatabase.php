<?php

$serverName = 'mysql';
$userName = 'root';
$password = 'root';
$databaseName = 'mini';

// $serverName = $_ENV['SERVER_NAME'];
// $userName = $_ENV['USER_NAME'];
// $password = $_ENV['PASSWORD'];
// $databaseName = $_ENV['DATABASE_NAME'];

$connectDatabase = mysqli_connect(
    $serverName, 
    $userName,
    $password,
    $databaseName
);

if (!$connectDatabase) {
    die('Connection failed: ' . mysqli_connect_error());
}
echo 'Connect successfully';
