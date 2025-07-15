<?php
session_start();
require_once 'conexao.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtém os dados do perfil
    $bio = $_POST['bio'];
    $foto_capa = $_POST['foto_capa'];  // Foto de capa
    $cidade = $_POST['cidade'];
    $estado = $_POST['estado'];
    $pais = $_POST['pais'];
    $profissao = $_POST['profissao'];
    $data_nascimento = $_POST['data_nascimento'];

    // Prepara a query para atualizar o perfil
    $sql = "UPDATE perfil SET bio = ?, foto_capa = ?, cidade = ?, estado = ?, pais = ?, profissao = ?, data_nascimento = ? WHERE usuario_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssi", $bio, $foto_capa, $cidade, $estado, $pais, $profissao, $data_nascimento, $userId);

    // Executa a query
    if ($stmt->execute()) {
        header("Location: perfil.php");  // Redireciona para a página de perfil ou onde deseja mostrar as atualizações
        exit();
    } else {
        echo "Erro ao atualizar o perfil: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Atualizar Perfil</title>
</head>
<body>

    <h2>Atualizar Perfil</h2>
    
    <form method="POST" action="atualizar_perfil.php">
        <label for="bio">Bio:</label>
        <textarea name="bio"></textarea>

        <label for="foto_capa">Foto de Capa:</label>
        <input type="text" name="foto_capa" placeholder="Caminho da foto de capa">

        <label for="cidade">Cidade:</label>
        <input type="text" name="cidade">

        <label for="estado">Estado:</label>
        <input type="text" name="estado">

        <label for="pais">País:</label>
        <input type="text" name="pais">

        <label for="profissao">Profissão:</label>
        <input type="text" name="profissao">

        <label for="data_nascimento">Data de Nascimento:</label>
        <input type="date" name="data_nascimento">

        <button type="submit">Atualizar</button>
    </form>

</body>
</html>
