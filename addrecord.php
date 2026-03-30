<?php

session_start();

include 'connectdatabase.php';

if (!isset($_SESSION['user_id'])) {
    session_write_close();
    header('Location: login.php');
    exit();
}

$errorList = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $recordName = $_POST['record_name'];
    
    if (empty($recordName)) {
        $errorList['record_name'] = 'Record name is required';
    }
    
    if (empty($errorList)) {
       $checkRecordName = $connectDatabase->prepare('SELECT id FROM records WHERE name = ?');
       $checkRecordName->bind_param('s', $recordName);
       $checkRecordName->execute();
       $checkRecordName->store_result();
       
        if ($checkRecordName->num_rows > 0) {
           $errorList['record_name'] = 'This name already exists';
       } else {
           $addRecord = $connectDatabase->prepare('INSERT INTO records (name) VALUES (?)');
           $addRecord->bind_param('s', $recordName);
           $addRecord->execute();
           
           header('Location: recordlist_test.php');
           exit();
       }
   }
   
   $checkRecordName->close();
}

?>

<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Add Record</title>
    <script>
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                window.location.reload();
            }
        });
    </script>
</head>
<body>
    <h1>Add Record</h1>
    <form method='post'>
        <label for='record_name'>Record Name:</label>
        <input type='text' id='record_name' name='record_name' value='<?php echo isset($_POST['record_name']) ? htmlspecialchars($_POST['record_name']) : ''; ?>'>
        <?php if (!empty($errorList['record_name'])) {
                echo '<p>' . htmlspecialchars($errorList['record_name']) . '</p>';
        } ?>
        <input type='submit' value='Save'>
    </form>
</body>
</html>
