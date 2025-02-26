<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Função para log
function writeLog($message) {
    file_put_contents('webhook_log.txt', date('Y-m-d H:i:s') . " - " . $message . "\n", FILE_APPEND);
}

writeLog("Iniciando processamento");


$host = 'localhost';
$user = 'root';
$password = '';
$database = 'crm';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
}

// Obtendo os dados recebidos do webhook (Z-API ou outra integração)
$data = json_decode(file_get_contents('php://input'), true);

// Verifica se os dados existem
if (isset($data['phone']) && isset($data['senderName'])) {
    $telefone = $data['phone'];
    $mensagem = $data['text']['message'];
    $nome = $data['senderName'];
    $audioUrl = $data['audio']['audioUrl'] ?? null;
    $tipo = $audioUrl ? 'audio' : 'text';
    writeLog("Teste tipagem:" . $tipo);
    $mensagem = $audioUrl ? $audioUrl : $mensagem;

    $atendente_msg = null;
    $stmt = $conn->prepare("SELECT atendente FROM user_status WHERE user_id = ?");
    $stmt->bind_param("s", $telefone);
    $stmt->execute();
    $stmt->bind_result($atendente_msg);
    $stmt->fetch();
    $stmt->close();
    // Prepara a query SQL para inserir os dados
    $stmt = $conn->prepare("INSERT INTO mensagens_wpp (telefone, remetente, mensagem, tipo, atendente) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $telefone, $nome, $mensagem, $tipo, $atendente_msg);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Mensagem salva com sucesso.']);
        writeLog("Sucesso");
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erro ao salvar a mensagem.']);
        writeLog("Erro");
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Dados incompletos recebidos.']);
}


function enviarMensagem($telefone, $mensagem) {
    $apiUrl = "https://api.z-api.io/instances/-CHAVE-/send-text";
    $apiToken = "-TOKEN-";

    $payload = json_encode([
        "phone" => $telefone,
        "message" => $mensagem
    ]);

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


// Função para processar mensagens recebidas
function processarMensagem($telefone, $mensagem, $nome) {
    global $conn;

    // Busca o cliente no banco
    $sql = "SELECT * FROM user_status WHERE user_id = ?";
    writeLog("Deu certo 1");
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $telefone);
    $stmt->execute();
    $cliente = $stmt->get_result()->fetch_assoc();
    
    writeLog($cliente);

    if (!$cliente) {
        writeLog("Cliente nao existe");
        writeLog($telefone);
        writeLog($mensagem);
        writeLog($nome);
        // Se o cliente não existe, cria um novo registro
        $sql = "INSERT INTO user_status (user_id, nome) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $telefone, $nome);
        writeLog("stmt->execute()");
        $stmt->execute();
        $cliente = ['status' => 0, 'user_id' => $telefone];
    }
    $status = $cliente['status'];
    $resposta = "";
    writeLog("Status: " . $status);
    if ($status == 0) {
        $resposta = "Olá, tudo bem?\nDigite 1 a 3 o que você precisa:\n1--> Falar com nossa área técnica\n2--> Falar com nosso financeiro\n3--> Falar o SBE";
        $novoStatus = 1;
    } elseif ($status == 1) {
        if ($mensagem == "1") {
            $resposta = "Com quem do técnico deseja falar?\n1--> Leandro\n2-->Ana Menezes\n3-->Pedro\n4-->Jucy\nSe não é do técnico a área que quer, digite 'Exit'.";
            $novoStatus = 2;
        } elseif ($mensagem == "2") {
            $resposta = "Com quem do financeiro deseja falar?\n1--> Jucy\n2-->Denise\n3-->Poliana\n4-->Leticia\n5-->David\nSe não é do financeiro a área que quer, digite 'Exit'.";
            $novoStatus = 2;
        } elseif ($mensagem == "3") {
            $resposta = "Com quem do SBE deseja falar?\n1--> Felipe\n2-->Addams\n3-->Alessandro\nSe não é do SBE a área que quer, digite 'Exit'.";
            $novoStatus = 2;
        } else {
            $resposta = "Opção inválida. Por favor, digite 1, 2 ou 3.";
            $novoStatus = 1;
        }
    } elseif ($status == 2) {
        if ($mensagem == "1") {
            writeLog("Falando com Leandro");
            $resposta = "Você escolheu falar com Leandro. Digite aqui do que você precisa para o atendente resolver!";
            $novoStatus = 3;
            $sql = "UPDATE user_status SET atendente = 'Leandro' WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $telefone);
            $stmt->execute();
        } elseif (strtolower($mensagem) == "2") {
            $resposta = "Você escolheu falar com Ana Menezes. Digite aqui do que você precisa para o atendente resolver!"; 
            $novoStatus = 3;
            $sql = "UPDATE user_status SET atendente = 'Ana Menezes' WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $telefone);
            $stmt->execute();
        } elseif (strtolower($mensagem) == "exit") {
            $resposta = "Voltando ao menu principal...\nDigite 1 a 3 o que você precisa:\n1--> Falar com nossa área técnica\n2--> Falar com nosso financeiro\n3--> Enviar um feedback";
            $novoStatus = 0;
        } else {
            $resposta = "Opção inválida. Por favor, escolha 1 ou digite 'Exit'.";
            $novoStatus = $status;
        }
    }

    // Atualiza o status do cliente
    if ($status < 3) {
        writeLog("Novo Status: " . $novoStatus);
        $sql = "UPDATE user_status SET status = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $novoStatus, $telefone);
        $stmt->execute();
    }

    // Envia a resposta
    enviarMensagem($telefone, $resposta);
}

writeLog("Dados brutos recebidos: " . $telefone);
writeLog("Dados brutos recebidos: " . $mensagem);
writeLog($nome);
processarMensagem($telefone, $mensagem, $nome);

$conn->close();
?>