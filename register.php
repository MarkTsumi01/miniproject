<?php

session_start();

include 'connectdatabase.php';

if (isset($_SESSION['user_id'])) {
    header('Location: record_list.php');
    exit();
}

$errorList = [];

if (isset($_POST['submit'])) {
    $userName = $_POST['username'];
    $password = $_POST['password'];

    if (empty($userName)) {
        $errorList['username'] = 'Username is required';
    }

    if (empty($password)) {
        $errorList['password'] = 'Password is required';
    } 
    
    if (empty($errorList)) {
        $checkUsernameStmt = $connectDatabase->prepare('SELECT id FROM users WHERE username = ?');
        $checkUsernameStmt->bind_param('s', $userName);
        $checkUsernameStmt->execute();
        $checkUsernameStmt->store_result();

        if ($checkUsernameStmt->num_rows > 0) {
            $errorList['username'] = 'Username already exists';
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $insertStmt = $connectDatabase->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
            $insertStmt->bind_param('ss', $userName, $hashedPassword);
            $insertStmt->execute();
            $insertStmt->close();

            $_SESSION['user_id'] = $connectDatabase->insert_id;
            $_SESSION['username'] = $userName;
        }

        $checkUsernameStmt->close();
    }

    if (empty($errorList)) {
        header('Location: recordlist.php');
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Register</title>
</head>
<body>
    <h1>Register</h1>
    <div class='container'>
        <form method='post'>
            <label for='username'>Username:</label>
            <input type='text' id='username' name='username'><br>
            <?php if (!empty($errorList['username'])) { echo '<p>' . htmlspecialchars($errorList['username']) . '</p>'; } ?>
            <label for='password'>Password:</label>
            <input type='password' id='password' name='password'><br>
            <?php if (!empty($errorList['password'])) { echo '<p>' . htmlspecialchars($errorList['password']) . '</p>'; } ?>
            <input type='submit' value='Register' name='submit'>
        </form>
    </div>
</body>
</html>
