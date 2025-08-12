
<?php
session_start();
require_once '../restapi/Database.php';

// Debug log
error_log("Iniciando updateClientInfo.php");

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    error_log("Usuário não autenticado");
    echo json_encode([
        'success' => false,
        'message' => 'Usuário não autenticado'
    ]);
    exit;
}

// Receber dados JSON do POST
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Log dos dados recebidos
error_log("Dados recebidos: " . json_encode($data));

// Verificar se os dados são válidos
if (!is_array($data) || !isset($data['id_cliente'])) {
    error_log("Dados inválidos");
    echo json_encode([
        'success' => false,
        'message' => 'Dados inválidos'
    ]);
    exit;
}

// Verificar se o usuário está atualizando seu próprio perfil
if ($_SESSION['user_id'] != $data['id_cliente']) {
    error_log("Não autorizado a alterar este perfil");
    echo json_encode([
        'success' => false,
        'message' => 'Não autorizado a alterar este perfil'
    ]);
    exit;
}

// Configuração da API - LOCAL para testes
$apiUrl = "http://localhost/PTAW/restapi/PrintGoAPI.php";

// Preparar dados para enviar para a API
$apiData = [
    'id_cliente' => $data['id_cliente'],
    'nome_cliente' => $data['nome_cliente'] ?? '',
    'email_cliente' => $data['email_cliente'] ?? '',
    'contacto_cliente' => $data['contacto_cliente'] ?? '',
    'morada_cliente' => $data['morada_cliente'] ?? '',
    'cidade_cliente' => $data['cidade_cliente'] ?? '',
    'state_cliente' => $data['state_cliente'] ?? '',
    'cod_postal_cliente' => $data['cod_postal_cliente'] ?? '',
    'pais_cliente' => $data['pais_cliente'] ?? ''
];

error_log("Dados a enviar para API: " . json_encode($apiData));

// Enviar para a API
$ch = curl_init("$apiUrl/updateClientInfo");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($apiData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
error_log("Resposta da API: " . $response);

if (curl_errno($ch)) {
    error_log("Erro cURL: " . curl_error($ch));
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao comunicar com a API: ' . curl_error($ch)
    ]);
    exit;
}

curl_close($ch);

// Retornar resposta da API
echo $response;
?>