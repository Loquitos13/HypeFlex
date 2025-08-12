<?php
session_start();
require_once '../restapi/Database.php';

$apiUrl = "http://estga-dev.ua.pt/~ptaw-2025-gr4/restapi/PrintGoAPI.php";

function executeCurlRequest($ch) {
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        throw new Exception("CURL Error: $error");
    }

    curl_close($ch);

    json_decode($response);
    if(json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Invalid JSON response: " . json_last_error_msg());
    }

    return $response;
}

header('Content-Type: application/json');

try {
    $json = file_get_contents('php://input');

    if(empty($json)) {
        throw new Exception("Empty request body");
    }

    $data = json_decode($json, true);

    if(json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Invalid JSON: " . json_last_error_msg());
    }

    $result = insertDimensionData($data['id_produto'], $data['id_cor'], $data['promocao']);
    echo json_encode($result);

} catch(Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

function insertDimensionData($id_produto, $id_cor, $promocao) {
    global $apiUrl;

    if(empty($id_produto) || empty($id_cor)) {
        throw new Exception("Id de produto e id de cor são obrigatórios");
    }

    $payload = json_encode([
        'id_produto' => $id_produto,
        'id_cor' => $id_cor,
        'promocao' => $promocao
    ]);

    $ch = curl_init("$apiUrl/insertProductVariant");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);

    $response = executeCurlRequest($ch);
    $decoded = json_decode($response, true);

    if (!$decoded || (isset($decoded['success']) && !$decoded['success'])) {
        throw new Exception("Erro ao inserir produto na API: " . ($decoded['message'] ?? 'Erro desconhecido'));
    }

    return $decoded;
}