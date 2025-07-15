<?php
session_start();
require_once 'conexao.php';  // Conexão com o banco de dados

// Verifique se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Processar o envio de postagens
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['conteudo'])) {
    // Conteúdo da postagem
    $conteudo = $_POST['conteudo'];
    $midia = NULL;
    $tipo_midia = NULL;

    // Se houver foto, salva a imagem
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        $uploadFile = $uploadDir . basename($_FILES['foto']['name']);
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $uploadFile)) {
            $midia = $uploadFile;
            $tipo_midia = 'foto';
        }
    }

    // Se houver vídeo, salva o vídeo
    if (isset($_FILES['video']) && $_FILES['video']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        $uploadFile = $uploadDir . basename($_FILES['video']['name']);
        if (move_uploaded_file($_FILES['video']['tmp_name'], $uploadFile)) {
            $midia = $uploadFile;
            $tipo_midia = 'video';
        }
    }

    // Se houver GIF, salva o GIF
    if (isset($_FILES['gif']) && $_FILES['gif']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        $uploadFile = $uploadDir . basename($_FILES['gif']['name']);
        if (move_uploaded_file($_FILES['gif']['tmp_name'], $uploadFile)) {
            $midia = $uploadFile;
            $tipo_midia = 'gif';
        }
    }

    // Inserir a postagem no banco de dados
    $sql = "INSERT INTO posts (usuario_id, conteudo, midia, tipo_midia) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $userId, $conteudo, $midia, $tipo_midia);
    $stmt->execute();
    header("Location: feed.php");  // Redireciona para o feed após a postagem
    exit();
}

// ** Excluir Post **
// Verifica se a ação de excluir post foi feita
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excluir_post'])) {
    $postId = $_POST['post_id'];

    // Verifica se o post pertence ao usuário
    $sqlCheck = "SELECT * FROM posts WHERE id = ? AND usuario_id = ?";
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->bind_param("ii", $postId, $userId);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();

    if ($resultCheck->num_rows > 0) {
        // Exclui o post
        $sqlDelete = "DELETE FROM posts WHERE id = ?";
        $stmtDelete = $conn->prepare($sqlDelete);
        $stmtDelete->bind_param("i", $postId);
        $stmtDelete->execute();
    }

    header("Location: feed.php"); // Redireciona para a página de feed
    exit();
}

// Puxa as postagens recentes
$posts = [];
$sql = "SELECT p.id, p.conteudo, p.midia, p.tipo_midia, p.data_postagem, u.nome, u.foto, p.usuario_id 
        FROM posts p 
        JOIN usuarios u ON p.usuario_id = u.id 
        ORDER BY p.data_postagem DESC LIMIT 20";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
}

// *** Novo código para Curtir e Excluir Comentários *** //

// Verifica se a ação de curtir foi feita
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['curtir'])) {
    $postId = $_POST['post_id'];

    // Verifica se o usuário já curtiu o post
    $sqlCheck = "SELECT * FROM curtidas WHERE usuario_id = ? AND post_id = ?";
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->bind_param("ii", $userId, $postId);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();

    if ($resultCheck->num_rows > 0) {
        // Se já curtiu, remove a curtida
        $sqlDelete = "DELETE FROM curtidas WHERE usuario_id = ? AND post_id = ?";
        $stmtDelete = $conn->prepare($sqlDelete);
        $stmtDelete->bind_param("ii", $userId, $postId);
        $stmtDelete->execute();
    } else {
        // Se não curtiu, adiciona a curtida
        $sqlInsert = "INSERT INTO curtidas (usuario_id, post_id) VALUES (?, ?)";
        $stmtInsert = $conn->prepare($sqlInsert);
        $stmtInsert->bind_param("ii", $userId, $postId);
        $stmtInsert->execute();
    }

    header("Location: feed.php"); // Redireciona para a página de feed
    exit();
}

// Puxa as curtidas de cada postagem
foreach ($posts as &$post) {
    $sqlLikes = "SELECT COUNT(*) AS total_likes FROM curtidas WHERE post_id = ?";
    $stmtLikes = $conn->prepare($sqlLikes);
    $stmtLikes->bind_param("i", $post['id']);
    $stmtLikes->execute();
    $resultLikes = $stmtLikes->get_result();
    $rowLikes = $resultLikes->fetch_assoc();
    $post['total_likes'] = $rowLikes['total_likes'];

    // Verifica se o usuário já curtiu o post
    $sqlUserLiked = "SELECT * FROM curtidas WHERE usuario_id = ? AND post_id = ?";
    $stmtUserLiked = $conn->prepare($sqlUserLiked);
    $stmtUserLiked->bind_param("ii", $userId, $post['id']);
    $stmtUserLiked->execute();
    $post['user_liked'] = $stmtUserLiked->get_result()->num_rows > 0;
}

// Verifica se a ação de excluir comentário foi feita
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excluir_comentario'])) {
    $comentarioId = $_POST['comentario_id'];

    // Verifica se o comentário pertence ao usuário
    $sqlCheck = "SELECT * FROM comentarios WHERE id = ? AND usuario_id = ?";
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->bind_param("ii", $comentarioId, $userId);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();

    if ($resultCheck->num_rows > 0) {
        // Exclui o comentário
        $sqlDelete = "DELETE FROM comentarios WHERE id = ?";
        $stmtDelete = $conn->prepare($sqlDelete);
        $stmtDelete->bind_param("i", $comentarioId);
        $stmtDelete->execute();
    }

    header("Location: feed.php"); // Redireciona para a página de feed
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Feed • Up!S</title>
  <link rel="icon" href="imagens/logo.ico" type="image/x-icon">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Seu CSS Geral -->
  <link rel="stylesheet" href="style.css">
  <!-- CSS exclusivo do feed -->
  <link rel="stylesheet" href="css/feed.css">
  <style>
      .card-body {
          padding: 20px;  /* Aumentando o padding para dar mais espaço ao conteúdo do post */
      }
      .btn-excluir {
          background-color: orange;
          color: white;
      }
      .btn-excluir:hover {
          background-color: darkorange;
      }
      .curtidas-num {
          font-size: 1.1em;
          font-weight: bold;
          color: #333;
      }
  </style>
</head>
<body>

  <?php require_once 'includes/header.php';?>

  <div class="container mt-4">
    <div class="row gx-4">
      
      <!-- Coluna esquerda vazia ou com atalhos -->
      <div class="col-md-3 d-none d-md-block">
        <!-- Você pode colocar atalhos aqui -->
      </div>

      <!-- Coluna central: caixa de postagem + feed de amigos -->
      <div class="col-12 col-md-6">

        <!-- Caixa de postagem (reaproveitada do index.php) -->
        <section class="postar card mb-4">
          <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
              <textarea class="form-control mb-3" name="conteudo" placeholder="Qual foi o seu destino?" required></textarea>
              <div class="d-flex justify-content-center mb-3 botoes-midia">
                <button type="button" class="btn btn-primary rounded-circle me-2" onclick="document.getElementById('inputFoto').click()">
                  <i class="bi bi-camera"></i>
                </button>
                <button type="button" class="btn btn-primary rounded-circle me-2" onclick="document.getElementById('inputVideo').click()">
                  <i class="bi bi-camera-video"></i>
                </button>
                <button type="button" class="btn btn-primary rounded-circle" onclick="document.getElementById('inputGif').click()">
                  <i class="bi bi-film"></i>
                </button>
              </div>
              <input type="file" id="inputFoto" name="foto" accept="image/*" style="display:none" />
              <input type="file" id="inputVideo" name="video" accept="video/*" style="display:none" />
              <input type="file" id="inputGif" name="gif" accept="image/gif" style="display:none" />
              <div class="text-end">
                <button type="submit" class="btn btn-primary">Publicar</button>
              </div>
            </form>
          </div>
        </section>

        <!-- Feed de amigos -->
        <section id="friend-feed">
          <?php if (empty($posts)): ?>
            <p>Nenhuma publicação ainda. Seja o primeiro a postar!</p>
          <?php else: ?>
            <?php foreach ($posts as $post): ?>
              <article class="card mb-4 friend-post">
                <div class="card-header d-flex align-items-center">
                  <img src="<?= htmlspecialchars($post['foto']) ?: 'imagens/users/default.png' ?>" class="rounded-circle me-2 friend-post__avatar" alt="Avatar">
                  <div>
                    <strong><?= htmlspecialchars($post['nome']) ?></strong><br>
                    <small><?= date('d/m/Y H:i', strtotime($post['data_postagem'])) ?></small>
                  </div>
                </div>
                <?php if (!empty($post['midia'])): ?>
                  <?php if ($post['tipo_midia'] === 'foto' || $post['tipo_midia'] === 'gif'): ?>
                    <img src="<?= htmlspecialchars($post['midia']) ?>" class="card-img-top" alt="Post com imagem">
                  <?php elseif ($post['tipo_midia'] === 'video'): ?>
                    <video controls class="card-img-top">
                      <source src="<?= htmlspecialchars($post['midia']) ?>" type="video/mp4">
                      Seu navegador não suporta o vídeo.
                    </video>
                  <?php endif; ?>
                <?php endif; ?>
                <div class="card-body">
                  <div class="text-center">
                    <span class="curtidas-num"><?= $post['total_likes'] ?> Curtidas</span>
                  </div>
                  <div class="d-flex justify-content-around">
                    <!-- Botão de curtir -->
                    <form method="POST" class="flex-fill me-2">
                        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                        <button type="submit" name="curtir" class="btn btn-light">
                            <i class="bi bi-hand-thumbs-up"></i> 
                            <?= $post['user_liked'] ? 'Descurtir' : 'Curtir' ?>
                        </button>
                    </form>
                    
                    <!-- Botão de comentar -->
                    <button class="btn btn-light flex-fill me-2"><i class="bi bi-chat"></i> Comentar</button>
                    <button class="btn btn-light flex-fill"><i class="bi bi-share"></i> Enviar</button>
                  </div>
                  
                  <!-- Botão de excluir post -->
                  <?php if ($post['usuario_id'] == $userId): ?>
                      <form method="POST" class="mt-2">
                          <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                          <button type="submit" name="excluir_post" class="btn btn-excluir w-100">
                              <i class="bi bi-trash"></i> Excluir Post
                          </button>
                      </form>
                  <?php endif; ?>
                </div>
              </article>
            <?php endforeach; ?>
          <?php endif; ?>
        </section>

      </div>

      <!-- Coluna direita para patrocinado, aniversários, contatos -->
      <div class="col-md-3 d-none d-md-block">
        <div class="card mb-4">
          <div class="card-header">Patrocinado</div>
          <div class="card-body">
            <img src="imagens/pat.png" class="img-fluid rounded mb-2" alt="Patrocinado">
            <p class="mb-0"><strong>Rede de Carreiras - Senac-MG</strong><br><small>www.rededecarreiras.com.br</small></p>
          </div>
        </div>
        <!-- Outros widgets à direita... -->
         <div class="card mb-4">
          <div class="card-header">Aniversários</div>
          <div class="card-body">
            <img src="imagens/hinata.jpeg" class="img-fluid rounded mb-2" alt="Patrocinado">
            <img src="imagens/sakura.jpeg" class="img-fluid rounded mb-2" alt="Patrocinado">
            <img src="imagens/sasuke.jpg" class="img-fluid rounded mb-2" alt="Patrocinado">
            
      </div>

    </div>
  </div>

  

  <!-- Bootstrap JS Bundle (opcional, para dropdowns etc) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="script.js"></script>

<?php require_once 'includes/footer.php';?>
</body>

</html>
