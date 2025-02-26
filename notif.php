<?php
include 'db.php';

$sql = "SELECT * 
        FROM mensagens_wpp 
        INNER JOIN user_status 
        ON mensagens_wpp.atendente = user_status.atendente
        WHERE mensagens_wpp.notified = 0 
        AND mensagens_wpp.atendente IS NOT NULL;";
$result = $conn->query($sql);

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

if (!empty($messages)) {
    // Atualiza as mensagens como notificadas
    $update_sql = "UPDATE mensagens_wpp SET notified = 1 WHERE notified = 0";
    $conn->query($update_sql);
}

echo json_encode($messages);
?>
