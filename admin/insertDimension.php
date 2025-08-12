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

    $result = insertDimensionData($data['dimensao_tipo'], $data['tamanho'], $data['id_produto']);
    echo json_encode($result);

} catch(Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

function insertDimensionData($dimensao_tipo, $tamanho, $id_produto) {
    global $apiUrl;

    if(empty($dimensao_tipo) || empty($tamanho) || empty($id_produto)) {
        throw new Exception("Tipo de dimensão, tamanho e id de produto são obrigatórios");
    }

    $payload = json_encode([
        'dimensao_tipo' => $dimensao_tipo,
        'tamanho' => $tamanho,
        'id_produto' => $id_produto
    ]);

    $ch = curl_init("$apiUrl/insertDimension");
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