<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat CRM</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <header>
        <div style="display: flex; align-items: center; justify-content: center; background-color: #5cb85c; padding: 10px;">
            <img src="img/consorcio.png" alt="Consórcio TRANSUPLE" style="width: 50px; height: auto; margin-right: 10px;">
            <h1 style="font-size: 24px; color: white; margin: 0;">Consórcio TRANSUPLE</h1>
        </div>
    </header>
    <main>
        <aside id="sidebar">
            <h3>Usuários</h3>
            <ul id="user-list">
                <!-- Lista de usuários será carregada aqui -->
            </ul>
        </aside>

        <section id="chat-section">
            <div id="chat-header">
                <h3 id="general-chat">Chat Geral</h3>
            </div>
            <div id="chat" class="chat-content">
                <!-- Mensagens aparecerão aqui -->
            </div>
                <form id="chat-form" method="post" enctype="multipart/form-data">
                    <input type="text" id="message" name="message" placeholder="Digite sua mensagem">
                    <input type="file" id="file" name="file" accept="image/*,application/pdf,audio/*,video/*">
                    <button type="submit">Enviar</button>
                </form>
        </section>

        <aside id="groups">
            <h3>Grupos</h3>
            <ul id="group-list">
                <!-- Grupos aparecerão aqui -->
            </ul>
        </aside>
        
        <aside id="clients">
            <h3>Clientes</h3>
            <ul id="client-list">
                <!-- Lista de clientes será carregada aqui -->
            </ul>
        </aside>

    </main>

    <script src="js/ajax.js"></script>
    <script src="js/notific.js"></script>
</body>
</html>
