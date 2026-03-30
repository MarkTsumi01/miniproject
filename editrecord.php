<?php

session_start();

include 'connectdatabase.php';

if (!isset($_SESSION['user_id'])) {
    session_write_close();
    header('Location: login.php');
    exit();
}

$errorList = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recordId = $_GET['record_id'];
    $recordName = $_POST['record_name'];
    
    if (empty($recordName)) {
        $errorList['record_name'] = 'Record name is required';
    }
        
    if (empty($errorList)) {
        $checkRecordName = $connectDatabase->prepare('SELECT id FROM records WHERE name = ? and id != ?');
        $checkRecordName->bind_param('ss', $recordName, $recordId);
        $checkRecordName->execute();
        $checkRecordName->store_result();
         
        if ($checkRecordName->num_rows > 0) {
            $errorList['record_name'] = 'This name already exists';
        } else {
            $editRecordName = $connectDatabase->prepare('UPDATE records SET name = ? WHERE id = ?');
            $editRecordName->bind_param('ss', $recordName, $recordId);
            $editRecordName->execute();
            
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
    <title>Edit Record</title>
</head>
<body>
    <h1>Edit Record</h1>
    <form method='post'>
        <label for='record_name'>Record Name:</label>
        <input type='text' id='record_name' name='record_name' required>
        <input type='submit' value='Save'>
    </form>
</body>
</html>
