<?php
session_start();
require_once 'conexao.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    echo "Usuário não está logado.";
    exit();
}

$usuario_id = $_SESSION['user_id'];
$amigo_id = $_POST['friend_id'] ?? null;

if (!$amigo_id) {
    echo "Amigo não encontrado.";
    exit();
}

// Insere a amizade na tabela amigos
$sql = "INSERT INTO amigos (usuario_id, amigo_id) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $usuario_id, $amigo_id);
if ($stmt->execute()) {
    // Atualiza o número de seguidores
    $updateFollowers = "UPDATE usuarios SET seguidores = seguidores + 1 WHERE id = ?";
    $stmt2 = $conn->prepare($updateFollowers);
    $stmt2->bind_param("i", $amigo_id);
    $stmt2->execute();

    // Atualiza o número de pessoas seguidas
    $updateFollowing = "UPDATE usuarios SET seguindo = seguindo + 1 WHERE id = ?";
    $stmt3 = $conn->prepare($updateFollowing);
    $stmt3->bind_param("i", $usuario_id);
    $stmt3->execute();

    echo "Amizade adicionada com sucesso!";
} else {
    echo "Erro ao adicionar amizade.";
}
?>
