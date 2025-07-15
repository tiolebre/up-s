<?php
session_start();
require_once 'conexao.php';  // Inclua sua conexão com o banco de dados

// Verifique se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    echo "Erro: Usuário não está logado.";
    exit();
}

$usuario_id = $_SESSION['user_id'];  // Pega o ID do usuário logado

// Verifica se os dados do comentário foram enviados
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['comentario']) && !empty($_POST['comentario']) && isset($_POST['post_id'])) {
        $comentario = $_POST['comentario'];  // O conteúdo do comentário
        $post_id = $_POST['post_id'];        // O ID do post onde o comentário será adicionado
        $comentario_id = isset($_POST['comentario_id']) ? $_POST['comentario_id'] : NULL;  // Caso seja uma resposta, pegamos o ID do comentário original

        // Se for uma resposta a um comentário, o comentario_id será preenchido
        if ($comentario_id != NULL) {
            // Inserindo a resposta ao comentário
            $sql = "INSERT INTO comentarios (usuario_id, post_id, comentario, comentario_id) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iisi", $usuario_id, $post_id, $comentario, $comentario_id);  // Inserindo como resposta
        } else {
            // Se for um comentário normal, sem resposta a outro comentário
            $sql = "INSERT INTO comentarios (usuario_id, post_id, comentario) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iis", $usuario_id, $post_id, $comentario);  // Inserindo comentário simples
        }

        // Executa a query para adicionar o comentário
        if ($stmt->execute()) {
            // Se inserido corretamente, vamos pegar todos os comentários atualizados para esse post
            $comentarios = [];
            $comentariosSql = "SELECT c.comentario, c.data_comentario, u.nome, c.id, c.comentario_id 
                               FROM comentarios c 
                               JOIN usuarios u ON c.usuario_id = u.id  // Fazendo referência ao 'id' da tabela usuarios
                               WHERE c.post_id = ? 
                               ORDER BY c.data_comentario DESC";
            $stmt = $conn->prepare($comentariosSql);
            $stmt->bind_param("i", $post_id);
            $stmt->execute();
            $resultComentarios = $stmt->get_result();

            while ($comentario = $resultComentarios->fetch_assoc()) {
                $comentarios[] = $comentario;
            }

            // Exibe todos os comentários (ou resposta) atualizados
            foreach ($comentarios as $comentario) {
                echo "<div class='comentario'>
                        <strong>" . htmlspecialchars($comentario['nome']) . "</strong>
                        <small>" . date('d/m/Y H:i', strtotime($comentario['data_comentario'])) . "</small>
                        <p>" . nl2br(htmlspecialchars($comentario['comentario'])) . "</p>
                        <button class='curtir-comentario' onclick='curtirComentario(" . $comentario['id'] . ")'>
                            <i class='bi bi-hand-thumbs-up'></i> Curtir
                        </button>
                        <span id='comentario-likes-count-" . $comentario['id'] . "'>0</span> curtidas";

                // Exibe o botão de responder se for um comentário normal
                if ($comentario['comentario_id'] == NULL) {
                    echo "<button class='responder' onclick='responderComentario(" . $comentario['id'] . ", " . $post_id . ")'>Responder</button>";
                    echo "<div id='respostas-comentario-" . $comentario['id'] . "'></div>";
                }

                echo "</div>";
            }
        } else {
            echo "Erro ao adicionar o comentário: " . $stmt->error;
        }
    }
}
?>
