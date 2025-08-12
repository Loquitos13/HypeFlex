<?php
session_start();
require_once '../restapi/Database.php';

$baseUrl = (strpos($_SERVER['HTTP_HOST'], 'estga-dev.ua.pt') !== false) 
    ? "http://estga-dev.ua.pt/~ptaw-2025-gr4" 
    : "http://localhost/PTAW";

$apiUrl = "$baseUrl/restapi/PrintGoAPI.php";

error_log("getRecentOrdersByClient.php - Iniciando...");
error_log("API URL: $apiUrl");

header('Content-Type: application/json');

try {
    $rawInput = file_get_contents('php://input');
    error_log("Input recebido: $rawInput");
    
    if (empty($rawInput)) {
        throw new Exception("Corpo da requisição vazio");
    }
    
    $data = json_decode($rawInput, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("JSON inválido: " . json_last_error_msg());
    }
    
    if (!isset($data['id_cliente'])) {
        throw new Exception("ID do cliente não fornecido");
    }
    
    $clientId = $data['id_cliente'];
    error_log("ID do cliente: $clientId");
    
    $requestUrl = "$apiUrl/userOrders/$clientId";
    error_log("Requisição para: $requestUrl");
    
    $ch = curl_init($requestUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPGET, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        throw new Exception("Erro CURL: $error");
    }
    
    curl_close($ch);
    
    error_log("Código HTTP da resposta: $httpCode");
    error_log("Resposta: " . substr($response, 0, 200) . "...");
    
    if ($httpCode === 500) {
        error_log("Erro 500 da API, retornando array vazio");
        echo json_encode([]);
        exit;
    } else if ($httpCode !== 200) {
        throw new Exception("Erro na API: Código HTTP $httpCode");
    }
    
    $responseData = json_decode($response, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Resposta não é um JSON válido: " . json_last_error_msg());
    }
    
    echo $response;
    
} catch (Exception $e) {
    error_log("Erro em getRecentOrdersByClient.php: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
