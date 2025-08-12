<?php
session_start();
require_once '../restapi/Database.php';

header('Content-Type: application/json');

try {
    $result = getTeamsForSelect();
    echo json_encode($result);
    
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}

function getTeamsForSelect() {
    // Direct database connection
    $db = new Database();
    $connection = $db->getConnection();
    
    // Query to get all teams
    $query = "SELECT id_team, nome_team, descricao_team FROM Teams ORDER BY nome_team ASC";
    $stmt = $connection->prepare($query);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $teams = [];
    
    while ($row = $result->fetch_assoc()) {
        $teams[] = $row;
    }
    
    $stmt->close();
    $connection->close();
    
    return $teams;
}
?>