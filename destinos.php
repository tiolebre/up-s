<?php
// Incluir o cabeçalho (header.php) da pasta "includes"
include('includes/header.php');
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Turístico - Minas Gerais</title>
    <link rel="stylesheet" href="css/destinos.css"> <!-- Caminho para o arquivo CSS -->
    <!-- Incluir o Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEJXQzPOa7lyqC11rS7DdEm2sG56TzcYPi5SlDdGVxuPt+5hM6poH5poS9AKT" crossorigin="anonymous">
</head>
<body>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8">
            <div class="chat-container">
                <h1 class="chat-title">Up!S Chat: Qual o seu Destino? </h1>
                <p>Faça uma pergunta sobre os melhores destinos turísticos de Minas Gerais!</p>

                <div id="chat-box" class="chat-box"></div>
            
                <div id="input-area" class="input-area d-flex align-items-center">
                    <!-- Caixa de entrada do usuário -->
                    <input type="text" id="user-input" placeholder="Digite sua pergunta..." class="input-field form-control" />
                    <!-- GIF de "digitando" -->
                    <img id="typing-gif" src="imagens/typing.gif" alt="Digitando..." class="typing-gif ms-2" style="display: none; width: 25px;" />
                    <button onclick="sendQuestion()" class="btn btn-primary ms-2">Enviar</button>
                </div>

                <div id="map" class="map-container mt-4"></div>
            </div>
        </div>

        <!-- Coluna para o mascote -->
        <div class="col-md-4 d-flex justify-content-center">
            <div class="mascote-container text-center">
                <!-- Mascote -->
                <img src="imagens/mascote.gif" alt="Mascote" class="img-fluid rounded-circle">
            </div>
        </div>
    </div>
</div>

<script>
    // Função para enviar a pergunta do usuário
    function sendQuestion() {
        const question = document.getElementById('user-input').value.trim(); // Remover espaços extras

        // Verifica se a pergunta não está vazia
        if (question === '') {
            addMessage('Up!S Chat: Por favor, digite uma pergunta válida.'); // Resposta se a pergunta estiver vazia
            return;
        }

        // Adiciona a mensagem do usuário no chat
        addMessage('Você: ' + question);

        // Exibe o GIF de "digitando"
        document.getElementById('typing-gif').style.display = 'inline-block';

        // Chama a função para buscar a resposta do ChatGPT
        getAnswerFromChatGPT(question);
    }

    // Função para adicionar uma mensagem ao chat
    function addMessage(message) {
        const chatBox = document.getElementById('chat-box');
        chatBox.innerHTML += '<p>' + message + '</p>';
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    // Função para interagir com o modelo de linguagem (ChatGPT)
    async function getAnswerFromChatGPT(question) {
        try {
            const response = await fetch('ask_chatgpt.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ question: question })  // Passando a pergunta corretamente
            });

            const data = await response.json();
            const answer = data.answer;

            // Exibe a resposta do ChatGPT
            addMessage('ChatGPT: ' + answer);

            // Esconde o GIF de "digitando" assim que a resposta é recebida
            document.getElementById('typing-gif').style.display = 'none';

            // Se a resposta mencionar algum lugar, buscar o local no Google Maps
            const places = extractPlacesFromAnswer(answer);
            if (places.length > 0) {
                showPlacesOnMap(places);
            }

        } catch (error) {
            console.error('Erro ao chamar a API do ChatGPT:', error);
            addMessage('Desculpe, houve um erro ao processar sua pergunta.');
            document.getElementById('typing-gif').style.display = 'none'; // Esconde o GIF em caso de erro
        }
    }

    // Função simples para extrair nomes de lugares da resposta
    function extractPlacesFromAnswer(answer) {
        const places = ['Ouro Preto', 'Tiradentes', 'Capitolio', 'Inhotim']; // Exemplos fixos, pode-se melhorar com um NLP mais avançado.
        return places.filter(place => answer.includes(place));
    }

    // Função para mostrar lugares no mapa
    function showPlacesOnMap(places) {
        const map = new google.maps.Map(document.getElementById('map'), {
            center: { lat: -18.5153, lng: -44.1910 }, // Centraliza o mapa em Minas Gerais
            zoom: 7
        });

        places.forEach(place => {
            new google.maps.Geocoder().geocode({ address: place }, function(results, status) {
                if (status === 'OK' && results[0]) {
                    new google.maps.Marker({
                        position: results[0].geometry.location,
                        map: map,
                        title: place
                    });
                }
            });
        });
    }
</script>

<!-- Incluir API do Google Maps -->
<script async defer
src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_API_KEY&callback=initMap">
</script>

<!-- Incluir o Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pzjw8f+ua7Kw1TIq0mC9L8+SAtHxdC+Qlh7tNkZ4lqVK3jIuC7hpynTgbd46Qrk9" crossorigin="anonymous"></script>

</body>
</html>
