<?php

session_start();

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Expires: 0');
header('Pragma: no-cache');

if (!isset($_SESSION['user_id'])) {
    session_write_close();
    header('Location: login.php');

    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION = [];

    $cookieParams = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 3600,
        $cookieParams['path'],
        $cookieParams['domain'],
        $cookieParams['secure'],
        $cookieParams['httponly']
    );

    session_destroy();
    header('Location: login.php');

    exit();
}

session_write_close();

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
