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
    
    $result = loginUser($data['email'], $data['pass']);
    echo json_encode($result);
    
} catch(Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

function loginUser($userEmail, $userPassword) {
    global $apiUrl;

    if(empty($userEmail) || empty($userPassword)) {
        throw new Exception("Some data is missing");
    }

    $ch = curl_init("$apiUrl/userByEmail/$userEmail");

    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'

  ]);

    $response = executeCurlRequest($ch);
    $userData = json_decode($response, true);

    if (!$userData) {
        return [
            'status' => 'error',
            'message' => 'User not found'
        ];
    }

    if (!password_verify($userPassword, $userData['pass_cliente'])) {
        return [
            'status' => 'error',
            'message' => 'Incorrect password'
        ];
    }

    $_SESSION['user_id'] = $userData['id_cliente'];
    $_SESSION['user_email'] = $userData['email_cliente'];

     return [
         'status' => 'success',
         'message' => 'Login successful',
         'id_cliente' => $userData['id_cliente'],
    ];
  }

