<?php
require_once '../restapi/Database.php';

$apiUrl = "http://estga-dev.ua.pt/~ptaw-2025-gr4/restapi/PrintGoAPI.php";

function executeCurlRequest($ch)
{
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        throw new Exception("cURL Error: $error");
    }

    curl_close($ch);

    // Log the response for debugging
    error_log("API Response: " . $response);
    error_log("HTTP Code: " . $httpCode);

    // Validate JSON response
    $decodedResponse = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Invalid JSON response: " . json_last_error_msg());
    }
    
    // Set HTTP response code
    if ($httpCode >= 400) {
        http_response_code($httpCode);
        throw new Exception("API returned HTTP $httpCode");
    }
    
    return $decodedResponse;
}

header('Content-Type: application/json');

try {
    $requestMethod = $_SERVER['REQUEST_METHOD'];
    
    if ($requestMethod === 'POST') {
        $json = file_get_contents('php://input');

        if (empty($json)) {
            throw new Exception("Empty request body");
        }

        error_log("Received JSON: " . $json);

        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Invalid JSON: " . json_last_error_msg());
        }

        if (!isset($data['id'])) {
            throw new Exception("Order ID is required");
        }

        error_log("Fetching items for order ID: " . $data['id']);
        $response = getOrderItems($data['id']);
        echo json_encode($response);
    } else {
        throw new Exception("Method not allowed. Use POST instead.");
    }
} catch (Exception $e) {
    error_log("Error in orderItemsEngine.php: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

function getOrderItems($id)
{
    global $apiUrl;
    

    $url = $apiUrl . "/orderItems/" . $id;
    error_log("Making request to: " . $url);
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    
    $response = executeCurlRequest($ch);
    
    // Verificar se a resposta é um array direto ou se tem uma estrutura específica
    if (is_array($response)) {
        return [
            'status' => 'success',
            'message' => [
                'items' => $response
            ]
        ];
    } else {
        // Se a API retorna uma estrutura diferente, ajustar aqui
        return [
            'status' => 'success',
            'message' => [
                'items' => isset($response['data']) ? $response['data'] : []
            ]
        ];
    }
}
?>