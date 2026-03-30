<?php

session_start();

include 'connectdatabase.php';

if (!isset($_SESSION['user_id'])) {
    session_write_close();
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_record_id'])) {
    $deleteRecordId = $_POST['delete_record_id'];
    $deleteStmt = $connectDatabase->prepare('DELETE FROM records WHERE id = ?');
    $deleteStmt->bind_param('i', $deleteRecordId);
    $deleteStmt->execute();
    $deleteStmt->close();
    header('Location: recordlist_test.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
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

$selectRecordsStmt = $connectDatabase->prepare('
    SELECT records.id, records.name, COUNT(bands.id) AS band_count
    FROM records
    LEFT JOIN bands ON bands.record_id = records.id
    GROUP BY records.id, records.name
    ORDER BY records.name ASC
');

$selectRecordsStmt->execute();
$recordsResult = $selectRecordsStmt->get_result();
$selectRecordsStmt->close();

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

    <a href='addrecord.php'>Add Record</a>

    <table border='1'>
        <thead>
            <tr>
                <th>Record Name</th>
                <th>Bands</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($record = $recordsResult->fetch_assoc()): ?>
            <tr>
                <td>
                    <a href='bandlist.php?record_id=<?php echo $record['id']; ?>'>
                        <?php echo htmlspecialchars($record['name']); ?>
                    </a>
                </td>
                <td><?php echo $record['band_count']; ?></td>
                <td>
                    <a href='editrecord.php?record_id=<?php echo $record['id']; ?>'>Edit</a>
                    <form method='post' style='display:inline;'>
                        <input type='hidden' name='delete_record_id' value='<?php echo $record['id']; ?>'>
                        <input type='submit' value='Delete'
                            <!--onclick='return confirm("ลบค่ายเพลงนี้? วง อัลบั้ม และเพลงทั้งหมดจะถูกลบด้วย")'>-->
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <form method='post'>
        <input type='submit' value='Logout' name='logout'>
    </form>
</body>
</html>
