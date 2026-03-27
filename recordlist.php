<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

function logOut() {
    session_destroy();
    header('Location: login.php');
    exit();
}

if (isset($_POST['logout'])) {
    logOut();
}


?>

<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Record List</title>
</head>
<body>
    <h1>Record List</h1>
    <form method='post'>
        <input type='submit' value='Logout' name='logout'>
    </form>
</body>
</html>
