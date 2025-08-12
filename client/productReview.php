<?php

session_start();
require_once '../restapi/Database.php';
$base_url = "/~ptaw-2025-gr4"; 

$apiUrl = "http://estga-dev.ua.pt/~ptaw-2025-gr4/restapi/PrintGoAPI.php";

function executeCurlRequest($ch) {
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        throw new Exception("CURL Error: $error");
    }
    curl_close($ch);

    // Verificar se a resposta é JSON válido
    json_decode($response);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Invalid JSON response: " . json_last_error_msg());
    }

    return $response;
}

header('Content-Type: application/json');

try {
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'GET') {
        // Chama função para obter feedbacks (sem ler corpo)
        $result = getFeedbacks();
        echo json_encode([
            'status' => 'success',
            'data' => $result
        ]);
        exit;
    } elseif ($method === 'POST') {
        // Processa requisição POST normalmente
        $json = file_get_contents('php://input');
        if (empty($json)) {
            throw new Exception("Empty request body");
        }
        
        // Log para debug - escreve no arquivo de log do servidor
        error_log("Request body recebido em productReview.php: " . $json);
        
        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Invalid JSON: " . json_last_error_msg());
        }
        
        // Log para debug
        error_log("Dados decodificados em productReview.php: " . print_r($data, true));
        
        // Verificação detalhada de cada campo necessário
        if (empty($data['id_cliente'])) {
            throw new Exception("ID do cliente não fornecido");
        }
        if (empty($data['id_produto'])) {
            throw new Exception("ID do produto não fornecido");
        }
        if (empty($data['comentario'])) {
            throw new Exception("Comentário não fornecido");
        }
        if (empty($data['classificacao'])) {
            throw new Exception("Classificação não fornecida");
        }
        if (empty($data['data_review'])) {
            // Se não for fornecido, usar a data atual
            $data['data_review'] = date('Y-m-d');
        }
        if (!isset($data['recommend'])) {
            // Se não for fornecido, definir como 0 (não recomenda)
            $data['recommend'] = "0";
        }
        
        // Fazer request diretamente para a API
        $ch = curl_init("$apiUrl/insertFeedback");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        
        $response = curl_exec($ch);
        $curl_error = curl_error($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        error_log("CURL HTTP Code: $http_code, Error: $curl_error, Response: $response");
        
        if ($http_code != 200) {
            throw new Exception("API retornou código HTTP $http_code: $response");
        }
        
        if (empty($response)) {
            throw new Exception("API retornou resposta vazia");
        }
        
        $decoded = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("API retornou JSON inválido: " . json_last_error_msg());
        }
        
        if (isset($decoded['success'])) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Feedback enviado com sucesso!',
                'data' => $decoded
            ]);
        } else {
            throw new Exception("Erro ao inserir feedback na API: " . ($decoded['message'] ?? 'Erro desconhecido'));
        }
        
        exit;
    } else {
        throw new Exception("Método HTTP não suportado");
    }
} catch (Exception $e) {
    error_log("Erro em productReview.php: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
    exit;
}


function addFeedback($data) {
    global $apiUrl;

    if (empty($data)) {
        throw new Exception("No data provided");
    }

    $ch = curl_init("$apiUrl/insertFeedback");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);

    $response = executeCurlRequest($ch);
    $decoded = json_decode($response, true);

    error_log("API response: " . print_r($decoded, true));

    if (!is_array($decoded) || !isset($decoded['status']) || $decoded['status'] !== 'success') {
        throw new Exception("Erro ao inserir feedback na API: " . ($decoded['message'] ?? 'Erro desconhecido'));
    }

    return $decoded;
}


function getFeedbacks() {
    global $apiUrl;

    $ch = curl_init("$apiUrl/feedbacks");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPGET, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);

    $response = executeCurlRequest($ch);

    return json_decode($response, true);
}

function getUserById($id) {
    global $apiUrl;

    $ch = curl_init("$apiUrl/userById/$id");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['id_cliente' => $id]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    $response = executeCurlRequest($ch);
    return json_decode($response, true); // Adicionado return
}
?>