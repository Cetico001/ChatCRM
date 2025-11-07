<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: dashboard.php");
            exit();
        } else {
            echo "Incorrect password!";
        }
    } else {
        echo "Seu usuário não está cadastrado no nosso sistema!";
    }

    $stmt->close();
}
?>

<?php
include 'db.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM - Login</title>
    <link rel="stylesheet" href="css/inicial.css">
</head>
<body>
    <header>
        <img src="img/logo.png" alt="Consórcio TRANSUPLE" class="logo">
        <h1>Consórcio TRANSUPLE</h1>
    </header>
    <main>
        <h2>Entre no CRM</h2>
        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="username">Usuário:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Senha:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
        </form>
        <div class="links">
            <a href="register.php">Criar uma conta</a>
            <a href="index.php"><br>Voltar</a>
        </div>
    </main>
</body>
</html>
