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

$telefone = $_GET['telefone'];

$stmt = $conn->prepare("SELECT atendente FROM user_status WHERE user_id = :telefone");
$stmt->bindParam(':telefone', $telefone);
$stmt->execute();
$atendente = $stmt->fetchColumn();


echo ($atendente);

?>
