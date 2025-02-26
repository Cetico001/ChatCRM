<?php
if (isset($_FILES['file'])) {
    $file = $_FILES['file'];

    // Informações do arquivo
    $fileName = $file['name']; // Nome original do arquivo
    $fileTmpPath = $file['tmp_name']; // Caminho temporário do arquivo
    $fileSize = $file['size']; // Tamanho do arquivo
    $fileType = $file['type']; // Tipo MIME do arquivo

    // Pasta onde o arquivo será salvo
    $uploadDir = 'uploads/';
    $destination = $uploadDir . $fileName;

    // Mover o arquivo para o destino final
    if (move_uploaded_file($fileTmpPath, $destination)) {
        echo "Arquivo enviado com sucesso: " . $fileName;
    } else {
        echo "Erro ao enviar o arquivo.";
    }
} else {
    echo "Nenhum arquivo foi enviado.";
}
?>
