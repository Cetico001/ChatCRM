<?php

include 'db.php';

$sql = "SELECT id, user_id, nome, status, atendente FROM user_status";
$result = $conn->query($sql);

$user_status = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $user_status[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($user_status);

?>