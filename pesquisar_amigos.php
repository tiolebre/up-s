
<?php
session_start();
require_once 'conexao.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$query = $_GET['query'] ?? '';

// Busca amigos pelo nome
$sql = "SELECT id, nome, foto FROM usuarios WHERE nome LIKE ? LIMIT 20";
$stmt = $conn->prepare($sql);
$searchQuery = "%$query%";
$stmt->bind_param("s", $searchQuery);
$stmt->execute();
$result = $stmt->get_result();

$amigos = [];
while ($row = $result->fetch_assoc()) {
    $amigos[] = $row;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Pesquisar Amigos</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>

<?php require_once 'includes/header.php'; ?>

<div class="container mt-4">
    <h2>Resultados da Pesquisa</h2>
    <div class="row">
        <?php foreach ($amigos as $amigo): ?>
            <div class="col-12 col-md-4">
                <div class="card mb-3">
                    <img src="<?= htmlspecialchars($amigo['foto']) ?: 'imagens/users/default.png' ?>" class="card-img-top" alt="Foto de <?= htmlspecialchars($amigo['nome']) ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($amigo['nome']) ?></h5>
                        <button class="btn btn-primary" onclick="addFriend(<?= $amigo['id'] ?>)">Adicionar Amigo</button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
function addFriend(userId) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'add_friend.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            alert(xhr.responseText);
            location.reload(); // Recarrega a página para atualizar o número de seguidores
        }
    };
    xhr.send('friend_id=' + userId);
}
</script>

</body>
</html>
