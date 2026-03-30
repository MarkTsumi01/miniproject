<?php

session_start();

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
        $selectUserStmt = $connectDatabase->prepare('SELECT id, password FROM users WHERE username = ?');
        $selectUserStmt->bind_param('s', $userName);
        $selectUserStmt->execute();
        $selectUserStmt->store_result();
        $selectUserStmt->bind_result($userId, $hashedPassword);
        $selectUserStmt->fetch();

        if ($selectUserStmt->num_rows === 0 || !password_verify($password, $hashedPassword)) {
            $errorList['credentials'] = 'Invalid username or password';
        } else {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $userId;
            $_SESSION['username'] = $userName;
            session_write_close();
            header('Location: recordlist.php');

            exit();
        }

        $selectUserStmt->close();
    }
    
    if($_POST['register']) {
        header('Location: register.php');
        
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Login</title>
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
    <h1>Login</h1>
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
            <?php if (!empty($errorList['credentials'])) {
                echo '<p>' . htmlspecialchars($errorList['credentials']) . '</p>';
            } ?>
            <input type='submit' value='Login' name='submit'>
        </form>
        <form method='post'>
            <input type='submit' value='Register' name='register'>
        </form>
    </div>
</body>
</html>
