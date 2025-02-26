<?php
$host = 'localhost';
$db = 'crm';
$user = 'root';
$pass = '';

$conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);

// Lê o corpo da requisição JSON
$data = json_decode(file_get_contents('php://input'), true);

// Verifica se os parâmetros foram enviados
if (isset($data['user_id']) && isset($data['status'])) {
    $user_id = $data['user_id'];
    $status = $data['status'];

    // Atualiza o status no banco de dados
    $query = "UPDATE user_status SET status = :status WHERE user_id = :user_id";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':status', $status);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Status atualizado com sucesso.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao atualizar status.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Parâmetros inválidos.']);
}


if (isset($data['atendente'])) {
    $atendente = $data['atendente'];
    $query = "UPDATE user_status SET atendente = :atendente WHERE user_id = :user_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':atendente', $atendente);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Status atualizado com sucesso.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao atualizar status.']);
    }

}
?>
