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
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $idProduto = isset($_POST['id_produto']) ? $_POST['id_produto'] : null;

        // Processa nova imagem, se enviada
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {

            $uploadDir = __DIR__ . '/../imagens/img_product/';
            $fileName = $idProduto . '_' . basename($_FILES['product_image']['name']);
            $uploadFile = $uploadDir . $fileName;

            if (!move_uploaded_file($_FILES['product_image']['tmp_name'], $uploadFile)) {
                throw new Exception("Erro ao guardar a nova imagem.");
            }

            $data = $_POST;
            $data['imagem_principal'] = $fileName;
        } else {
            $data = $_POST;
            if (empty($data['imagem_principal'])) {
                throw new Exception("Imagem do produto não especificada.");
            }
        }

        // Processa novo modelo 3D, se enviado
        if (isset($_FILES['modelo3d_produto']) && $_FILES['modelo3d_produto']['error'] === UPLOAD_ERR_OK) {

            $model3dDir = __DIR__ . '/../public/modelos3D/';
            $model3dName = uniqid() . '_' . basename($_FILES['modelo3d_produto']['name']);
            $model3dFile = $model3dDir . $model3dName;

            if (!move_uploaded_file($_FILES['modelo3d_produto']['tmp_name'], $model3dFile)) {
                throw new Exception("Erro ao guardar o novo modelo 3D.");
            }

            $data['modelo3d_produto'] = $model3dName;
        }

         if (isset($data['cores'])) {
            $cores = is_array($data['cores']) ? $data['cores'] : json_decode($data['cores'], true);
            $data['variantes'] = [];
            foreach ($cores as $cor) {
                $data['variantes'][] = [
                    'id_cor' => $cor['id_cor'] ?? null,
                ];
            }
            unset($data['cores']);
        }
        if (isset($data['dimensoes'])) {
            $data['dimensoes'] = json_decode($data['dimensoes'], true);
        }

        $response = updateProductData($data);
        echo json_encode($response);
    } else {
        throw new Exception("Método inválido.");
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'error' => 'Request failed',
        'message' => $e->getMessage()
    ]);
}

function updateProductData($data) {
    global $apiUrl;

    if (empty($data)) {
        throw new Exception("No data provided");
    }

    $ch = curl_init("$apiUrl/updateProduct");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);

    $response = executeCurlRequest($ch);
    $decoded = json_decode($response, true);

    if (!is_array($decoded) || (isset($decoded['success']) && !$decoded['success'])) {
        throw new Exception("Erro ao atualizar produto na API: " . ($decoded['message'] ?? 'Erro desconhecido'));
    }

    return $decoded;
}

?>