<?php
session_start();
include 'db.php';

$sql = "SELECT id, username FROM users";
$result = $conn->query($sql);

$users = array();
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

echo json_encode($users);
?>


