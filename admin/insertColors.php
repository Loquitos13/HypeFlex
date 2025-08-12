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

    $result = insertColorData($data['nome_cor'], $data['hex_cor']);
    echo json_encode($result);

} catch(Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

function insertColorData($nome_cor, $hex_cor) {
    global $apiUrl;

    if(empty($nome_cor) || empty($hex_cor)) {
        throw new Exception("Nome e hexadecimal da cor são obrigatórios");
    }

    $payload = json_encode([
        'nome_cor' => $nome_cor,
        'hex_cor' => $hex_cor
    ]);

    $ch = curl_init("$apiUrl/insertColor");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);

    $response = executeCurlRequest($ch);
    $decoded = json_decode($response, true);

    if (!isset($decoded['id_cor'])) {
        throw new Exception("Erro ao inserir produto na API: " . ($decoded['message'] ?? 'Erro desconhecido'));
    }

    return $decoded;
}