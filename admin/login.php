<?php
session_start();
require_once '../restapi/Database.php';
$userId = $_SESSION['user_id'] ?? null;

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
    
    $result = adminUser($data['email'], $data['pass']);
    echo json_encode($result);
    
} catch(Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

function adminUser($adminEmail, $adminPassword) {
    global $apiUrl;

    if(empty($adminEmail) || empty($adminPassword)) {
        throw new Exception("Some data is missing");
    }

    $ch = curl_init("$apiUrl/adminByEmail/$adminEmail");

    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'

    ]);

    $response = executeCurlRequest($ch);
    $adminData = json_decode($response, true);

    if (!$adminData) {
        return [
            'status' => 'error',
            'message' => 'User not found'
        ];
    }

   /* if (!password_verify($adminPassword, $adminData['pass_admin'])) {
        return [
            'status' => 'error',
            'message' => 'Incorrect password'
        ];
    }*/

    $_SESSION['admin_email'] = $adminData['email_admin'];
    $_SESSION['admin_id'] = $adminData['id_admin'];

     return [
         'status' => 'success',
         'message' => 'Login successful',
    ];
  }

