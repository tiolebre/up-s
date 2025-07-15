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
    // Obtém o conteúdo do post
    $conteudo = $_POST['conteudo'];
    $midia = null;
    $tipo_midia = null;

    // Verifica se foi enviado um arquivo de mídia (foto, vídeo ou gif)
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
        // Verifique se a pasta 'uploads/' existe e crie-a se não existir
        if (!is_dir('uploads/')) {
            mkdir('uploads/', 0777, true);  // Cria a pasta uploads se ela não existir
        }
        
        // Salva a foto
        $midia = 'uploads/' . $_FILES['foto']['name'];
        if (!move_uploaded_file($_FILES['foto']['tmp_name'], $midia)) {
            die("Erro ao mover o arquivo de upload. Verifique as permissões da pasta.");
        }
        $tipo_midia = 'foto';
    } elseif (isset($_FILES['video']) && $_FILES['video']['error'] === 0) {
        // Verifique se a pasta 'uploads/' existe e crie-a se não existir
        if (!is_dir('uploads/')) {
            mkdir('uploads/', 0777, true);  // Cria a pasta uploads se ela não existir
        }

        // Salva o vídeo
        $midia = 'uploads/' . $_FILES['video']['name'];
        if (!move_uploaded_file($_FILES['video']['tmp_name'], $midia)) {
            die("Erro ao mover o arquivo de upload. Verifique as permissões da pasta.");
        }
        $tipo_midia = 'video';
    } elseif (isset($_FILES['gif']) && $_FILES['gif']['error'] === 0) {
        // Verifique se a pasta 'uploads/' existe e crie-a se não existir
        if (!is_dir('uploads/')) {
            mkdir('uploads/', 0777, true);  // Cria a pasta uploads se ela não existir
        }

        // Salva o GIF
        $midia = 'uploads/' . $_FILES['gif']['name'];
        if (!move_uploaded_file($_FILES['gif']['tmp_name'], $midia)) {
            die("Erro ao mover o arquivo de upload. Verifique as permissões da pasta.");
        }
        $tipo_midia = 'gif';
    }

    // Prepara a query para salvar o post no banco de dados
    $sql = "INSERT INTO posts (usuario_id, conteudo, midia, tipo_midia) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $userId, $conteudo, $midia, $tipo_midia);

    // Executa a query
    if ($stmt->execute()) {
        // Redireciona para o feed após salvar a postagem
        header("Location: index.php");
        exit();
    } else {
        echo "Erro ao publicar o post: " . $stmt->error;
    }
} else {
    // Se a requisição não for POST, redireciona para o feed
    header("Location: index.php");
    exit();
}
