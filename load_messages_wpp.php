<?php
session_start();
include 'db.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function writeLog($message) {
    file_put_contents('webhook_log.txt', date('Y-m-d H:i:s') . " - " . $message . "\n", FILE_APPEND);
}

$atendente = isset($_GET['atendente']) ? $_GET['atendente'] : null;

if ($atendente) {
    $sql = "SELECT mensagens_wpp.*, mensagens_wpp.remetente AS nome 
    FROM mensagens_wpp 
    JOIN user_status ON mensagens_wpp.telefone = user_status.user_id 
    WHERE mensagens_wpp.telefone = ? 
      AND user_status.status = 3 
      AND mensagens_wpp.atendente = user_status.atendente
    ORDER BY mensagens_wpp.created_at ASC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $atendente);
    
    $stmt->execute();
    $result = $stmt->get_result();
    $messages = [];
    
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
    
    echo json_encode($messages);
} else {
    echo json_encode(['error' => 'Atendente não especificado.']);
}

?>

