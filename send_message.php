<?php
session_start();
include 'db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Função para log
function writeLog($message) {
    file_put_contents('webhook_log.txt', date('Y-m-d H:i:s') . " - " . $message . "\n", FILE_APPEND);
}

$message = $_POST['message'];
$recipient_id = isset($_POST['recipient_id']) ? $_POST['recipient_id'] : null;
$group_id = isset($_POST['group_id']) ? $_POST['group_id'] : null;
$sender_id = $_SESSION['user_id'];
$atendente_id = isset($_POST['atendente_id']) ? $_POST['atendente_id'] : null;
$useratendenteId = isset($_POST['useratendenteId']) ? $_POST['useratendenteId'] : null;
$tipo = isset($_POST['tipo']) ? $_POST['tipo'] : null;
$number = 1;

if ($recipient_id) {
    $sql = "INSERT INTO messages (sender_id, recipient_id, message) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $sender_id, $recipient_id, $message);
} elseif ($group_id) {
    $sql = "INSERT INTO messages (sender_id, group_id, message) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $sender_id, $group_id, $message);
} elseif ($atendente_id) {
    writeLog($message);
    writeLog($atendente_id);
    $sql = "INSERT INTO mensagens_wpp (remetente, telefone, mensagem, notified, tipo, atendente) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssiss", $useratendenteId, $atendente_id, $message, $number, $tipo, $useratendenteId);
    enviarMensagem($atendente_id, "*" . $useratendenteId . ":*" . "\n" . $message, $tipo, $message);
} else {
    $sql = "INSERT INTO messages (sender_id, message) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $sender_id, $message);
}


if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Message sent successfully!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to send message.']);
}




function enviarMensagem($telefone, $mensagem, $tipo, $namearq) {
    if ($tipo!='text') {
        $filePath = 'uploads/'. $namearq; // Caminho da imagem
        $imageData = base64_encode(file_get_contents($filePath));
        $imageBase64 = $imageData; // Imagem em Base64
    }

    writeLog("Certo");
    $apiUrl = "https://api.z-api.io/instances//token//send-text";
    $apiToken = "";
    if ($tipo=='text') {
        $payload = json_encode([
            "phone" => $telefone,
            "message" => $mensagem
        ]);
        $decodedPayload = json_decode($payload, true);
    } else {
        $ext = explode(".", $namearq);
        writeLog($namearq);
        if ($ext[1]=='pdf') {
            $apiUrl = "https://api.z-api.io/instances//token//send-document/pdf";
            $payload = json_encode([
                "phone" => $telefone,
                "document" => "data:application/pdf;base64," . $imageBase64,
                "fileName" => $ext[0]
            ]);
        } elseif ($ext[1]=='png') {
            $apiUrl = "https://api.z-api.io/instances//token//send-image";
            $payload = json_encode([
            "phone" => $telefone,
            "image" => "data:image/png;base64," . $imageBase64,
            "caption" => "Descrição da imagem"
            ]);
        }

    }
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $apiUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => array(
            "client-token: $apiToken",
            "Content-Type: application/json"
        ),
    ));
    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);
    writeLog("Erro de cURL: $err");
    writeLog("Resposta da API: $response");

}
?>
