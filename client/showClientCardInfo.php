<?php
session_start();
require_once '../restapi/Database.php';

error_log("=== showClientCardInfo.php ===");
error_log("Iniciando busca de cartão para cliente ID: " . ($_GET['id_cliente'] ?? $_SESSION['user_id'] ?? 'não especificado'));

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

    // Criar URL para API
    $endpoint = "$apiUrl/showClientCardInfo/$id_cliente";

    // Inicializar cURL
    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_HTTPGET, true);

    $response = executeCurlRequest($ch);
    error_log("Resposta da API: " . $response);

    // Verificar resposta válida (pode ser null se o cliente não tiver cartão)
    if (empty($response) || $response === "null") {
        echo json_encode([
            'success' => false,
            'message' => 'Cliente não tem cartão cadastrado'
        ]);
        exit;
    }

    // Decodificar resposta para verificar se é JSON válido
    $decoded = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode([
            'success' => false,
            'message' => 'Resposta inválida da API: ' . json_last_error_msg()
        ]);
        exit;
    }

    // Se chegou até aqui, a resposta é JSON válido
    echo json_encode([
        'success' => true,
        'data' => $decoded
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>