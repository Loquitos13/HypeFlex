<?php
require_once '../restapi/Database.php';

$apiUrl = "http://estga-dev.ua.pt/~ptaw-2025-gr4/restapi/PrintGoAPI.php";

function executeCurlRequest($ch)
{
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        throw new Exception("cURL Error: $error");
    }

    curl_close($ch);

    // Log detailed response info for debugging
    error_log("=== API Response Debug ===");
    error_log("HTTP Code: " . $httpCode);
    error_log("Content Type: " . ($contentType ?? 'unknown'));
    error_log("Response Length: " . strlen($response));
    error_log("Raw Response: " . $response);
    error_log("========================");

    // Check if response is empty
    if (empty($response)) {
        throw new Exception("Empty response from API");
    }

    // Check content type
    if ($contentType && strpos($contentType, 'text/html') !== false) {
        throw new Exception("API returned HTML instead of JSON. Response: " . substr($response, 0, 500));
    }

    // Try to clean the response (remove any PHP warnings/notices)
    $cleanResponse = trim($response);
    
    // Find the first { or [ to start JSON
    $jsonStart = max(strpos($cleanResponse, '{'), strpos($cleanResponse, '['));
    if ($jsonStart !== false && $jsonStart > 0) {
        $cleanResponse = substr($cleanResponse, $jsonStart);
        error_log("Cleaned response: " . $cleanResponse);
    }

    // Validate JSON response
    $decodedResponse = json_decode($cleanResponse, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        $jsonError = json_last_error_msg();
        error_log("JSON Error: " . $jsonError);
        error_log("Attempted to parse: " . substr($cleanResponse, 0, 200));
        throw new Exception("Invalid JSON response: $jsonError. Raw response: " . substr($response, 0, 200));
    }
    
    // Set HTTP response code
    if ($httpCode >= 400) {
        http_response_code($httpCode);
        throw new Exception("API returned HTTP $httpCode. Response: " . substr($response, 0, 200));
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

        if (!isset($data['order_id'])) {
            throw new Exception("Order ID is required");
        }

        error_log("Processing order ID: " . $data['order_id']);
        
        // Preparar dados para enviar à API
        $orderData = [
            'tracking_number' => $data['numero_seguimento'] ?? null,
            'carrier' => $data['transportadora'] ?? null,
            'notify_customer' => $data['notify_customer'] ?? 0
        ];
        
        $response = processItems($data['order_id'], 'Enviado', $orderData);
        echo json_encode($response);
    } else {
        throw new Exception("Method not allowed. Use POST instead.");
    }
} catch (Exception $e) {
    error_log("Error in processItemsEngine.php: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

function processItems($orderId, $status, $additionalData = [])
{
    global $apiUrl;
    
    // CORREÇÃO: Construir URL corretamente
    // Remove .php do final e adiciona o endpoint
    $url = $apiUrl. "/updateOrderStatus/" . $orderId . "/" . urlencode($status);
    error_log("Making request to: " . $url);
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    
    // CORREÇÃO: Enviar dados adicionais se fornecidos
    if (!empty($additionalData)) {
        // Remove campos vazios ou null
        $cleanData = array_filter($additionalData, function($value) {
            return $value !== null && $value !== '';
        });
        
        if (!empty($cleanData)) {
            $jsonData = json_encode($cleanData);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
            error_log("Sending additional data: " . $jsonData);
        }
    }
    
    try {
        $response = executeCurlRequest($ch);
        
        // Verificar se a API retornou sucesso
        if (isset($response['success']) && $response['success']) {
            return [
                'success' => true,
                'message' => $response['message'] ?? 'Encomenda processada com sucesso',
                'data' => $response
            ];
        } else {
            return [
                'success' => false,
                'message' => $response['message'] ?? 'Erro ao processar encomenda',
                'data' => $response
            ];
        }
    } catch (Exception $e) {
        error_log("Error in processItems: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Erro ao comunicar com a API: ' . $e->getMessage()
        ];
    }
}
?>