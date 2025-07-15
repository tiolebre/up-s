<?php
// Obter a pergunta do usuário
$data = json_decode(file_get_contents('php://input'), true);
$question = $data['question'] ?? '';

// Verifique se a pergunta foi passada
if (empty($question)) {
    echo json_encode(['answer' => 'Por favor, forneça uma pergunta.']);
    exit;
}

// Configuração da API do OpenAI
$openai_api_key = 'YOUR_OPENAI_API_KEY'; // Substitua com sua chave da API OpenAI

// Configuração da requisição para o OpenAI
$openai_url = 'https://api.openai.com/v1/chat/completions'; // Usando o endpoint correto para chat completions
$openai_data = [
    'model' => 'gpt-3.5-turbo', // Usando o modelo GPT-3.5 Turbo
    'messages' => [
        ['role' => 'system', 'content' => 'Você é um assistente de viagem especializado em destinos turísticos em Minas Gerais.'],
        ['role' => 'user', 'content' => $question]
    ],
    'max_tokens' => 150
];

// Simulação dos pontos turísticos em Minas Gerais
$simulated_tourist_spots = [
    "Igreja de São Francisco de Assis",
    "Praça da Liberdade",
    "Museu de Artes e Ofícios",
    "Parque Nacional da Serra do Cipó",
    "Inhotim (Museu de Arte Contemporânea)",
    "Lagoa do Manso",
    "Caminho dos Diamantes",
    "Igreja de Nossa Senhora do Pilar"
];

// Converter os pontos turísticos simulados em uma lista
$tourist_spot_list = implode(', ', $simulated_tourist_spots);

// Iniciar o cURL para chamar a API do OpenAI
$ch = curl_init($openai_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $openai_api_key
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($openai_data));

// Executar a requisição
$response = curl_exec($ch);

// Verificar se houve algum erro na requisição
if (curl_errno($ch)) {
    echo json_encode(['answer' => 'Erro na requisição à API OpenAI: ' . curl_error($ch)]);
    exit;
}

// Fechar cURL
curl_close($ch);

// Decodificar a resposta do OpenAI
$response_data = json_decode($response, true);

// Verificar se houve um erro na resposta do OpenAI
if (isset($response_data['choices'][0]['message']['content'])) {
    $answer = $response_data['choices'][0]['message']['content'];
} else {
    $answer = 'Claro!!!';
}

// Adicionar a lista simulada de pontos turísticos à resposta
$answer .= "\n\nAqui estão alguns dos melhores pontos turísticos em Minas Gerais: " . $tourist_spot_list;

// Retornar a resposta
echo json_encode(['answer' => $answer]);
?>
