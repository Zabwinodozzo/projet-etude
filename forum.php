<?php
// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "messaging";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Créer la table si elle n'existe pas encore
$sql = "CREATE TABLE IF NOT EXISTS messages (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sender VARCHAR(30) NOT NULL,
    recipient VARCHAR(30) NOT NULL,
    text TEXT NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messagerie Simple</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            height: 100vh;
            margin: 0;
        }
        #user-list {
            width: 200px;
            border-right: 1px solid #ccc;
            height: 100%;
            overflow-y: auto;
            padding: 10px;
        }
        #chat-container {
            flex-grow: 1;
            padding: 10px;
        }
        #messages {
            height: 400px;
            overflow-y: auto;
            border: 1px solid #ccc;
            margin-bottom: 10px;
            padding: 10px;
        }
    </style>
</head>
<body>

       <h1>Plateforme d'Étude</h1>
    <nav>
        <a href="index.html">Accueil</a>
        <a href="cours.html">Cours</a>
        <a href="exercices.html">Exercices</a>
        <a href="forum.html">Forum</a>
        <a href="apropos.html">À propos</a>
    </nav>
<div id="user-list">
    <h3>Utilisateurs en ligne</h3>
    <div id="online-users"></div>
</div>

<div id="chat-container">
    <h3>Discussion</h3>
    <div id="messages"></div>
    <input id="messageInput" placeholder="Votre message...">
    <button id="sendButton">Envoyer</button>
</div>

<script src="https://cdn.socket.io/4.0.0/socket.io.min.js"></script>
<script>
    const socket = io();

    const username = prompt("Entrez votre nom d'utilisateur:");
    socket.emit('register', username);

    socket.on('userList', (users) => {
        const onlineUsersDiv = document.getElementById('online-users');
        onlineUsersDiv.innerHTML = '';
        users.forEach(user => {
            const userDiv = document.createElement('div');
            userDiv.textContent = user;
            onlineUsersDiv.appendChild(userDiv);
        });
    });

    socket.on('newMessage', (messageData) => {
        const messagesDiv = document.getElementById('messages');
        const messageDiv = document.createElement('div');
        messageDiv.textContent = `${messageData.sender}: ${messageData.text}`;
        messagesDiv.appendChild(messageDiv);
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    });

    document.getElementById('sendButton').onclick = () => {
        const messageInput = document.getElementById('messageInput');
        const message = messageInput.value;
        const recipient = prompt("À qui envoyer ce message?");
        socket.emit('sendMessage', { sender: username, recipient: recipient, text: message });
        messageInput.value = '';
    };
</script>

<?php
// Enregistrement d'un message
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sender = $_POST['sender'];
    $recipient = $_POST['recipient'];
    $text = $_POST['text'];

    $stmt = $conn->prepare("INSERT INTO messages (sender, recipient, text) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $sender, $recipient, $text);
    $stmt->execute();
    $stmt->close();
}
?>

</body>
</html>
