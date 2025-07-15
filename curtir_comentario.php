<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

if (isset($_POST['comentario_id'])) {
    $comentarioId = $_POST['comentario_id'];

    // Incrementa a quantidade de curtidas para o comentário
    $sql = "UPDATE comentarios SET curtidas = curtidas + 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $comentarioId);

    if ($stmt->execute()) {
        // Exibe o número de curtidas do comentário
        $sql = "SELECT curtidas FROM comentarios WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $comentarioId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        echo $row['curtidas'];
    } else {
        echo "Erro ao curtir o comentário: " . $stmt->error;
    }
}
?>
