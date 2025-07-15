<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

if (isset($_POST['post_id'])) {
    $postId = $_POST['post_id'];

    // Incrementa a quantidade de curtidas para o post
    $sql = "UPDATE posts SET curtidas = curtidas + 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $postId);

    if ($stmt->execute()) {
        // Exibe o número de curtidas do post
        $sql = "SELECT curtidas FROM posts WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $postId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        echo $row['curtidas'];
    } else {
        echo "Erro ao curtir o post: " . $stmt->error;
    }
}
?>
