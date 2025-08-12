<?php
session_start();
require_once '../restapi/Database.php';

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

    $response = getShoppingCartItensBackup($data['id_carrinho']);
    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'error' => 'Request failed',
        'message' => $e->getMessage()
    ]);
}

function getShoppingCartItensBackup($id_carrinho)
{

    global $apiUrl;

    $ch = curl_init("$apiUrl/carrinhoItensbackup/$id_carrinho");

    curl_setopt($ch, CURLOPT_HTTPHEADER, [

        'Content-Type: application/json'

    ]);

    $response = executeCurlRequest($ch);

    $cartItensData = json_decode($response, true);

    return [
        'status' => 'success',
        'message' => json_decode($response, true),
    ];
}

?>