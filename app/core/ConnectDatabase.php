<?php

$serverName = '';
$userName = '';
$passWord = '';
$databaseName = '';

$connectDatabase = mysqli_connect(
    $serverName, 
    $userName, 
    $passWord, 
    $databaseName
);

if (!$connectDatabase) {
    die('Connection failed: ' . mysqli_connect_error());
}
echo 'Connect successfully';
