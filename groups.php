<?php
session_start();
include 'db.php';

$sender_id = $_SESSION['user_id'];

$sql = "SELECT g.* FROM groups g
        JOIN group_memberships gm ON g.id = gm.group_id
        WHERE gm.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $sender_id);
$stmt->execute();
$result = $stmt->get_result();

$groups = array();
while ($row = $result->fetch_assoc()) {
    $groups[] = $row;
}

echo json_encode($groups);

?>