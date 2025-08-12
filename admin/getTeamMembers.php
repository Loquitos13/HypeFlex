<?php
session_start();
require_once '../restapi/Database.php';

header('Content-Type: application/json');

try {
    $json = file_get_contents('php://input');
    
    if(empty($json)) {
        $json = '{}';
    }
    
    $data = json_decode($json, true);
    
    if(json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Invalid JSON: " . json_last_error_msg());
    }
    
    $result = getTeamMembers($data);
    echo json_encode([
        'status' => 'success',
        'data' => $result
    ]);
    
} catch(Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

function getTeamMembers($data) {
    $db = new Database();
    $connection = $db->getConnection();
    
    $teamId = isset($data['team_id']) ? intval($data['team_id']) : null;
    
    if ($teamId) {
        // Get members of specific team
        $query = "SELECT tm.id_team_member as id, tm.role_member as role, tm.status_member as status,
                         c.nome_cliente as first_name, c.email_cliente as email,
                         t.nome_team as team_name
                  FROM TeamMembers tm
                  JOIN Clientes c ON tm.id_cliente = c.id_cliente
                  JOIN Teams t ON tm.id_team = t.id_team
                  WHERE tm.id_team = ?
                  ORDER BY tm.data_adicao DESC";
        
        $stmt = $connection->prepare($query);
        $stmt->bind_param("i", $teamId);
    } else {
        // Get all team members
        $query = "SELECT tm.id_team_member as id, tm.role_member as role, tm.status_member as status,
                         c.nome_cliente as first_name, c.email_cliente as email,
                         t.nome_team as team_name
                  FROM TeamMembers tm
                  JOIN Clientes c ON tm.id_cliente = c.id_cliente
                  JOIN Teams t ON tm.id_team = t.id_team
                  ORDER BY tm.data_adicao DESC";
        
        $stmt = $connection->prepare($query);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $members = [];
    
    while ($row = $result->fetch_assoc()) {
        $members[] = $row;
    }
    
    $stmt->close();
    $connection->close();
    
    return $members;
}
?>