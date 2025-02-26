<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_POST['email'];

    $sql = "INSERT INTO users (username, password, email) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $password, $email);

    if ($stmt->execute()) {
        echo "Registration successful!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="css/inicial.css">
</head>
<body>
    <h2>Register</h2>
    <form method="POST" action="register.php">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br>
        
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br>

        <button type="submit">Register</button>
    </form>
    <a href="index.php"><br>Voltar</a>
</body>
</html>
