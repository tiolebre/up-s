<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Amigos • Up!S</title>
  <link rel="icon" href="imagens/logo.ico" type="image/x-icon">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <!-- CSS geral -->
  <link rel="stylesheet" href="css/style.css" />
  <!-- CSS exclusivo da página de amigos -->
  <link rel="stylesheet" href="css/amigos.css" />
</head>
<body>

  <?php require_once 'includes/header.php'; ?>

  <div class="container mt-4">
    <div class="row justify-content-center">
      <div class="col-12 col-md-10">

        <section class="ups-friends-container">
          <h2>Meus Amigos</h2>
          
          <!-- Wrapper para centralizar a grade de amigos -->
          <div class="ups-friends-grid-wrapper">
            <div class="ups-friends-grid">
              <!-- Linha 1 -->
              <div class="ups-friend-card">
                <img src="imagens/hinata.jpeg" alt="Hinata Hyuga" />
                <span>Hinata Hyuga</span>
              </div>
              <div class="ups-friend-card">
                <img src="imagens/naruto.webp" alt="Naruto Uzumaki" />
                <span>Naruto Uzumaki</span>
              </div>
              <div class="ups-friend-card">
                <img src="imagens/sasuke.jpg" alt="Sasuke Uchiha" />
                <span>Sasuke Uchiha</span>
              </div>
              <div class="ups-friend-card">
                <img src="imagens/sakura.jpeg" alt="Sakura Haruno" />
                <span>Sakura Haruno</span>
              </div>

              <!-- Linha 2 -->
              <div class="ups-friend-card">
                <img src="imagens/amigo-placeholder.png" alt="Neji Hyuga" />
                <span>Neji Hyuga</span>
              </div>
              <div class="ups-friend-card">
                <img src="imagens/amigo-placeholder.png" alt="Rock Lee" />
                <span>Rock Lee</span>
              </div>
              <div class="ups-friend-card">
                <img src="imagens/amigo-placeholder.png" alt="Tenten" />
                <span>Tenten</span>
              </div>
              <div class="ups-friend-card">
                <img src="imagens/amigo-placeholder.png" alt="Shino Aburame" />
                <span>Shino Aburame</span>
              </div>

              <!-- Linha 3 -->
              <div class="ups-friend-card">
                <img src="imagens/amigo-placeholder.png" alt="Ino Yamanaka" />
                <span>Ino Yamanaka</span>
              </div>
              <div class="ups-friend-card">
                <img src="imagens/amigo-placeholder.png" alt="Kiba Inuzuka" />
                <span>Kiba Inuzuka</span>
              </div>
              <div class="ups-friend-card">
                <img src="imagens/amigo-placeholder.png" alt="Choji Akimichi" />
                <span>Choji Akimichi</span>
              </div>
              <div class="ups-friend-card">
                <img src="imagens/amigo-placeholder.png" alt="Shikamaru Nara" />
                <span>Shikamaru Nara</span>
              </div>
            </div>
          </div>

        </section>

      </div>
    </div>
  </div>

  <?php require_once 'includes/footer.php'; ?>

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
