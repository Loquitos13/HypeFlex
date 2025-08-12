<?php

header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST");

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
    $response = getDimensoesByProductData();
    echo json_encode($response);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'error' => 'Request failed',
        'message' => $e->getMessage(),
        'dimensoes' => []
    ]);
}

function getDimensoesByProductData() {
    global $apiUrl;

    if (!isset($_GET['id_produto']) || empty($_GET['id_produto'])) {
        throw new Exception("ID do produto não fornecido");
    }

    $idProduto = (int)$_GET['id_produto'];

    $ch = curl_init("$apiUrl/getDimensionsByProduct/$idProduto");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPGET, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);

    $response = executeCurlRequest($ch);
    $decodedResponse = json_decode($response, true);

    if (!$decodedResponse || !$decodedResponse['success']) {
        throw new Exception($decodedResponse['message'] ?? "Erro ao obter dimensões do produto");
    }

    return [
        'dimensoes' => $decodedResponse['dimensoes'] ?? []
    ];
}
?>