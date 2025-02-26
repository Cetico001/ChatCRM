<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capturar informações do formulário
    $telefone = $_POST['phone'];

    if (isset($_FILES['file'])) {
        $file = $_FILES['file'];

        // Verificar se não houve erro no upload
        if ($file['error'] === UPLOAD_ERR_OK) {
            $fileName = $file['name'];
            $fileTmpName = $file['tmp_name'];

            // Fazer upload para o servidor Z-API
            enviarArquivo($telefone, $fileTmpName, $fileName);
        } else {
            echo "Erro no upload do arquivo.";
        }
    } else {
        echo "Nenhum arquivo foi enviado.";
    }
}

function enviarArquivo($telefone, $filePath, $fileName) {
    $apiUrl = "https://api.z-api.io/instances//token//send-text";
    $payload = [
        'phone' => $telefone,
        'file' => curl_file_create($filePath, mime_content_type($filePath), $fileName),
        'fileName' => $fileName,
        'caption' => "Aqui está o arquivo solicitado!"
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

    $response = curl_exec($ch);
    $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    if ($httpStatus === 200) {
        echo "Arquivo enviado com sucesso!";
    } else {
        echo "Erro ao enviar arquivo. Resposta da API: $response";
    }
}
?>
