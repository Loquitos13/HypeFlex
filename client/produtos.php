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

    curl_close($ch);

    json_decode($response);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Invalid JSON response: " . json_last_error_msg());
    }

    return $response;
}

header('Content-Type: application/json');

try {
    // Receber filtros via GET
    $categoria   = isset($_GET['categorias']) ? $_GET['categorias'] : '';
    $precoMinimo = isset($_GET['precoMin'])   ? $_GET['precoMin']   : '0';
    $precoMaximo = isset($_GET['precoMax'])   ? $_GET['precoMax']   : '999999';
    $cor         = isset($_GET['cores'])      ? $_GET['cores']      : '';
    $tamanho     = isset($_GET['tamanhos'])   ? $_GET['tamanhos']   : '';

    // Montar placeholders se vazio
    $categoriaParam = empty($categoria) ? '_' : $categoria;
    $corParam       = empty($cor)       ? '_' : $cor;
    $tamanhoParam   = empty($tamanho)   ? '_' : $tamanho;

    // Se nenhum filtro, buscar todos
    if ($categoriaParam === '_' && $corParam === '_' && $tamanhoParam === '_' &&
        $precoMinimo === '0' && $precoMaximo === '999999') {
        $url = "$apiUrl/products";
        $ch  = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $response = executeCurlRequest($ch);
        echo $response;
        exit;
    }

    // Se algum filtro for string com vírgula, transforma em array (se quiser)
    if (!is_array($categoriaParam) && strpos($categoriaParam, ',') !== false) {
        $categoriaParam = implode(',', $categoriaParam);
    }
    if (!is_array($corParam) && strpos($corParam, ',') !== false) {
        $corParam = implode(',', $corParam);
    }
    if (!is_array($tamanhoParam) && strpos($tamanhoParam, ',') !== false) {
        $tamanhoParam = implode(',', $tamanhoParam);
    }

    // Codificar antes de enviar
    $categoriaParam = urlencode($categoriaParam);
    $corParam       = urlencode($corParam);
    $tamanhoParam   = urlencode($tamanhoParam);

    $url = "$apiUrl/filterProducts/$categoriaParam/$precoMinimo/$precoMaximo/$corParam/$tamanhoParam";
    $ch  = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $response = executeCurlRequest($ch);
    echo $response;
    exit;

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status'  => 'error',
        'message' => $e->getMessage()
    ]);
}