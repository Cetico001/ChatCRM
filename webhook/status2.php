<?php
$host = 'localhost';
$db = 'crm';
$user = 'root';
$pass = '';

// Conexão utilizando PDO
try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo 'ERROR: ' . $e->getMessage();
    die();
}

// Obtendo o número de telefone da query string
$telefone = $_GET['telefone'];

// Verifica o status do cliente no banco de dados
$stmt = $conn->prepare("SELECT status FROM user_status WHERE user_id = :telefone");
$stmt->bindParam(':telefone', $telefone);
$stmt->execute();
$status = $stmt->fetchColumn();

if ($status !== false) {
    // Retorna o status como resposta JSON
    echo json_encode(["status" => $status]);
} else {
    // Se o cliente não for encontrado
    echo json_encode(["status" => "não encontrado"]);
}
?>
