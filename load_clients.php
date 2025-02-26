<?php
session_start();
include 'db.php';
$username = $conn->real_escape_string($_SESSION['username']);
$sql = "SELECT id, user_id, nome, atendente FROM user_status WHERE user_status.status = 3 AND user_status.atendente = '$username';";
$result = $conn->query($sql);

$clients = array();
while ($row = $result->fetch_assoc()) {
    $clients[] = $row;
}

echo json_encode($clients);
?>


