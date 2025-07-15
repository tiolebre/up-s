<?php
session_start();

$tipo = $_POST['tipo'] ?? null;

if (!isset($_FILES['imagem']) || !$tipo) {
    http_response_code(400);
    echo "Dados incompletos.";
    exit;
}

$diretorio = '';
if ($tipo === 'foto') {
    $diretorio = 'imagens/users/';
} elseif ($tipo === 'capa') {
    $diretorio = 'imagens/capas/';
} else {
    http_response_code(400);
    echo "Tipo inválido.";
    exit;
}

if (!is_dir($diretorio)) {
    mkdir($diretorio, 0755, true);
}

$extensao = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
$nomeArquivo = uniqid() . '.' . $extensao;
$caminhoCompleto = $diretorio . $nomeArquivo;

if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminhoCompleto)) {
    // Atualizar sessão (ou banco) para refletir nova imagem
    if ($tipo === 'foto') {
        $_SESSION['user_foto'] = $caminhoCompleto;
    } elseif ($tipo === 'capa') {
        $_SESSION['user_capa'] = $caminhoCompleto;
    }

    // Retorna o caminho para o fetch usar no front
    echo $caminhoCompleto;
} else {
    http_response_code(500);
    echo "Erro ao salvar a imagem.";
}
?>
