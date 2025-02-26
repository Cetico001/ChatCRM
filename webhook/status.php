<?php
// Conexão com o banco de dados
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

// Obtendo a mensagem e o número de telefone do webhook (Make)
$dados = json_decode(file_get_contents('php://input'), true);
$telefone = $dados['telefone'];
$mensagem = $dados['mensagem'];
$nome = $dados['nome'];

// Verifica se o telefone já está cadastrado
$stmt = $conn->prepare("SELECT * FROM user_status WHERE user_id = :telefone");
$stmt->bindParam(':telefone', $telefone);
$stmt->execute();

// Se o telefone não estiver cadastrado, insere o cliente
if($stmt->rowCount() == 0) {
    $status = 0;  // Status inicial
    $stmt = $conn->prepare("INSERT INTO user_status (user_id, nome, status) VALUES (:telefone, :nome, :status)");
    $stmt->bindParam(':telefone', $telefone);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':status', $status);
    $stmt->execute();
    echo "Cliente cadastrado com sucesso.";
} else {
    echo "Cliente já cadastrado.";
}

?>
