<?php
session_start();
require_once 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    // Consulta usuário por email
    $stmt = $conn->prepare("SELECT id, nome, email, senha, foto, capa FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();

        // Verifica a senha
        if (password_verify($senha, $usuario['senha'])) {
            $_SESSION['user_id'] = $usuario['id'];
            $_SESSION['user_name'] = $usuario['nome'];
            $_SESSION['user_foto'] = $usuario['foto'];
            $_SESSION['user_capa'] = $usuario['capa'];

            header("Location: feed.php");
            exit;
        } else {
            $erro = "Senha incorreta.";
        }
    } else {
        $erro = "Email não encontrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="css/login.css">
    <link rel="icon" href="imagens/logo.ico" type="image/x-icon">
</head>
<body>
    <?php require_once 'includes/footer.php'; ?> 

    <div class="container login-container">
        <div class="logo-login">
            <img src="imagens/logo.png" alt="Logo da Rede Social">
            <p class="slogan-login">Compartilhe e inspire!</p>
        </div>

        <h2>Login</h2>

        <form method="POST">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required><br>

            <label for="senha">Senha:</label>
            <input type="password" name="senha" id="senha" required><br>

            <button type="submit">Entrar</button>
        </form>

        <p>Não tem conta? <a href="cadastro.php">Cadastre-se aqui</a></p>

        <?php if (isset($erro)) echo "<p style='color:red;'>$erro</p>"; ?>
    </div>
</body>
</html>
