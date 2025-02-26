function checkNewMessages() {
    console.log('Checando');
    fetch('notif.php')
        .then(response => response.json())
        .then(messages => {
            if (messages.length > 0) {
                messages.forEach(message => {
                    showNotification(message);
                });
            }
        })
        .catch(error => console.error('Error fetching messages:', error));
}

function showNotification(message) {
    // Verifica se o navegador suporta notificações
    if (Notification.permission === 'granted') {
        new Notification(`Nova mensagem de ${message.atendente}`, {
            body: message.mensagem,
            icon: 'img/logo.png' // Opcional
        });
    } else if (Notification.permission !== 'denied') {
        Notification.requestPermission().then(permission => {
            if (permission === 'granted') {
                showNotification(message);
            }
        });
    }
}

// Solicita permissão para notificações
if (Notification.permission !== 'granted') {
    Notification.requestPermission();
}

setInterval(checkNewMessages, 3000);
