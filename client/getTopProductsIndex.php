
<?php
require_once '../restapi/Database.php';

$apiUrl = "http://estga-dev.ua.pt/~ptaw-2025-gr4/restapi/PrintGoAPI.php";

// Headers CORS para permitir fetch do frontend
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Responder a requisições OPTIONS (preflight CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

try {
    // Debug logging
    error_log("Requesting top products from API: $apiUrl/topProductsIndex");
    
    // Initialize curl session
    $ch = curl_init("$apiUrl/topProductsIndex");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPGET, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    
    // Execute request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    // Check for curl errors
    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        throw new Exception("cURL Error: $error");
    }
    
    curl_close($ch);
    error_log("API response code: $httpCode");
    
    // Check HTTP response code
    if ($httpCode < 200 || $httpCode >= 300) {
        throw new Exception("HTTP Error: $httpCode");
    }
    
    // Verify it's valid JSON
    $decoded = json_decode($response);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("Invalid JSON: " . substr($response, 0, 255));
        throw new Exception("Invalid JSON response: " . json_last_error_msg());
    }
    
    // Output the response directly
    echo $response;

} catch (Exception $e) {
    error_log("getTopProductsIndex.php error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Request failed',
        'message' => $e->getMessage()
    ]);
}
?>