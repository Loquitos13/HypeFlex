<?php
session_start();
require_once '../restapi/Database.php';

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
    
    $result = updateAdminInfo($data);
    echo json_encode([
        'status' => 'success',
        'message' => 'Admin information updated successfully',
        'data' => $result
    ]);
    
} catch(Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

function updateAdminInfo($data) {
    if(empty($data['id_admin'])) {
        throw new Exception("Admin ID is required");
    }
    
    $adminId = intval($data['id_admin']);
    
    // Direct database connection
    $db = new Database();
    $connection = $db->getConnection();
    
    // Build update query dynamically
    $updateFields = [];
    $params = [];
    $types = "";
    
    $allowedFields = ['nome_admin', 'email_admin', 'contacto_admin', 'funcao_admin'];
    
    foreach ($allowedFields as $field) {
        if (isset($data[$field])) {
            $updateFields[] = "$field = ?";
            $params[] = $data[$field];
            $types .= "s";
        }
    }
    
    if (empty($updateFields)) {
        throw new Exception("No fields to update");
    }
    
    $query = "UPDATE Admins SET " . implode(", ", $updateFields) . " WHERE id_admin = ?";
    $params[] = $adminId;
    $types .= "i";
    
    $stmt = $connection->prepare($query);
    $stmt->bind_param($types, ...$params);
    
    if (!$stmt->execute()) {
        throw new Exception("Failed to update admin information");
    }
    
    $affectedRows = $stmt->affected_rows;
    $stmt->close();
    $connection->close();
    
    if ($affectedRows === 0) {
        throw new Exception("No changes made or admin not found");
    }
    
    return ['updated' => true, 'affected_rows' => $affectedRows];
}
?>