<?php
// Definir a URL de uma imagem externa para o exemplo
$image_url = "https://example.com/imagem.jpg"; // Substitua com a URL da sua imagem

// Definir a chave da API e o CSE ID para a API do Google Custom Search (se você for usar)
$api_key = 'YOUR_GOOGLE_API_KEY'; // Substitua pela sua chave de API
$cse_id = 'YOUR_CSE_ID'; // Substitua pelo seu CSE ID

// Processar a ação escolhida pelo usuário
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Se a ação for 'exibir_imagem_externa', exibe a imagem externa
if ($action === 'exibir_imagem_externa') {
    echo "<h1>Exibindo Imagem Externa</h1>";
    echo "<img src='{$image_url}' alt='Imagem Externa' style='width:500px; height:auto;'>";
}

// Se a ação for 'buscar_imagem_com_curl', exibe uma imagem usando cURL
elseif ($action === 'buscar_imagem_com_curl') {
    $image_url_curl = "https://example.com/imagem.jpg"; // Substitua com o URL da imagem para cURL
    echo "<h1>Exibindo Imagem com cURL</h1>";

    // Inicializa o cURL
    $ch = curl_init($image_url_curl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $image_data = curl_exec($ch);
    curl_close($ch);

    // Exibe a imagem usando cURL
    if ($image_data) {
        header("Content-Type: image/jpeg");
        echo $image_data;
    } else {
        echo "Erro ao carregar a imagem.";
    }
}

// Se a ação for 'buscar_imagem_google', exibe imagens usando a API do Google Custom Search
elseif ($action === 'buscar_imagem_google') {
    echo "<h1>Exibindo Imagens da Pesquisa no Google</h1>";

    // Termo de pesquisa
    $query = 'pontos turísticos em Minas Gerais';

    // URL da API do Google Custom Search
    $url = "https://www.googleapis.com/customsearch/v1?q={$query}&searchType=image&key={$api_key}&cx={$cse_id}";

    // Fazer a requisição à API
    $response = file_get_contents($url);
    $data = json_decode($response, true);

    // Exibe as imagens da resposta
    if (isset($data['items'])) {
        foreach ($data['items'] as $item) {
            $image_url = $item['link'];  // URL da imagem
            echo "<img src='{$image_url}' alt='Imagem de turismo' style='width:300px; margin:10px;'>";
        }
    } else {
        echo "Não foram encontradas imagens.";
    }
}

// Caso a ação não seja especificada, exibe um menu de opções
else {
    echo "<h1>Escolha uma Opção</h1>";
    echo "<ul>
            <li><a href='?action=exibir_imagem_externa'>Exibir Imagem Externa</a></li>
            <li><a href='?action=buscar_imagem_com_curl'>Buscar Imagem com cURL</a></li>
            <li><a href='?action=buscar_imagem_google'>Buscar Imagem com Google API</a></li>
          </ul>";
}
?>

