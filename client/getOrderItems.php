<?php
require_once '../restapi/Database.php'; // ajusta caminho conforme a tua estrutura

$apiUrl = "http://estga-dev.ua.pt/~ptaw-2025-gr4/restapi/PrintGoAPI.php";

function executeCurlRequest($ch)
{
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        throw new Exception("cURL Error: $error");
    }

    curl_close($ch);

    json_decode($response);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Invalid JSON response: " . json_last_error_msg());
    }

    http_response_code($httpCode);
    return $response;
}

header('Content-Type: application/json');

try {
    $json = file_get_contents('php://input');

    if (empty($json)) {
        throw new Exception("Empty request body");
    }

    $data = json_decode($json, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Invalid JSON: " . json_last_error_msg());
    }

    if (!isset($data['id_encomenda'])) {
        throw new Exception("Missing 'id_encomenda' in request body");
    }

    $response = getOrderItemsData($data['id_encomenda']);
    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'error' => 'Request failed',
        'message' => $e->getMessage()
    ]);
}

function getOrderItemsData($orderId)
{
    global $apiUrl;

    $ch = curl_init("$apiUrl/getOrderItems/$orderId");

    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);

    $response = executeCurlRequest($ch);

    return [
        'status' => 'success',
        'data' => json_decode($response, true)
    ];
}
?>
