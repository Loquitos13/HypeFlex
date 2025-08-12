
<?php
session_start();
require_once '../restapi/Database.php';

$apiUrl = "http://estga-dev.ua.pt/~ptaw-2025-gr4/restapi/PrintGoAPI.php";

function executeCurlRequest($ch)
{
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        throw new Exception("CURL Error: $error");
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return $response;
}

header('Content-Type: application/json');

try {
    // Obter ID do cliente da URL ou da sessão
    $id_cliente = isset($_GET['id_cliente']) ? $_GET['id_cliente'] : ($_SESSION['user_id'] ?? null);

    if (!$id_cliente) {
        throw new Exception("ID do cliente não fornecido");
    }
    
    // Log para debug
    error_log("Buscando informações para cliente ID: $id_cliente");
    
    // Criar URL para API
    $endpoint = "$apiUrl/showClientInfo/$id_cliente";
    error_log("Endpoint: $endpoint");

    // Inicializar cURL
    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_HTTPGET, true);
    
    // Executar requisição
    $response = executeCurlRequest($ch);
    
    // Verificar resposta válida
    if (empty($response)) {
        // Se a resposta estiver vazia, criar uma resposta JSON válida
        echo json_encode([
            'success' => false,
            'message' => 'Resposta vazia da API'
        ]);
        exit;
    }
    
    // Verificar se é JSON válido
    $decoded = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("Resposta inválida da API: $response");
        echo json_encode([
            'success' => false,
            'message' => 'Resposta inválida da API: ' . json_last_error_msg()
        ]);
        exit;
    }
    
    // Se chegou até aqui, a resposta é JSON válido
    echo $response;
    
} catch (Exception $e) {
    error_log("Erro em showClientInfo.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>