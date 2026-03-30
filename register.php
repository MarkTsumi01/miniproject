<?php

session_start();

// header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
// header('Expires: 0');
// header('Pragma: no-cache');

include 'connectdatabase.php';

if (isset($_SESSION['user_id'])) {
    header('Location: recordlist.php');

    exit();
}

$errorList = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userName = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

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
            $checkUsernameStmt->close();
        } else {
            $checkUsernameStmt->close();

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $insertStmt = $connectDatabase->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
            $insertStmt->bind_param('ss', $userName, $hashedPassword);
            $insertStmt->execute();
            $newUserId = $connectDatabase->insert_id;
            $insertStmt->close();

            session_regenerate_id(true);
            $_SESSION['user_id'] = $newUserId;
            $_SESSION['username'] = $userName;

            session_write_close();
            header('Location: recordlist.php');

            exit();
        }
    }
    
    if($_POST['login']) {
        header('Location: login.php');
        
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
    <script>
        // window.addEventListener('pageshow', function(event) {
        //     if (event.persisted) {
        //         window.location.replace('recordlist.php');
        //     }
        // });
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                window.location.reload();
            }
        });
    </script>
</head>
<body>
    <h1>Register</h1>
    <div class='container'>
        <form method='post'>
            <label for='username'>Username:</label>
            <input type='text' id='username' name='username' value='<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>'><br>
            <?php if (!empty($errorList['username'])) {
                echo '<p>' . htmlspecialchars($errorList['username']) . '</p>';
            } ?>
            <label for='password'>Password:</label>
            <input type='password' id='password' name='password'><br>
            <?php if (!empty($errorList['password'])) {
                echo '<p>' . htmlspecialchars($errorList['password']) . '</p>';
            } ?>
            <input type='submit' value='Register' name='submit'>
        </form>
        <form method='post'>
            <input type='submit' value='Login' name='login'>
        </form>
    </div>
</body>
</html>
