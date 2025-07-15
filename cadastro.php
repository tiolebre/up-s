<?php
session_start();
require_once 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome      = $_POST['nome'];
    $email     = $_POST['email'];
    $telefone  = $_POST['telefone'] ?? '';
    $senha     = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $pais      = $_POST['pais'] ?? '';
    $estado    = $_POST['estado'] ?? '';
    $cidade    = $_POST['cidade'] ?? '';
    $cep       = $_POST['cep'] ?? '';
    $endereco  = $_POST['endereco'] ?? '';
    $bio       = $_POST['bio'] ?? '';

    // FOTO de perfil - caminho padrão
    $foto = 'imagens/users/default.png';
    if (!empty($_FILES['foto']['name'])) {
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $nomeFoto = uniqid('user_') . '.' . $ext;
        $destino = "imagens/users/$nomeFoto";
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $destino)) {
            $foto = $destino;
        }
    }

    // Capa padrão
    $capa = 'imagens/users/narutocapa.png';

    // Prepare statement com coluna "capa" correta
    $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, telefone, senha, pais, estado, cidade, cep, endereco, bio, foto, capa) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if (!$stmt) {
        die("Erro na preparação da query: " . $conn->error);
    }

    $stmt->bind_param("ssssssssssss", $nome, $email, $telefone, $senha, $pais, $estado, $cidade, $cep, $endereco, $bio, $foto, $capa);

    if ($stmt->execute()) {
        $_SESSION['user_id']    = $stmt->insert_id;
        $_SESSION['user_name']  = $nome;
        $_SESSION['user_foto']  = $foto;
        $_SESSION['user_capa']  = $capa;
        $_SESSION['followers']  = 0;
        $_SESSION['following']  = 0;

        header("Location: login.php");
        exit();
    } else {
        $erro = "Erro ao cadastrar: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro</title>
    <link rel="stylesheet" href="css/cadastro.css">
    <link rel="icon" href="imagens/logo.ico" type="image/x-icon">
</head>
<body>

    <?php require_once 'includes/footer.php'; ?>

    <div class="login-container">

        <div class="logo-login">
            <img src="imagens/logo.png" alt="Logo da Rede Social">
            <p class="slogan-login">Compartilhe e inspire!</p>
        </div>

        <h2>Cadastro</h2>

        <form method="POST" enctype="multipart/form-data">
            <label for="nome">Nome completo:</label>
            <input type="text" name="nome" id="nome" required>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>

            <label for="repita_email">Repita o email:</label>
            <input type="email" name="repita_email" id="repita_email" required>

            <label for="telefone">Telefone:</label>
            <input type="tel" name="telefone" id="telefone" placeholder="(xx) xxxxx-xxxx">

            <label for="senha">Senha:</label>
            <input type="password" name="senha" id="senha" required>

            <label for="repita_senha">Repita a senha:</label>
            <input type="password" name="repita_senha" id="repita_senha" required>

            <label for="pais">País:</label>
            <input type="text" name="pais" id="pais">

            <label for="estado">Estado:</label>
            <input type="text" name="estado" id="estado">

            <label for="cidade">Cidade:</label>
            <input type="text" name="cidade" id="cidade">

            <label for="cep">CEP:</label>
            <input type="text" name="cep" id="cep">

            <label for="endereco">Endereço:</label>
            <input type="text" name="endereco" id="endereco" placeholder="Rua, número, complemento">

            <label for="bio">Sua bio:</label>
            <textarea name="bio" id="bio" rows="3" placeholder="Escreva algo sobre você..."></textarea>

            <label for="foto">Foto de perfil:</label>
            <input type="file" name="foto" id="foto" accept="image/*">

            <button type="submit">Cadastrar</button>
        </form>

        <div class="social-icons">
            <a href="#" class="facebook" title="Cadastrar com Facebook">
                <i class="bi bi-facebook"></i>
            </a>
            <a href="#" class="google" title="Cadastrar com Google">
                <i class="bi bi-google"></i>
            </a>
        </div>

        <p>Já tem conta? <a href="login.php">Faça login aqui</a></p>

        <?php if (isset($erro)) echo "<p style='color:red;'>$erro</p>"; ?>

    </div>

</body>
</html>
