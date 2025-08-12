<?php
$base_url = "http://estga-dev.ua.pt/~ptaw-2025-gr4";

require_once '../restapi/Database.php';

//para o servidor usar: $base_url/restapi/PrintGoAPI.php
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
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['imagem_principal'])) {

        $idProduto = isset($_POST['id_produto']) ? $_POST['id_produto'] : null;

        // --- Processamento da imagem principal ---
        if ($_FILES['imagem_principal']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Erro no upload da imagem.");
        }

        $uploadDir = __DIR__ . '/../imagens/img_product/';
        $fileName = basename($_FILES['imagem_principal']['name']);
        $uploadFile = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['imagem_principal']['tmp_name'], $uploadFile)) {
             global $base_url;
            $fullImageUrl = $base_url . '/imagens/img_product/' . $fileName;
        }
        if (!move_uploaded_file($_FILES['imagem_principal']['tmp_name'], $uploadFile)) {
            throw new Exception("Erro ao guardar a imagem.");
        }

        // --- Processamento do modelo 3D (opcional) ---
        $model3dName = null;
        if (isset($_FILES['modelo3d_produto']) && $_FILES['modelo3d_produto']['error'] === UPLOAD_ERR_OK) {

            $model3dDir = __DIR__ . '/../public/modelos3D/';
            $model3dName = basename($_FILES['modelo3d_produto']['name']);
            $model3dFile = __DIR__ . '/../public/modelos3D/' . $model3dName;

            if (!is_uploaded_file($_FILES['modelo3d_produto']['tmp_name'])) {
                throw new Exception("Ficheiro temporário do modelo 3D não existe ou upload falhou.");
            }
            if (!is_dir($model3dDir)) {
                throw new Exception("Diretório de upload do modelo 3D não existe: $model3dDir");
            }
            if (!is_writable($model3dDir)) {
                throw new Exception("Diretório de upload do modelo 3D sem permissões de escrita: $model3dDir");
            }

            if (!move_uploaded_file($_FILES['modelo3d_produto']['tmp_name'], $model3dFile)) {
                throw new Exception("Erro ao guardar o modelo 3D.");
            }
        }

        $data = $_POST;
        $data['imagem_principal'] = $fullImageUrl;
        if ($model3dName) {
            $data['modelo3d_produto'] = $base_url . '/public/modelos3D/' . $model3dName;
        }

        $result = addProduct($data);

        echo json_encode([
            'status' => 'success',
            'data' => $result,
            'imagem' => $fileName,
            'modelo3d' => $model3dName
        ]);
        exit;
    } else {
        throw new Exception("Pedido inválido ou sem imagem.");
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
    exit;
}

function addProduct($data) {
    global $apiUrl;

    if(empty($data)) {
        throw new Exception("No user data provided");
    }

    $ch = curl_init("$apiUrl/insertProduct");

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    $response = executeCurlRequest($ch);
    $decoded = json_decode($response, true);



    if (!$decoded || (isset($decoded['success']) && !$decoded['success'])) {
        throw new Exception("Erro ao inserir produto na API: " . ($decoded['message'] ?? 'Erro desconhecido'));
    }

    return $decoded;

}
?>