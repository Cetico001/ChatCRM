<?php
$conn = new mysqli("localhost", "root", "", "crm");

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

$data = file_get_contents("php://input");
$json = json_decode($data, true);

$telefone = $json['telefone'] ?? '';
$mensagem = $json['texto']['mensagem'] ?? '';
$remetente = $json['nomeRemetente'] ?? '';
$atendente = $json['atendente'] ?? '';
$audioUrl = $json['audio']['audioUrl'] ?? null;
$tipo = $audioUrl ? 'audio' : 'texto';

$conteudoMensagem = $audioUrl ? $audioUrl : $mensagem;

if (!empty($telefone) && !empty($conteudoMensagem)) {
    $stmt = $conn->prepare("INSERT INTO mensagens_wpp (telefone, mensagem, tipo, remetente, atendente) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $telefone, $conteudoMensagem, $tipo, $remetente, $atendente);
    $stmt->execute();
    $stmt->close();
    echo "Dados inseridos com sucesso!";
} else {
    echo "Dados inválidos!";
}

$conn->close();
?>
