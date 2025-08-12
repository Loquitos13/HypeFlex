<?php
session_start();
require_once '../restapi/Database.php';

header('Content-Type: application/json');

try {
    $result = getAllUsers();
    echo json_encode($result);
    
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}

function getAllUsers() {
    // Direct database connection
    $db = new Database();
    $connection = $db->getConnection();
    
    // Query to get all clients
    $query = "SELECT id_cliente, nome_cliente, email_cliente, contacto_cliente, morada_cliente FROM Clientes ORDER BY nome_cliente ASC";
    $stmt = $connection->prepare($query);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $users = [];
    
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    
    $stmt->close();
    $connection->close();
    
    return $users;
}
?>