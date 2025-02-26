document.addEventListener('DOMContentLoaded', () => {
    currentChatUserId = null;
    const chatBox = document.getElementById('chat');
    const chatForm = document.getElementById('chat-form');
    const messageInput = document.getElementById('message');
    const generalChatButton = document.getElementById('general-chat');
    let messageInterval;
    let userIsAtBottom = true;
    let atendenteId = null;
    let usuarioatendente = null;
    function checkIfUserIsAtBottom() {
        const threshold = 50;
        userIsAtBottom = (chatBox.scrollTop + chatBox.clientHeight >= chatBox.scrollHeight - threshold);
    }
    chatBox.addEventListener('scroll', checkIfUserIsAtBottom);

    load_group();
    function load_group() {
        fetch('groups.php')
            .then(response => response.json())
            .then(groups => {
                const groupList = document.getElementById('group-list');
                groupList.innerHTML = '';
    
                groups.forEach(group => {
                    const groupElement = document.createElement('li');
                    groupElement.textContent = group.group_name;
    
                    groupElement.style.cursor = 'pointer';
                    groupElement.style.padding = '10px';
                    groupElement.style.borderBottom = '1px solid #ccc';
                    groupElement.addEventListener('click', () => openGroup(group.id));
                    groupList.appendChild(groupElement);
                });
    
            })
            .catch(error => console.error('Error loading groups:', error));
    }

    if (generalChatButton) {
        generalChatButton.addEventListener('click', () => {
            currentChatUserId = null;
            currentGroupId = null;
            atendenteId = null;
            usuarioatendente = null;
            console.log("Abrindo chat geral");
            chatBox.innerHTML = '';
            loadMessages(null);
            clearInterval(messageInterval);
            messageInterval = setInterval(() => {
                loadMessages(currentChatUserId);
            }, 3000);
        });
    } else {
        console.error('Elemento general-chat não encontrado.');
    }

    document.querySelector('form').addEventListener('submit', (event) => {
        event.preventDefault(); // Impede o envio padrão do formulário
    
        const fileInput = document.getElementById('file');
        const file = fileInput.files[0]; // Pega o primeiro arquivo
    
        if (!file) {
            console.error("Nenhum arquivo selecionado.");
            return;
        }
    
        // Cria um objeto FormData para enviar o arquivo
        const formData = new FormData();
        formData.append('file', file); // Adiciona o arquivo ao FormData
    
        // Envia para o servidor usando fetch
        fetch('upload.php', {
            method: 'POST',
            body: formData, // Envia os dados no formato multipart/form-data
        })
            .then((response) => response.text())
            .then((result) => {
                console.log("Resposta do servidor:", result);
            })
            .catch((error) => {
                console.error("Erro no envio:", error);
            });
    });
    
    chatForm.addEventListener('submit', (event) => {
        event.preventDefault();
        let message = messageInput.value;
        let tipo;
        let file = document.getElementById('file');
        if (!message && !file) return;
        if (message) {
            tipo = `&tipo=text`;
        } else {
            tipo = `&tipo=file`;
            console.log(file.files[0]);
            message = file.files[0].name;
        }
        const recipientId = currentChatUserId ? `&recipient_id=${currentChatUserId}` : '';
        const groupId = currentGroupId ? `&group_id=${currentGroupId}` : '';
        const atendenteIdC = atendenteId ? `&atendente_id=${atendenteId}` : '';
        const useratendenteId = usuarioatendente ? `&useratendenteId=${usuarioatendente}` : '';
        console.log(recipientId);
        console.log(groupId);
        console.log(atendenteIdC);
        console.log(useratendenteId);
        console.log("Enviando mensagem:", `message=${encodeURIComponent(message)}${recipientId}${groupId}${atendenteIdC}${useratendenteId}`);
        fetch('send_message.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            
            body: `message=${encodeURIComponent(message)}${recipientId}${groupId}${atendenteIdC}${useratendenteId}${tipo}`
        })
        .then(response => response.json())
        .then(data => {
            console.log("Resposta do servidor:", data);
            if (data.status === "success") {
                messageInput.value = "";
                file.value = "";
                if (atendenteId) {
                    loadMessagesWpp(atendenteId);                
                } else if(!groupId) {
                    loadMessages(currentChatUserId);
                } else {
                    loadMessagesGroup(currentGroupId);
                }
            } else {
                console.error("Erro ao enviar mensagem:", data.message);
            }
        })
        .catch(error => console.error('Erro na requisição AJAX:', error));
    });

    chatForm.addEventListener('file', (event) => {
        console.log("Deu certo");
    });

    function loadMessages(userId) {
        const url = userId ? `load_messages.php?recipient_id=${userId}` : 'load_messages.php';
        console.log(`Carregando mensagens para ${userId ? `o usuário: ${userId}` : 'o chat geral'}`);
        fetch(url)
            .then(response => response.json())
            .then(messages => {
                console.log(`Mensagens carregadas para ${userId ? `o usuário ${userId}` : 'o chat geral'}:`, messages);
                chatBox.innerHTML = '';

                messages.forEach(message => {
                    const messageElement = document.createElement('div');
                    messageElement.textContent = `${message.username}: ${message.message}`;
                    chatBox.appendChild(messageElement);
                });
                if (userIsAtBottom) {
                    chatBox.scrollTop = chatBox.scrollHeight;
                }
            })
            .catch(error => console.error('Error loading messages:', error));
    }

    loadUsers();
    function loadUsers() {
        fetch('load_users.php')
            .then(response => response.json())
            .then(users => {
                const userList = document.getElementById('user-list');
                userList.innerHTML = '';
    
                users.forEach(user => {
                    const userElement = document.createElement('li');
                    userElement.textContent = user.username;
                    userElement.style.cursor = 'pointer';
                    userElement.style.padding = '10px';
                    userElement.style.borderBottom = '1px solid #ccc';
                    userElement.addEventListener('click', () => openChat(user.id));
                    userList.appendChild(userElement);
                });
            })
            .catch(error => console.error('Error loading users:', error));
    }
    

    load_group();
    function load_group() {
        fetch('groups.php')
            .then(response => response.json())
            .then(groups => {
                const groupList = document.getElementById('group-list');
                groupList.innerHTML = '';

                groups.forEach(group => {
                    const groupElement = document.createElement('li');
                    groupElement.textContent = group.group_name;

                    groupElement.style.cursor = 'pointer';
                    groupElement.style.padding = '10px';
                    groupElement.style.borderBottom = '1px solid #ccc';
                    groupElement.addEventListener('click', () => openGroup(group.id));
                    groupList.appendChild(groupElement);
                });

            })
            .catch(error => console.error('Error loading groups:', error));
    }

    function openChat(userId) {
        currentGroupId = null;
        atendenteId = null;
        usuarioatendente = null;
        currentChatUserId = userId;
        console.log(`Abrindo chat privado id: ${userId}`);
        chatBox.innerHTML = '';

        loadMessages(userId);
        clearInterval(messageInterval);
        messageInterval = setInterval(() => {
            loadMessages(currentChatUserId);
        }, 3000);
    }

    function openGroup(groupId) {
        currentChatUserId = null;
        atendenteId = null;
        usuarioatendente = null;
        currentGroupId = groupId;
        console.log(`Abrindo chat do grupo: ${groupId}`);
        chatBox.innerHTML = '';

        loadMessagesGroup(groupId);
        clearInterval(messageInterval);
        messageInterval = setInterval(() => {
            loadMessagesGroup(groupId);
        }, 3000);
    }

    function loadMessagesGroup(groupId) {
        const url = groupId ? `load_messages.php?group_id=${groupId}` : 'load_messages.php';
        fetch(url)
            .then(response => response.json())
            .then(messages => {
                chatBox.innerHTML = '';

                messages.forEach(message => {
                    const messageElement = document.createElement('div');
                    messageElement.textContent = `${message.username}: ${message.message}`;
                    chatBox.appendChild(messageElement);
                });
                if (userIsAtBottom) {
                    chatBox.scrollTop = chatBox.scrollHeight;
                }
            })
            .catch(error => console.error('Error loading messages:', error));
    }

    loadClients();
    function loadClients() {
        fetch('load_clients.php')
            .then(response => response.json())
            .then(clients => {
                const clientsList = document.getElementById('client-list');
                clientsList.innerHTML = '';
                clients.forEach(clients => {
                    const clientsElement = document.createElement('li');
                    clientsElement.textContent = clients.nome;
                    clientsElement.style.cursor = 'pointer';
                    clientsElement.style.padding = '10px';
                    clientsElement.style.borderBottom = '1px solid #ccc';
                    clientsElement.addEventListener('click', () => openChatWpp(clients.user_id, clients.atendente));
                    clientsList.appendChild(clientsElement);
                });
            })
            .catch(error => console.error('Error loading clients:', error));
    }
    loadUsers();

    function openChatWpp(atendente, usuarioat) {
        currentGroupId = null;
        currentChatUserId = null;
        atendenteId = atendente;
        usuarioatendente = usuarioat;
        console.log(atendente);
        chatBox.innerHTML = '';

        loadMessagesWpp(atendente);
        clearInterval(messageInterval);
        messageInterval = setInterval(() => {
            if (!isAudioPlaying) {
                loadMessagesWpp(atendente);
            }
        }, 3000);
    }

    function loadMessagesWpp(atendente) {
        const url = atendente ? `load_messages_wpp.php?atendente=${atendente}` : 'load_messages_wpp.php';
        fetch(url)
            .then(response => response.json())
            .then(messages => {
                console.log(messages);
                chatBox.innerHTML = '';
    
                messages.forEach(message => {
                    const messageElement = document.createElement('div');
    
                    if (message.tipo === 'audio') {
                        const audioPlayer = document.createElement('audio');
                        audioPlayer.controls = true;
                        audioPlayer.src = message.mensagem;
                        messageElement.appendChild(audioPlayer);
                    } else {
                        messageElement.textContent = `${message.nome}: ${message.mensagem}`;
                    }
    
                    chatBox.appendChild(messageElement);
                });
    
                if (userIsAtBottom) {
                    chatBox.scrollTop = chatBox.scrollHeight;
                }
            })
            .catch(error => console.error('Error loading messages:', error));
    }
    let isAudioPlaying = false;

    document.addEventListener("play", function (e) {
        if (e.target.tagName === "AUDIO") {
            isAudioPlaying = true;
        }
    }, true);

    document.addEventListener("pause", function (e) {
        if (e.target.tagName === "AUDIO") {
            isAudioPlaying = false;
        }
    }, true);

    
    
});