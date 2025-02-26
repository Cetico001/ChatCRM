<?php
session_start();
include 'db.php';

$recipient_id = isset($_GET['recipient_id']) ? $_GET['recipient_id'] : null;
$group_id = isset($_GET['group_id']) ? $_GET['group_id'] : null;
$sender_id = $_SESSION['user_id'];

if ($recipient_id) {
    $sql = "SELECT messages.*, users.username FROM messages JOIN users ON messages.sender_id = users.id WHERE (messages.sender_id = ? AND messages.recipient_id = ?) OR (messages.sender_id = ? AND messages.recipient_id = ?) ORDER BY created_at ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiii", $sender_id, $recipient_id, $recipient_id, $sender_id);
} elseif ($group_id) {
    $sql = "SELECT messages.*, users.username FROM messages JOIN users ON messages.sender_id = users.id WHERE messages.group_id = ? ORDER BY created_at ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $group_id);
} else {
    $sql = "SELECT messages.*, users.username FROM messages JOIN users ON messages.sender_id = users.id WHERE messages.recipient_id IS NULL AND messages.group_id IS NULL ORDER BY created_at ASC";
    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$result = $stmt->get_result();
$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

echo json_encode($messages);
?>