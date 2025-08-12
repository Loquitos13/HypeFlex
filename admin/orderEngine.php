
<?php
require_once '../restapi/Database.php';

$apiUrl = "http://estga-dev.ua.pt/~ptaw-2025-gr4/restapi/PrintGoAPI.php";

function executeCurlRequest($ch) {
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        throw new Exception("cURL Error: $error");
    }
    
    curl_close($ch);
    
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
    
    if ($requestMethod === 'GET') {
        // Handle GET request - fetch all orders
        $response = getAllOrders();
        echo json_encode($response);
        
    } elseif ($requestMethod === 'POST') {
        // Handle POST request - get specific order by ID
        $json = file_get_contents('php://input');
        
        if (empty($json)) {
            throw new Exception("Empty request body");
        }
        
        $data = json_decode($json, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Invalid JSON: " . json_last_error_msg());
        }
        
        if (!isset($data['id'])) {
            throw new Exception("Order ID is required");
        }
        
        $response = getOrderById($data['id']);
        echo json_encode($response);
        
    } else {
        throw new Exception("Method not allowed");
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'error' => 'Request failed',
        'message' => $e->getMessage(),
        'status' => 'error'
    ]);
}

function getAllOrders() {
    global $apiUrl;
    
    $ch = curl_init("$apiUrl/getOrders");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    
    $response = executeCurlRequest($ch);
    return [
        'status' => 'success',
        'message' => 'Orders retrieved successfully',
        'data' => $response
    ];
}

function getOrderById($id) {
    global $apiUrl;
    
    $ch = curl_init("$apiUrl/completeOrderInfo/$id");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    
    $response = executeCurlRequest($ch);
    
    return [
        'status' => 'success',
        'message' => 'Order retrieved successfully',
        'data' => $response
    ];
}
?>