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
    
    $result = addTeamMember($data);
    echo json_encode([
        'status' => 'success',
        'message' => 'Team member added successfully',
        'data' => $result
    ]);
    
} catch(Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

function addTeamMember($data) {
    if(empty($data['id_cliente'])) {
        throw new Exception("Client ID is required");
    }
    
    $clienteId = intval($data['id_cliente']);
    $role = isset($data['role']) ? $data['role'] : 'member';
    $teamId = isset($data['id_team']) ? intval($data['id_team']) : 1; // Default team
    
    // Validate role
    if (!in_array($role, ['member', 'admin'])) {
        throw new Exception("Invalid role. Must be 'member' or 'admin'");
    }
    
    $db = new Database();
    $connection = $db->getConnection();
    
    // Check if member already exists in the team
    $checkQuery = "SELECT id_team_member FROM TeamMembers WHERE id_team = ? AND id_cliente = ?";
    $checkStmt = $connection->prepare($checkQuery);
    $checkStmt->bind_param("ii", $teamId, $clienteId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        $checkStmt->close();
        $connection->close();
        throw new Exception("User is already a member of this team");
    }
    $checkStmt->close();
    
    // Add team member
    $insertQuery = "INSERT INTO TeamMembers (id_team, id_cliente, role_member) VALUES (?, ?, ?)";
    $insertStmt = $connection->prepare($insertQuery);
    $insertStmt->bind_param("iis", $teamId, $clienteId, $role);
    
    if (!$insertStmt->execute()) {
        $insertStmt->close();
        $connection->close();
        throw new Exception("Failed to add team member");
    }
    
    $newMemberId = $connection->insert_id;
    $insertStmt->close();
    $connection->close();
    
    return ['id' => $newMemberId, 'added' => true];
}
?>