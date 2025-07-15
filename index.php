<?php
session_start();
require_once 'conexao.php';  // Inclui a conexão com o banco de dados

// Verifique se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");  // Se não estiver logado, redireciona para a página de login
    exit();
}

// Pega o ID do usuário logado
$usuario_id = $_SESSION['user_id'];

// Puxa os últimos posts
$posts = [];
$sql = "SELECT p.id, p.conteudo, p.midia, p.tipo_midia, p.data_postagem, u.nome, u.foto 
        FROM posts p 
        JOIN usuarios u ON p.usuario_id = u.id 
        ORDER BY p.data_postagem DESC LIMIT 20";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $posts[] = $row;
  }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Up!S</title>
  <link rel="icon" href="imagens/logo.ico" type="image/x-icon" />
  <link rel="stylesheet" href="style.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">  <!-- Inclusão dos ícones Bootstrap -->
</head>
<body>

<?php require_once 'includes/header.php'; ?>

<div class="container">
  <!-- Sidebar perfil -->
  <aside class="profile-sidebar">
    <div class="ps-box">
      <h3>Apresentação</h3>
      <p id="apresentacao-text"><?= $_SESSION['user_apresentacao'] ?? 'Apresentação ainda não definida' ?></p>
      <button class="btn-edit" onclick="editarApresentacao()">
        <i class="bi bi-pencil"></i> Editar apresentação
      </button>
    </div>

    <div class="ps-box">
      <h3>Sobre</h3>
      <ul id="sobre-lista">
        <li><i class="bi bi-geo-alt-fill"></i> <?= $_SESSION['user_localizacao'] ?? 'Não informado' ?></li>
        <li><i class="bi bi-calendar3"></i> <?= $_SESSION['user_data_nascimento'] ?? 'Não informado' ?></li>
        <li><i class="bi bi-briefcase-fill"></i> <?= $_SESSION['user_profissao'] ?? 'Não informado' ?></li>
        <li><i class="bi bi-globe"></i> <?= $_SESSION['user_idiomas'] ?? 'Não informado' ?></li>
      </ul>
      <button class="btn-edit" onclick="editarSobre()">
        <i class="bi bi-pencil"></i> Editar detalhes
      </button>
    </div>

    <div class="ps-box">
      <h3>Destinos marcantes</h3>
      <ul id="destinos-lista">
        <li><i class="bi bi-star-fill text-warning"></i> Paris, França</li>
        <li><i class="bi bi-star-fill text-warning"></i> Kyoto, Japão</li>
        <li><i class="bi bi-star-fill text-warning"></i> Rio de Janeiro, Brasil</li>
      </ul>
      <button class="btn-edit" onclick="editarDestinos()">
        <i class="bi bi-pencil"></i> Editar destinos
      </button>
    </div>
  </aside>

  <!-- Feed de postagens -->
  <div class="main-column">
    <!-- Formulário de Postagem -->
    <section class="postar">
      <form id="formPostar" method="POST" action="publicar.php" enctype="multipart/form-data">
        <textarea name="conteudo" placeholder="Qual foi o seu destino?" required></textarea>

        <input type="file" id="inputFoto" name="foto" accept="image/*" style="display:none" />
        <input type="file" id="inputVideo" name="video" accept="video/*" style="display:none" />
        <input type="file" id="inputGif" name="gif" accept="image/gif" style="display:none" />

        <div class="botoes-midia">
          <button type="button" onclick="document.getElementById('inputFoto').click()" aria-label="Adicionar foto"><i class="bi bi-camera"></i></button>
          <button type="button" onclick="document.getElementById('inputVideo').click()" aria-label="Adicionar vídeo"><i class="bi bi-camera-video"></i></button>
          <button type="button" onclick="document.getElementById('inputGif').click()" aria-label="Adicionar GIF"><i class="bi bi-film"></i></button>
        </div>

        <button type="submit">Publicar</button>
      </form>
    </section>

    <!-- Timeline -->
    <section id="timeline">
      <?php if (empty($posts)): ?>
        <p>Nenhuma publicação ainda. Seja o primeiro a postar!</p>
      <?php else: ?>
        <?php foreach ($posts as $post): ?>
          <article class="post" data-postid="<?= $post['id'] ?>">
            <header>
              <img src="<?= htmlspecialchars($post['foto'] ?: 'imagens/users/default.png') ?>" alt="Avatar de <?= htmlspecialchars($post['nome']) ?>" class="post-avatar" />
              <strong><?= htmlspecialchars($post['nome']) ?></strong>
              <small><?= date('d/m/Y H:i', strtotime($post['data_postagem'])) ?></small>
            </header>
            <p><?= nl2br(htmlspecialchars($post['conteudo'])) ?></p>

            <?php if (!empty($post['midia'])): 
              $tipo = $post['tipo_midia'];
              $src = htmlspecialchars($post['midia']);
              if ($tipo === 'foto' || $tipo === 'gif'):
            ?>
              <img src="<?= $src ?>" alt="Imagem postada" style="max-width:100%; border-radius: 8px; margin-top: 8px;" />
            <?php elseif ($tipo === 'video'): ?>
              <video controls style="max-width: 100%; border-radius: 8px; margin-top: 8px;">
                <source src="<?= $src ?>" type="video/mp4" />
                Seu navegador não suporta vídeo.
              </video>
            <?php endif; endif; ?>

            <footer>
              <!-- Botões de Curtir, Comentar, Enviar -->
              <div class="d-flex justify-content-between">
                <button class="curtir flex-fill me-2">
                  <i class="bi bi-hand-thumbs-up"></i> Curtir
                </button>
                <button class="comentar flex-fill me-2">
                  <i class="bi bi-chat"></i> Comentar
                </button>
                <button class="enviar flex-fill">
                  <i class="bi bi-share"></i> Enviar
                </button>
              </div>
              <div class="text-center mt-2">
                <span id="likes-count-<?= $post['id'] ?>" class="curtidas-num">0</span> curtidas
              </div>
              <!-- Botão Excluir Post -->
              <form method="POST" class="mt-2">
                <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                <button type="submit" name="excluir_post" class="btn-excluir w-100">
                  <i class="bi bi-trash"></i> Excluir Post
                </button>
              </form>
            </footer>

            <!-- Exibir Comentários -->
            <div class="comentarios" id="comentarios-post-<?= $post['id'] ?>">
              <?php
              // Exibir comentários do post
              $comentarios = [];
              $comentariosSql = "SELECT c.comentario, c.data_comentario, u.nome, c.id, c.comentario_id 
                                 FROM comentarios c 
                                 JOIN usuarios u ON c.usuario_id = u.id 
                                 WHERE c.post_id = ? 
                                 ORDER BY c.data_comentario DESC";
              $stmt = $conn->prepare($comentariosSql);
              $stmt->bind_param("i", $post['id']);
              $stmt->execute();
              $resultComentarios = $stmt->get_result();
              while ($comentario = $resultComentarios->fetch_assoc()) {
                $comentarios[] = $comentario;
              }
              foreach ($comentarios as $comentario):
              ?>
                <div class="comentario">
                  <strong><?= htmlspecialchars($comentario['nome']) ?></strong>
                  <small><?= date('d/m/Y H:i', strtotime($comentario['data_comentario'])) ?></small>
                  <p><?= nl2br(htmlspecialchars($comentario['comentario'])) ?></p>
                </div>
              <?php endforeach; ?>
            </div>

            <div class="adicionar-comentario">
              <input type="text" placeholder="Comente..." id="input-comentario-<?= $post['id'] ?>" />
              <button onclick="adicionarComentario(<?= $post['id'] ?>)">Enviar</button>
            </div>
          </article>
        <?php endforeach; ?>
      <?php endif; ?>
    </section>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>

<script>
// Função para adicionar um comentário via AJAX
function adicionarComentario(postId) {
  var comentario = document.getElementById('input-comentario-' + postId).value;
  if (comentario.trim() === "") return;

  var xhr = new XMLHttpRequest();
  xhr.open('POST', 'adicionar_comentario.php', true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.onreadystatechange = function () {
    if (xhr.readyState == 4 && xhr.status == 200) {
      document.getElementById('comentarios-post-' + postId).innerHTML = xhr.responseText;  // Atualiza os comentários
    }
  };
  xhr.send('post_id=' + postId + '&comentario=' + comentario);
}
</script>

</body>
</html>
