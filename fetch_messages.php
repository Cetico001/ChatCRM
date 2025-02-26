<?php
include 'db.php';

$sql = "SELECT * FROM messages ORDER BY created_at DESC";
$result = $conn->query($sql);

$messages = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
}
header('Content-Type: application/json');
echo json_encode($messages);
?>